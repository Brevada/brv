<?php
/**
 * Location | Common Entity Trait
 *
 * @version v0.0.1 (Feb. 03, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\impl\entities\common;

trait Location {
    /**
     * Gets the country.
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->get('Country');
    }

    /**
     * Sets the country.
     *
     * @param string $val
     * @return string
     */
    public function setCountry($val)
    {
        $this->set('Country', is_null($val) ? null : $val);
        return $this->getCountry();
    }

    /**
     * Gets the province.
     *
     * @return string
     */
    public function getProvince()
    {
        return $this->get('Province');
    }

    /**
     * Sets the province.
     *
     * @param string $val
     * @return string
     */
    public function setProvince($val)
    {
        $this->set('Province', is_null($val) ? null : $val);
        return $this->getProvince();
    }

    /**
     * Gets the city.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->get('City');
    }

    /**
     * Sets the city.
     *
     * @param string $val
     * @return string
     */
    public function setCity($val)
    {
        $this->set('City', is_null($val) ? null : $val);
        return $this->getCity();
    }

    /**
     * Gets the postal code.
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->get('PostalCode');
    }

    /**
     * Sets the country.
     *
     * @param string $val
     * @return string
     */
    public function setPostalCode($val)
    {
        $this->set('PostalCode', is_null($val) ? null : $val);
        return $this->getPostalCode();
    }

    /**
     * Gets the longitude.
     *
     * @return double
     */
    public function getLongitude()
    {
        return $this->get('Longitude');
    }

    /**
     * Sets the longitude.
     *
     * @param double $val
     * @return double
     */
    public function setLongitude($val)
    {
        $this->set('Longitude', is_null($val) ? null : (double) $val);
        return $this->getLongitude();
    }

    /**
     * Gets the latitude.
     *
     * @return double
     */
    public function getLatitude()
    {
        return $this->get('Latitude');
    }

    /**
     * Sets the latitude.
     *
     * @param double $val
     * @return double
     */
    public function setLatitude($val)
    {
        $this->set('Latitude', is_null($val) ? null : (double) $val);
        return $this->getLatitude();
    }
}
