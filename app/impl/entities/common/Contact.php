<?php
/**
 * Contact | Common Entity Trait
 *
 * @version v0.0.1 (Feb. 03, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\impl\entities\common;

trait Contact {
    /**
     * Gets the phone number.
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->get('PhoneNumber');
    }

    /**
     * Sets the phone number.
     *
     * @param string $phone
     * @return string
     */
    public function setPhoneNumber($phone)
    {
        $this->set('PhoneNumber', $phone);
        return $this->getPhoneNumber();
    }

    /**
     * Gets the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->get('Name');
    }

    /**
     * Sets the name.
     *
     * @param string $name
     * @return string
     */
    public function setName($name)
    {
        $this->set('Name', $name);
        return $this->getName();
    }
}
