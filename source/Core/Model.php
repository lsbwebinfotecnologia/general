<?php

namespace Source\Core;

use Source\Support\Message;

/**
 * FSPHP | Class Model Layer Supertype Pattern
 *
 * @author Robson V. Leite <cursos@upinside.com.br>
 * @package Source\Models
 */
abstract class Model
{

    /** ID do tenant atual (vem do bootstrap TENANT_ID). */
    protected int $tenantId;

    /**
     * Controla se ESTE model aplica escopo de tenant automaticamente.
     * Deixe TRUE por padrão aqui, e nos modelos que NÃO devem ser escopados, defina como FALSE.
     */
    protected bool $tenantScoped = true;

    /** @var object|null */
    protected $data;

    /** @var \PDOException|null */
    protected $fail;

    /** @var Message|null */
    protected $message;

    /** @var string */
    protected $query;

    /** @var string */
    protected $params;

    /** @var string */
    protected $order;

    /** @var int */
    protected $limit;

    /** @var int */
    protected $offset;

    /** @var string $entity database table */
    protected $entity;

    /** @var array $protected no update or create */
    protected $protected;

    /** @var array $entity database table */
    protected $required;

    /**
     * Model constructor.
     * @param string $entity database table name
     * @param array $protected table protected columns
     * @param array $required table required columns
     */
    public function __construct(string $entity, array $protected, array $required)
    {
        $this->entity   = $entity;
        $this->protected = array_merge($protected, ['created_at', "updated_at"]);
        $this->required  = $required;
        $this->message   = new Message();

        // 👇 importa o tenant resolvido no bootstrap
        $this->tenantId = defined('TENANT_ID') ? TENANT_ID : 0;
//        var_dump($this->tenantId);

        // evita notices na concatenação
        $this->order = $this->limit = $this->offset = "";
    }



    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if (empty($this->data)) {
            $this->data = new \stdClass();
        }

