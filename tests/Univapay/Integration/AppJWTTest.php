<?php
namespace UnivapayTest\Integration;

use Univapay\Resources\Authentication\InvalidJWTFormat;
use Univapay\Resources\Authentication\AppJWT;
use Univapay\Resources\Authentication\MerchantAppJWT;
use Univapay\Resources\Authentication\StoreAppJWT;
use PHPUnit\Framework\TestCase;

class AppJWTTest extends TestCase
{
    use IntegrationSuite;

    public function testAppJWTParse()
    {
        // phpcs:disable
        $merchantJWT = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJhcHBfdG9rZW4iLCJpYXQiOjE1MjU2NjYzMTUsIm1lcmNoYW50X2lkIjoiMTFlODUxYWMtYjY1OS03ZGE4LThkYzgtYmJhYzFkYmViMGNlIiwiY3JlYXRvcl9pZCI6IjExZTg1MWFjLWI2NTktN2RhOC04ZGM4LWJiYWMxZGJlYjBjZSIsInZlcnNpb24iOjEsImp0aSI6IjExZTg1MWFjLWM3ODAtNTBiZS1iMmVmLWFiNTZjZjMwMDljNCJ9.UkqZCcn6mMDfA0L_URE2y3o6s-PJ1D2t3ItvCMm9UYE';
        $storeJWT = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJhcHBfdG9rZW4iLCJpYXQiOjE1MjU2NjgwNTQsIm1lcmNoYW50X2lkIjoiMTFlODUxYWMtYjY1OS03ZGE4LThkYzgtYmJhYzFkYmViMGNlIiwic3RvcmVfaWQiOiIxMWU4NTFhYy1iNjZlLTRmMTItOGRjOC04M2E4YWU3YzhiYzYiLCJkb21haW5zIjpbIioiXSwibW9kZSI6InRlc3QiLCJjcmVhdG9yX2lkIjoiMTFlODUxYWMtYjY1OS03ZGE4LThkYzgtYmJhYzFkYmViMGNlIiwidmVyc2lvbiI6MSwianRpIjoiMTFlODUxYjAtZDM4YS1jMTgwLWIyZWYtNDc1NzliYTU2M2I4In0.GV_f6CSokNpRcQQjLw6wsJCxmQECgXBVl6BowUyMj_g';
        // phpcs:enable

        $this->assertInstanceOf(MerchantAppJWT::class, AppJWT::createToken($merchantJWT, ""));
        $this->assertInstanceOf(StoreAppJWT::class, AppJWT::createToken($storeJWT, ""));
    }

    public function testInvalidJWTExceptionForWrongSubject()
    {
        // phpcs:disable
        $erroneousJWT = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJsb2dpbl90b2tlbiIsImV4cCI6MCwiaWF0IjowLCJqdGkiOiIxMTExMTExMS0xMTExLTExMTEtMTExMS0xMTExMTExMTExMTEiLCJtZXJjaGFudF9pZCI6IjExMTExMTExLTExMTEtMTExMS0xMTExLTExMTExMTExMTExMSIsIm5hbWUiOiJhYmNkZWZnIiwiZW1haWwiOiJhYmNkZWZnQGFiY2RlZmcuaW50IiwibGFuZyI6ImphIiwiaXBfYWRkcmVzcyI6IjA6MDowOjA6MDowOjA6MSJ9.BDN2Q3vSvO2I14_gBg9WshiT-gzafCAO9okL2idYOgk';
        // phpcs:enable

        $this->expectException(InvalidJWTFormat::class);
        AppJWT::createToken($erroneousJWT, "");
    }

    public function testInvalidJWTExceptionForInvalidFormat()
    {
        // phpcs:disable
        $erroneousJWT = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJyYW5kb20iLCJleHAiOjAsImlhdCI6MCwianRpIjoiMTExMTExMTEtMTExMS0xMTExLTExMTEtMTExMTExMTExMTExIiwibWVyY2hhbnRfaWQiOiIxMTExMTExMS0xMTExLTExMTEtMTExMS0xMTExMTExMTExMTEifQ.TW_D4As74AHdSyTkfCADG4S1THITIZdKcdaM0QYjemw';
        // phpcs:enable

        $this->expectException(InvalidJWTFormat::class);
        AppJWT::createToken($erroneousJWT, "");
    }

    public function testInvalidJWTExceptionForInvalidJWT()
    {
        $erroneousJWT = 'abcdefg.abcdefg.abcdefg';

        $this->expectException(InvalidJWTFormat::class);
        AppJWT::createToken($erroneousJWT, "");
    }

    public function testInvalidJWTExceptionForRandom()
    {
        $erroneousJWT = 'abcdefgabcdefgabcdefg';

        $this->expectException(InvalidJWTFormat::class);
        AppJWT::createToken($erroneousJWT, "");
    }
}
