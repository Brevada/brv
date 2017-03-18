<?php
/**
 * StoreFeatures | Common Entity Trait
 *
 * @version v0.0.1 (Feb. 03, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\impl\entities\common;

trait StoreFeatures {
    /**
     * Gets the store collection template.
     *
     * @return string
     */
    public function getCollectionTemplate()
    {
        return $this->get('CollectionTemplate');
    }

    /**
     * Sets the store's collection template.
     *
     * @param string $template
     * @return string
     */
    public function setCollectionTemplate($template)
    {
        $this->set('CollectionTemplate', $template);
        return $this->getCollectionTemplate();
    }

    /**
     * Gets the location of the extended data collection form.
     *
     * @return integer
     */
    public function getCollectionLocation()
    {
        return $this->get('CollectionLocation');
    }

    /**
     * Sets the store's data collection form location.
     *
     * @param integer $location
     * @return string
     */
    public function setCollectionLocation($location)
    {
        $this->set('CollectionLocation', is_null($location) ? null : (int) $location);
        return $this->getCollectionLocation();
    }

    /**
     * Gets the store's welcome message.
     *
     * @return string
     */
    public function getWelcomeMessage()
    {
        return $this->get('WelcomeMessage');
    }

    /**
     * Sets the store's welcome message.
     *
     * @param string $message
     * @return string
     */
    public function setWelcomeMessage($message)
    {
        $this->set('WelcomeMessage', $message);
        return $this->getWelcomeMessage();
    }

    /**
     * Gets the store's comment message.
     *
     * @return string
     */
    public function getCommentMessage()
    {
        return $this->get('CommentMessage');
    }

    /**
     * Sets the store's comment message.
     *
     * @param string $message
     * @return string
     */
    public function setCommentMessage($message)
    {
        $this->set('CommentMessage', $message);
        return $this->getCommentMessage();
    }

    /**
     * Checks if a users session should be checked
     *
     * @return boolean
     */
    public function isSessionCheck()
    {
        return ((int) $this->get('SessionCheck')) === 1;
    }

    /**
     * Sets the store's session check state.
     *
     * @param boolean $state
     * @return boolean
     */
    public function setSessionCheck($state)
    {
        $this->set('SessionCheck', (int) $state);
        return $this->isSessionCheck();
    }

    /**
     * Checks if the store's feedback page allows comments
     *
     * @return boolean
     */
    public function isAllowComments()
    {
        return ((int) $this->get('AllowComments')) === 1;
    }

    /**
     * Sets whether the store's feedback page allows comments.
     *
     * @param boolean $state
     * @return boolean
     */
    public function setAllowComments($state)
    {
        $this->set('AllowComments', (int) $state);
        return $this->isAllowComments();
    }
}
?>
