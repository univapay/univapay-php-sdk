<?php

namespace Univapay\Resources;

use Univapay\Errors\UnivapayNoMoreItemsError;
use Univapay\Requests\RequestContext;
use Univapay\Requests\Requester;
use Univapay\Utility\FunctionalUtils as fp;
use Univapay\Utility\RequesterUtils;

function get_other_direction($direction)
{
    if ($direction === 'asc') {
        return 'desc';
    } else {
        return 'asc';
    }
}

class Paginated
{
    public $items;
    public $hasMore;
    private $jsonableClass;
    private $context;
    private $query;

    public function __construct(
        $items,
        $hasMore,
        $query,
        $jsonableClass,
        RequestContext $context
    ) {
        $this->items = $items;
        $this->hasMore = $hasMore;
        $this->jsonableClass = $jsonableClass;
        $this->context = $context;
        $this->query = $query;
    }

    private function parse($json)
    {
        return $this->formatFn($json, $this->context);
    }

    public static function fromResponse(
        $response,
        $query,
        $jsonableClass,
        $context
    ) {
        $parser = $jsonableClass::getContextParser($context);
        return new Paginated(
            array_map($parser, $response['items']),
            $response['has_more'],
            $query,
            $jsonableClass,
            $context
        );
    }

    public function getNext()
    {
        if (!$this->hasMore) {
            throw new UnivapayNoMoreItemsError();
        }
        $last = end($this->items);
        $newQuery = ['cursor' => $last->id] + $this->query;
        return RequesterUtils::executeGetPaginated($this->jsonableClass, $this->context, $newQuery);
    }

    public function getPrevious()
    {
        if (!array_key_exists('cursor', $this->query)) {
            throw new UnivapayNoMoreItemsError();
        }

        $previousPage = $this->reverse()->getNext();
        if (empty($previousPage->items)) {
            throw new UnivapayNoMoreItemsError();
        }
        return $previousPage->reverse();
    }

    private function reverse()
    {
        $currentDirection = fp::getOrElse($this->query, 'cursor_direction', 'desc');
        $newQuery = ['cursor_direction' => get_other_direction($currentDirection)] + $this->query;
        return new Paginated(
            array_reverse($this->items),
            true,
            $newQuery,
            $this->jsonableClass,
            $this->context
        );
    }
}
