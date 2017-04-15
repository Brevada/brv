<?php
/**
 * HTTP | Constants
 *
 * @version v0.0.1 (Dec. 21, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

/**
 * HTTP Status Codes
 *
 * @category Constants
 */
class HTTP
{
    /** @var integer HTTP Code 200: OK */
    const OK = 200;

    /** @var integer HTTP Code 201: Created */
    const CREATED = 201;

    /** @var integer HTTP Code 204: No Content */
    const NO_CONTENT = 204;

    /** @var integer HTTP Code 400: Bad Request */
    const BAD_REQUEST = 400;

    /** @var integer HTTP Code 401: Unauthorized */
    const UNAUTHORIZED = 401;

    /** @var integer HTTP Code 403: Forbidden */
    const LOGIN_REQUIRED = 403;

    /** @var integer Alias of HTTP Code 403: Forbidden */
    const FORBIDDEN = 403;

    /** @var integer HTTP Code 404: Not Found */
    const NOT_FOUND = 404;

    /** @var integer HTTP Code 422: Unprocessable Entity */
    const BAD_PARAMS = 422;

    /** @var integer HTTP Code 429: Too Many Requests */
    const TOO_MANY_REQUESTS = 429;

    /** @var integer HTTP Code 500: Internal Server Error */
    const SERVER = 500;
}
