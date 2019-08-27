<?php

namespace Univapay\Utility;

use Univapay\Requests\RequestContext;
use Univapay\Requests\Requester;
use Univapay\Resources\Paginated;
use Univapay\Resources\SimpleList;

abstract class RequesterUtils
{
    public static function getHeaders(RequestContext $requestContext, array $headers = [])
    {
        return array_merge(
            HttpUtils::addJsonHeader($requestContext->getAuthorizationHeaders()),
            $headers
        );
    }

    public static function executeGet($parser, RequestContext $requestContext, $query = [])
    {
        $response = $requestContext->getRequester()->get(
            $requestContext->getFullURL(),
            $query,
            self::getHeaders($requestContext)
        );
        return $parser::getSchema()->parse($response, [$requestContext]);
    }

    public static function executeGetPaginated($parser, RequestContext $context, $query = [])
    {
        $response = $context->getRequester()->get($context->getFullURL(), $query, self::getHeaders($context));
        return Paginated::fromResponse($response, $query, $parser, $context);
    }

    public static function executePost(
        $parser,
        RequestContext $requestContext,
        $payload = []
    ) {
        $response = $requestContext->getRequester()->post(
            $requestContext->getFullURL(),
            $payload,
            self::getHeaders($requestContext)
        );
        if (is_null($parser)) {
            return $response;
        } else {
            return $parser::getSchema()->parse($response, [$requestContext]);
        }
    }

    public static function executePostSimpleList(
        $parser,
        RequestContext $requestContext,
        $payload = []
    ) {
        $response = $requestContext->getRequester()->post(
            $requestContext->getFullURL(),
            $payload,
            self::getHeaders($requestContext)
        );
        return SimpleList::fromResponse($response, $parser, $requestContext);
    }

    public static function executePatch(
        $parser,
        RequestContext $requestContext,
        $payload = []
    ) {
        $response = $requestContext->getRequester()->patch(
            $requestContext->getFullURL(),
            $payload,
            self::getHeaders($requestContext)
        );
        return $parser::getSchema()->parse($response, [$requestContext]);
    }

    public static function executeDelete(RequestContext $requestContext)
    {
        return $requestContext->getRequester()->delete(
            $requestContext->getFullURL(),
            self::getHeaders($requestContext)
        );
    }
}
