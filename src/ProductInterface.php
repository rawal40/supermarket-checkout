<?php

namespace App;

interface ProductInterface
{
    public function getSku() : string;
    public function getPrice() : int;
}
