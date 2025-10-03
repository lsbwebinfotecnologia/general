<?php

namespace Source\App\Admin;

use Source\Models\Partner\PrtPartner;
use Source\Models\Partner\PrtPartnerType;
use Source\Support\Pager;


class School extends Admin
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
            echo json_encode(["redirect" => url("/admin/school/home/{$s}/1")]);
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
                redirect("/admin/school/home");
            }
        }

        // 3) paginação
        $all = ($search ?? "all");
        $page = !empty($data["page"]) ? (int)$data["page"] : 1;

        $pager = new Pager(url("/admin/school/home/{$all}/"));
        $pager->pager($partners->count(), 12, $page);

        $list = $partners
            ->limit($pager->limit())
            ->offset($pager->offset())
            ->order("updated_at DESC")
            ->fetch(true);

        // 4) head + render
        $head = $this->seo->render(
            CONF_SITE_NAME . " | escolas",
            CONF_SITE_DESC,
            url("/admin"),
            url("/admin/assets/images/image.jpg"),
            false
        );

        echo $this->view->render("widgets/school/home", [
            "app" => "school/home",
            "head" => $head,
            "partners" => $list,
            "paginator" => $pager->render(),
            "search" => $search,
            "types" => (new PrtPartnerType())->find("active=1")->order("name")->fetch(true) ?? []
        ]);
    }

    public function school(array $data)
    {

        //create
        if (!empty($data["action"]) && $data["action"] == "create") {

            $data = filter_var_array($data, FILTER_SANITIZE_SPECIAL_CHARS);

            $document = $data["document"] ?? false;

//            var_dump($document);
//            die;

            $prtPartnerCreate = new PrtPartner();
            $prtPartnerCreate->display_name = $data["display_name"] ?? "";
            $prtPartnerCreate->code  = $data["reference"] ?? "";
            $prtPartnerCreate->document = $data["document"] ?? "";
            $prtPartnerCreate->email = $data["email"] ?? "";
            $prtPartnerCreate->detail = $data["detail"] ?? "";
            $prtPartnerCreate->status = $data["status"] ?? "";
            $prtPartnerCreate->typePartner = "school";

            if (!$prtPartnerCreate->save()) {
                $json["message"] = $prtPartnerCreate->message()->render();
                echo json_encode($json);
                return;
            }

            $this->message->success("escola cadastrada com sucesso...")->flash();
            echo json_encode(["redirect" => url("/admin/school/edit/{$prtPartnerCreate->id}")]);

            return;
        }

        $prtPartner = null;

        $head = $this->seo->render(
            CONF_SITE_NAME . " | " . ($prtPartner ? "escola: {$prtPartner->legal_name}" : "escola: nova escola"),
            CONF_SITE_DESC,
            url("/admin"),
            url("/admin/assets/images/image.jpg"),
            false
        );

        echo $this->view->render("widgets/school/school", [
            "app" => "school/create",
            "head" => $head,
            "school" => $prtPartner
        ]);
    }

}