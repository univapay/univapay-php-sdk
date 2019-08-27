<?php

namespace Univapay\Errors;

class UnivapayRequestError extends UnivapayError
{
    public $url;
    public $status;
    public $code;
    public $errors;

    public function __construct($url, $status, $code, $errors, $httpStatus = 400)
    {
        $this->url = $url;
        $this->status = $status;
        $this->code = $code;
        $this->errors = $errors;
        parent::__construct(print_r([
            'url' => $url,
            'http_status' => $httpStatus,
            'status' => $status,
            'code' => $code,
            'errors' => $errors
        ], true));
    }

    public static function fromJson($url, $json)
    {
        return new UnivapayRequestError(
            $url,
            $json['status'],
            $json['code'],
            $json['errors']
        );
    }
}
