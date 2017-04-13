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
        $id = $this->get('LocationID');
        return is_null($id) ? null : (int) $id;
    }

    /**
     * Sets the location id.
     * @param integer $id
     */
    protected function setLocationId($id)
    {
        $this->set('LocationID', is_null($id) ? null : (int) $id);
        return $this->getLocationId();
    }
}