        $this->data->$name = $value;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data->$name);
    }

    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        return ($this->data->$name ?? null);
    }

    /**
     * @return null|object
     */
    public function data(): ?object
    {
        return $this->data;
    }

    /**
     * @return \PDOException
     */
    public function fail(): ?\PDOException
    {
        return $this->fail;
    }

    /**
     * @return Message|null
     */
    public function message(): ?Message
    {
        return $this->message;
    }

    /**
     * @param null|string $terms
     * @param null|string $params
     * @param string $columns
     * @return Model|mixed
     */
    public function find(?string $terms = null, ?string $params = null, string $columns = "*")
    {

        [$terms, $params] = $this->applyTenant($terms, $params);

        if ($terms) {
            $this->query = "SELECT {$columns} FROM {$this->entity} WHERE {$terms}";
            parse_str($params, $this->params);
//            var_dump($this);

            return $this;
        }

        $this->query  = "SELECT {$columns} FROM {$this->entity}";
        $this->params = []; // sem WHERE


        return $this;
    }

    /**
     * @param int $id
     * @param string $columns
     * @return null|mixed|Model
     */
    public function findById(int $id, string $columns = "*"): ?Model
    {
        [$t, $p] = $this->applyTenant("id = :id", "id={$id}");
        $find = $this->find($t ? $t : null, $p ?: null, $columns);
        return $find->fetch();
    }

    /**
     * @param string $columnOrder
     * @return Model
     */
    public function order(string $columnOrder): Model
    {
        $this->order = " ORDER BY {$columnOrder}";
        return $this;
    }

    /**
     * @param int $limit
     * @return Model
     */
    public function limit(int $limit): Model
    {
        $this->limit = " LIMIT {$limit}";
        return $this;
    }

    /**
     * @param int $offset
     * @return Model
     */
    public function offset(int $offset): Model
    {
        $this->offset = " OFFSET {$offset}";
        return $this;
    }

    /**
     * @param bool $all
     * @return null|array|mixed|Model
     */
    public function fetch(bool $all = false)
    {
        try {
            $stmt = Connect::getInstance()->prepare($this->query . $this->order . $this->limit . $this->offset);
            $stmt->execute($this->params);

            if (!$stmt->rowCount()) {
                return null;
            }

            if ($all) {
                return $stmt->fetchAll(\PDO::FETCH_CLASS, static::class);
            }

            return $stmt->fetchObject(static::class);
        } catch (\PDOException $exception) {
            $this->fail = $exception;
            return null;
        }
    }

    /**
     * @param string $key
     * @return int
     */
    public function count(string $key = "id"): int
    {
        $stmt = Connect::getInstance()->prepare($this->query);
        $stmt->execute($this->params);
        return $stmt->rowCount();
    }

    /**
     * @param array $data
     * @return int|null
     */
    protected function create(array $data): ?int
    {
        try {
            $columns = implode(", ", array_keys($data));
            $values = ":" . implode(", :", array_keys($data));

            $stmt = Connect::getInstance()->prepare("INSERT INTO {$this->entity} ({$columns}) VALUES ({$values})");
            $stmt->execute($this->filter($data));

            return Connect::getInstance()->lastInsertId();
        } catch (\PDOException $exception) {
            $this->fail = $exception;
            return null;
        }
    }

    /**
     * @param array $data
     * @param string $terms
     * @param string $params
     * @return int|null
     */
    protected function update(array $data, string $terms, string $params): ?int
    {
        try {
            $dateSet = [];
            foreach ($data as $bind => $value) {
                $dateSet[] = "{$bind} = :{$bind}";
            }
            $dateSet = implode(", ", $dateSet);
            parse_str($params, $params);

            $stmt = Connect::getInstance()->prepare("UPDATE {$this->entity} SET {$dateSet} WHERE {$terms}");
            $stmt->execute($this->filter(array_merge($data, $params)));
            return ($stmt->rowCount() ?? 1);
        } catch (\PDOException $exception) {
            $this->fail = $exception;
            return null;
        }
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        // garante idCompany antes da validação de required()
        $this->injectTenantOnData();

        if (!$this->required()) {
            $this->message->warning("Preencha todos os campos para continuar");
            return false;
        }

        /** Update */
        if (!empty($this->id)) {
            $id = (int)$this->id;

            // trava o update ao registro do tenant atual
            $terms  = "id = :id";
            $params = "id={$id}";
            if ($this->tenantScoped) {
                $terms  .= " AND idCompany = :cid";
                $params .= "&cid={$this->tenantId}";
            }

            $this->update($this->safe(), $terms, $params);
            if ($this->fail()) {
                $this->message->error("Erro ao atualizar, verifique os dados");
                return false;
            }
        }

        /** Create */
        if (empty($this->id)) {
            $id = $this->create($this->safe());
            if ($this->fail()) {
                $this->message->error("Erro ao cadastrar, verifique os dados");
                return false;
            }
        }

        $this->data = $this->findById((int)$id)->data();
        return true;
    }


    /**
     * @return int
     */
    public function lastId(): int
    {
        return Connect::getInstance()->query("SELECT MAX(id) as maxId FROM {$this->entity}")->fetch()->maxId + 1;
    }

    /**
     * @param string $terms
     * @param null|string $params
     * @return bool
     */
    public function delete(string $terms, ?string $params): bool
    {
        try {
            [$terms, $params] = $this->applyTenant($terms, $params);

            $stmt = Connect::getInstance()->prepare("DELETE FROM {$this->entity} WHERE {$terms}");
            if ($params) {
                parse_str($params, $paramsArr);
                $stmt->execute($paramsArr);
            } else {
                $stmt->execute();
            }
            return true;
        } catch (\PDOException $exception) {
            $this->fail = $exception;
            return false;
        }
    }

    /**
     * @return bool
     */
    public function destroy(): bool
    {
        if (empty($this->id)) {
            return false;
        }

        $destroy = $this->delete("id = :id", "id={$this->id}");
        return $destroy;
    }

    /**
     * @return array|null
     */
    protected function safe(): ?array
    {
        $safe = (array)$this->data;
        foreach ($this->protected as $unset) {
            unset($safe[$unset]);
        }
        return $safe;
    }

    /**
     * @param array $data
     * @return array|null
     */
    private function filter(array $data): ?array
    {
        $filter = [];
        foreach ($data as $key => $value) {
            $filter[$key] = (is_null($value) ? null : filter_var($value, FILTER_DEFAULT));
        }
        return $filter;
    }

    /**
     * @return bool
     */
    protected function required(): bool
    {
        $data = (array)$this->data();
        foreach ($this->required as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }
        return true;
    }

    /** Troca o tenant desta instância (admin global etc.) */
    public function setTenant(int $idCompany): self
    {
        $this->tenantId = $idCompany;
        return $this;
    }

    /** Desliga o escopo de tenant só para esta instância */
    public function noTenant(): self
    {
        $this->tenantScoped = false;
        return $this;
    }

    /** Religa o escopo de tenant */
    public function withTenant(): self
    {
        $this->tenantScoped = true;
        return $this;
    }

    /** Monta termos/params com idCompany quando o escopo estiver ativo */
    protected function applyTenant(?string $terms, ?string $params): array
    {
        if (!$this->tenantScoped) {
            return [$terms ?? "", $params ?? ""];
        }

        // aplica filtro de tenant: idCompany = :cid
        $terms  = $terms ? "idCompany = :cid AND ({$terms})" : "idCompany = :cid";
        $params = $params ? "cid={$this->tenantId}&{$params}" : "cid={$this->tenantId}";

        return [$terms, $params];
    }

    /** Garante idCompany no this->data antes de validar/salvar */
    protected function injectTenantOnData(): void
    {
        if (!$this->tenantScoped) return;

        if (empty($this->data)) {
            $this->data = new \stdClass();
        }
        if (empty($this->data->idCompany)) {
            $this->data->idCompany = $this->tenantId;
        }
    }

}