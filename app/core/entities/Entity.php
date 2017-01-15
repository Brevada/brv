<?php
/**
 * Abstract Entity
 *
 * @version v0.0.1 (Dec. 21, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

namespace Brv\core\entities;

/**
 * Represents an instance of a real world model which
 * can be amalgamated from multiple relations in the core schema.
 * An entity may optionally contain methods to operate on the the data
 * represented or referenced by said instance.
 */
abstract class Entity
{

    /**#@+
     * Data hydration flags which define the default hydration behaviour
     * for the entity instance.
     */
    /** @var integer Hydrates data from all row KV pairs. */
    const HYDRATE_ALL = 0b1;

    /** @var integer Only hydrates data from KV pairs with a key prefixed by '_'. */
    const HYDRATE_UNDERSCORE = 0b10;

    /**
     * @todo Intelligent casting has not been implemented.
     * @var integer Intelligently casts strings to more appropriate types.
     */
    const HYDRATE_CAST = 0b100;
    /**#@-*/

    /** @var array Internal KV-pair storage of entity data. */
    private $properties = [];

    /**
     * Instantiates an entity from a data row.
     *
     * @param array $row KV-pairs denoting the entity's initial data.
     */
    abstract public function __construct(array $row = []);

    /**
     * Commits entity state to persistant data storage.
     *
     * @throws \Exception on error.
     * @return mixed Indicates result of commit.
     */
    public function commit()
    {
        throw new \Exception("Unimplemented Entity method: commit");
    }

    /**
     * Looks up a data value of the entity by property name.
     *
     * @param string $property The name of the KV-pair to lookup.
     * @param mixed $blank The default value if the key does not exist.
     * @return mixed The value associated with the supplied property key.
     */
    protected function get($property, $blank = null)
    {
        return $this->has($property) ? $this->properties[$property] : $blank;
    }

    /**
     * Determines if a data value exists with an associated property name (key).
     *
     * @param  string $property The key to check.
     * @return boolean True if the property exists.
     */
    protected function has($property)
    {
        return isset($this->properties[$property]);
    }

    /**
     * Stores or updates a property with a value.
     *
     * @param string $property The property name to associate with the value.
     * @param mixed $value The value to store.
     * @return void
     */
    protected function set($property, $value)
    {
        $this->properties[$property] = $value;
    }

    /**
     * Hydrates the entity using a source data row.
     *
     * @see Entity::HYDRATE_* for flags to use as the hydration filter.
     * @param array $row The source data row.
     * @param integer $filter Hydration flags.
     * @return self Allows chaining.
     */
    public function hydrate(array $row, $filter = self::HYDRATE_ALL)
    {
        foreach ($row as $property => $value) {
            if (is_numeric($property)) {
                continue;
            }
            if ($filter & self::HYDRATE_UNDERSCORE) {
                if (strpos($property, '_') !== 0) {
                    continue;
                }
                $property = substr($property, 1);
            }

            $this->properties[$property] = $value;
        }

        return $this;
    }

    /**
     * Static factory method to instantiate an entity from a row.
     *
     * @param  array $row The initial data underlying the entity.
     * @return self An instantiated entity.
     */
    public static function from(array $row)
    {
        $cl = get_called_class();
        return new $cl($row);
    }
}
