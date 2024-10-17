<?php

namespace utils;

use utils\JwtManager;

require_once "../utils/JwtManager.php";

/**
 * Class for static methodes.
 * 
 * @method void methodSecurity(string $authorizedMethod)
 * @method void objectSecurity(string $authorizedObject)
 * @method callable errorSecurity()
 * @method void removeSecurity(array $postData)
 * @method void sendJson(array $data)
 * @method void sendError(string $object)
 * @method void getEntity(string $object)
 * @method void getTable(string $object)
 * @method void dd(object $message)
 */
class Functions
{
    // Variable to show debug message on errors, or not.
    private static $debug = true;

    public static function tokenSecurity()
    {
        if (!array_key_exists("HTTP_JWT", $_SERVER)) {
            http_response_code(401); // Unauthorized
            exit;
        }
    }

    public static function jwtSecurity(string $token)
    {
        $jwtManager = new JwtManager($_SERVER["REMOTE_ADDR"] . "." . $_SERVER["SERVER_ADDR"]);

        if ($jwtManager->validateToken($token)) {
            $payload = $jwtManager->decodeToken($token);
            if (time() > $payload["exp"]) {
                http_response_code(401); // Unauthorized
                exit;
            } else
                return $payload;
        } else {
            http_response_code(401); // Unauthorized
            exit;
        }
    }

    /**
     * Verify if the method of call equals the method arg.
     *
     * @param string $authorizedMethod The method to respect.
     * @return void
     */
    public static function methodSecurity(string $authorizedMethod): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== $authorizedMethod) {
            http_response_code(405); // Method not allowed
            exit;
        }
    }

    /**
     * Verify if object passed as arg is a class of webservice.
     *
     * @param string $authorizedObject The object to verify.
     * @return void
     */
    public static function objectSecurity(string $authorizedObject): void
    {
        $classes = ["fandom", "author", "language", "tag", "character", "relation", "fanfiction", "series", "user"];
        if (!array_search($authorizedObject, $classes)) {
            http_response_code(403); // Forbidden
            exit;
        }
    }

    /**
     * Method to transform a fatal error to an 500 HTTP code.
     *
     * @return callable
     */
    public static function errorSecurity(): callable
    {
        return function () {
            $error = error_get_last();
            if (isset($error)) {
                if ($error['type'] === E_ERROR) {
                    // var_dump($error);
                    // die();
                    http_response_code(500);
                    exit;
                }
            }
        };
    }

    /**
     * Method to format the data to send to caller.
     *
     * @param array $data The data to format.
     * @return void
     */
    public static function sendJson(array $data): void
    {
        http_response_code(200);

        header("Content-type: application/json; charset=utf-8");

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Method to format the error to send to caller.
     *
     * @param string $message The error message to format.
     * @return void
     */
    public static function sendError(string $message): void
    {
        if (Functions::$debug) {
            http_response_code(200);
            header("Content-type: application/json; charset=utf-8");
            echo json_encode(["message" => $message]);
        } else {
            http_response_code(500);
            exit;
        }
    }

    /**
     * The method to show message.
     *
     * @param object $obj the object to show off.
     * @return void
     */
    public static function dd(array|object $obj)
    {
        header("Content-type: application/json; charset=utf-8");
        echo json_encode(["message" => $obj]);
        die();
    }
}
