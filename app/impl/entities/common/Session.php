<?php
/**
 * Session | Common Entity Trait
 *
 * @version v0.0.1 (Apr. 12, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\impl\entities\common;

trait Session {
    /**
     * Gets the session code.
     *
     * @return string
     */
    public function getSessionCode()
    {
        return $this->get('SessionCode');
    }

    /**
     * Sets the session code.
     *
     * @param string $code
     * @return string
     */
    public function setSessionCode($code)
    {
        $this->set('SessionCode', $code);
        return $this->getSessionCode();
    }
}
?>
