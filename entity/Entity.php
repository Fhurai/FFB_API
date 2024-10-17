<?php

namespace entity;

use DateTime;
use DateTimeZone;
use JsonSerializable;
use stdClass;

/**
 * Entity class
 * 
 * All properties & methods who will be used in children classes.
 * 
 * @property int $id Identifier of the entity.
 * @property DateTime $creation_date Creation date of the entity.
 * @property DateTime $update_date Update date of the entity.
 * @property DateTime|null $suppression_date Suppression date of the entity.
 * 
 * @method integer getId() Returns the identifier.
 * @method void setId(int|string $id) Set the identifier.
 * @method DateTime getCreationDate() Returns the creation date.
 * @method void setCreationDate(DateTime|string $creation_date) Set the creation date.
 * @method DateTime getUpdateDate() Returns the update date.
 * @method void setUpdateDate(DateTime|string $update_date) Set the update date.
 * @method DateTime getSuppressionDate() Returns the suppression date.
 * @method void setSuppressionDate(DateTime|string|null $suppression_date) Set the suppression date.
 * @method bool isDeleted() Method to know if the entity is deleted or not.
 * @method array jsonSerialize() Method to convert the entity into a JSON array.
 */
abstract class Entity extends stdClass implements JsonSerializable
{

    // Identifier of the entity.
    private int $id;

    // Creation date of the entity.
    private DateTime $creation_date;

    // Update date of the entity.
    private DateTime $update_date;

    // Suppression date of the entity.
    private ?DateTime $suppression_date;

    abstract public function isValid() : bool;

    /**
     * Constructor.
     *
     * @param array|null $array Array of data
     */
    protected function __construct(?array $array = null)
    {
        if (is_array($array)) {
            $this->setId(array_key_exists("id", $array) ? $array["id"] : 0);
            $this->setCreationDate(array_key_exists("creation_date", $array) ? $array["creation_date"] : "now");
            $this->setUpdateDate(array_key_exists("update_date", $array) ? $array["update_date"] : "now");
            $this->setSuppressionDate(array_key_exists("suppression_date", $array) ? $array["suppression_date"] : null);
        } else {
            $this->setId(0);
            $this->setCreationDate("now");
            $this->setUpdateDate("now");
            $this->setSuppressionDate(null);
        }
    }

    /**
     * Returns the identifier.
     *
     * @return integer Identifier
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the identifier.
     *
     * @param integer|string $id Identifier
     * @return void
     */
    public function setId(int|string $id): void
    {
        // If the identifier arg is not an integer, it is cast as one.
        $this->id = is_string($id) ? intval($id) : $id;
    }

    /**
     * Returns the creation date.
     *
     * @return DateTime Creation date.
     */
    public function getCreationDate(): DateTime
    {
        return $this->creation_date;
    }

    /**
     * Set the creation date.
     *
     * @param DateTime|string $creation_date Creation date.
     * @return void
     */
    public function setCreationDate(DateTime|string $creation_date): void
    {
        // If the creation date arg is not a datetime, it is cast as one with the Europe/Paris timezone.
        $this->creation_date = is_string($creation_date) ? new DateTime($creation_date, new DateTimeZone("Europe/Paris")) : $creation_date;
    }

    /**
     * Returns the update date.
     *
     * @return DateTime Update date.
     */
    public function getUpdateDate(): DateTime
    {
        return $this->update_date;
    }

    /**
     * Set the update date.
     *
     * @param DateTime|string $update_date Update date
     * @return void
     */
    public function setUpdateDate(DateTime|string $update_date): void
    {
        // If the update date arg is not a datetime, it is cast as one with the Europe/Paris timezone.
        $this->update_date = is_string($update_date) ? new DateTime($update_date, new DateTimeZone("Europe/Paris")) : $update_date;
    }

    /**
     * Returns the suppression date.
     *
     * @return DateTime|null Suppression date or null.
     */
    public function getSuppressionDate(): ?DateTime
    {
        return $this->suppression_date;
    }

    /**
     * Set the suppression date.
     *
     * @param DateTime|string|null $suppression_date Suppression date
     * @return void
     */
    public function setSuppressionDate(DateTime|string|null $suppression_date): void
    {
        // If the suppression date arg is not a datetime, it is cast as one with the Europe/Paris timezone.
        $this->suppression_date = is_string($suppression_date) ? new DateTime($suppression_date, new DateTimeZone("Europe/Paris")) : $suppression_date;
    }

    /**
     * Method to know if the entity is deleted or not.
     *
     * @return boolean Indication of deletion.
     */
    public function isDeleted(): bool
    {
        return isset($this->suppression_date) && !is_null($this->suppression_date);
    }

    /**
     * Method to convert the entity into a JSON array.
     *
     * @return array Json content
     */
    public function jsonSerialize(): array
    {
        return [
            "id" => $this->id,
            "creation_date" => $this->creation_date->format("Y-m-d H:i:s"),
            "update_date" => $this->update_date->format("Y-m-d H:i:s"),
            "suppression_date" => !is_null($this->suppression_date) ? $this->suppression_date->format("Y-m-d H:i:s") : null,
        ];
    }
}
