<?php
/**
 * Store Id | Common Entity Trait
 *
 * @version v0.0.1 (Feb. 03, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\impl\entities\common;

trait StoreId {
    /**
     * Gets the store id.
     *
     * @return integer
     */
    public function getStoreId()
    {
        return (int) $this->get('StoreID');
    }

    /**
     * Sets the store id.
     * @param integer $id
     */
    public function setStoreId($id)
    {
        $this->set('StoreID', (int) $id);
        return $this->getStoreId();
    }
}
