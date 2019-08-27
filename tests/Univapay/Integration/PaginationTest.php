<?php
namespace UnivapayTest\Integration;

use Univapay\Enums\CursorDirection;
use Univapay\Enums\RefundReason;
use Univapay\Errors\UnivapayRequestError;
use Univapay\Errors\UnivapayNoMoreItemsError;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

/**
 * @backupGlobals disabled
 */
class PaginationTest extends TestCase
{
    use IntegrationSuite;

    protected function setUp()
    {
        if (isset($GLOBALS['testPagination:setupComplete']) &&
            !$GLOBALS['testPagination:setupComplete']) {
            $this->markTestSkipped('The charge setup for pagination testing has not been indexed properly');
        }
    }

    public function testSetUpPagination()
    {
        sleep(5); // Prevent interference of delayed charges from previous suites
        $GLOBALS['testPagination:limit'] = 3;
        $GLOBALS['testPagination:charges'] = [];
        $GLOBALS['testPagination:from'] = date_create();
        sleep(1); // Pause to to let time elapse to ensure timestamp is in the past
        
        foreach (range(1, 6) as $i) {
            $charge = $this->createValidCharge();
            $GLOBALS['testPagination:charges'][] = $charge->id;
        }
        
        // Give it some time for the charges to get indexed
        $maxRetries = 20;
        do {
            $listCharge = $this->getClient()->listChargesByOptions([
                'from' => $GLOBALS['testPagination:from']
            ]);
            $maxRetries--;
            sleep(1);
        } while ($maxRetries > 0 && count($listCharge->items) !== 6);
        
        $GLOBALS['testPagination:setupComplete'] = $maxRetries > 0;
        $this->assertTrue(
            $GLOBALS['testPagination:setupComplete'],
            'The charge setup for pagination testing has not been indexed properly'
        );
    }

    /**
     * @depends testSetUpPagination
     */
    public function testDefaultOrder()
    {
        $listCharge = $this->getClient()->listChargesByOptions([
            'from' => $GLOBALS['testPagination:from']
        ]);

        $this->assertEquals(array_reverse($GLOBALS['testPagination:charges']), $this->mapToId($listCharge->items));
        $this->assertFalse($listCharge->hasMore);
    }

    /**
     * @depends testSetUpPagination
     */
    public function testLimitSetting()
    {
        $listCharge = $this->getClient()->listChargesByOptions([
            'from' => $GLOBALS['testPagination:from'],
            'limit' => $GLOBALS['testPagination:limit']
        ]);

        $this->assertEquals(
            array_reverse(array_slice($GLOBALS['testPagination:charges'], -3)),
            $this->mapToId($listCharge->items)
        );
        $this->assertTrue($listCharge->hasMore);
    }

    /**
     * @depends testSetUpPagination
     */
    public function testCursorSetting()
    {
        $listCharge = $this->getClient()->listChargesByOptions([
            'cursor' => $GLOBALS['testPagination:charges'][1],
            'from' => $GLOBALS['testPagination:from'],
            'limit' => $GLOBALS['testPagination:limit']
        ]);

        $this->assertEquals(array_slice($GLOBALS['testPagination:charges'], 0, 1), $this->mapToId($listCharge->items));
        $this->assertFalse($listCharge->hasMore);
    }

    /**
     * @depends testSetUpPagination
     */
    public function testCursorDirection()
    {
        $listCharge = $this->getClient()->listChargesByOptions([
            'cursor' => $GLOBALS['testPagination:charges'][1],
            'from' => $GLOBALS['testPagination:from'],
            'limit' => $GLOBALS['testPagination:limit'],
            'cursor_direction' => CursorDirection::ASC()
        ]);

        $this->assertEquals(array_slice($GLOBALS['testPagination:charges'], 2, 3), $this->mapToId($listCharge->items));
        $this->assertTrue($listCharge->hasMore);
    }

    /**
     * @depends testSetUpPagination
     */
    public function testPagingForward()
    {
        $listCharge = $this->getClient()->listChargesByOptions([
            'from' => $GLOBALS['testPagination:from'],
            'limit' => $GLOBALS['testPagination:limit'],
            'cursor_direction' => CursorDirection::ASC()
        ]);
        $this->assertEquals(array_slice($GLOBALS['testPagination:charges'], 0, 3), $this->mapToId($listCharge->items));
        $this->assertTrue($listCharge->hasMore);

        $listCharge = $listCharge->getNext();
        $this->assertEquals(array_slice($GLOBALS['testPagination:charges'], 3), $this->mapToId($listCharge->items));
        $this->assertFalse($listCharge->hasMore);

        $this->expectException(UnivapayNoMoreItemsError::class);
        $listCharge->getNext();
    }

    /**
     * @depends testSetUpPagination
     */
    public function testPagingBackwards()
    {
        $listCharge = $this->getClient()->listChargesByOptions([
            'from' => $GLOBALS['testPagination:from'],
            'limit' => $GLOBALS['testPagination:limit'],
            'cursor' => $GLOBALS['testPagination:charges'][2],
            'cursor_direction' => CursorDirection::ASC()
        ]);
        $this->assertEquals(array_slice($GLOBALS['testPagination:charges'], 3), $this->mapToId($listCharge->items));
        $this->assertFalse($listCharge->hasMore);

        $listCharge = $listCharge->getPrevious();
        $this->assertEquals(array_slice($GLOBALS['testPagination:charges'], 0, 3), $this->mapToId($listCharge->items));
        $this->assertTrue($listCharge->hasMore);

        $this->expectException(UnivapayNoMoreItemsError::class);
        $listCharge->getPrevious();
    }

    /**
     * @depends testSetUpPagination
     */
    public function testPaging()
    {
        $listCharge = $this->getClient()->listChargesByOptions([
            'from' => $GLOBALS['testPagination:from'],
            'limit' => $GLOBALS['testPagination:limit'],
            'cursor' => $GLOBALS['testPagination:charges'][0],
            'cursor_direction' => CursorDirection::ASC()
        ]);
        $this->assertEquals(array_slice($GLOBALS['testPagination:charges'], 1, 3), $this->mapToId($listCharge->items));
        $this->assertTrue($listCharge->hasMore);

        $listCharge = $listCharge->getPrevious();
        $this->assertEquals(array_slice($GLOBALS['testPagination:charges'], 0, 1), $this->mapToId($listCharge->items));
        $this->assertTrue($listCharge->hasMore); // $hasMore is in relation to forward paging and not backwards

        $listCharge = $listCharge->getNext();
        $this->assertEquals(array_slice($GLOBALS['testPagination:charges'], 1, 3), $this->mapToId($listCharge->items));
        $this->assertTrue($listCharge->hasMore);

        $listCharge = $listCharge->getNext();
        $this->assertEquals(array_slice($GLOBALS['testPagination:charges'], 4), $this->mapToId($listCharge->items));
        $this->assertFalse($listCharge->hasMore);

        $listCharge = $listCharge->getPrevious();
        $this->assertEquals(array_slice($GLOBALS['testPagination:charges'], 1, 3), $this->mapToId($listCharge->items));
        $this->assertTrue($listCharge->hasMore);
    }

    private function mapToId($charges)
    {
        return array_map(function ($charge) {
            return $charge->id;
        }, $charges);
    }
}
