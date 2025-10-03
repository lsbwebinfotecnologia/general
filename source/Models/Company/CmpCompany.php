<?php

namespace Source\Models\Company;

use Source\Core\Model;

class CmpCompany extends Model
{
    protected bool $tenantScoped = false;
    public function __construct()
    {
        parent::__construct("cmp_companies", ["id"], ["name"]);
    }
}