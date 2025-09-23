<?php

namespace Source\Models\Company;

use Source\Core\Model;

class CmpCompany extends Model
{
    public function __construct()
    {
        parent::__construct("cmp_companies", ["id"], ["name"]);
    }
}