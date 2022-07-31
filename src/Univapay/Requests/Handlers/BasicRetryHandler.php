<?php

namespace Univapay\Requests\Handlers;

use Closure;
use Exception;

class BasicRetryHandler implements RequestHandler
{
    private $exceptionClass;
    private $filter;
    private $tries;
    private $interval;

    public function __construct($exceptionClass, $tries = 3, $interval = 1, Closure $filter = null)
    {
        $this->exceptionClass = $exceptionClass;
        $this->filter = $filter;
        $this->tries = $tries;
        $this->interval = $interval;
    }

    public function handle(Closure $request, array $requestData)
    {
        $retryCount = 0;
        while ($retryCount < $this->tries) {
            try {
                return $request($requestData);
            } catch (Exception $error) {
                if (!$error instanceof $this->exceptionClass) {
                    throw $error;
                }
                if (isset($this->filter) && !$this->filter->__invoke($error)) {
                    throw $error;
                }
                $retryCount++;
                sleep($this->interval);
            }
        }
        return $request($requestData);
    }
}
