<?php

namespace Univapay\Requests;

use Closure;
use Univapay\Requests\Handlers\RequestHandler;
use Univapay\Utility\HttpUtils;
use WpOrg\Requests\Requests;

class HttpRequester implements Requester
{
    private $handlers = [];

    public function __construct(RequestHandler ...$handlers)
    {
        $this->handlers = $handlers;
    }

    public function get($url, $query = [], array $headers = [])
    {
        return call_user_func($this->encapsulate(function (array $requestData) {
            list($url, $headers, $query) = $requestData;
            if (is_array($query) && sizeof($query) > 0) {
                $url .= HttpUtils::getQueryString($query);
            }
            return HttpUtils::checkResponse(Requests::get($url, $headers));
        }), [$url, $headers, $query]);
    }

    public function post($url, $payload = [], array $headers = [])
    {
        return call_user_func($this->encapsulate(function (array $requestData) {
            list($url, $headers, $payload) = $requestData;
            return HttpUtils::checkResponse(Requests::post($url, $headers, json_encode($payload)));
        }), [$url, $headers, $payload]);
    }

    public function patch($url, $payload = [], array $headers = [])
    {
        return call_user_func($this->encapsulate(function (array $requestData) {
            list($url, $headers, $payload) = $requestData;
            return HttpUtils::checkResponse(Requests::patch($url, $headers, json_encode($payload)));
        }), [$url, $headers, $payload]);
    }

    public function delete($url, array $headers = [])
    {
        return call_user_func($this->encapsulate(function (array $requestData) {
            list($url, $headers) = $requestData;
            return HttpUtils::checkResponse(Requests::delete($url, $headers));
        }), [$url, $headers]);
    }

    public function addHandlers(RequestHandler ...$handlers)
    {
        $this->handlers = array_merge($this->handlers, $handlers);
    }

    public function setHandlers(RequestHandler ...$handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * Encapsulates the actual HTTP request with a cascade of RequestHandlers in order of addition, with
     * the first handler being the last to modify the $requestData and first to modify the response.
     * @param Closure $request The last request to be executed. Usually the http request.
     * @param array $requestData A tuple of the following data in order
     * list(string $url, array $headers, array? $payload).
     * @return Closure $encapsulatedRequest A cascading function that ends with the actual request being
     * executed with the $requestData being passed along.
     */
    private function encapsulate(Closure $request)
    {
        return array_reduce($this->handlers, function (Closure $request, RequestHandler $handler) {
            return function (array $requestData) use ($request, $handler) {
                return $handler->handle($request, $requestData);
            };
        }, $request);
    }
}
