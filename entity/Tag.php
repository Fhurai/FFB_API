<?php

namespace entity;

require_once "../entity/Basic.php";

use entity\Basic;
use JsonSerializable;

/**
 * Language.
 * 
 * @property string $description
 * 
 * @method string getDescription() Returns the description.
 * @method void setDescription() Sets the description.
 * @method array jsonSerialize() Method to convert the language into a JSON array.
 */
final class Tag extends Basic implements JsonSerializable
{

    // Description of the language.
    private string $description;

    /**
     * Constructor.
     * 
     * @param array|null $array Array of data
     */
    public function __construct(?array $array = null)
    {
        parent::__construct($array);
        if (is_array($array))
            $this->setDescription(array_key_exists("description", $array) ? $array["description"] : "");
        else
            $this->setDescription("");
    }

    /**
     * Returns the description.
     * 
     * @return string Description
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Sets the description.
     * 
     * @param string $abbreviation New description of the language.
     * @return void
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Method to convert the language into a JSON array.
     *
     * @return array Json content
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            "description" => $this->description
        ]);
    }
}