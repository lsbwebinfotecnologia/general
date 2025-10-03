<?php $this->layout("_admin"); ?>
<?php $this->insert("widgets/partners/sidebar"); ?>

<section class="dash_content_app">
    <header class="dash_content_app_header">
        <h2 class="icon-users">parceiros</h2>
        <form action="<?= url("/admin/partners/home"); ?>" method="post" class="app_search_form">
            <input type="text" name="s" value="<?= $search; ?>" placeholder="Pesquisar Parceiro:">
            <button class="icon-search icon-notext"></button>
        </form>
        <button class="btn btn-blue" data-modal-open="#partnerCreateModal">+ Novo parceiro</button>
    </header>

    <div class="dash_content_app_box">
        <section>
            <div class="app_blog_home"><!-- reaproveitando o grid do tema -->
                <?php if (!$partners): ?>
                    <div class="message info icon-info">Ainda não existem parceiros cadastrados.</div>
                <?php else: ?>
                    <?php foreach ($partners as $p): ?>
                        <article>
                            <div class="cover embed radius" style="background:#f7f7f7; display:flex; align-items:center; justify-content:center;">
                                <span class="icon-user" style="font-size:2rem;color:#999;"></span>
                            </div>

                            <h3 class="tittle">
                                <span class="icon-check"><?= $p->display_name; ?></span>
                            </h3>

                            <div class="info">
                                <p class="icon-tag"><?= $p->code ?: "—"; ?></p>
                                <p class="icon-id-card-o"><?= $p->document ?: "—"; ?></p>
                                <p class="icon-envelope-o"><?= $p->email ?: "—"; ?></p>
                                <p class="icon-phone"><?= $p->phone ?: "—"; ?></p>
                                <p class="icon-check-circle"><?= $p->status === "active" ? "Ativo" : "Inativo"; ?></p>
                            </div>

                            <div class="actions">
                                <a class="icon-pencil btn btn-blue" href="<?= url("/admin/partners/partner/{$p->id}"); ?>">Editar</a>

                                <a class="icon-trash-o btn btn-red" href="#"
                                   data-post="<?= url("/admin/partners/partner"); ?>"
                                   data-action="delete"
                                   data-confirm="Tem certeza que deseja deletar este parceiro?"
                                   data-partner_id="<?= $p->id; ?>">
                                    Deletar
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?= $paginator; ?>
        </section>
    </div>
</section>

<!-- MODAL criar parceiro (AJAX) -->
<div class="modal" id="partnerCreateModal" style="display:none;">
    <div class="modal_content">
        <header class="modal_header">
            <h2 class="icon-plus">Novo parceiro</h2>
            <span class="modal_close" data-modal-close>&times;</span>
        </header>
        <div class="modal_body">
            <form id="partnerCreateForm" action="<?= url('/admin/partners/partner'); ?>" method="post">
                <div class="label_group">
                    <label>Nome *</label>
                    <input type="text" name="display_name" required>
                </div>

                <div class="label_group">
                    <label>Razão Social</label>
                    <input type="text" name="legal_name">
                </div>

                <div class="label_group">
                    <label>Código</label>
                    <input type="text" name="code" placeholder="ex.: escola_cronuz">
                    <small>Letras, números, "-" e "_".</small>
                </div>

                <div class="label_group">
                    <label>Documento (CPF/CNPJ)</label>
                    <input type="text" name="document">
                </div>

                <div class="label_group">
                    <label>Status</label>
                    <select name="status">
                        <option value="active" selected>Ativo</option>
                        <option value="inactive">Inativo</option>
                    </select>
                </div>

                <div class="label_group">
                    <label>E-mail</label>
                    <input type="email" name="email">
                </div>

                <div class="label_group">
                    <label>Telefone</label>
                    <input type="text" name="phone">
                </div>

                <div class="label_group">
                    <label>Tipos</label>
                    <select name="type_ids[]" multiple size="4">
                        <?php foreach (($types ?? []) as $t): ?>
                            <option value="<?= $t->id; ?>"><?= $t->name; ?> (<?= $t->slug; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="label_group">
                    <label>Meta (JSON opcional)</label>
                    <textarea name="meta" rows="3" placeholder='{"obs":"vip"}'></textarea>
                </div>

                <div class="modal_actions">
                    <button class="btn btn-blue">Salvar</button>
                    <button type="button" class="btn btn-light" data-modal-close>Cancelar</button>
                </div>
            </form>
            <div class="ajax_response" id="partnerCreateResp" style="margin-top:10px;"></div>
        </div>
    </div>
</div>