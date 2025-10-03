<?php

namespace Source\App\Admin;

use Source\Models\Partner\PrtPartner;
use Source\Models\Partner\PrtPartnerType;
use Source\Support\Pager;

class Partner extends Admin
{
    /**
     * Control constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function home(array $data): void
    {

        // 1) search redirect (POST -> GET com placeholders)
        if (!empty($data["s"])) {
            $s = str_search($data["s"]);
            echo json_encode(["redirect" => url("/admin/partners/home/{$s}/1")]);
            return;
        }

        // 2) montar consulta
        $search = null;
        $partners = (new PrtPartner())->find();

        if (!empty($data["search"]) && str_search($data["search"]) !== "all") {
            $search = str_search($data["search"]);
            $partners = (new PrtPartner())->find(
                "(display_name LIKE :q OR code LIKE :q OR document LIKE :q)",
                "q=%{$search}%"
            );

            if (!$partners->count()) {
                $this->message->info("Sua pesquisa não retornou resultados")->flash();
                redirect("/admin/partners/home");
            }
        }

        // 3) paginação
        $all = ($search ?? "all");
        $page = !empty($data["page"]) ? (int)$data["page"] : 1;

        $pager = new Pager(url("/admin/partners/home/{$all}/"));
        $pager->pager($partners->count(), 12, $page);

        $list = $partners
            ->limit($pager->limit())
            ->offset($pager->offset())
            ->order("updated_at DESC")
            ->fetch(true);

        // 4) head + render
        $head = $this->seo->render(
            CONF_SITE_NAME . " | Parceiros",
            CONF_SITE_DESC,
            url("/admin"),
            url("/admin/assets/images/image.jpg"),
            false
        );

        echo $this->view->render("widgets/partners/home", [
            "app" => "partners/home",
            "head" => $head,
            "partners" => $list,
            "paginator" => $pager->render(),
            "search" => $search,
            "types" => (new PrtPartnerType())->find("active=1")->order("name")->fetch(true) ?? []
        ]);
    }

    // /admin/partners/partner   e   /admin/partners/partner/{partner_id}
    public function partner(?array $data): void
    {
        $partnerId = !empty($data["partner_id"]) ? (int)$data["partner_id"] : null;

        // 1) POST (criar/atualizar) — tudo vem em $data
        if (!empty($data) && $_SERVER["REQUEST_METHOD"] === "POST") {
            [$ok, $errors, $in] = PartnerValidator::validate($data, TENANT_ID, $partnerId);

            if (!$ok) {
                $this->message->warning("Verifique os campos obrigatórios")->flash();
                $partner = $partnerId ? (new PrtPartner())->findById($partnerId) : null;

                echo $this->view->render("widgets/partners/partner", [
                    "head" => $this->seo->render(CONF_SITE_NAME . " | Parceiro", CONF_SITE_DESC, url("/admin")),
                    "partner" => $partner,
                    "types" => (new PrtPartnerType())->find("active=1")->order("name")->fetch(true) ?? [],
                    "errors" => $errors
                ]);
                return;
            }

            $pdo = Connect::getInstance();
            $pdo->beginTransaction();
            try {
                $p = $partnerId ? (new PrtPartner())->findById($partnerId) : new PrtPartner();
                if ($partnerId && !$p) {
                    $this->message->error("Registro não encontrado")->flash();
                    redirect("/admin/partners/home");
                    return;
                }

                // atribuições
                $p->display_name = $in['display_name'];
                $p->legal_name = $in['legal_name'];
                $p->code = $in['code'];
                $p->document = $in['document'];
                $p->email = $in['email'];
                $p->phone = $in['phone'];
                $p->status = $in['status'];
                $p->meta = $in['meta'];

                if (!$p->save()) throw new \Exception("Erro ao salvar parceiro");

                // tipos (reset simples)
                (new PrtPartnerTypeLink())->find("idPartner=:p", "p={$p->id}")->destroy();
                foreach ($in['type_ids'] as $idType) {
                    $lnk = new PrtPartnerTypeLink();
                    $lnk->idPartner = $p->id;
                    $lnk->idType = (int)$idType;
                    $lnk->save();
                }

                $pdo->commit();
                $this->message->success("Parceiro salvo com sucesso")->flash();
                redirect("/admin/partners/partner/{$p->id}");
                return;

            } catch (\Throwable $e) {
                $pdo->rollBack();
                $this->message->error("Erro ao salvar: " . $e->getMessage())->flash();
                redirect("/admin/partners/home");
                return;
            }
        }

        // 2) GET (novo/editar)
        $partner = null;
        if ($partnerId) {
            $partner = (new PrtPartner())->findById($partnerId);
            if (!$partner) {
                $this->message->warning("Registro não encontrado")->flash();
                redirect("/admin/partners/home");
                return;
            }
        }

        $head = $this->seo->render(
            CONF_SITE_NAME . " | Parceiro",
            CONF_SITE_DESC,
            url("/admin"),
            url("/admin/assets/images/image.jpg"),
            false
        );

        echo $this->view->render("widgets/partners/partner", [
            "head" => $head,
            "partner" => $partner,
            "types" => (new PrtPartnerType())->find("active=1")->order("name")->fetch(true) ?? []
        ]);
    }

    // /admin/partners/partner/{partner_id}/delete
    public function delete(?array $data): void
    {
        $id = !empty($data["partner_id"]) ? (int)$data["partner_id"] : null;
        if (!$id) {
            $this->message->error("Registro inválido")->flash();
            redirect("/admin/partners/home");
            return;
        }

        $p = (new PrtPartner())->findById($id);
        if ($p) {
            $p->destroy();
            $this->message->success("Parceiro removido")->flash();
        } else {
            $this->message->warning("Registro não encontrado")->flash();
        }
        redirect("/admin/partners/home");
    }

    public function createForm(array $data): void
    {
        $types = (new PrtPartnerType())->find("active=1")->order("name ASC")->fetch(true) ?? [];
        echo $this->view->render("partners/form", [
            "types" => $types,
            "partner" => null
        ]);
    }
}