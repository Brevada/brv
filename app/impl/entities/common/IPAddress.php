<?php
/**
 * IPAddress | Common Entity Trait
 *
 * @version v0.0.1 (Apr. 12, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\impl\entities\common;

trait IPAddress {
    /**
     * Gets the IP Address.
     *
     * @return string
     */
    public function getIPAddress()
    {
        return $this->get('IPAddress');
    }

    /**
     * Sets the IP Address.
     *
     * If $addr is true, it will be automatically populated.
     *
     * @param string $addr
     * @return string
     */
    public function setIPAddress($addr = true)
    {
        if ($addr === true) {
            $addr = $_SERVER['REMOTE_ADDR'];
        }

        $this->set('IPAddress', $addr);
        return $this->getIPAddress();
    }
}
?>
