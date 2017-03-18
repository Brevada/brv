<?php
/**
 * Response Interface
 *
 * @version v0.0.1 (Jan. 2, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\core\data;

/**
 * Represents a single feedback response entity.
 */
interface IResponse
{
    /**
     * Gets the response rating value.
     *
     * @return double
     */
    public function getValue();

    /**
     * Gets the response submission date.
     *
     * @return integer
     */
    public function getDate();
}
