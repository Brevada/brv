import unittest
import Refinery

class Test_avgOrZero(unittest.TestCase):
    def testEmpty(self):
        self.assertEqual(Refinery.avgOrZero([], False, True), [0, 0])
        self.assertEqual(Refinery.avgOrZero([], False, False), 0)
        self.assertEqual(Refinery.avgOrZero([], True, True), [0, 0])
        self.assertEqual(Refinery.avgOrZero([], True, False), 0)

    def testNonEmpty(self):
        self.assertEqual(Refinery.avgOrZero([0,10], False, True), [5, 2])
        self.assertEqual(Refinery.avgOrZero([5,10,10,15], False, True), [10, 4])


class Test_compareMedoids(unittest.TestCase):
    def testEmpty(self):
        self.assertTrue(Refinery.compareMedoids([],[]))

    def testNonEmpty(self):
        self.assertTrue(Refinery.compareMedoids([1],[1]))
        self.assertTrue(Refinery.compareMedoids([1,2,3],[3,2,1]))
        self.assertFalse(Refinery.compareMedoids([1,2,3],[1,2]))
        self.assertTrue(Refinery.compareMedoids([5,4,3],[3,5,4]))
        self.assertFalse(Refinery.compareMedoids([],[2]))
        self.assertFalse(Refinery.compareMedoids([1,2,3],[4,2,1]))


class Test_getMedian(unittest.TestCase):
    def testEmpty(self):
        with self.assertRaises(IndexError):
            Refinery.getMedian([])

    def testOneLength(self):
        self.assertEqual(Refinery.getMedian([5]), 5)

    def testTwoLength(self):
        self.assertEqual(Refinery.getMedian([1,2]), 2)

    def testEvenLength(self):
        self.assertEqual(Refinery.getMedian([1,2,3,4]), 3)

    def testOddLength(self):
        self.assertEqual(Refinery.getMedian([1,2,3,4,5]), 3)


class Test_findClusterMedoid(unittest.TestCase):
    def testEmpty(self):
        with self.assertRaises(IndexError):
            Refinery.findClusterMedoid([], None)
        self.assertEqual(Refinery.findClusterMedoid([], 3), 3)

    def testNonEmpty(self):
        self.assertEqual(Refinery.findClusterMedoid([1,2,3,50,99,100,101], None), 50)
        self.assertEqual(Refinery.findClusterMedoid([1,2,3,99,100,101], 50), 50)
        self.assertEqual(Refinery.findClusterMedoid([1,2,50,99,100,101], 3), 50)


class Test_calcDistance(unittest.TestCase):
    def setUp(self):
        Refinery.feedbackMeta = {
            '5' : [None, 1419984000, 70, None, None, None],
            '6' : [None, 1419974000, 60, None, None, None],
            '7' : [None, 1419964000, 50, None, None, None],
            '8' : [None, 1419954000, 100, None, None, None],
            '9' : [None, 1419944000, 25, None, None, None],
            '10' : [None, 1419980000, 75, None, None, None],
            '11' : [None, 1419974321, 40, None, None, None],
            '12' : [None, 1418927853, 90, None, None, None]
        }

    def tearDown(self):
        Refinery.feedbackMeta = dict()

    def testEmpty(self):
        with self.assertRaises(IndexError):
            Refinery.calcDistance(1,2)

    def testNonEmpty(self):
        self.assertEqual(Refinery.calcDistance(5,6), 10000)
        self.assertEqual(Refinery.calcDistance(5,12), 1056147)


class Test_findSuitableMedoid(unittest.TestCase):
    def setUp(self):
        Refinery.feedbackMeta = {
            '7' : [None, 9964000, 50, None, None, None],
            '12' : [None, 9964010, 90, None, None, None],
            '13' : [None, 9964001, 90, None, None, None],
            '14' : [None, 9963990, 90, None, None, None],
            '15' : [None, 10, 90, None, None, None],
            '16' : [None, 5235421, 90, None, None, None],
            '17' : [None, 9963000, 90, None, None, None]
        }

    def tearDown(self):
        Refinery.feedbackMeta = dict()

    def testEmptyMedoids(self):
        self.assertEqual(Refinery.findSuitableMedoid(1, []), -1)

    def testNear(self):
        m1 = [15, 17, 13, 16]
        self.assertEqual(Refinery.findSuitableMedoid(7, m1), 2)

    def testHalfWay(self):
        # In a tie, choose first medoid.
        m1 = [14, 12]
        self.assertEqual(Refinery.findSuitableMedoid(7, m1), 0)



