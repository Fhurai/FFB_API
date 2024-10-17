<?php

namespace entity;

require_once "../entity/Basic.php";

use entity\Basic;
use JsonSerializable;

/**
 * Fandom.
 * 
 * @method array jsonSerialize() Method to convert the fandom into a JSON array.
 */
final class Fandom extends Basic implements JsonSerializable
{

    public function __construct(?array $array = null)
    {
        parent::__construct($array);
    }

    /**
     * Method to convert the fandom into a JSON array.
     *
     * @return array Json content
     */
    public function jsonSerialize(): array
    {
        return parent::jsonSerialize();
    }
}