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
        if ($this->get('StoreID') === null) {
            return null;
        }
        return (int) $this->get('StoreID');
    }

    /**
     * Sets the store id.
     * @param integer $id
     */
    public function setStoreId($id)
    {
        $this->set('StoreID', is_null($id) ? null : (int) $id);
        return $this->getStoreId();
    }
}
