<?php
require_once dirname(__FILE__).'/DataResult.php';

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
		$this->dTo = time();
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
		$this->dFrom = $date;
		return $this;
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
		$this->dTo = $date;
		return $this;
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
			$this->dAspectType = array_unique(array_merge($this->dAspectType, $id));
		} else {
			$this->dAspectType[] = $id;
		}
		return $this;
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
			$this->dStore = array_unique(array_merge($this->dStore, $id));
		} else {
			$this->dStore[] = $id;
		}
		return $this;
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
			$this->dCompany = array_unique(array_merge($this->dCompany, $id));
		} else {
			$this->dCompany[] = $id;
		}
		return $this;
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
			$this->dIndustry = array_unique(array_merge($this->dIndustry, $id));
		} else {
			$this->dIndustry[] = $id;
		}
		return $this;
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
			$this->dKeywords = array_unique(array_merge($this->dKeywords, $id));
		} else {
			$this->dKeywords[] = $id;
		}
		return $this;
	}
	
	/**
	* Retrieves averages of clusters, filtered by domain.
	*
	* @return array()
	*/
	public function getAveragedClusters()
	{		
		$wheres = [];

		$daysBack = ceil(($this->dTo - $this->dFrom)/(3600.0*24.0));
		
		/* Eliminate data sets that are too small/narrow. */
		$wheres[] = "(`DaysBack` >= {$daysBack} OR `DaysBack` = -1)";
		$wheres[] = "(`EndDate` >= FROM_UNIXTIME({$this->dTo}) OR `EndDate` = '0000-00-00 00:00:00')";
		
		if(!empty($this->dAspectType)){
			$wheres[] = self::domainToWhere('Domain_AspectID', '-1', $this->dAspectType);
		}
		if(!empty($this->dStore)){
			$wheres[] = self::domainToWhere('Domain_StoreID', '-1', $this->dStore);
		}
		if(!empty($this->dCompany)){
			$wheres[] = self::domainToWhere('Domain_CompanyID', '-1', $this->dCompany);
		}
		if(!empty($this->dIndustry)){
			$wheres[] = self::domainToWhere('Domain_IndustryID', '-1', $this->dIndustry);
		}
		
		if(!empty($this->dKeywords)){
			$keywords = implode(',', $this->dKeywords);
			$wheres[] = "`company_keywords_link`.`CompanyKeywordID` IN ({$keywords})";
		}
		
		$domain_where = implode(' AND ', $wheres);
		
		$table = "
			(SELECT `data_cache`.* FROM `data_cache`
			LEFT JOIN `company_keywords_link`
			ON `company_keywords_link`.`CompanyID` = `data_cache`.`Domain_CompanyID`
			WHERE {$domain_where}
			AND `CachedData` IS NOT NULL)
		";
		
		$upperDaysBack = ceil(time()+1/(3600.0*24.0));
		$upperEndDate = date('Y-m-d H:i:s', time()+1);
		
		$sql = "
			SELECT dcA.CachedData FROM {$table} dcA
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
					FROM {$table} dcB
					GROUP BY
						dcB.`Domain_AspectID`, dcB.`Domain_StoreID`,
						dcB.`Domain_CompanyID`, dcB.`Domain_IndustryID`
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

		if(($stmt = Database::prepare($sql)) !== false){
			$stmt->execute();
			$stmt->bind_result($cachedData);
			while($stmt->fetch()){
				$cached = json_decode($cachedData, true);
				
				$clusterAvgs = []; /* [[AvgRating, AvgDate, Count]] */
				
				foreach($cached['clusters'] as $cluster){
					$sumDate = 0; $sumRating = 0;
					$count = 0;
					
					foreach($cluster as $datapoint){
						$rDate = @intval($datapoint['date']);
						if($this->dFrom <= $rDate && $rDate <= $this->dTo){
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
		$stmt->close();
		
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

		if(empty($clusterAvgs)){
			return new DataResult([[self::AVERAGE_RATING => 0.0, self::AVERAGE_DATE => 0, self::TOTAL_DATASIZE => 0]]);
		}
		
		$result = [];
		
		/* Find largest cluster dimensions <= $numPoints. */
		$index = 0; $maxSize = 0;
		for($i = 0; $i < count($clusterAvgs); $i++){
			$index = min(count($clusterAvgs[$i]), $numPoints) > $maxSize ? $i : $index;
			$maxSize = max(min(count($clusterAvgs[$i]), $numPoints), $maxSize);
		}
		
		/* Set largest cluster to result. */
		$result = array_splice($clusterAvgs, $index, 1)[0];
		
		/* Collapse all other clusters into $numPoints. */
		foreach($clusterAvgs as $clusters){
			// according to closest avg date. //work with 1 numPoint
			foreach($clusters as $cluster){
				$distance = -1;
				$rIndex = 0;
				for($r = 0; $r < count($result); $r++){
					$newDistance = abs($cluster[self::AVERAGE_DATE] - $result[$r][self::AVERAGE_DATE]);
					if($distance == -1 || $newDistance < $distance){
						$distance = $newDistance;
						$rIndex = $r;
					}
				}
				$result[$rIndex] = self::mergeClusterAvgs($result[$rIndex], $cluster);
			}
		}
		
		/* Squeeze into $numPoints. This damages kMedoid property. */
		$density = ceil(count($result)/$numPoints);
		$squeezed = array_fill(0, min($numPoints, max(count($result), 1)), [self::AVERAGE_RATING => 0.0, self::AVERAGE_DATE => 0, self::TOTAL_DATASIZE => 0]);
		
		for($i = 0; $i < count($squeezed); $i++){
			for($j = 0; $j < $density; $j++){
				if(($i*$density) + $j >= count($result)){
					break;
				}
				$squeezed[$i] = self::mergeClusterAvgs($squeezed[$i], $result[($i*$density) + $j]);
			}
		}
		
		usort($squeezed, function($a, $b){
			return $a[self::AVERAGE_DATE] < $b[self::AVERAGE_DATE] ? -1 : 1;
		});
		
		return new DataResult($squeezed);
	}
	
	/**
	* Merges two cluster averages by weight.
	*
	* @param array() $a First cluster average.
	* @param array() $b Second cluster average.
	*
	* @return array() The merged cluster average.
	*/
	public static function mergeClusterAvgs($a, $b)
	{
		$total = $a[self::TOTAL_DATASIZE] + $b[self::TOTAL_DATASIZE];
		
		if($total == 0){
			return [self::AVERAGE_RATING => 0.0, self::AVERAGE_DATE => 0, self::TOTAL_DATASIZE => 0];
		}
		
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
			$list = implode(',', $domain);
			return "`{$column}` IN ({$list})";
		}
	}
}
?>