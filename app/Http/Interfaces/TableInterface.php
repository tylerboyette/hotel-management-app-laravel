<?php

namespace App\Http\Interfaces;

interface TableInterface
{
    public function getRouteName();

    public function getColumns();
}