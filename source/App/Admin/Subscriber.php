<?php

namespace Source\App\Admin;

use Source\Models\CafeApp\AppSubscription;

class Subscriber extends Admin
{
    /**
     * Control constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function home(): void
    {
        $head = $this->seo->render(
            CONF_SITE_NAME . " | Controle de assinantes",
            CONF_SITE_DESC,
            url("/admin"),
            theme("/assets/images/image.jpg", CONF_VIEW_ADMIN),
            false
        );

        echo $this->view->render("widgets/subscriber/home", [
            "app" => "subscriber/home",
            "head" => $head,
            "stats" => (object)[
                "subscriptions" => (new AppSubscription())->find("pay_status = :s", "s=active")->count(),
                "subscriptionsMonth" => (new AppSubscription())->find("pay_status = :s AND year(started) = year(now()) AND month(started) = month(now())",
                    "s=active")->count(),
                "recurrence" => (new AppSubscription())->recurrence(),
                "recurrenceMonth" => (new AppSubscription())->recurrenceMonth()
            ],
            "subscriptions" => (new AppSubscription())->find()->order("started DESC")->limit(10)->fetch(true)
        ]);
    }

    public function subscriber(array $data)
    {
        //create
        if (!empty($data["action"]) && $data["action"] == "create") {
            $data = filter_var_array($data, FILTER_SANITIZE_SPECIAL_CHARS);

            $channelCreate = new Channel();
            $channelCreate->channel = $data["channel"];
            $channelCreate->description = $data["description"];

            if (!$channelCreate->save()) {
                $json["message"] = $channelCreate->message()->render();
                echo json_encode($json);
                return;
            }

            $this->message->success("Canal cadastrado com sucesso...")->flash();
            echo json_encode(["redirect" => url("/admin/faq/channel/{$channelCreate->id}")]);

            return;
        }

        //update
        if (!empty($data["action"]) && $data["action"] == "update") {
            $data = filter_var_array($data, FILTER_SANITIZE_SPECIAL_CHARS);
            $channelEdit = (new Channel())->findById($data["channel_id"]);

            if (!$channelEdit) {
                $this->message->error("Você tentou editar um canal que não existe ou foi removido")->flash();
                echo json_encode(["redirect" => url("/admin/faq/home")]);
                return;
            }

            $channelEdit->channel = $data["channel"];
            $channelEdit->description = $data["description"];

            if (!$channelEdit->save()) {
                $json["message"] = $channelEdit->message()->render();
                echo json_encode($json);
                return;
            }

            $json["message"] = $this->message->success("Canal atualizado com sucesso...")->render();
            echo json_encode($json);

            return;
        }

        //delete
        if (!empty($data["action"]) && $data["action"] == "delete") {
            $data = filter_var_array($data, FILTER_SANITIZE_SPECIAL_CHARS);
            $channelDelete = (new Channel())->findById($data["channel_id"]);

            if (!$channelDelete) {
                $this->message->error("Você tentou remover um canal que não existe ou já foi removido")->flash();
                echo json_encode(["redirect" => url("/admin/faq/home")]);
                return;
            }

            $channelDelete->destroy();
            $this->message->success("Canal excluído com sucesso...")->flash();

            echo json_encode(["redirect" => url("/admin/faq/home")]);
            return;
        }

        $channelEdit = null;
        if (!empty($data["channel_id"])) {
            $channelId = filter_var($data["channel_id"], FILTER_VALIDATE_INT);
            $channelEdit = (new Channel())->findById($channelId);
        }

        $head = $this->seo->render(
            CONF_SITE_NAME . " | " . ($channelEdit ? "FAQ: {$channelEdit->channel}" : "cadastro de assinante"),
            CONF_SITE_DESC,
            url("/admin"),
            url("/admin/assets/images/image.jpg"),
            false
        );

        echo $this->view->render("widgets/subscriber/subscriber", [
            "app" => "subscriber/subscriber",
            "head" => $head,
            "subscriber" => "",
        ]);
    }
}