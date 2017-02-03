<?php
/**
 * Company Features | Common Entity Trait
 *
 * @version v0.0.1 (Feb. 03, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\impl\entities\common;

trait CompanyFeatures {
    /**
     * Gets the max number of stores the company can have.
     *
     * @return integer
     */
    public function getMaxStores()
    {
        return (int) $this->get('MaxStores', 0);
    }

    /**
     * Sets the max number of stores the company can have.
     *
     * @param integer $val The new value.
     * @return integer
     */
    public function setMaxStores($val)
    {
        $this->set('MaxStores', (int) $val);
        return $this->getMaxStores();
    }

    /**
     * Gets the max number of accounts the company can have.
     *
     * @return integer
     */
    public function getMaxAccounts()
    {
        return (int) $this->get('MaxAccounts', 0);
    }

    /**
     * Sets the max number of accounts the company can have.
     *
     * @param integer $val The new value.
     * @return integer
     */
    public function setMaxAccounts($val)
    {
        $this->set('MaxAccounts', (int) $val);
        return $this->getMaxAccounts();
    }

    /**
     * Gets the max number of tablets the company can have.
     *
     * @return integer
     */
    public function getMaxTablets()
    {
        return (int) $this->get('MaxTablets', 0);
    }

    /**
     * Sets the max number of tablets the company can have.
     *
     * @param integer $val The new value.
     * @return integer
     */
    public function setMaxTablets($val)
    {
        $this->set('MaxTablets', (int) $val);
        return $this->getMaxTablets();
    }
}
?>
