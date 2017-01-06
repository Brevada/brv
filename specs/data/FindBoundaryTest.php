<?php
/**
 * FindBoundaryTest | TestCase
 *
 * @version v0.0.1 (Dec. 25, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

use PHPUnit\Framework\TestCase;
use Brv\core\libs\data\Data;

/**
 * Asserts that the Data FindBoundary function works.
 */
class FindBoundaryTest extends TestCase
{
    /**
     * An empty array has no defined boundaries.
     * @covers Data::lowerBoundIndex
     * @test
     */
    public function testEmpty()
    {
        $array = [];
        $result = Data::lowerBoundIndex($array, function ($item) {
            return $item;
        }, 5);
        $this->assertNull($result);
    }

    /**
     * No defined boundary if subsequence does not exist.
     * @covers Data::lowerBoundIndex
     * @test
     */
    public function testSingleNotFound()
    {
        $array = [3];
        $result = Data::lowerBoundIndex($array, function ($item) {
            return $item;
        }, 5);
        $this->assertNull($result);
    }

    /**
     * Boundary is entire array.
     * @covers Data::lowerBoundIndex
     * @test
     */
    public function testSingleFound()
    {
        $array = [5];
        $index = Data::lowerBoundIndex($array, function ($item) {
            return $item;
        }, 3);
        $this->assertEquals(0, $index);
    }

    /**
     * Test a fraction as the min value.
     * @covers Data::lowerBoundIndex
     * @test
     */
    public function testDouble()
    {
        $array = [3, 4];
        $index = Data::lowerBoundIndex($array, function ($item) {
            return $item;
        }, 3.5);
        $this->assertEquals(1, $index);
    }

    /**
     * Test large array.
     * @covers Data::lowerBoundIndex
     * @test
     */
    public function testLargeArray()
    {
        $array = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $index = Data::lowerBoundIndex($array, function ($item) {
            return $item;
        }, 7);
        $this->assertEquals(7, $index);
    }
}
?>
