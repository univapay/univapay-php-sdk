<?php

namespace Univapay\Resources\Authentication;

use Univapay\Enums\AppTokenMode;
use Univapay\Resources\Jsonable;
use Univapay\Utility\Json\JsonSchema;

class StoreAppJWT extends AppJWT
{
    use Jsonable;

    public $sub;
    public $iat;
    public $merchantId;
    public $storeId;
    public $domains;
    public $mode;
    public $creatorId;
    public $version;
    public $jti;

    public function __construct(
        $sub,
        $iat,
        $merchantId,
        $storeId,
        $domains,
        $mode,
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
        $this->storeId = $storeId;
        $this->domains = $domains;
        $this->mode = AppTokenMode::fromValue($mode);
        $this->creatorId = $creatorId;
        $this->version = $version;
        $this->jti = $jti;
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(StoreAppJWT::class, true, false);
    }
}
