<?php
/**
 * Active | Common Entity Trait
 *
 * @version v0.0.1 (Feb. 03, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\impl\entities\common;

trait Active {
    /**
     * Checks if the entity is active.
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->get('Active') == 1;
    }

    /**
     * Sets the active state.
     *
     * @param boolean $state The new state.
     * @return boolean
     */
    public function setActive($state = true)
    {
        $this->set('Active', (int) $state);
        return $this->isActive();
    }
}
