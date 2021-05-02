<?php

namespace Carloosolrac\Metricool;

use Illuminate\Support\Facades\Http;

class Metricool
{
    private $token;
    private $user;
    private $base;
    private $url;
    private $fields = [];

    public function __construct()
    {
        $this->token = config('laravel-metricool.token');
        $this->user = config('laravel-metricool.userId');
        $this->base = config('laravel-metricool.baseUrl');
    }

    public function companies()
    {
        return Http::get($this->getUrl('admin/profiles'))->json();
    }

    private function addField($key, $value)
    {
        $this->fields = [...$this->fields, $key => $value];
    }

    private function getUrl($path = null)
    {
        $query = http_build_query([
            ...$this->fields,
            'userId' => $this->user,
            'userToken' => $this->token
        ]);

        return $this->base . $path . '?' . $query;
    }
}
