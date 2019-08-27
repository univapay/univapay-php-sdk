<?php

namespace Univapay\Resources\Authentication;

use Univapay\Resources\Jsonable;
use Univapay\Utility\Json\JsonSchema;

class MerchantAppJWT extends AppJWT
{
    use Jsonable;

    public $sub;
    public $issuedAt;
    public $merchantId;
    public $creatorId;
    public $version;
    public $jti;

    public function __construct(
        $sub,
        $iat,
        $merchantId,
        $creatorId,
        $version,
        $jti,
        $token,
        $secret
    ) {
        if ($sub != 'app_token') {
            throw new InvalidJWTFormat('Invalid subject');
        }
        parent::__construct($token, $secret);
        $this->iat = $iat;
        $this->merchantId = $merchantId;
        $this->creatorId = $creatorId;
        $this->version = $version;
        $this->jti = $jti;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(MerchantAppJWT::class, true, false);
    }
}
