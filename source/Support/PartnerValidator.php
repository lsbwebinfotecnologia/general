<?php

namespace Source\Support;

use Source\Models\Partner\{PrtPartner, PrtPartnerType};
class PartnerValidator
{
    public static function normalizeDoc(?string $doc): ?string {
        if (!$doc) return null;
        $d = preg_replace('/\D+/', '', $doc);
        return $d ?: null;
    }

    public static function isValidEmail(?string $email): bool {
        return !$email || filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function uniqueCode(?string $code, int $idCompany, ?int $exceptId=null): bool {
        if (!$code) return true;
        $m = new PrtPartner();
        $t = "code=:c AND idCompany=:co";
        $p = "c={$code}&co={$idCompany}";
        if ($exceptId) { $t .= " AND id != :id"; $p .= "&id={$exceptId}"; }
        return $m->find($t,$p)->count() === 0;
    }

    public static function uniqueDoc(?string $doc, int $idCompany, ?int $exceptId=null): bool {
        if (!$doc) return true;
        $m = new PrtPartner();
        $t = "document=:d AND idCompany=:co";
        $p = "d={$doc}&co={$idCompany}";
        if ($exceptId) { $t .= " AND id != :id"; $p .= "&id={$exceptId}"; }
        return $m->find($t,$p)->count() === 0;
    }

    public static function checkTypes(array $ids): array {
        if (!$ids) return [];
        $ids = array_values(array_unique(array_map('intval',$ids)));
        $in  = implode(',', $ids);
        $rows = (new PrtPartnerType())->find("id IN ({$in}) AND active=1")->fetch(true) ?? [];
        $found = array_map(fn($r)=>(int)$r->id, $rows);
        return array_values(array_diff($ids, $found)); // ids inválidos
    }

    public static function validate(array $in, int $idCompany, ?int $partnerId=null): array {
        $err = []; $out = [];

        $out['display_name'] = trim((string)($in['display_name'] ?? ''));
        if (strlen($out['display_name']) < 3) $err['display_name'] = "Nome obrigatório (mín. 3).";

        $out['legal_name']   = trim((string)($in['legal_name'] ?? '')) ?: null;

        $out['code'] = trim((string)($in['code'] ?? '')) ?: null;
        if ($out['code'] && !preg_match('/^[a-z0-9\-_]+$/i', $out['code'])) {
            $err['code'] = "Código deve conter só letras, números, '-' e '_'.";
        } elseif (!self::uniqueCode($out['code'], $idCompany, $partnerId)) {
            $err['code'] = "Código já utilizado.";
        }

        $out['document'] = self::normalizeDoc($in['document'] ?? null);
        if (!self::uniqueDoc($out['document'], $idCompany, $partnerId)) {
            $err['document'] = "Documento já utilizado.";
        }

        $out['email'] = trim((string)($in['email'] ?? '')) ?: null;
        if (!self::isValidEmail($out['email'])) $err['email'] = "E-mail inválido.";

        $out['phone'] = trim((string)($in['phone'] ?? '')) ?: null;

        $out['status'] = (in_array(($in['status'] ?? 'active'), ['active','inactive']) ? $in['status'] : 'active');

        $typeIds = array_map('intval', $in['type_ids'] ?? []);
        $typeIds = array_values(array_unique(array_filter($typeIds)));
        $invalid = self::checkTypes($typeIds);
        if ($invalid) $err['type_ids'] = "Tipos inválidos: ".implode(',',$invalid);
        $out['type_ids'] = $typeIds;

        $out['meta'] = !empty($in['meta']) ? (is_array($in['meta']) ? json_encode($in['meta']) : (string)$in['meta']) : null;

        return [empty($err), $err, $out];
    }
}