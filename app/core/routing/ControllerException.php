<?php
/**
 * Controller Exception | Exception
 *
 * @version v0.0.1 (Dec. 21, 2016)
 * @copyright Copyright (c) 2016, Brevada
 */

namespace Brv\core\routing;

/**
 * Indicates an error processing the business logic of the
 * {@link [Brv\core\routing\Controller] [Controller]}.
 */
class ControllerException extends \Exception
{
    public function __construct($message, $code, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
