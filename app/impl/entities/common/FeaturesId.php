<?php
/**
 * Features Id | Common Entity Trait
 *
 * @version v0.0.1 (Feb. 03, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\impl\entities\common;

trait FeaturesId {
    /**
     * Gets the features id.
     *
     * @return integer
     */
    public function getFeaturesId()
    {
        if (is_null($this->get('FeaturesID'))) {
            return null;
        }

        return (int) $this->get('FeaturesID');
    }

    /**
     * Sets the features id.
     * @param integer $id
     */
    protected function setFeaturesId($id)
    {
        $this->set('FeaturesID', (int) $id);
        return $this->getFeaturesId();
    }
}
