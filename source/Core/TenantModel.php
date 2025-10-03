<?php

namespace Source\Core;

abstract class  TenantModel extends Model
{
    protected int $cid;

    public function __construct(string $entity, array $required = [], string $primary = "id", bool $timestamps = true)
    {
        parent::__construct($entity, $required, $primary, $timestamps);
        $this->cid = defined('TENANT_ID') ? (int)TENANT_ID : 0;
    }

    /** Opcional: permite consultar/gravar em outro tenant (uso consciente, ex.: telas de admin global). */
    public function setCompany(int $idCompany): self
    {
        $this->cid = $idCompany;
        return $this;
    }

    /** Aplica o escopo de idCompany às cláusulas. */
    protected function scope(?string $terms, ?string $params): array
    {
        $terms  = $terms ? "idCompany = :cid AND ({$terms})" : "idCompany = :cid";
        $params = $params ? "cid={$this->cid}&{$params}" : "cid={$this->cid}";
        return [$terms, $params];
    }

    /** find com escopo multi-tenant */
    public function find(?string $terms = null, ?string $params = null, string $columns = "*"): DataLayer
    {
        [$t, $p] = $this->scope($terms, $params);
        return parent::find($t, $p, $columns);
    }

    /** findById também escopado (evita vazar registro de outra empresa) */
    public function findById(int $id, string $columns = "*"): ?Model
    {
        return $this->find("id = :id", "id={$id}", $columns)->fetch();
    }

    /** Garante idCompany no insert/update */
    public function save(): bool
    {
        if (empty($this->idCompany)) {
            $this->idCompany = $this->cid;
        }
        return parent::save();
    }

    /**
     * Escape hatch: consulta SEM escopo de tenant (use com extremo cuidado).
     * Útil para telas de admin global, relatórios cross-tenant, etc.
     */
    public function findAllTenants(?string $terms = null, ?string $params = null, string $columns = "*"): DataLayer
    {
        return parent::find($terms, $params, $columns);
    }
}