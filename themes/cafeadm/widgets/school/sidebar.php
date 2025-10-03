
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

            echo $nav("pencil-square-o", "school/home", "escolas");
            echo $nav("plus-circle", "school/create", "nova escola");
            ?>

            <?php if (!empty($post->cover)): ?>
                <img class="radius" style="width: 100%; margin-top: 30px" src="<?= image($post->cover, 680); ?>"/>
            <?php endif; ?>

            <?php if (!empty($category->cover)): ?>
                <img class="radius" style="width: 100%; margin-top: 30px" src="<?= image($category->cover, 680); ?>"/>
            <?php endif; ?>
        </nav>
    </div>




</div>

