<?php

namespace Univapay\Resources\Authentication;

use Exception;
use Univapay\Utility\Json\JsonSchema;

abstract class AppJWT
{
    public $token;
    public $secret;

    protected function __construct($token, $secret)
    {
        $this->token = $token;
        $this->secret = $secret;
    }

    public static function createToken($appToken, $appSecret)
    {
        try {
            $tokenBody = base64_decode(explode('.', $appToken)[1]);
        } catch (Exception $e) {
            throw new InvalidJWTFormat($appToken);
        }
        $appTokenBody = json_decode($tokenBody, true);

        if ($appTokenBody == null) {
            throw new InvalidJWTFormat('JWT body is not JSON');
        }
        
        if (array_key_exists('store_id', $appTokenBody)) {
            $class = StoreAppJWT::class;
        } else {
            $class = MerchantAppJWT::class;
        }
        $result = $class::getSchema()->parse($appTokenBody, [$appToken, $appSecret]);
        return $result;
    }
}
