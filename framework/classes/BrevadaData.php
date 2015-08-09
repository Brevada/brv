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

	public static function execute_analysis()
	{
		/* Retrieve current time to standardize Data_LastUpdate. */
		$now = time();
	
		/* Contains meta information for each aspect_type, i.e. info for Data_RatingPercentOther */
		$metaAspects = array();
		
		/* Retrieve list of stores. */
		$stores = array();
		if(($qStores = Database::query("SELECT stores.id as StoreID, dashboard.id as DashboardID FROM dashboard LEFT JOIN stores ON stores.id = dashboard.StoreID")) !== false){
			while($row = $qStores->fetch_assoc()){
				$stores[] = array('StoreID' => $row['StoreID'], 'DashboardID' => $row['DashboardID']);
			}
			
			$qStores->close();
		}
		
		/* Overall market benchmark considering all aspects. */
		$benchmarkSum = $benchmarkCount = 0;
		
		/* Perform aspect data analysis for each store. */
		foreach($stores as $store){
		
			/* Local version of metaAspects, limited to scope of store. */
			$localMetaAspects = array();
			$aspects = array();
			
			if(($qFeedback = Database::query("SELECT feedback.Rating, feedback.Date, aspect_type.Title, aspects.ID as AspectID, aspects.Data_RatingPercent as PreviousRating, aspects.AspectTypeID as AspectTypeID FROM feedback LEFT JOIN aspects ON aspects.ID = feedback.AspectID LEFT JOIN aspect_type ON aspect_type.ID = aspects.AspectTypeID WHERE aspects.`Active` = 1 AND feedback.Rating IS NOT NULL AND feedback.Rating > -1 AND aspects.StoreID = {$store['StoreID']}")) !== false){
				while($row = $qFeedback->fetch_assoc()){
					if(!isset($aspects[$row['Title']])){
						$aspects[$row['Title']] = array();
						$localMetaAspects[$row['Title']] = array('AspectID' => $row['AspectID'], 'AspectTypeID' => $row['AspectTypeID'], 'PreviousRating' => (float) $row['PreviousRating']);
					}
					
					/* Multidimensional data array linking feedback to store's aspects. */
					$aspects[$row['Title']][] = array('Rating' => (float) $row['Rating'], 'Date' => (int) $row['Date']);
				}
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
				$spanOf4W = self::extract_nested(self::subdata_date($aspect, time() - SECONDS_MONTH), 'Rating');
				$data_Percent4W = self::biased_mean($spanOf4W, $previousRating) - $data_RatingPercent;
				
				/* Change in aspect rating over 8W; compares 8W to all time. */
				$spanOf8W = self::extract_nested(self::subdata_date($aspect, time() - 2*SECONDS_MONTH), 'Rating');
				$data_Percent8W = self::biased_mean($spanOf8W, $previousRating) - $data_RatingPercent;
				
				/* Change in aspect rating over 6M; compares 6M to all time. */
				$spanOf6M = self::extract_nested(self::subdata_date($aspect, time() - (SECONDS_YEAR/2)), 'Rating');
				$data_Percent6M = self::biased_mean($spanOf6M, $previousRating) - $data_RatingPercent;
				
				/* Change in aspect rating over 1Y; compares 1Y to all time. */
				$spanOf1Y = self::extract_nested(self::subdata_date($aspect, time() - SECONDS_YEAR), 'Rating');
				$data_Percent1Y = self::biased_mean($spanOf1Y, $previousRating) - $data_RatingPercent;
				
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
				
				if(!isset($metaAspects[$aspectTitle])){
					$metaAspects[$aspectTitle] = array('ID' => $localMetaAspects[$aspectTitle]['AspectTypeID'], 'Sum' => 0, 'Count' => 0);
				}
				$metaAspects[$aspectTitle]['Sum'] += $data_RatingPercent;
				$metaAspects[$aspectTitle]['Count']++;
			}
			
			/* Calculate overall 4W change and overall % for dashboard, considering ALL aspects. */
			if(count($aspects) > 0){
				$overall4W /= count($aspects);
				$overallAll /= count($aspects);
				
				/* 	TODO: See Simpson's Paradox. Considering the averages of each store equally may skew data. 
					If a single store occupies a large stake of the market, perhaps the store's data 
					should be weighed more using a weighted arithmatic sum (WAS). */
				$benchmarkCount++;
				$benchmarkSum += $overallAll;
			}
			
			/* Update dashboard data. */
			$dashboardID = $store['DashboardID'];
			if(($stmt = Database::prepare("UPDATE dashboard SET `Data_Overall4W` = ?, `Data_OverallAll` = ? WHERE dashboard.id = ?")) !== false){
				$stmt->bind_param('ddi', $overall4W, $overallAll, $dashboardID);
				$stmt->execute();
				$stmt->close();
			}
			
		}
		
		/* Calculate overall market benchmark, considering ALL aspects. TODO: See previous comment regarding WAS. */
		$benchmarkPercent = $benchmarkCount > 0 ? ((float) $benchmarkSum / $benchmarkCount) : 0;
		
		/* Update relative global benchmark percentages. */
		Database::query("UPDATE dashboard SET dashboard.Data_RelativeBenchmark = dashboard.Data_OverallAll - {$benchmarkPercent}");
		
		/* 	Update relative aspect specific benchmark percentages.
			TODO: Benchmark can theoretically be calculated based on data within a geographical area. */
		foreach($metaAspects as $metaAspect){
			$benchmark = @intval($metaAspect['Count']) > 0 ? ((float) $metaAspect['Sum']/$metaAspect['Count']) : 0;
			$metaAspectID = $metaAspect['ID'];
			
			Database::query("UPDATE aspects SET aspects.Data_RatingPercentOther = {$benchmark} WHERE aspects.AspectTypeID = {$metaAspectID}");
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