<?php
/**
 * Location Id | Common Entity Trait
 *
 * @version v0.0.1 (Feb. 03, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\impl\entities\common;

trait LocationId {
    /**
     * Gets the location id.
     *
     * @return integer
     */
    public function getLocationId()
    {
        return (int) $this->get('LocationID');
    }

    /**
     * Sets the location id.
     * @param integer $id
     */
    protected function setLocationId($id)
    {
        $this->set('LocationID', (int) $id);
        return $this->getLocationId();
    }
}
