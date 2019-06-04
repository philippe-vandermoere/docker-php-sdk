<?php
/**
 * @author Philippe VANDERMOERE <philippe@wizaplace.com>
 * @copyright Copyright (C) Philippe VANDERMOERE
 * @license MIT
 */

declare(strict_types=1);

namespace PhilippeVandermoere\DockerPhpSdk\Exception;

class JsonException extends \Exception
{
    public function __construct(int $jsonError, int $code = 0, \Throwable $previous = null)
    {
        switch ($jsonError) {
            case JSON_ERROR_DEPTH:
                $message = 'Maximum stack depth exceeded.';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $message = 'Underflow or the modes mismatch.';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $message = 'Unexpected control character found.';
                break;
            case JSON_ERROR_SYNTAX:
                $message = 'Syntax error, malformed JSON.';
                break;
            case JSON_ERROR_UTF8:
                $message = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
                break;
            default:
                $message = 'Unknown error.';
        }

        parent::__construct($message, $code, $previous);
    }
}
