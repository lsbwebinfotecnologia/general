<?php $this->layout("_admin"); ?>

<?php $this->insert("widgets/school/sidebar"); ?>

<section class="dash_content_app">

    <?php if (!$school): ?>
        <header class="dash_content_app_header">
            <h2 class="icon-bookmark-o">nova escola</h2>
        </header>
        <div class="app_card">
        <div class="dash_content_app_box">

            <form class="app_form" action="<?= url("/admin/school/create"); ?>" method="post">
                <!-- ACTION SPOOFING-->
                <input type="hidden" name="action" value="create"/>

                <label class="label">
                    <span class="legend">*nome escola:</span>
                    <input type="text" name="display_name" placeholder="nome da escola" required/>
                </label>
                <label class="label">
                    <span class="legend">referencia:</span>
                    <input type="text" name="reference" placeholder="código referencia da escola"/>
                </label>
                <label class="label">
                    <span class="legend">CNPJ:</span>
                    <input type="text" name="document" placeholder="CNPJ"/>
                </label>
                <label class="label">
                    <span class="legend">e-mail:</span>
                    <input type="email" name="email" placeholder="e-mail"/>
                </label>
                <label class="label">
                    <span class="legend">observação:</span>
                    <textarea name="detail" placeholder="digite uma observação para escola"></textarea>
                </label>

                <div class="label_g2">
                    <label class="label">
                        <span class="legend">*status:</span>
                        <select name="status" required>
                            <option value="active">ativo</option>
                            <option value="inactive">inativo</option>
                        </select>
                    </label>
                </div>

                <div class="al-right">
                    <button class="btn btn-green icon-check-square-o">salvar</button>
                </div>
            </form>

        </div>
        </div>
    <?php else: ?>
    <?php endif; ?>
</section>
