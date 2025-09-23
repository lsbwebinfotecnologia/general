<?php

namespace Source\Models\Company;

use Source\Core\Model;

class CmpDomain extends Model
{

    public function __construct()
    {
        parent::__construct("cmp_domains", ["id"], ["idCompany", "domain"]);
    }

}