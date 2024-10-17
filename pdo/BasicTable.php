<?php

namespace pdo;

require_once "../pdo/Connection.php";
require_once "../entity/Entity.php";

use entity\Basic;
use pdo\Connection;
use entity\Entity;
use DateTime;
use DateTimeZone;
use PDO;

abstract class BasicTable extends Connection
{

    /**
     * Constructor 
     * 
     * @param string $db The database for the entity.
     */
    function __construct(string $db)
    {
        parent::__construct($db);
    }

    /**
     * Method to turn an entity into a parameters array
     * 
     * @param \entity\Basic $entity Entity whom extract data.
     * @param ?array $dirty Array of dirty fields to replace.
     * @return array Data array for SQL query execution.
     */
    protected function getEntityValues(Basic $entity, array $dirty = null)
    {
        // Array initialization.
        $params = [];

        // Get fields to edit, whether this is for creation or update.
        $fields = is_null($dirty) ? $this->getFields() : array_merge($dirty, ["id"]);

        foreach ($fields as $field) {
            // Going through entity fields.

            if ($field === "creation_date" || $field === "update_date" || $field === "suppression_date") {
                // If field is a date, function is to get the Datetime object then using the format function.
                $function = "get" . explode("_", $field)[0] . "Date";
                $params[$field] = !is_null($entity->$function()) ? $entity->$function()->format('Y-m-d H:i:s') : null;
            } else {
                // If not those two fields, then function is to get the data from object.
                $function = "get" . ucfirst($field);
                $params[$field] = $entity->$function();
            }
        }

        // Returns the parameters array.
        return $params;
    }

    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * ABSTRACT METHODS.
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     */

    /**
     * Method to get current table name.
     * 
     * @return string Current table name, defined in each table.
     */
    abstract protected function getTable(): string;

    abstract public function getFields(): array;

    abstract protected function dataObjectify(array $data): array;


    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * SQL METHODS.
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     */

    /**
     * Returns the create SQL query.
     * 
     * @return string create SQL query.
     */
    protected function getCreateQuery(): string
    {
        $query = "INSERT INTO `" . $this->getTable() . "`(";

        // Filter the id & suppression field from tables fields then put remaining fields into create query as fields.
        $query .= implode(", ", array_map(function ($field) {
            return "`" . $field . "`";
        }, array_filter($this->getFields(), function ($field) {
            return $field !== "id";
        })));

        $query .= ") VALUES (";

        // Filter the id & suppression field from tables fields then put remaining fields into create query as tokens.
        $query .= implode(", ", array_map(function ($field) {
            return ":" . $field;
        }, array_filter($this->getFields(), function ($field) {
            return $field !== "id";
        })));

        $query .= ")";

        return $query;
    }

    /**
     * Returns the remove SQL query.
     * 
     * @return string remove SQL query.
     */
    protected function getRemoveQuery(): string
    {
        return "DELETE FROM `" . $this->getTable() . "` WHERE `id`=:id AND suppression_date IS NOT NULL";
    }

    /**
     * Returns the update SQL query.
     * 
     * @param array $dirty Array of dirty updated fields
     * @return string update SQL query.
     */
    protected function getUpdateQuery(array $dirty): string
    {
        $query = "UPDATE `" . $this->getTable() . "` SET ";

        $query .= implode(", ", array_map(function ($field) {
            return $field . "= :" . $field;
        }, $dirty));

        $query .= " WHERE id=:id";
        return $query;
    }


    /**
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * WEBSERVICES METHODS.
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     */

    /**
     * Method to get all entities of table.
     * 
     * @param ?array $params array of parameters ["fields", "start", "count"]
     * @return array Array of entities or array of array.
     */
    public function getAll(array $params = null): array
    {

        return $this->getSearch($params);
    }

    /**
     * Method to get one entity of table.
     * 
     * @param string|int $id Identiier of entity.
     * @param array $params array of parameters ["fields"].
     * @return array|Entity Entity obtained.
     */
    public function get(string|int $id, array $params = null): array|Entity
    {
        $search = ["search" => ["id" => $id]];
        $params = !is_null($params) ? array_merge($params, $search) : $search;
        return $this->getSearch($params)[0];
    }


