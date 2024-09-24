<?php

namespace Univapay\Requests;

use Univapay\Resources\Authentication\AppJWT;

class RequestContext
{
    private $path;
    private $endpoint;
    private $appJWT;
    private $requester;
    private $headers;

    public function __construct($requester, $endpoint, $path, AppJWT $appJWT, $headers = [])
    {
        $this->requester = $requester;
        $this->path = $path;
        $this->endpoint = $endpoint;
        $this->appJWT = $appJWT;
        $this->headers = $headers;
    }

    public function getRequester()
    {
        return $this->requester;
    }

    public function withAppToken($appJWT)
    {
        return new RequestContext($this->requester, $this->endpoint, $this->path, $appJWT, $this->headers);
    }

    public function withPath($path)
    {
        $newPath = is_array($path) ? join('/', $path) : $path;
        return new RequestContext($this->requester, $this->endpoint, $newPath, $this->appJWT, $this->headers);
    }

    public function appendPath($path)
    {
        if (is_array($path)) {
            return $this->withPath($this->path . '/' . join('/', $path));
        } elseif (is_string($path)) {
            return $this->withPath("{$this->path}/$path");
        } else {
            return $this;
        }
    }

    public function getAuthorizationHeaders()
    {
        if ($this->appJWT == null) {
            return [];
        } else {
            $key = $this->appJWT->token;
            $secret = $this->appJWT->secret;
            $secretText = $secret ? $secret . '.' : '';
            return ['Authorization' => "Bearer $secretText$key"];
        }
    }

    public function getConfigHeaders()
    {
        return $this->headers;
    }

    public function getFullURL()
    {
        return trim($this->endpoint, '/') . '/' . trim($this->path, '/');
    }
}
