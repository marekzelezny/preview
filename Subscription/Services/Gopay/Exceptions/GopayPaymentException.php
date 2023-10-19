<?php

namespace Services\Gopay\Exceptions;

use Exception;
use Throwable;

class GopayPaymentException extends \Exception
{
    public string $scope;
    public int $error_code;
    public string $error_name;

    public static function fromResponse(array $response): self
    {
        $exception = new self(
            message: self::formatMessage($response),
            code: $response['error_code'],
        );

        $exception->scope = $response['scope'];
        $exception->error_code = $response['error_code'];
        $exception->error_name = $response['error_name'];

        return $exception;
    }

    public static function formatMessage(array $response): string
    {
        return match($response['scope']) {
            'F' => self::formatMessageForScopeF($response),
            'G' => self::formatMessageForScopeG($response),
        };
    }

    public static function formatMessageForScopeF($response)
    {
        return "Gopay ERROR {$response['error_code']}: Field {$response['field']} - {$response['message']}";
    }

    public static function formatMessageForScopeG($response)
    {
        return "Gopay ERROR GLOBAL: {$response['message']}";
    }
}
