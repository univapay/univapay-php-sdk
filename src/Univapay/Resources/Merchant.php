<?php

namespace Univapay\Resources;

use Univapay\Requests\RequestContext;
use Univapay\Resources\Configuration\Configuration;
use Univapay\Utility\Json\JsonSchema;

class Merchant extends Resource
{

    use Jsonable;
    public $verificationDataId;
    public $name;
    public $email;
    public $verified;
    public $configuration;
    public $createdOn;

    public function __construct(
        $id,
        $verificationDataId,
        $name,
        $email,
        $verified,
        $configuration,
        $createdOn,
        $context = null
    ) {
        parent::__construct($id, $context);
        $this->verificationDataId = $verificationDataId;
        $this->name = $name;
        $this->email = $email;
        $this->verified = $verified;
        $this->configuration = $configuration;
        $this->createdOn = date_create($createdOn);
    }

    public static function fromJson(array $json, RequestContext $requestContext)
    {
        return new Merchant(
            $json['id'],
            $json['verification_data_id'],
            $json['name'],
            $json['email'],
            $json['verified'],
            Configuration::fromJson(fp::getOrElse($json, 'configuration', [])),
            $json['created_on'],
            $context = $requestContext
        );
    }

    protected static function initSchema()
    {
        return JsonSchema::fromClass(self::class)
            ->upsert('configuration', true, Configuration::getSchema()->getParser());
    }
}
