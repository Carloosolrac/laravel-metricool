<?php

namespace Carloosolrac\Metricool;

class Metricool
{
    private $token;
    private $user;

    public function __construct()
    {
        $this->token = config('laravel-metricool.token');
        $this->user = config('laravel-metricool.userId');
    }
}
