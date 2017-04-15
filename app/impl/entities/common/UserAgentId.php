<?php
/**
 * UserAgentId | Common Entity Trait
 *
 * @version v0.0.1 (Apr. 12, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\impl\entities\common;

trait UserAgentId {
    /**
     * Gets the id of the user agent.
     *
     * @return integer
     */
    public function getUserAgentId()
    {
        $data = $this->get('UserAgentID');
        return is_null($data) ? $data : (int) $data;
    }

    /**
     * Sets the user agent id.
     *
     * @param integer $id
     * @return integer
     */
    public function setUserAgentId($id)
    {
        $this->set('UserAgentID', is_null($id) ? null : (int) $id);
        return $this->getUserAgentId();
    }
}
?>
