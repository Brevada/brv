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
        return (int) $this->get('CompanyID');
    }

    /**
     * Sets the company id.
     * @param integer $id
     */
    public function setCompanyId($id)
    {
        $this->set('CompanyID', (int) $id);
        return $this->getCompanyId();
    }
}
