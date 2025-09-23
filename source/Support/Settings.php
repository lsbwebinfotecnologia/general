<?php

namespace Source\Support;

use Source\Core\Connect;

class Settings
{
    private static array $cache = [];

    public static function load(int $idCompany): array
    {
        // Cache em memória do processo
        if (isset(self::$cache[$idCompany])) return self::$cache[$idCompany];

        // Cache APCu (opcional)
        $ver = self::version($idCompany);
        $key = "cfg:{$idCompany}:{$ver}";
        if (function_exists('apcu_fetch')) {
            $hit = apcu_fetch($key, $ok);
            if ($ok) return self::$cache[$idCompany] = $hit;
        }

        $pdo = Connect::getInstance();

        // 1) defaults (idCompany=0)
        $stmt = $pdo->prepare("SELECT scope,skey,svalue,is_secret,is_json FROM cmp_settings WHERE idCompany=0");
        $stmt->execute();
        $data = self::inflate($stmt->fetchAll(\PDO::FETCH_ASSOC));

        // 2) específicos da empresa
        $stmt = $pdo->prepare("SELECT scope,skey,svalue,is_secret,is_json FROM cmp_settings WHERE idCompany=:c");
        $stmt->execute(['c' => $idCompany]);
        $spec = self::inflate($stmt->fetchAll(\PDO::FETCH_ASSOC));

        // 3) merge (empresa sobrescreve defaults)
        $cfg = array_replace_recursive($data, $spec);

        if (function_exists('apcu_store')) apcu_store($key, $cfg, 300);
        return self::$cache[$idCompany] = $cfg;
    }

    public static function bumpVersion(int $idCompany): void
    {
        $pdo = Connect::getInstance();
        $pdo->prepare("
            INSERT INTO cmp_config_versions (idCompany, version) VALUES (:c,1)
            ON DUPLICATE KEY UPDATE version = version + 1, updated_at = NOW()
        ")->execute(['c'=>$idCompany]);
    }

    private static function version(int $idCompany): int
    {
        $pdo = Connect::getInstance();
        $stmt = $pdo->prepare("SELECT version FROM cmp_config_versions WHERE idCompany=:c");
        $stmt->execute(['c'=>$idCompany]);
        return (int)($stmt->fetchColumn() ?: 1);
    }

    private static function inflate(array $rows): array
    {
        $out = [];
        foreach ($rows as $r) {
            $val = $r['svalue'];
            if ((int)$r['is_secret'] === 1 && $val !== null && $val !== '') {
                $val = \Source\Support\Crypto::decrypt($val);
            }
            if ((int)$r['is_json'] === 1 && $val) {
                $val = json_decode($val, true);
            }
            // monta array nested: $out[scope][skey] = valor
            $out[$r['scope']][$r['skey']] = $val;
        }
        return $out;
    }
}