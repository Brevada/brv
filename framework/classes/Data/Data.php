<?php
/*
	Data Retrieval
*/
class Data
{
	const AVERAGE_RATING = 'AverageRating';
	const AVERAGE_DATE = 'AverageDate';
	const TOTAL_DATASIZE = 'TotalDataSize';
	
	/* d* domain variables. */
	private $dFrom = 0;
	private $dTo = 0;
	private $dAspectType = [];
	private $dStore = [];
	private $dCompany = [];
	private $dIndustry = []; /* not significant */
	private $dKeywords = []; /* TODO: not implemented yet */
	
	function __construct()
	{
		
	}
	
	/* Domain Parameters */
	
	/**
	* Specifies from date in data range.
	*
	* @param string|int $date UTC time in seconds or a UTC
	*    or a UTC string according to
	*    http://php.net/manual/en/datetime.formats.php
	*
	* @return Data
	*/
	public function from($date)
	{
		if(is_string($date)){
			$date = @intval(strtotime($date));
		}
		$this->from = $date;
	}
	
	/**
	* Specifies to date in data range.
	*
	* @param string|int $date UTC time in seconds or a UTC
	*    or a UTC string according to
	*    http://php.net/manual/en/datetime.formats.php
	*
	* @return Data
	*/
	public function to($date)
	{
		if(is_string($date)){
			$date = @intval(strtotime($date));
		}
		$this->to = $date;
	}
	
	/**
	* Specifies domain aspect type.
	*
	* @param int|int[] $id aspect_type#id
	*
	* @return Data
	*/
	public function aspectType($id)
	{
		if(is_array($id)){
			$this->aspectType = array_unique(array_merge($this->aspectType, $id));
		} else {
			$this->aspectType[] = $id;
		}
	}
	
	/**
	* Specifies domain store.
	*
	* @param int|int[] $id stores#id
	*
	* @return Data
	*/
	public function store($id)
	{
		if(is_array($id)){
			$this->store = array_unique(array_merge($this->store, $id));
		} else {
			$this->store[] = $id;
		}
	}
	
	/**
	* Specifies domain company.
	*
	* @param int|int[] $id companies#id
	*
	* @return Data
	*/
	public function company($id)
	{
		if(is_array($id)){
			$this->company = array_unique(array_merge($this->company, $id));
		} else {
			$this->company[] = $id;
		}
	}
	
	/**
	* Specifies domain industry.
	*
	* @param int|int[] $id company_categories#id
	*
	* @return Data
	*/
	public function industry($id)
	{
		if(is_array($id)){
			$this->industry = array_unique(array_merge($this->industry, $id));
		} else {
			$this->industry[] = $id;
		}
	}
	
	/**
	* Specifies domain keywords.
	*
	* @param int|int[] $id company_keywords#id
	*
	* @return Data
	*/
	public function keyword($id)
	{
		if(is_array($id)){
			$this->keywords = array_unique(array_merge($this->keywords, $id));
		} else {
			$this->keywords[] = $id;
		}
	}
	
	/**
	* Retrieves averages of clusters, filtered by domain.
	*
	* @return array()
	*/
	public function getAveragedClusters()
	{		
		$wheres = [];
		
		$daysBack = ceil(($this->to - $this->from)/(3600.0*24.0));
		
		/* Eliminate data sets that are too small/narrow. */
		$wheres[] = "(`DaysBack` >= {$daysBack} OR `DaysBack` = -1)";
		$wheres[] = "(`EndDate` >= FROM_UNIXTIME({$this->to}) OR `EndDate` = '0000-00-00 00:00:00')";
		
		$wheres[] = self::domainToWhere('Domain_AspectID', '-1', $this->aspectType);
		$wheres[] = self::domainToWhere('Domain_StoreID', '-1', $this->store);
		$wheres[] = self::domainToWhere('Domain_CompanyID', '-1', $this->company);
		$wheres[] = self::domainToWhere('Domain_IndustryID', '-1', $this->industry);
		
		$domain_where = implode(' AND ', $wheres);
		
		$table = "
			(SELECT * FROM `data_cache`
			WHERE {$domain_where}
			AND `CachedData` IS NOT NULL)
		";
		
		$upperDaysBack = ceil(time()+1/(3600.0*24.0));
		$upperEndDate = date('Y-m-d H:i:s', time()+1);
		
		$sql = "
			SELECT dcA.* FROM {$table} dcA
			JOIN (
				SELECT
					`Domain_AspectID`, `Domain_StoreID`,
					`Domain_CompanyID`, `Domain_IndustryID`,
					MIN(
						IF(`DaysBack` = -1, {$upperDaysBack}, `DaysBack`) +
						UNIX_TIMESTAMP(
							IF(`EndDate` = '0000-00-00 00:00:00', '{$upperEndDate}', `EndDate`)
						)
					) AS min_de
					FROM {$table}
					GROUP BY
						`Domain_AspectID`, `Domain_StoreID`,
						`Domain_CompanyID`, `Domain_IndustryID`
			) s ON
				dcA.`Domain_AspectID` = s.`Domain_AspectID` AND
				dcA.`Domain_StoreID` = s.`Domain_StoreID` AND
				dcA.`Domain_CompanyID` = s.`Domain_CompanyID` AND
				dcA.`Domain_IndustryID` = s.`Domain_IndustryID` AND
				IF(dcA.`DaysBack` = -1, {$upperDaysBack}, dcA.`DaysBack`) +
				UNIX_TIMESTAMP(
					IF(dcA.`EndDate` = '0000-00-00 00:00:00', '{$upperEndDate}', dcA.`EndDate`)
				) = s.min_de
			GROUP BY
				dcA.`Domain_AspectID`, dcA.`Domain_StoreID`,
				dcA.`Domain_CompanyID`, dcA.`Domain_IndustryID`
		";
		
		/* Each element consists of an array of cluster averages. */
		$avgs = [];
		
		if(($stmt = Database::query($sql)) !== false){
			while($row = $stmt->fetch_assoc()){
				$cached = json_decode($row['dcA.CachedData'], true);
				
				$clusterAvgs = []; /* [[AvgRating, AvgDate, Count]] */
				
				foreach($cached['clusters'] as $cluster){
					$sumDate = 0; $sumRating = 0;
					$count = 0;
					
					foreach($cluster as $datapoint){
						$rDate = @intval($datapoint['date']);
						if($this->from <= $rDate && $rDate <= $this->to){
							$sumRating += floatval($datapoint['rating']);
							$sumDate += $rDate;
							$count++;
						}
					}
					
					$avgRating = $count > 0 ? round($sumRating / $count, 2) : 0;
					$avgDate = $count > 0 ? ceil($sumDate / $count) : null;
					
					
					
					$clusterAvgs[] = [self::AVERAGE_RATING => $avgRating, self::AVERAGE_DATE => $avgDate, self::TOTAL_DATASIZE => $count];
				}
				
				$avgs[] = $clusterAvgs;
			}
		}
		
		return $avgs;
	}
	
