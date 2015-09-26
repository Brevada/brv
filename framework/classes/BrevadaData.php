<?php
/**
	Written by Noah Negin-Ulster
	BrevadaData class.
	
	Contains algorithms for data analysis.
*/

define('SECONDS_DAY', 3600*24);
define('SECONDS_WEEK', SECONDS_DAY * 7);
define('SECONDS_MONTH', SECONDS_WEEK * 4);
define('SECONDS_YEAR', SECONDS_MONTH * 12);

class BrevadaData
{

	public static function execute_analysis($debug_store = false)
	{
		/*
			If $debug_store is set to a store id, debug info will be written
			for selected store.
		*/
	
		/* Retrieve current time to standardize Data_LastUpdate. */
		$now = time();
		
		/* Retrieve list of stores. */
		$stores = array();
		if(($qStores = Database::query("SELECT stores.id as StoreID, dashboard.id as DashboardID, IFNULL(GROUP_CONCAT(company_keywords_link.CompanyKeywordID ORDER BY company_keywords_link.CompanyKeywordID ASC SEPARATOR ','), '') as keywords FROM dashboard LEFT JOIN stores ON stores.id = dashboard.StoreID LEFT JOIN companies ON companies.`id` = stores.CompanyID LEFT JOIN company_keywords_link ON company_keywords_link.CompanyID = companies.id GROUP BY stores.id")) !== false){
			while($row = $qStores->fetch_assoc()){
				$stores[] = array('StoreID' => $row['StoreID'], 'DashboardID' => $row['DashboardID'], 'Keywords' => explode(',', $row['keywords']), 'OverallPercent' => 0);
				
				if($debug_store && $debug_store == $row['StoreID']){
					echo "StoreID => {$row['StoreID']}<br />";
					echo "DashboardID => {$row['DashboardID']}<br />";
					echo "Keywords => {$row['keywords']}<br />";
					echo "<br />";
				}
			}
			
			$qStores->close();
		}
		
		/* Perform aspect data analysis for each store. */
		foreach($stores as &$store){
		
			$localMetaAspects = array();
			$aspects = array();
			
			if(($qFeedback = Database::query("SELECT feedback.Rating, UNIX_TIMESTAMP(feedback.Date) as `Date`, aspect_type.Title, aspects.ID as AspectID, aspects.Data_RatingPercent as PreviousRating, aspects.AspectTypeID as AspectTypeID FROM feedback LEFT JOIN aspects ON aspects.ID = feedback.AspectID LEFT JOIN aspect_type ON aspect_type.ID = aspects.AspectTypeID WHERE aspects.`Active` = 1 AND feedback.Rating IS NOT NULL AND feedback.Rating > -1 AND aspects.StoreID = {$store['StoreID']}")) !== false){
				while($row = $qFeedback->fetch_assoc()){
					if(!isset($aspects[$row['Title']])){
						$aspects[$row['Title']] = array();
						$localMetaAspects[$row['Title']] = array('AspectID' => $row['AspectID'], 'AspectTypeID' => $row['AspectTypeID'], 'PreviousRating' => (float) $row['PreviousRating'], 'Sum' => 0, 'Count' => 0);
						
						if($debug_store && $store['StoreID'] == $debug_store){
							echo "Aspect: {$row['Title']}<br />";
						}
					}
					
					/* Multidimensional data array linking feedback to store's aspects. */
					$aspects[$row['Title']][] = array('Rating' => (float) $row['Rating'], 'Date' => (int) $row['Date']);
				}
			}
			
			if($debug_store && $store['StoreID'] == $debug_store){
				echo "<br />";
			}
			
			/* Overall change of all aspects over 4W and overall rating % of all time. */
			$overall4W = $overallAll = 0;
			
			/* Iterate through all of store's aspects and perform data analysis. */
			foreach($aspects as $aspectTitle => $aspect){
				
				/* If no data is available for a particular time span, use previous overall rating %. */
				$previousRating = $localMetaAspects[$aspectTitle]['PreviousRating'];
			
				/* Overall aspect rating % for all time. */
				/* TODO: Consider using limited time span for more relevant data. */
				$data_RatingPercent = self::biased_mean(self::extract_nested($aspect, 'Rating'));
				
				/* Change in aspect rating over 4W; compares 4W to all time. */
				$spanOf4W = self::extract_nested(self::subdata_date($aspect, 0, time() - SECONDS_MONTH), 'Rating'); 
				$data_Percent4W = $data_RatingPercent - self::biased_mean($spanOf4W, $previousRating);
				
				/* Change in aspect rating over 8W; compares 8W to all time. */
				$spanOf8W = self::extract_nested(self::subdata_date($aspect, 0, time() - 2*SECONDS_MONTH), 'Rating');
				$data_Percent8W = $data_RatingPercent - self::biased_mean($spanOf8W, $previousRating);
				
				/* Change in aspect rating over 6M; compares 6M to all time. */
				$spanOf6M = self::extract_nested(self::subdata_date($aspect, 0, time() - (SECONDS_YEAR/2)), 'Rating');
				$data_Percent6M = $data_RatingPercent - self::biased_mean($spanOf6M, $previousRating);
				
				/* Change in aspect rating over 1Y; compares 1Y to all time. */
				$spanOf1Y = self::extract_nested(self::subdata_date($aspect, 0, time() - SECONDS_YEAR), 'Rating');
				$data_Percent1Y = $data_RatingPercent - self::biased_mean($spanOf1Y, $previousRating);
				
				/*	Calculate attention score. Greater attention places aspect under 'Areas of Focus'.
					Compare past 4W of aggregate data to beginning of time up until the 4W. */
				//$attentionScore = 100-$data_RatingPercent; //Alternative, base comparison.
				$attentionScore = 100 - ($data_RatingPercent*2 - $data_Percent4W);
				
				/* Update data for individual aspect belonging to store. */
				$aspectID = $localMetaAspects[$aspectTitle]['AspectID'];				
				if(($stmt = Database::prepare("UPDATE aspects SET `Data_RatingPercent` = ?, `Data_Percent4W` = ?, `Data_Percent6M` = ?, `Data_Percent1Y` = ?, `Data_AttentionScore` = ?, `Data_LastUpdate` = {$now} WHERE aspects.ID = ?")) !== false){
					$stmt->bind_param('dddddi', $data_RatingPercent, $data_Percent4W, $data_Percent6M, $data_Percent1Y, $attentionScore, $aspectID);
					$stmt->execute();
					$stmt->close();
				}
				
				/* Contribute to dashboard data. */
				$overall4W += $data_Percent4W;
				$overallAll += $data_RatingPercent;
				
				/* Weigh data_RatingPercent by number of responses [count] */
				$localMetaAspects[$aspectTitle]['Sum'] += $data_RatingPercent * count($aspect);
				$localMetaAspects[$aspectTitle]['Count'] += count($aspect);
				
				if($debug_store && $store['StoreID'] == $debug_store){
					echo "Aspect Analysis: {$aspectTitle}<br />";
					echo "Aspect ID: {$aspectID}<br />";
					echo "-- Previous Rating: {$previousRating}%<br />";
					echo "-- All Time: {$data_RatingPercent}%<br />";
					echo "-- 4 Weeks: {$data_Percent4W}%<br />";
					echo "-- 8 Weeks: {$data_Percent8W}%<br />";
					echo "-- 6 Months: {$data_Percent6M}%<br />";
					echo "-- 1 Year: {$data_Percent1Y}%<br />";
					echo "-- Attention Score = {$attentionScore} = 100 - ({$data_RatingPercent} * 2 - {$data_Percent4W})<br/>";
					echo "<br />";
				}
				
			}
			
			/* Calculate overall 4W change and overall % for dashboard, considering ALL aspects. */
			if(count($aspects) > 0){
				
				if($debug_store && $store['StoreID'] == $debug_store){
					echo "4 Weeks (Overall) = {$overall4W} / " . count($aspects);
				}
			
				$overall4W /= count($aspects);
				
				if($debug_store && $store['StoreID'] == $debug_store){
					echo " = {$overall4W}<br />";
				}
				
				if($debug_store && $store['StoreID'] == $debug_store){
					echo "All Time (Overall) = {$overallAll} / " . count($aspects);
				}
				
				$overallAll /= count($aspects);
				
				if($debug_store && $store['StoreID'] == $debug_store){
					echo " = {$overallAll}<br /><br />";
				}
			}
			
			/* Update dashboard data. */
			$dashboardID = $store['DashboardID'];
			if(($stmt = Database::prepare("UPDATE dashboard SET `Data_Overall4W` = ?, `Data_OverallAll` = ? WHERE dashboard.id = ?")) !== false){
				$stmt->bind_param('ddi', $overall4W, $overallAll, $dashboardID);
				$stmt->execute();
				$stmt->close();
			}
			
			$store['OverallPercent'] = $overallAll;
			$store['MetaAspects'] = $localMetaAspects;
		}
		unset($store);
		
		/* 	Update relative aspect specific benchmark percentages.
			This implements a Weighted Arithmatic Sum, weighted by quanitity of feedback.
			TODO: Benchmark can theoretically be calculated based on data within a geographical area.
			In addition to similar keywords. */
		if($debug_store){ echo "<br />Calculating aspect benchmarks...<br /><br />"; }
		foreach($stores as $store){
			if(!isset($store['MetaAspects'])){ continue; }
			foreach($store['MetaAspects'] as $title => $meta){
				
				if($debug_store && $store['StoreID'] == $debug_store){
					echo "Aspect: {$title}<br /><br />";
				}
			
				$ckCount = 0; $ckSum = 0;
				foreach($stores as $ckStore){
					if(!isset($ckStore['MetaAspects']) || !isset($ckStore['MetaAspects'][$title])){
						continue;
					}
					if(empty($ckStore['Keywords']) && empty($store['Keywords'])){
						$ckCount += $ckStore['MetaAspects'][$title]['Count'];
						$ckSum += $ckStore['MetaAspects'][$title]['Sum'];
					} else {
						$common = array_intersect($ckStore['Keywords'], $store['Keywords']);
						if(!empty($common)){
							$ckCount += $ckStore['MetaAspects'][$title]['Count'];
							$ckSum += $ckStore['MetaAspects'][$title]['Sum'];
							
							if($debug_store && $store['StoreID'] == $debug_store){
								echo "-- Found match: {$ckStore['StoreID']}<br />";
								echo "---- # of Responses: {$ckStore['MetaAspects'][$title]['Count']}<br />";
								/* Weighed Sum has already been multiplied by # of Responses. */
								echo "---- Weighed Sum: {$ckStore['MetaAspects'][$title]['Sum']}<br />";
								echo "<br />";
							}
							
						}
					}
				}
				
				$ckPercent = $ckCount > 0 ? ((float) $ckSum / $ckCount) : 0;
				
				if($debug_store && $store['StoreID'] == $debug_store){
					echo "-- Percent = {$ckPercent}% = {$ckSum} / {$ckCount}<br /><br />";
				}
				
				Database::query("UPDATE aspects SET aspects.Data_RatingPercentOther = {$ckPercent} WHERE aspects.ID = {$meta['AspectID']}");
			}
		}
		
		/* Calculate overall market benchmark, considering ALL aspects. based on common keywords (ck). */
		foreach($stores as $store){
			$ckCount = 0; $ckSum = 0;
			foreach($stores as $ckStore){
				if(empty($ckStore['Keywords']) && empty($store['Keywords'])){
					$ckCount++;
					$ckSum += $ckStore['OverallPercent'];
				} else {
					$common = array_intersect($ckStore['Keywords'], $store['Keywords']);
					if(!empty($common)){
						$ckCount++;
						$ckSum += $ckStore['OverallPercent'];
					}
				}
			}
			
			$ckBenchmarkPercent = $ckCount > 0 ? ((float) $ckSum / $ckCount) : 0;
			
			/* Update relative benchmark percentages of current store. */
			Database::query("UPDATE dashboard SET dashboard.Data_RelativeBenchmark = dashboard.Data_OverallAll - {$ckBenchmarkPercent} WHERE dashboard.StoreID = {$store['StoreID']}");
		}
			
	}
	
