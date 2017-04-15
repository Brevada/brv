<?php
/**
 * AspectId | Common Entity Trait
 *
 * @version v0.0.1 (Apr. 12, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\impl\entities\common;

trait AspectId {
    /**
     * Gets the id of the aspect.
     *
     * @return integer
     */
    public function getAspectId()
    {
        $data = $this->get('AspectID');
        return is_null($data) ? $data : (int) $data;
    }

    /**
     * Sets the aspect id.
     *
     * @param integer $id
     * @return integer
     */
    public function setAspectId($id)
    {
        $this->set('AspectID', is_null($id) ? null : (int) $id);
        return $this->getAspectId();
    }
}
?>
