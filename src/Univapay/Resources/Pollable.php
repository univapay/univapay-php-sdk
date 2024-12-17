<?php

namespace Univapay\Resources;

use Univapay\Utility\RequesterUtils;

trait Pollable
{
    abstract protected function getIdContext();

    // Map of [currentStatus => array(validStatusesToTransition)]
    abstract protected function pollableStatuses();

    // Returns the property path that contains the status of the object
    // e.g. 'status' for $response->status, 'charge->status' for $response->charge->status
    abstract protected function statusPropertyPath();

    private function getNestedProperty($object, $path)
    {
        $keys = explode('->', $path);
        foreach ($keys as $key) {
            if (is_object($object) && isset($object->$key)) {
                $object = $object->$key;
            } elseif (is_array($object) && isset($object[$key])) {
                $object = $object[$key];
            } else {
                return null; // Property not found
            }
        }
        return $object;
    }

    public function awaitResult($retry = 0)
    {
        $idContext = $this->getIdContext();
        $pollableStatuses = $this->pollableStatuses();
        $response = RequesterUtils::executeGet(self::class, $idContext, ['polling' => 'true']);
        $status = $this->getNestedProperty($this, $this->statusPropertyPath());
        $retryCount = 0;
        while ($retryCount < $retry &&
            array_key_exists($status->__toString(), $pollableStatuses) &&
                !in_array($status, $pollableStatuses[$status->__toString()])
        ) {
            $retryCount++;
            $response = RequesterUtils::executeGet(self::class, $idContext, ['polling' => 'true']);
        }
        return $response;
    }
}
