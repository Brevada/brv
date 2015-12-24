<?php
/*
	DataResult
*/
class DataResult
{
	private $result;
	
	function __construct($result = [])
	{
		$this->result = result;
	}
	
	/**
	* Retrieves cluster metadata from result at an index.
	*
	* @param int $index Cluster index.
	*
	* @return array() Cluster metadata array.
	*/
	public function get($index = 0)
	{
		if(isset($this->result) && count($this->result) > 0){
			if($index >= 0 && $index < count($this->result)){
				return $this->result[$index];
			}
		}
		
		return [];
	}
	
	/**
	* Retrieves cluster average from result at an index.
	*
	* @param int $index Cluster index.
	*
	* @return float|boolean Average rating of cluster if it exists,
	*     otherwise, false.
	*/
	public function getRating($index = 0){
		$cluster = $this->get($index);
		if(isset($cluster[Data::AVERAGE_RATING])){
			return $cluster[Data::AVERAGE_RATING];
		} else {
			return false;
		}
	}
	
	/**
	* Retrieves cluster average date from result at an index.
	*
	* @param int $index Cluster index.
	*
	* @return int|boolean Average date of cluster if it exists,
	*     otherwise, false.
	*/
	public function getUTC($index = 0){
		$cluster = $this->get($index);
		if(isset($cluster[Data::AVERAGE_DATE])){
			return $cluster[Data::AVERAGE_DATE];
		} else {
			return false;
		}
	}
	
	/**
	* Retrieves number of data points considered in cluster from result at an index.
	*
	* @param int $index Cluster index.
	*
	* @return int|boolean Number of data points in cluster if it exists,
	*     otherwise, false.
	*/
	public function getSize($index = 0){
		$cluster = $this->get($index);
		if(isset($cluster[Data::TOTAL_DATASIZE])){
			return $cluster[Data::TOTAL_DATASIZE];
		} else {
			return false;
		}
	}
	
	/**
	* Calculates difference between first cluster of two DataResults.
	*
	* @param DataResult $dataA First DataResult.
	* @param DataResult $dataB Second DataResult.
	*
	* @return float Returns the difference between first clusters of
	*     $dataA and $dataB.
	*/
	public static function diffRating($dataA, $dataB)
	{
		return round($dataA[0][Data::AVERAGE_RATING] - $dataB[0][Data::AVERAGE_RATING], 2);
	}

	public function __toString()
	{
		return $this->getRating();
	}
}
?>