	/**
	* Retrieves data average.
	*
	* @param int $numPoints Maximum number of clusters.
	*
	* @return DataResult
	*/
	public function getAvg($numPoints = 1)
	{
		$clusterAvgs = $this->getAveragedClusters();
		
		$result = [];
		
		/* Find largest cluster dimensions <= $numPoints. */
		$index = 0; $maxSize = 0;
		for($i = 0; $i < count($clusterAvgs); $i++){
			$index = min(count($clusterAvgs[$i]), $numPoints) > $maxSize ? $i : $index;
			$maxSize = max(min(count($clusterAvgs[$i]), $numPoints), $maxSize);
		}
		
		/* Set largest cluster to result. */
		$result = array_splice($clusterAvgs, $index, 1);
		
		/* TODO: if result is empty... */
		
		/* Collapse all other clusters into $numPoints. */
		foreach($clusterAvgs as $clusters){
			// according to closest avg date. //work with 1 numPoint
			foreach($clusters as $cluster){
				for($c = 0; $c < count($cluster); $c++){
					$distance = -1;
					$rIndex = 0;
					for($r = 0; $r < count($result); $r++){
						$newDistance = abs($cluster[$i][self::AVERAGE_DATE] - $result[$r][self::AVERAGE_DATE]);
						if($distance == -1 || $newDistance < $distance){
							$distance = $newDistance;
							$rIndex = $r;
						}
					}
					$result[$rIndex] = self::mergeClusterAvgs($result[$rIndex], $cluster[$c]);
				}
			}
		}
		
		return new DataResult($result);
	}
	
	/**
	* Merges two cluster averages by weight.
	*
	* @param array() $a First cluster average.
	* @param array() $b Second cluster average.
	*
	* @return array() The merged cluster average.
	*/
	public static function mergeClusterAvgs($a, b)
	{
		$total = $a[self::TOTAL_DATASIZE] + $b[self::TOTAL_DATASIZE];
		$avgRating = round((($a[self::AVERAGE_RATING]*$a[self::TOTAL_DATASIZE]) + ($b[self::AVERAGE_RATING]*$b[self::TOTAL_DATASIZE]))/$total, 2);
		$avgDate = ceil((($a[self::AVERAGE_DATE]*$a[self::TOTAL_DATASIZE]) + ($b[self::AVERAGE_DATE]*$b[self::TOTAL_DATASIZE]))/$total);
		
		return [self::AVERAGE_RATING => $avgRating, self::AVERAGE_DATE => $avgDate, self::TOTAL_DATASIZE => $total];
	}
	
	/**
	* Returns MySQL snippet to satisfy domain condition.
	*
	* @param string $column `data_cache`.column
	* @param string $default Default value of `data_cache`.column
	* @param string $domain Domain parameter.
	*
	* @return string The MySQL snippet.
	*/
	public static function domainToWhere($column, $default, $domain)
	{
		if(empty($domain)){
			return "`{$column}` = {$default}";
		} else {
			$list = implode(',', $this->domain);
			return "`{$column}` IN ({$list})"
		}
	}
}
?>