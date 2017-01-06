<?php
/**
 * Core Data Class | Data
 *
 * @version v0.0.1 (Jan. 2, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\core\data;

use Brv\core\data\IResponse;

/**
 * Data
 */
class Data
{
    /**#@+
     * Seconds in a time interval.
     */
    /** @var integer Seconds in a minute. */
    const SECONDS_MINUTE = 60;

    /** @var integer Seconds in an hour. */
    const SECONDS_HOUR = 3600;

    /** @var integer Seconds in a day. */
    const SECONDS_DAY = 86400;
    /**#@-*/

    /** @var Response[] The collection of Responses. */
    protected $responses;

    /** @var integer The "from" unix time for which this Data collection represents. */
    protected $from = null;

    /** @var integer The "to" unix time for which this Data collection represents. */
    protected $to = null;

    /**
     * Instantiate a Data instance from an array of responses.
     *
     * @param IResponse[] $responses
     * @param array $opts Data options.
     */
    public function __construct($responses = [], $opts = [])
    {
        $this->responses = $responses;

        if (isset($opts['from'])) {
            $this->from = $opts['from'];
        }
        if (isset($opts['to'])) {
            $this->to = $opts['to'];
        }
    }

    /**
     * Gets the number of responses.
     *
     * @return integer
     */
    public function getCount()
    {
        return count($this->responses);
    }

    /**
     * Gets the sum of all values.
     *
     * @return double
     */
    public function getSum()
    {
        return array_reduce($this->responses, function ($carry, $item) {
            return $carry + $item->getValue();
        }, 0);
    }

    /**
     * Checks if there are no responses.
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->responses);
    }

    /**
     * Gets the average of all values.
     *
     * @return double
     */
    public function getAverage()
    {
        if ($this->isEmpty()) {
            return null;
        }
        return $this->getSum() / $this->getCount();
    }

    /**
     * Gets the earliest date or default from date if set.
     *
     * @return integer
     */
    public function getFrom()
    {
        if ($this->isEmpty() || $this->from !== null) {
            return $this->from;
        }
        return $this->getEarliestDate();
    }

    /**
     * Gets the latest date or default to date if set.
     *
     * @return integer
     */
    public function getTo()
    {
        if ($this->isEmpty() || $this->to !== null) {
            return $this->to;
        } else {
            return $this->getLatestDate();
        }
    }

    /**
     * Gets the earliest date.
     *
     * @return integer
     */
    public function getEarliestDate()
    {
        if ($this->isEmpty()) {
            return null;
        }
        return $this->responses[0]->getDate();
    }

    /**
     * Gets the latest date.
     *
     * @return integer
     */
    public function getLatestDate()
    {
        if ($this->isEmpty()) {
            return null;
        }
        return $this->responses[count($this->responses)-1]->getDate();
    }

    /**
     * Returns a new Data instance based off the current instance, discarding
     * data points outside the time bounds.
     *
     * @param integer $from Start time in seconds since epoch (inclusive).
     * @param integer $to End time in seconds since epoch (exclusive).
     * @return self
     */
    public function subsetTime($from, $to)
    {
        $opts = [
            "from" => $from,
            "to" => $to
        ];

        if ($this->isEmpty()) {
            return new self([], $opts);
        }

        $start = self::getLowerBound($this->responses, function ($item) {
            return $item->getDate();
        }, $from);

        if ($start === null) {
            return new self([], $opts);
        }

        $end = self::getLowerBound($this->responses, function ($item) {
            return $item->getDate();
        }, $to);

        if ($end === null) {
            $end = count($this->responses);
        }
        $end--; /* Not inclusive. */

        return new self(array_slice($this->responses, $start, $end - $start + 1), $opts);
    }

    /**
     * Returns max of n subsets of this Data instance where each subset is an equal
     * duration.
     *
     * @param integer $n The number of groups.
     * @param integer $from If set, will pad to start from $from.
     * @param integer $to If set, will pad to end at $to.
     * @return self[]
     */
    public function group($n, $from = null, $to = null)
    {
        $from = $from === null ? $this->getFrom() : $from;
        $to = $to === null ? $this->getTo() : $to;

        // If both data sets are empty, no groups.
        if ($from === null || $to === null) {
            return [];
        }

        $groups = [];

        // Determine whether to round to day or not to round at all.
        if ($to - $from > self::SECONDS_DAY - self::SECONDS_HOUR) {
            // Round if duration is greater than 23 hours (arbitrary).
            // TODO: Timezone should be determined per user.
            $from = self::infDay($from);
            $to = self::supDay($to);
        }

        $total = $to - $from;
        $perGroup = floor($total / $n);

        for ($i = 0; $i < $n - 1; $i++) {
            $group = $this->subsetTime($from + ($perGroup * $i), $from + ($perGroup * ($i + 1)));
            $groups[] = $group;
        }

        // Fill remainder.
        $group = $this->subsetTime($from + ($perGroup * ($n - 1)), $to);
        $groups[] = $group;

        return $groups;
    }

    /**
     * Rounds unix time down to the nearest day.
     *
     * @todo Move to time trait/class.
     * @todo $timezone not implemented.
     * @param integer $time The unix time to round.
     * @param integer $timezone A timezone offset.
     * @return integer
     */
    public static function infDay($time, $timezone = 0)
    {
        // TODO: Timezone not currently used.
        return max(0, strtotime("today", $time));
    }

    /**
     * Rounds unix time up to the nearest day.
     *
     * @todo Move to time trait/class.
     * @todo $timezone not implemented.
     * @param integer $time The unix time to round.
     * @param integer $timezone A timezone offset.
     * @return integer
     */
    public static function supDay($time, $timezone = 0)
    {
        // TODO: Timezone not currently used.
        return strtotime("tomorrow", $time);
    }

    /**
     * Finds the minimum index such that the corresponding value is greater than
     * or equal to $min in a sorted array.
     *
     * @todo Move this to trait.
     *
     * @param array $array The array to search.
     * @param callable $key A callback function to parse the "value" of the element.
     * @param integer $min The minimum value to compare against.
     * @return integer
     */
    public static function getLowerBound(array $array, callable $key, $min)
    {
        $lo = 0;
        $hi = count($array) - 1;

        // Based on a non-recursive binary search.
        while ($lo <= $hi) {
            $m = floor(($lo + $hi) / 2);
            $value = $key($array[$m]);
            if ($value < $min) {
                $lo = $m + 1;
            } elseif ($value >= $min) {
                if (!isset($array[$m - 1]) || $key($array[$m - 1]) < $min) {
                    return $m;
                }
                $hi = $m - 1;
            }
        }

        return null;
    }

    /**
     * Returns the difference between the averages of two datasets if both
     * averages are defined, otherwise returns null.
     *
     * @param self $other
     * @return integer|double
     */
    public function getAverageDiff($other)
    {
        $a = $this->getAverage();
        $b = $other->getAverage();

        if ($a === null || $b === null) {
            return null;
        }

        return $a - $b;
    }
}
