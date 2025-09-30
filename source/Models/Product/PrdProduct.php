<?php

namespace Source\Models\Product;

use Source\Core\TenantModel;

class PrdProduct extends TenantModel
{
    public function __construct()
    {
        parent::__construct("prd_products", ["name","sku","price","idCompany"]);
    }
}