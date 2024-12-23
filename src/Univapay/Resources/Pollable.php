<?php

namespace Univapay\Resources;

use Univapay\Utility\RequesterUtils;

trait Pollable
{
    abstract protected function getIdContext();

    // Map of [currentStatus => array(validStatusesToTransition)]
    abstract protected function pollableStatuses();

    public function awaitResult($retry = 0)
    {
        $idContext = $this->getIdContext();
        $pollableStatuses = $this->pollableStatuses();
        $response = RequesterUtils::executeGet(self::class, $idContext, ['polling' => 'true']);
        $retryCount = 0;
        while ($retryCount < $retry &&
            array_key_exists($this->status->__toString(), $pollableStatuses) &&
            !in_array($response->status, $pollableStatuses[$this->status->__toString()])
        ) {
            $retryCount++;
            $response = RequesterUtils::executeGet(self::class, $idContext, ['polling' => 'true']);
        }
        return $response;
    }
}
