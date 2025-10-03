
<div class="dash_content_sidebar">
    <h3 class="icon-bookmark-o"> opções </h3>
    <div class="app_card">
        <nav>
            <?php
            $nav = function ($icon, $href, $title) use ($app) {
                $active = ($app == $href ? "active" : null);
                $url = url("/admin/{$href}");
                return "<a class=\"icon-{$icon} radius {$active}\" href=\"{$url}\">{$title}</a>";
            };

            echo $nav("pencil-square-o", "subscriber/home", "associados");
            echo $nav("angle-down", "subscriber/type_subscriber", "tipo de associados");
            echo $nav("flag", "control/plans", "Planos");
            echo $nav("cog", "subscriber/options", "opções");
            ?>
        </nav>
    </div>
</div>


