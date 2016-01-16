<?php
class DataResultTest extends PHPUnit_Framework_TestCase
{
	protected $resultA;
	protected $resultB;
	
	protected function setUp()
	{
		$this->resultA = [
			[Data::AVERAGE_RATING => 50.0, Data::AVERAGE_DATE => 1000, Data::TOTAL_DATASIZE => 3],
			[Data::AVERAGE_RATING => 60.0, Data::AVERAGE_DATE => 1200, Data::TOTAL_DATASIZE => 6],
			[Data::AVERAGE_RATING => 70.0, Data::AVERAGE_DATE => 1100, Data::TOTAL_DATASIZE => 4],
			[Data::AVERAGE_RATING => 30.0, Data::AVERAGE_DATE => 900, Data::TOTAL_DATASIZE => 8]
		];
		
		$this->resultB = [
			[Data::AVERAGE_RATING => 70.0, Data::AVERAGE_DATE => 1000, Data::TOTAL_DATASIZE => 3],
			[Data::AVERAGE_RATING => 60.0, Data::AVERAGE_DATE => 1200, Data::TOTAL_DATASIZE => 6]
		];
	}
	
	public function testGetRating()
	{
		$dataResult = new DataResult($this->resultA);
		
		$this->assertEquals($dataResult->getRating(0), 50.0);
		$this->assertEquals($dataResult->getRating(1), 60.0);
		$this->assertEquals($dataResult->getRating(2), 70.0);
		$this->assertEquals($dataResult->getRating(3), 30.0);
		$this->assertFalse($dataResult->getRating(4));
	}
	
	public function testGetUTC()
	{
		$dataResult = new DataResult($this->resultA);
		
		$this->assertEquals($dataResult->getUTC(0), 1000);
		$this->assertEquals($dataResult->getUTC(1), 1200);
		$this->assertEquals($dataResult->getUTC(2), 1100);
		$this->assertEquals($dataResult->getUTC(3), 900);
		$this->assertFalse($dataResult->getUTC(4));
	}
	
	public function testGetSize()
	{
		$dataResult = new DataResult($this->resultA);
	
		$this->assertEquals($dataResult->getSize(0), 3);
		$this->assertEquals($dataResult->getSize(1), 6);
		$this->assertEquals($dataResult->getSize(2), 4);
		$this->assertEquals($dataResult->getSize(3), 8);
		$this->assertFalse($dataResult->getSize(4));
	}
	
	public function testString()
	{
		$dataResult = new DataResult($this->resultA);
		
		$expected = (string)$dataResult->getRating();
		
		$this->assertEquals($expected, '50.0');
		$this->assertEquals($expected, (string) $dataResult);
	}
	
	public function testDiff()
	{
		$dataResultA = new DataResult($this->resultA);
		$dataResultB = new DataResult($this->resultB);
		
		$this->assertEquals(DataResult::diffRating($dataResultA, $dataResultB), -20.0);
	}
}
?>