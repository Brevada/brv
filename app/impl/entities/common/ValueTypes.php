<?php
/**
 * ValueTypes | Common Entity Trait
 *
 * @version v0.0.1 (July. 03, 2017)
 * @copyright Copyright (c) 2017, Brevada
 */

namespace Brv\impl\entities\common;

trait ValueTypes {
    /**
     * Returns the possible value types of the aspect type. Returns null
     * if traditional rating system is used.
     *
     * @return array Assoc. array where key is internal value type key, and value
     * is the human readable label.
     */
    public function getValueTypes()
    {
        $raw = $this->get('ValueTypes');
        if (empty($raw)) return null;

        return json_decode($raw);
    }
}