	/* Advanced Statistical Functions */
	
	/**
		Calculates mean, weighing data outside of mean +/- factor*std_deviation differently.
		Return $default instead of error when biased_mean cannot be calculated.
	*/
	private static function biased_mean($data, $default = 0, $factor = 1)
	{
		if(empty($data)){ return $default; }
	
		$sigma = self::std_deviation($data);
		$mean = self::mean($data);
		
		$filtered = array();
		foreach($data as $datum){
			if(!($mean - ($sigma*$factor) <= $data && $mean + ($sigma*$factor) >= $data)){
				$filtered[] = $datum;
			}
		}
		
		if(empty($filtered)){ return $default; }
		
		return 0.3*$mean + 0.7*self::mean($filtered);
	}
	
	/* Array Manipulation */
	
	private static function extract_nested($data, $key)
	{
		$nested = array();
		foreach($data as $datum){
			$nested[] = $datum[$key];
		}
		return $nested;
	}
	
	private static function subdata_date($data, $from = 0, $to = PHP_INT_MAX)
	{
		$res = array();
		foreach($data as $datum){
			if((int)$datum['Date'] >= $from && (int)$datum['Date'] <= $to){
				$res[] = $datum;
			}
		}
		return $res;
	}
	
	/* Base Statistical Functions */
	
	private static function std_deviation($data)
	{
		return sqrt(self::variance($data));
	}
	
	private static function variance($data)
	{
		$mean = self::mean($data);
		$sum = 0;
		foreach($data as $datum){
			$sum += pow($datum - $mean, 2);
		}
		return $sum / count($data);
	}
	
	private static function mean($data)
	{
		return array_sum($data) / count($data);
	}
}
?>