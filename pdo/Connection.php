<?php

namespace pdo;

use entity\Entity;
use Exception;
use PDO;

/**
 * Connection class.
 * 
 * All properties & method to manage a database connection.
 * 
 * @property PDO $pdo
 * 
 * @method Connection __construct(string $db, string $user, string $password)
 * @method PDO getConnection()
 * @method void suppressionSecurity(Entity $entity)
 */
class Connection
{

    // Php Database object of the connection.
    private PDO $pdo;

    /**
     * Constructor.
     *
     * @param string $db The database name.
     * @param string $user The user name.
     * @param string $password The user password.
     */
    public function __construct(string $db)
    {
        try {
            // Creation of the php database object.
            $this->pdo = new PDO(
                'mysql:host=localhost;dbname=' . $db . ';charset=utf8',
                "root", 
                "Sen5652466*",
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (Exception $e) {
            // One exception was catched, process die and show error.
            die("PDO Error : " . $e->getMessage());
        }
    }

    /**
     * Returns the php database object.
     *
     * @return PDO Php Database Object.
     */
    protected function getConnection(): PDO
    {
        return $this->pdo;
    }

    /**
     * Method to secure the suppression date is not setted in the wrong method in children.
     *
     * @param Entity $entity The entity to enter in the database.
     * @return void
     */
    protected function suppressionSecurity(Entity $entity): void
    {
        if ($entity->isDeleted()) {
            // If entity has suppression date, send http code for 403 Forbidden
            http_response_code(403);
            exit;
        }
    }

    public function setConnection(PDO $pdo): void
    {
        $this->pdo = $pdo;
    }
}