class Test_narrowDataRange(unittest.TestCase):
    def setUp(self):
        self.dates = list(range(0, Refinery.SECONDS_WEEK, 6*Refinery.SECONDS_HOUR))
        Refinery.feedbackMeta = {
            str(id): [None, self.dates[id], 50, None, None, None] for id in range(len(self.dates))
        }
        # len(dates) = 28; max(dates) = 583200
        # dates = \
        # [0, 21600, 43200, 64800, 86400, 108000, 129600,
        # 151200, 172800, 194400, 216000, 237600, 259200, 280800,
        # 302400, 324000, 345600, 367200, 388800, 410400, 432000,
        # 453600, 475200, 496800, 518400, 540000, 561600, 583200
        # ] -- Ideal 4 clusters.

    def tearDown(self):
        Refinery.feedbackMeta = dict()

    def testEvenDistribution(self):
        # start = 302400
        daysBack = 3
        # end = 561600
        end = Refinery.SECONDS_WEEK - (12*Refinery.SECONDS_HOUR)

        # Expected subset:
        # 302400, 324000, 345600, 367200, 388800, 410400, 432000, (14:21)
        # 453600, 475200, 496800, 518400, 540000, 561600 (21:27)

        # Input
        iMedoids = [3, 10, 17, 24]
        iClusters = [
            [i for i in range(7)],
            [i for i in range(7,14)],
            [i for i in range(14,21)],
            [i for i in range(21, 28)]
        ]
        iAvgs = [[50.0, 7], [50.0, 7], [50.0, 7], [50.0, 7]]

        # Output
        oMedoids = [17, 24]
        oClusters = [
            [i for i in range(14,21)],
            [i for i in range(21, 27)]
        ]
        oAvgs = [[50.0, 7], [50.0, 6]]

        param = (daysBack, end, iMedoids, iClusters, iAvgs)
        expected = (daysBack, end, oMedoids, oClusters, oAvgs)
        computed = Refinery.narrowDataRange(*param)
        self.assertEqual(computed, expected)


class Test_data_range(unittest.TestCase):
    def setUp(self):
        self.dates = list(range(0, Refinery.SECONDS_WEEK, 6*Refinery.SECONDS_HOUR))
        Refinery.feedbackMeta = {
            str(id): [None, self.dates[id], 50, None, None, None] for id in range(len(self.dates))
        }

    def tearDown(self):
        Refinery.feedbackMeta = dict()

    def testNonEmpty(self):
        start = 302400
        end = 561600
        expected = [
            14, 15, 16, 17,
            18, 19, 20, 21,
            22, 23, 24, 25, 26
        ]
        computed = Refinery.data_range(Refinery.feedbackMeta.keys(), start, end)
        computed.sort()
        self.assertEqual(computed, expected)


class Test_clusterMeta(unittest.TestCase):
    def setUp(self):
        self.dates = list(range(0, Refinery.SECONDS_WEEK-10, 6*Refinery.SECONDS_HOUR))
        Refinery.feedbackMeta = {
            str(id): [None, self.dates[id], 50, None, None, None] for id in range(len(self.dates))
        }
        Refinery.feedbackMeta['0'][1] = 6
        Refinery.feedbackMeta[str(-1)] = [None, 5, 50, None, None, None]

    def tearDown(self):
        Refinery.feedbackMeta = dict()

    def testNonEmpty(self):
        start = 5
        end = self.dates[-1]
        medoids = [0, 3, 6, 9, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27]
        clusters = [
            [-1, 0, 1], [2, 3, 4], [5, 6, 7],
            [8, 9, 10], [11, 12], [13], [14],
            [15], [16], [17], [18], [19], [20],
            [21], [22], [23], [24], [25], [26], [27]
        ]
        avgs = []

        expected = (start, end, medoids, clusters, avgs)
        computed = Refinery.clusterMeta([int(x) for x in list(Refinery.feedbackMeta.keys())])

        self.assertEqual(computed[:4], expected[:4])


class Test_kMedoids(unittest.TestCase):
    def setUp(self):
        pass

    def tearDown(self):
        pass


class Test_approxKMedoids(unittest.TestCase):
    def setUp(self):
        pass

    def tearDown(self):
        pass


if __name__ == '__main__':
    unittest.main()