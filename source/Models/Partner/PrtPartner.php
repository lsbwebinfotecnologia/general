<?php

namespace Source\Models\Partner;

use Source\Core\Model;

class PrtPartner extends Model
{
    public function __construct()
    {
        parent::__construct("prt_partners", ["id"], ["display_name","idCompany"]);
    }

    public function types(): array
    {
        $links = (new PrtPartnerTypeLink())->find("idPartner=:p", "p={$this->id}")
            ->fetch(true) ?? [];
        if (!$links) return [];
        $typeIds = implode(",", array_map(fn($l) => (int)$l->idType, $links));
        return (new PrtPartnerType())->find("id IN ({$typeIds})")->fetch(true) ?? [];
    }
}