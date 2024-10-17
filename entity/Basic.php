<?php

namespace entity;

require_once "../entity/Entity.php";

use entity\Entity;
use JsonSerializable;

/**
 * Basic object class
 * 
 * All properties & methods who will be used in children classes.
 * 
 * @property string $name Name of the basic object.
 * 
 * @method string getName() Returns the name.
 * @method void setName() Sets the name.
 * @method array jsonSerialize() Method to convert the basic object into a JSON array.
 */
class Basic extends Entity implements JsonSerializable
{
    // Name of the basic object.
    private string $name;

    /**
     * Constructor.
     * 
     * @param array|null $array Array of data
     */
    protected function __construct(?array $array = null)
    {
        parent::__construct($array);
        if (is_array($array))
            $this->setName(array_key_exists("name", $array) ? ucfirst(trim($array["name"])) : "");
        else
            $this->setName("");
    }

    public function isValid(): bool
    {
        return is_string($this->getName()) && !empty($this->getName());
    }

    /**
     * Returns the name.
     * 
     * @return string Name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name.
     * 
     * @param string $name New name of the basic object.
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Method to convert the basic object into a JSON array.
     *
     * @return array Json content
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            "name" => $this->getName(),
        ]);
    }
}