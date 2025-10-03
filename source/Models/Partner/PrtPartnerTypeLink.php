<?php

namespace Source\Models\Partner;

use Source\Core\TenantModel;

class PrtPartnerTypeLink extends TenantModel
{
    public function __construct()
    {
        parent::__construct("prt_partner_types_link", ["idPartner", "idType", "idCompany"]);
    }

}