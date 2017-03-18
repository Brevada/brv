<?php
/**
 * Simple Response
 *
 * @version v0.0.1 (Jan. 2, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\core\data;

use Brv\core\data\IResponse;

/**
 * Represents a simple implementation of IResponse.
 */
class SimpleResponse implements IResponse
{
    /** @var double The response value. */
    private $value;

    /** @var integer The response unix date. */
    private $date;

    /**
     * Instantiates a simple response from a value and date.
     *
     * @param double $value
     * @param integer $date
     */
    public function __construct($value, $date)
    {
        $this->value = $value;
        $this->date = $date;
    }

    /**
     * Gets the response rating value.
     *
     * @return double
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Gets the response submission date.
     *
     * @return integer
     */
    public function getDate()
    {
        return $this->date;
    }
}
