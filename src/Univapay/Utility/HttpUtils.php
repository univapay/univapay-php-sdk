<?php

namespace Univapay\Utility;

use Univapay\Errors\UnivapayForbiddenError;
use Univapay\Errors\UnivapayRateLimitedError;
use Univapay\Errors\UnivapayRequestError;
use Univapay\Errors\UnivapayResourceConflictError;
use Univapay\Errors\UnivapayNotFoundError;
use Univapay\Errors\UnivapayServerError;
use Univapay\Errors\UnivapayUnauthorizedError;
use WpOrg\Requests\Response;

const BAD_REQUEST = 400;

const UNAUTHORIZED = 401;

const FORBIDDEN = 403;

const NOT_FOUND = 404;

const TOO_MANY_REQUESTS = 429;

const INTERNAL_SERVER_ERROR = 500;

const CONFLICT = 409;

abstract class HttpUtils
{
    public static function getQueryString(array $params)
    {
        if (is_array($params) && sizeof($params) > 0) {
            return '?' . http_build_query($params);
        } else {
            return '?';
        }
    }

    public static function checkResponse(Response $response)
    {
        switch ($response->status_code) {
            case BAD_REQUEST:
                throw UnivapayRequestError::fromJson($response->url, json_decode($response->body, true));

            case UNAUTHORIZED:
                throw new UnivapayUnauthorizedError($response->url, json_decode($response->body, true));

            case FORBIDDEN:
                throw new UnivapayForbiddenError($response->url, json_decode($response->body, true));

            case NOT_FOUND:
                throw new UnivapayNotFoundError($response->url);

            case CONFLICT:
                throw new UnivapayResourceConflictError($response->url, json_decode($response->body, true));

            case TOO_MANY_REQUESTS:
                throw new UnivapayRateLimitedError($response->url);

            default:
                if ($response->status_code >= 200 && $response->status_code < 300) {
                    if ($response->body) {
                        return json_decode($response->body, true);
                    } else {
                        return true;
                    }
                } else {
                    throw new UnivapayServerError($response->status_code, $response->url);
                }
        }
    }

    public static function getJsonHeader()
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];
    }

    public static function getIdempotencyHeader()
    {
        return ['idempotency-key' => uniqid('', true)];
    }
}
