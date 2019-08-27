<?php

namespace Univapay\Requests;

interface Requester
{

    public function get($url, $query = [], array $headers = []);

    public function post($url, $payload = [], array $headers = []);

    public function patch($url, $payload = [], array $headers = []);

    public function delete($url, array $headers = []);
}