    /**
     * Method to get all entities corresponding to a search.
     * @param array $params array of parameters ["fields", "search", "start", "count"]
     * @return array Array of entities or array of array corresponding to search.
     */
    public function getSearch(array $params = null): array
    {
        if (is_array($params)) {
            // If parameters are an array.

            // fields in query by default are those defined in table.
            $fields = implode(", ", array_merge($this->getFields(), ["id"]));

            if (array_key_exists("fields", $params) && is_array($params["fields"]))
                // If fields parameter is an array, they are converted into a string.
                $fields = implode(",", $params["fields"]);
            else if (array_key_exists("fields", $params) && is_string($params["fields"]))
                // If fields parameters is a string, they are affected to a string.
                $fields = $params["fields"];

            // Query is defined with fields string and the table name defined in table.
            $query = "SELECT " . $fields . " FROM " . $this->getTable();

            if (array_key_exists("search", $params) && is_array($params["search"])) {
                $query .= " WHERE";
                foreach ($params["search"] as $field => $value) {
                    if (is_numeric($value) && is_string($field))
                        $query .= " " . strval($field) . "=" . $value;
                    else
                        $query .= " " . strval($field) . " LIKE '%" . $value . "%'";
                }
            }

            if (array_key_exists("start", $params) && array_key_exists("count", $params))
                // If start & count parameters are defined
                if (is_numeric($params["start"]) && is_numeric($params["count"]))
                    // Check if start & count parameters are numeric before using them.
                    $query .= " LIMIT " . intval($params["start"]) . ", " . intval($params["count"]) . ";";
        } else
            // No parameters or invalid parameters, default query created.
            $query = "SELECT " . implode(", ", array_merge($this->getFields(), ["id"])) . " FROM " . $this->getTable();

        // Query preparation.
        $stmt = $this->getConnection()->prepare($query);

        // Query execution.
        $stmt->execute();

        if (is_array($params) && array_key_exists("fields", $params))
            // Fields defined so array of array.
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        else
            // Fields not defined so array of entities.
            return $this->dataObjectify($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Method to create an entity.
     * 
     * @param \entity\Basic $entity Entity to create.
     * @param bool $ownTransaction State if method use own transaction. Default to true.
     * @return \entity\Basic|bool Entity if created. False otherwise.
     */
    public function create(Basic $entity, bool $ownTransaction = true): Basic|bool
    {
        // Check if entity is valid.
        if ($entity->isValid()) {

            if ($ownTransaction)
                // If method has to manage its own transaction, it begins now.
                $this->getConnection()->beginTransaction();

            // Get the creation query.
            $query = $this->getCreateQuery();

            // Prepare the query to be executed.
            $stmt = $this->getConnection()->prepare($query);

            // Get the parameters for the execution of the query.
            $params = $this->getEntityValues($entity);

            // Execution of the query.
            $success = $stmt->execute($params);

            if ($success) {
                // Success of the execution

                if ($ownTransaction)
                    // If method has to manage its own transaction, it commits now.
                    $this->getConnection()->commit();

                // Recuperation saved data.
                $object = $this->getSearch(["search" => ["name" => $entity->getName()]])[0];

                // If object is the saved entity, returns object. If not, returns false.
                return ($object->getName() === $entity->getName()) ? $object : false;
            } else {

                if ($ownTransaction)
                    // If method has to manage its own transaction, it rollbacks now.
                    $this->getConnection()->rollBack();
            }
        }
        // No creation, returns false.
        return false;
    }

    /**
     * Method to remove an entity
     * 
     * @param int|string|\entity\Basic $entity Entity to remove.
     * @param bool $ownTransaction State if method use own transaction. Default to true.
     * @return bool State if entity removed or not.
     */
    public function remove(int|string|Basic $entity, bool $ownTransaction = true): bool
    {
        // Initialization of entity to remove
        $entityToRemove = null;

        if (is_integer($entity) || is_string($entity)) {
            // If entity is integer or string, this is the identifier of the entity.
            $entityToRemove = $this->get($entity);
        } else {
            // Entity is not integer or string, this is the entity to remove.
            $entityToRemove = $entity;
        }

        if ($entityToRemove === null)
            // No entity to remove, returns false.
            return false;

        if ($ownTransaction)
            // If method has to manage its own transaction, it begins now.
            $this->getConnection()->beginTransaction();

        if ($entityToRemove->isDeleted()) {
            // Entity is logically deleted, can be removed.

            // Get the remove query.
            $query = $this->getRemoveQuery();

            // Prepare the query to be executed.
            $stmt = $this->getConnection()->prepare($query);

            // Execution of the query.
            $success = $stmt->execute([
                "id" => $entityToRemove->getId()
            ]);

            if ($success) {
                // Success of the execution

                if ($ownTransaction)
                    // If method has to manage its own transaction, it commits now.
                    $this->getConnection()->commit();

                // Remove successful, return true.
                return true;
            }
        }

        if ($ownTransaction)
            // If method has to manage its own transaction, it rollbacks now.
            $this->getConnection()->rollBack();

        // No remove, returns false.
        return false;
    }

    /**
     * Method to update an entity
     * 
     * @param \entity\Basic $entity Entity to update.
     * @param array $params Array of parameters ["dirty"] 
     * @param bool $ownTransaction State if method use own transaction. Default to true.
     * @return \entity\Basic|bool Entity if updated. False otherwise.
     */
    public function update(Basic $entity, array $params = null, bool $ownTransaction = true): Basic|bool
    {

        if (is_array($params)) {
            // If parameters are an array

            if (array_key_exists("dirty", $params) && !empty($params["dirty"])) {
                // If some dirty fields are provided to the method.

                if ($entity->isDeleted() && $params["dirty"][0] !== "suppression_date")
                    // If entity is deleted, check if update method is not called from delete method.
                    return false;

                if (!array_search("update_date", $params["dirty"])) {
                    // If update date is not amongst the dirty fields, it is added and entity is edited for it.
                    $params["dirty"][] = "update_date";
                    $entity->setUpdateDate(new DateTime("now", new DateTimeZone("Europe/Paris")));
                }

                if ($ownTransaction)
                    // If method has to manage its own transaction, it begins now.
                    $this->getConnection()->beginTransaction();

                // Get the update query.
                $query = $this->getUpdateQuery($params["dirty"]);

                // Get the parameters for the execution of the query.
                $params = $this->getEntityValues($entity, $params["dirty"]);

                // Prepare the query to be executed.
                $stmt = $this->getConnection()->prepare($query);

                // Execution of the query.
                $success = $stmt->execute($params);

                if ($success) {
                    // Success of the execution

                    if ($ownTransaction)
                        // If method has to manage its own transaction, it commits now.
                        $this->getConnection()->commit();

                    // Recuperation saved data.
                    $object = $this->getSearch(["search" => ["id" => $entity->getId()]])[0];

                    // If object is the saved entity, returns object. If not, returns false.
                    return ($object->getId() === $entity->getId()) ? $object : false;
                } else {

                    if ($ownTransaction)
                        // If method has to manage its own transaction, it rollbacks now.
                        $this->getConnection()->rollBack();
                }
            }
        }

        return false;
    }

    /**
     * Method to logically delete an entity.
     * 
     * @param \entity\Basic $entity Entity to delete
     * @param bool $ownTransaction State if method use own transaction. Default to true.
     * @return bool State if entity is deleted or not.
     */
    public function delete(Basic $entity, bool $ownTransaction = true): bool
    {
        if ($entity->getId() !== 0 && !$entity->isDeleted()) {
            $params["dirty"] = ["suppression_date"];
            $entity->setSuppressionDate(new DateTime("now", new DateTimeZone("Europe/Paris")));
            if ($ownTransaction)
                $this->getConnection()->beginTransaction();

            $success = $this->update($entity, $params, false);

            if ($success) {
                if ($ownTransaction)
                    $this->getConnection()->commit();

                return true;
            } else {
                if ($ownTransaction)
                    $this->getConnection()->rollBack();
            }
        }
        return false;

    }

    /**
     * Method to logically restore an entity.
     * 
     * @param \entity\Entity $entity Entity to restore.
     * @param bool $ownTransaction State if method use own transaction. Default to true.
     * @return bool State if entity is restored or not.
     */
    public function restore(Entity $entity, bool $ownTransaction = true): bool
    {
        if ($entity->getId() !== 0 && $entity->isDeleted()) {
            $params["dirty"] = ["suppression_date"];
            $entity->setSuppressionDate(null);
            if ($ownTransaction)
                $this->getConnection()->beginTransaction();

            $success = $this->update($entity, $params, false);

            if ($success) {
                if ($ownTransaction)
                    $this->getConnection()->commit();

                return true;
            } else {
                if ($ownTransaction)
                    $this->getConnection()->rollBack();
            }
        }
        return false;
    }
}