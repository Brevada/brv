"""
Refinery
"""

import mysql.connector
import time
import math
import random
import json

import logging
from logging.config import fileConfig

fileConfig("logging_config.ini")
logger = logging.getLogger()

SECONDS_MINUTE = 60
SECONDS_HOUR = SECONDS_MINUTE * 60
SECONDS_DAY = SECONDS_HOUR * 24
SECONDS_WEEK = SECONDS_DAY * 7
SECONDS_YEAR = SECONDS_DAY * 365
SECONDS_MONTH = SECONDS_YEAR // 12

config = {
    'user': 'root',
    'password': 'root',
    'host': 'localhost',
    'database': 'redreadu_brevada',
    'raise_on_warnings': True
}

refineryOpen = True
lastFeedbackID = -1
timeStarted = 0

# Refinery
aspects = dict()
feedbackMeta = dict()


def avgOrZero(lst, parse=False, asList=True):
    """
    Average a list if len(lst)>0, otherwise
    return 0.

    Args:
        lst: List to average.
        parse: Treat list data as feedback#id.
        asList: Return a list or number.

    Returns:
        Average of list elements or 0 if len(lst)==0.
        If asList = True, returns [average, length].
    """

    if not lst:
        return 0 if not asList else [0, 0]
    total = 0

    # If parse, treat as feedback#id.
    if parse:
        total = sum(float(getField(x, 'rating')) for x in lst)
    else:
        total = sum(lst)

    avg = round(total / len(lst), 2)
    return avg if not asList else [avg, len(lst)]


def retrieveData(db):
    """
    Retrieve feedback from Brevada database
    and group retrieved data by aspect ID.

    Args:
        db: Brevada database connection.

    Returns:
        None
    """

    cursor = db.cursor()
    cursor.execute("""
    SELECT
    feedback.`id` as `id`, `AspectID`,
    UNIX_TIMESTAMP(feedback.`Date`) as `Date`,
    `Rating`, `AspectTypeID`, aspects.`StoreID` as `StoreID`,
    stores.CompanyID as CompanyID
    FROM `feedback`
    JOIN aspects ON aspects.id = feedback.AspectID
    JOIN stores ON stores.id = aspects.StoreID
    WHERE aspects.Active = 1 AND stores.Active = 1
    """)

    # Gain write-access to globals.
    global aspects
    global feedbackMeta

    # Clear any previous data from memory.
    aspects = dict()
    feedbackMeta = dict()

    for row in cursor:
        # row[0]: feedback.id,
        # row[1]: aspects.id,
        # row[2]: feedback.date,
        # row[3]: feedback.rating,
        # row[4]: aspects.aspecttypeid,
        # row[5]: aspects.storeid,
        # row[6]: stores.companyid
        feedbackMeta[str(row[0])] = \
                    [row[1], row[2], row[3], row[4], row[5], row[6]]

        # Insert feedback ID into aspect group.
        if str(row[1]) not in aspects.keys():
            aspects[str(row[1])] = []

        aspects[str(row[1])].append(int(row[0]))

    cursor.close()

    logging.debug("Retrieved feedback. Data Length: %d", len(feedbackMeta))


def getField(index, field):
    """
    Lookup feedback metadata by ID.

    Args:
        index: Feedback ID.
        field: Fields name to lookup.

    Returns:
        feedback#index.field which should be
        casted before use.
    """

    # Allow periods in field name for readability.
    field = str.replace(field.lower(), ".", "")

    # Data order is defined in retrieveData()
    try:
        datum = feedbackMeta[str(index)]
        if field == "id":
            return index
        elif field == "aspectid":
            return datum[0]
        elif field == "date":
            return datum[1]
        elif field == "rating":
            return datum[2]
        elif field == "typeid":
            return datum[3]
        elif field == "storeid":
            return datum[4]
        elif field == "companyid":
            return datum[5]
        else:
            return None
    except IndexError:
        return None
    except KeyError:
        return None


