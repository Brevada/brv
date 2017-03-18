<?php
/**
 * Company Id | Common Entity Trait
 *
 * @version v0.0.1 (Feb. 03, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\impl\entities\common;

trait CompanyId {
    /**
     * Gets the company id.
     *
     * @return integer
     */
    public function getCompanyId()
    {
        if ($this->get('CompanyID') === null) {
            return null;
        }
        return (int) $this->get('CompanyID');
    }

    /**
     * Sets the company id.
     * @param integer $id
     */
    public function setCompanyId($id)
    {
        $this->set('CompanyID', is_null($id) ? null : (int) $id);
        return $this->getCompanyId();
    }
}
