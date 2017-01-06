<?php
/**
 * Abstract Controller
 *
 * @version v0.0.1 (Dec. 21, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

namespace Brv\core\routing;

use Brv\core\routing\ControllerException;

/**
 * The collective Controllers are invoked by the router and act
 * as the main logic layer. A controller processes input in some form and
 * outputs a View to be sent to the client.
 */
abstract class Controller
{

    /**
     * Halts execution of the controller and passes an error to the router.
     *
     * This method provides a generic way to return an error, useful when
     * the error is not a result of poorly formatted user input. In the case of
     * poorly formatted user input, use
     * {@link [https://github.com/Respect/Validation] [Respect\Validation\Validator]}.
     *
     * @see \HTTP::* for possible values for $code.
     *
     * @param  string $message The error message.
     * @param  integer $code An HTTP Code to use in sending the error message to the client.
     * @throws ControllerException to halt execution and "pseudo-return" to the router.
     */
    protected static function fail($message, $code = \HTTP::BAD_REQUEST)
    {
        throw new ControllerException($message, $code);
    }

    /**
     * Parsing and returns the appropriate (user) input based on the supplied
     * ContentType header.
     *
     * @return array The parsed user input or an empty array on error.
     */
    public static function getBody()
    {
        $contentType = self::from('CONTENT_TYPE', $_SERVER, false);

        /* A content type must be defined if content is supplied by client. */
        if ($contentType === false) {
            return [];
        }

        $contentType = strtolower(explode(';', $contentType)[0]);
        switch ($contentType) {
            case 'application/json':
                /* PHP does not have native support for JSON. Must extract from IO stream. */
                return json_decode(file_get_contents('php://input'), true);
            case 'multipart/form-data':
            case 'application/x-www-form-urlencoded':
                return $_POST;
            case 'text/plain':
                return ['data' => file_get_contents('php://input')];
            default:
                /* Unrecognized content type. */
                return [];
        }
    }

    /**
     * Gets the value corresponding to a key in an array, with a default fallback
     * if the key does not exist.
     *
     * @param  string|integer $prop  The key in the array.
     * @param  array $array The array containing the value.
     * @param  mixed $blank A default value if the key does not exist.
     * @return mixed
     */
    public static function from($prop, array $array, $blank = null)
    {
        if (isset($array[$prop])) {
            return $array[$prop];
        } else {
            return $blank;
        }
    }
}
