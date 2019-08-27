<?php

namespace Univapay\Utility;

use InvalidArgumentException;
use Throwable;

trait OptionsValidator
{
    protected function validate($opts, $rules)
    {
        // Workaround for PHP 5.x
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            if (E_RECOVERABLE_ERROR===$errno) {
                throw new InvalidArgumentException();
            }
                return false;
        });
        
        $validated = [];
        foreach ($opts as $key => $value) {
            if (array_key_exists($key, $rules)) {
                $transformFn = __NAMESPACE__ . '\\' .$rules[$key];

                try {
                    $validated[$key] = call_user_func($transformFn, $value);
                } catch (Throwable $e) { // Catches PHP 7 TypeError
                    throw new InvalidArgumentException(
                        "The value of [$key] is not of expected type"
                    );
                }
            } else {
                $validated[$key] = $value;
            }
        }
        return $validated;
    }
}
