<?php

namespace Source\Models\Partner;

use Source\Core\Model;

class PrtPartnerType extends Model
{
    public function __construct()
    {
        parent::__construct("prt_partner_types", ["id"], ["slug","name","idCompany"]);
    }
}