def refineAspects(db):
    """
    Generate clusters for each aspect group.

    Args:
        db: Brevada database connection.

    Returns:
        None
    """

    logger.debug("Refining aspects...")

    # Iterate through each aspect group.
    for aspectID in aspects.keys():
        feedback = aspects[aspectID]
        if len(feedback) == 0:
            continue

        # Skip if no new feedback.
        if max(feedback) <= lastFeedbackID:
            continue

        typeID = getField(feedback[0], "typeid")
        storeID = getField(feedback[0], "storeid")
        companyID = getField(feedback[0], "companyid")
        domain = (typeID, storeID, companyID, None)

        intvlAll = (0, timeStarted)
        clusterAllTime = clusterMeta(data_range(feedback, *intvlAll))
        updateCache(db, *domain, *intvlAll, *clusterAllTime[2:5])

        intvlYear = (max(1, SECONDS_YEAR // SECONDS_DAY), timeStarted)
        clusterOneYear = narrowDataRange(
                              *intvlYear,
                              *clusterAllTime[2:5]
                           )
        updateCache(db, *domain, *clusterOneYear)

        intvlSixMonths = (max(1, (SECONDS_MONTH * 6) // SECONDS_DAY),
                          timeStarted)
        clusterSixMonth = narrowDataRange(
                               *intvlSixMonths,
                               *clusterAllTime[2:5]
                            )
        updateCache(db, *domain, *clusterSixMonth)

        intvlMonth = (max(1, SECONDS_MONTH // SECONDS_DAY), timeStarted)
        clusterOneMonth = narrowDataRange(
                               *intvlMonth,
                               *clusterAllTime[2:5]
                            )
        updateCache(db, *domain, *clusterOneMonth)

        # re-calculate one week for higher short-term accuracy
        clusterOneWeek = clusterMeta(
                              data_range(
                                  feedback,
                                  timeStarted - SECONDS_WEEK,
                                  timeStarted
                               )
                           )
        intvlWeek = (max(1, SECONDS_WEEK // SECONDS_DAY), timeStarted)
        updateCache(db, *domain,
                    *intvlWeek,
                    *clusterOneWeek[2:5]
                    )


"""
pre-condition: parent cluster data must be 'wider'
"""


def narrowDataRange(daysBack, end, medoids, clusters, avgs):
    """
    Extract a subset from data according to
    date bounds; inclusive.

    Args:
        daysBack: Days from end date in which
                  data should be kept.
        end: Data after this end date is discarded.
        medoids: medoids of data superset.
        clusters: clusters of data superset.
        avgs: averages of data superset.

    Returns:
        Subset which conforms to date restrictions.
    """

    startDate = 0
    endDate = timeStarted
    if end is not None:
        endDate = end
    if daysBack is not None:
        startDate = endDate - (daysBack * SECONDS_DAY)

    if not clusters:
        return daysBack, end, medoids, clusters, avgs

    nMedoids, nClusters, nAvgs = [], [], []

    # Drop clusters, medoids, and averages entirely out of range.
    for c in range(len(clusters)):
        clusters[c].sort()
        if not clusters[c]:
            continue
        else:
            if int(getField(max(clusters[c]), "date")) < startDate:
                continue
            elif int(getField(min(clusters[c]), "date")) > endDate:
                continue
            else:
                nMedoids.append(medoids[c])
                nClusters.append(clusters[c])
                nAvgs.append(avgs[c])

    # Return empty medoids, clusters, avgs if no data
    # is in range.
    if not nClusters:
        return daysBack, end, medoids, clusters, avgs

    # Trim the first cluster.
    for i in range(len(nClusters[0])):
        if int(getField(nClusters[0][i], "date")) >= startDate:
            nClusters[0] = nClusters[0][i:]
            nMedoids[0] = findClusterMedoid(nClusters[0][:])
            nAvgs[0] = avgOrZero(nClusters[0], True)
            break

    # Trim the last cluster if it is not the first.
    if len(nClusters) > 1:
        for i in range(len(nClusters[-1])):
            d = int(getField(nClusters[-1][i], "date"))
            if d > endDate:
                nClusters[-1] = nClusters[-1][:i]
                nMedoids[-1] = findClusterMedoid(nClusters[-1][:])
                nAvgs[-1] = avgOrZero(nClusters[-1], True)
                break

    return daysBack, end, nMedoids, nClusters, nAvgs


def updateCache(
        db,
        aspectTypeID,
        storeID,
        companyID,
        industryID,
        daysBack,
        end,
        medoids,
        clusters,
        avgs):
    """
    Insert new data into data_cache table
    or update if row matching domain already found.

    Args:
        db: Brevada database connection.
        aspectTypeID: aspect_type#id
        storeID: stores#id
        companyID: companies#id
        industryID: company_categories#id
        daysBack: Denotes start of data span in terms
                  of days back from end date.
        end: End date of data span.
        medoids: Cluster medoids.
        clusters: Data clusters.
        avgs: Cluster averages.

    Returns:
        None
    """
    logger.debug("Updating cache...")

    # Phase-1 start and end date normalization.
    if daysBack == 0:
        daysBack = None
    if end == timeStarted:
        end = None

    data = []
    if daysBack is not None:
        data.append(str(daysBack))
    if end is not None:
        data.append(str(end))
    if aspectTypeID is not None:
        data.append(str(aspectTypeID))
    if storeID is not None:
        data.append(str(storeID))
    if companyID is not None:
        data.append(str(companyID))
    if industryID is not None:
        data.append(str(industryID))

    sql_NumberOfClusters = len(clusters)

    tranClusters = []
    for i in range(len(clusters)):
        tranClusters.append([
            {'rating': getField(fid, 'rating'),
             'date': getField(fid, 'date')}
            for fid in clusters[i]
        ])

    sql_TotalDataSize = sum(int(a[1]) for a in avgs)

    avg = 0
    if tranClusters and sql_TotalDataSize > 0:
        avg = sum(x[0] * x[1] for x in avgs) / sql_TotalDataSize

    # Add rating, date to clusters, just like with medoids.
    sql_CachedData = json.dumps({
        'avg': avg,
        'avgs': avgs,
        'medoids': [
            {'rating': getField(mid, 'rating'),
             'date': getField(mid, 'date')}
            for mid in medoids
        ],
        'clusters': tranClusters
    }, separators=(',', ':'))

    sql_TotalAverage = avg

    data.extend([
        sql_TotalAverage,
        sql_TotalDataSize,
        sql_NumberOfClusters,
        sql_CachedData
    ])
    data = tuple(data)

    zeroDate = "'0000-00-00 00:00:00.000000'"

    try:
        cursor = db.cursor()
        cursor.execute("""
        INSERT INTO data_cache (
            `DaysBack`,
            `EndDate`,
            `LastModified`,
            `Domain_AspectID`,
            `Domain_StoreID`,
            `Domain_CompanyID`,
            `Domain_IndustryID`,
            `TotalAverage`,
            `TotalDataSize`,
            `NumberOfClusters`,
            `CachedData`
        ) VALUES (
            """ + ("-1" if daysBack is None else "%s") + """,
            """ + (zeroDate if end is None else "FROM_UNIXTIME(%s)") + """,
            NOW(),
            """ + ("-1" if aspectTypeID is None else "%s") + """,
            """ + ("-1" if storeID is None else "%s") + """,
            """ + ("-1" if companyID is None else "%s") + """,
            """ + ("-1" if industryID is None else "%s") + """,
            %s,
            %s,
            %s,
            %s
        )
        ON DUPLICATE KEY UPDATE
            `LastModified` = NOW(),
            `TotalAverage` = VALUES(`TotalAverage`),
            `TotalDataSize` = VALUES(`TotalDataSize`),
            `NumberOfClusters` = VALUES(`NumberOfClusters`),
            `CachedData` = VALUES(`CachedData`)
        """, data)
        db.commit()
        cursor.close()
    except mysql.connector.Error as err:
        logger.error("Error updating cache: %s", str(err))


def data_range(data, start, end):
    """
    Splices unsorted data by date; inclusive.

    TODO: If data is sorted, we can seek from
          left and from right, instead of iterating
          over all elements.

    Args:
        data: List of feedback IDs.
        start: Start date.
        end: End date.

    Returns:
        Splice of data containing only the feedback
        which falls into the date range.
    """

    return [int(id) for id in data if start <= int(getField(id, "date")) <= end]


def clusterMeta(data):
    """
    Clusters feedback IDs based on date.

    Args:
        data: Feedback IDs to cluster.

    Returns:
        (startDate, endDate, medoids, clusters, avgs)
        Note that the clusters returned contain the medoids.
    """

    if not data:
        return 0, 0, [], [], []

    # Intelligently determine number of clusters.
    numClusters = 0

    # We can assume id dictates order of submission,
    # i.e. greater id equates to newer date.
    startDate = int(getField(min(data), "date"))
    endDate = int(getField(max(data), "date"))
    duration = endDate - startDate

    if duration > SECONDS_MONTH * 6:
        numClusters = min(len(data), duration // SECONDS_DAY)
    elif duration <= SECONDS_WEEK:
        # 3 parts to a day; morning, afternoon, evening
        numClusters = min(len(data), duration // (SECONDS_DAY / 3))
    else:
        numClusters = min(len(data), duration // SECONDS_DAY)

    if numClusters == 0:
        numClusters = len(data)

    numClusters = int(numClusters)

    medoids, clusters, avgs = kMedoids(data, math.ceil(numClusters))

    return startDate, endDate, medoids, clusters, avgs


def kMedoids(data, numClusters, trials=10):
    """
    Execute multiple trials of kMedoids algorithm
    and average said trials.

    Args:
        data: Feedback IDs upon which to calculate kMedoids.
        numClusters: Target number of clusters to attain.
        trials: Number of times to perform calculations.

    Returns:
        (medoids, clusters, avgs)
        Each cluster contains its medoid.
    """

    logger.debug(
        "Performing kMedoids on %d data points for %d clusters...",
        len(data),
        numClusters)
    startT = time.time()

    allMedoids = [[] for i in range(numClusters)]
    for t in range(trials):
        tMedoids, tClusters = approxKMedoids(data, numClusters)
        for i in range(len(tMedoids)):
            allMedoids[i].append(tMedoids[i])

    medianMedoids = [[] for i in range(numClusters)]
    for k in range(len(allMedoids)):
        allMedoids[k].sort()
        m = len(allMedoids[k]) // 2
        medianMedoids[k] = allMedoids[k][m]

    fMedoids, fClusters = approxKMedoids(data, numClusters, medianMedoids)

    # add medoids to cluster
    for j in range(len(fMedoids)):
        fClusters[j].append(fMedoids[j])
        fClusters[j].sort()

    # avgs[i] = avg, size (number of data points)
    avgs = []
    for n in range(len(fMedoids)):
        avgs.append(avgOrZero(fClusters[n], True))

    logger.debug("Clustering complete. Took %d seconds.",
                 (time.time() - startT))
    return fMedoids, fClusters, avgs


def approxKMedoids(data, k, medoids=[], maxRuns=100):
    """
    Perform kMedoids clustering based on Lloyd's
    algorithm for k-means.

    This is approximate as a result of the random
    selection of initial k-medoids.

    Args:
        data: Feedback IDs upon which to perform clustering.
        k: Target number of clusters.
        medoids: Optional. List of medoids to use.
                 This disables randomization.
        maxRuns: Maximum iterations of algorithm to execute.
                 Algorithm will iterate
                 min(maxRuns, runs until convergence).

    Returns:
        (medoids, clusters)
        Clusters do not contain the medoids.
    """

    if not data:
        return [], []
    if len(data) < k:
        return approxKMedoids(data, len(data))

    # sort raw data by date; first value of data tuple.
    data = sorted(data[:])

    # randomize k medoids if none supplied.
    if not medoids:
        medoids = [data.pop(random.randrange(0, len(data))) for i in range(k)]
        medoids.sort()
    else:
        medoids = medoids[:]
        data = [x for x in data if x not in medoids]
    medoidsNew = medoids[:]

    # create N blank clusters.
    clusters = [[] for i in range(k)]
    clusters[0] = data

    # if m > maxRuns, it is too computationaly expensive to continue.
    # TODO: ideally, this should be logged and looked into.
    for m in range(maxRuns):
        for j in range(len(clusters)):
            # re-assign data points to best cluster
            newCluster = []
            while len(clusters[j]) > 0:
                item = clusters[j].pop()
                bestMedoidIndex = findSuitableMedoid(item, medoidsNew)
                if bestMedoidIndex != j:
                    clusters[bestMedoidIndex].append(item)
                else:
                    newCluster.append(item)
            clusters[j] = newCluster
            clusters[j].sort() # sorting takes time...

            # choose best medoid for each cluster
            medoidsNew[j] = findClusterMedoid(clusters[j], medoidsNew[j])
        if compareMedoids(medoids, medoidsNew):
            # convergence reached.
            break
        medoids = medoidsNew[:]

    return medoids, clusters


def calcDistance(A, B):
    """
    Calculate 1-D distance between dates of
    feedback#A and feedback#B.

    Args:
        A: ID of first feedback.
        B: ID of second feedback.

    Returns:
        Distance between A and B

    Raises:
        IndexError if A or B are invalid feedback IDs.
    """
    aDate = getField(A, "date")
    bDate = getField(B, "date")
    if aDate is None or bDate is None:
        raise IndexError

    return abs(int(aDate) - int(bDate))


def findSuitableMedoid(datum, medoids):
    """
    Find the best medoid for the datum.

    Args:
        datum: Feedback ID.
        medoids: List of medoids.

    Returns:
        Index of best medoid for particular datum.
    """
    medoidIndex = -1
    oldDistance = -1
    for i in range(len(medoids)):
        newDistance = calcDistance(medoids[i], datum)
        if(oldDistance < 0 or newDistance < oldDistance):
            oldDistance = newDistance
            medoidIndex = i
    return medoidIndex


def findClusterMedoid(cluster, currentMedoid=None):
    """
    Find the datum which serves as the best medoid
    in a cluster, remove it and return it.

    Args:
        cluster: Cluster to search through.
        currentMedoid: If a medoid is set, it will be
                       added back to the cluster.

    Returns:
        Best medoid in cluster (feedback#id).

    Raises:
        IndexError if cluster is empty and currentMedoid = None
    """
    if currentMedoid is not None:
        cluster.append(currentMedoid)
    return getMedian(cluster)


def getMedian(cluster):
    """
    Remove the median in a cluster and return it.

    Args:
        cluster: Cluster to search through.

    Returns:
        Median element in a cluster.

    Raises:
        IndexError if cluster is empty.
    """
    cluster.sort()
    m = max(len(cluster) // 2, 0)
    return cluster.pop(m)


def compareMedoids(A, B):
    """
    Test equality of two medoid lists.

    Args:
        A: First medoid list.
        B: Second medoid list.

    Returns:
        True if both medoid lists are identical.
    """
    A = set(A[:])
    B = set(B[:])
    return len(A.union(B)) == len(A) and len(A) == len(B)


def main():
    global db
    global lastFeedbackID
    global timeStarted

    db = mysql.connector.connect(**config)

    lastFeedbackID = -1

    logger.debug("Refinery opened at: %d", math.floor(time.time()))

    while refineryOpen:
        # While there is new feedback, perform data refinement.

        cursor = db.cursor()
        cursor.execute("""
        SELECT id, UNIX_TIMESTAMP(`Date`) as `Date`
        FROM `feedback` ORDER BY id DESC LIMIT 1
        """)
        row = cursor.fetchone()
        db.commit()
        cursor.close()

        logger.debug("Last ID: %d", int(row[0]))

        if row is not None and int(row[0]) > lastFeedbackID:
            # new feedback
            timeStarted = math.floor(time.time())

            logger.debug(
                "New feedback detected at %d: %s. Refining...",
                timeStarted,
                time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(timeStarted))
            )
            retrieveData(db)
            refineAspects(db)
            logger.debug("Refinement complete. Took %d seconds.",
                         (time.time() - timeStarted))

            lastFeedbackID = int(row[0])

        time.sleep(3)

    db.close()

if __name__ == "__main__":
    main()
