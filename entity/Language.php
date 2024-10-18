<?php

namespace entity;

require_once "../entity/Basic.php";

use entity\Basic;
use JsonSerializable;

/**
 * Language.
 * 
 * @property string $abbreviation
 * 
 * @method string getAbbreviation() Returns the abbreviation.
 * @method void setAbbreviation() Sets the abbreviation.
 * @method array jsonSerialize() Method to convert the language into a JSON array.
 */
final class Language extends Basic implements JsonSerializable
{

    // Abbreviation of the language.
    private string $abbreviation;

    /**
     * Constructor.
     * 
     * @param array|null $array Array of data
     */
    public function __construct(?array $array = null)
    {
        parent::__construct($array);
        if (is_array($array))
            $this->setAbbreviation(array_key_exists("abbreviation", $array) ? $array["abbreviation"] : "");
        else
            $this->setAbbreviation("");
    }

    /**
     * Returns the abbreviation.
     * 
     * @return string Abbreviation
     */
    public function getAbbreviation(): string
    {
        return $this->abbreviation;
    }

    /**
     * Sets the abbreviation.
     * 
     * @param string $abbreviation New abbreviation of the language.
     * @return void
     */
    public function setAbbreviation(string $abbreviation): void
    {
        $this->abbreviation = $abbreviation;
    }

    /**
     * Method to convert the language into a JSON array.
     *
     * @return array Json content
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            "abbreviation" => $this->abbreviation
        ]);
    }
}