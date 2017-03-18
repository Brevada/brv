<?php
/**
 * SanityTest | TestCase
 *
 * @version v0.0.1 (Dec. 22, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

use PHPUnit\Framework\TestCase;

/**
 * Asserts that PHPUnit is working.
 */
class SanityTest extends TestCase
{
    /**
     * Simple true check for sanity.
     * @test
     */
    public function testTrue()
    {
        $this->assertTrue(true);
    }

    /**
     * Simple false check for sanity.
     * @test
     */
    public function testFalse()
    {
        $this->assertFalse(false);
    }

    /**
     * Checks access to the global constants.
     * @test
     */
    public function testConstant()
    {
        $this->assertEquals(APP_NAME, "Brevada");
    }
}
?>
