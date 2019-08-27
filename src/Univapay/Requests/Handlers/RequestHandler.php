<?php

namespace Univapay\Requests\Handlers;

use Closure;

interface RequestHandler
{
    /**
     * Handles the pre and post of a request. To execute the chain, pass the $requestData as
     * as the first parameter into $request. Always return the result from $request.
     * @param Closure $request The request chain. Execute it by passing the $requestData as the first parameter
     * @param array $requestData A tuple of the following data in order
     * list(string $url, array $headers, (array|Object)? $payload).
     * When modifying the $requestData, always reconstruct it as an array before passing it into the $request.
     * Modifying the $payload is not recommended as it results in side effects that are hard to track.
     * @return array $jsonArray The array representation of the JSON response.
     */
    public function handle(Closure $request, array $requestData);
}
