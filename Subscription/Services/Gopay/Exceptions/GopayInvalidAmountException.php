<?php

namespace Services\Gopay\Exceptions;

use Exception;

class GopayInvalidAmountException extends Exception
{
    public function __construct($message = "Invalid amount", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
