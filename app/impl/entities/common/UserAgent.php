<?php
/**
 * UserAgent | Common Entity Trait
 *
 * @version v0.0.1 (Apr. 12, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\impl\entities\common;

trait UserAgent {
    /**
     * Gets the full user agent.
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->get('UserAgent');
    }

    /**
     * Sets the user agent.
     *
     * @param string $agent
     * @return string
     */
    public function setUserAgent($agent)
    {
        $this->set('UserAgent', $agent);
        return $this->getUserAgent();
    }

    /**
     * Gets the id of the associated tablet (via the user agent relation).
     *
     * @return integer
     */
    public function getTabletId()
    {
        $data = $this->get('TabletID');
        return is_null($data) ? $data : (int) $data;
    }

    /**
     * Sets the associated tablet id (via user agent relation).
     *
     * @param integer $id
     * @return integer
     */
    public function setTabletId($id)
    {
        $this->set('TabletID', is_null($id) ? null : (int) $id);
        return $this->getTabletId();
    }
}
?>
