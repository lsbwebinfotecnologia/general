<?php $this->layout("_admin"); ?>
<?php $this->insert("widgets/subscriber/sidebar"); ?>

<section class="dash_content_app">
    <?php if (!$subscriber): ?>
        <header class="dash_content_app_header">
            <h2 class="icon-bookmark-o">novo assinante</h2>
        </header>

        <div class="dash_content_app_box">
            <form class="app_form" action="<?= url("/admin/subscriber/create"); ?>" method="post">
                <!-- ACTION SPOOFING-->
                <input type="hidden" name="action" value="create"/>

                <div class="radio-group">
                    <span class="legend">Tipo de Pessoa <span style="color: var(--color-red);">*</span></span>
                    <div class="radio-options">
                        <label for="typeF">
                            <input type="radio" name="type" id="typeF" value="F" checked="">
                            Física
                        </label>
                        <label for="typeJ">
                            <input type="radio" name="type" id="typeJ" value="J">
                            Jurídica
                        </label>
                    </div>
                </div>
                <hr>
                <div class="label_g2">
                    <label class="label" for="selectTypeAssociate">
                        <span class="legend">Tipo de Associado <span style="color: var(--color-red);">*</span></span>
                        <select name="idTypeAssociate" id="selectTypeAssociate" required="">
                            <option value="">Selecione o Tipo</option>
                            <option value="1">Associado Padrão</option>
                            <option value="2">Associado Premium</option>
                        </select>
                    </label>

                    <label class="label" for="gender">
                        <span class="legend">Gênero <span style="color: var(--color-red);">*</span></span>
                        <select name="gender" id="gender" required="">
                            <option value="masculino">Masculino</option>
                            <option value="feminino">Feminino</option>
                            <option value="outro">Outro</option>
                        </select>
                    </label>
                </div>

                <!-- 3. NOME COMPLETO e NOME PREFERENCIAL -->
                <div class="label_g2">
                    <label class="label" for="name">
                        <span class="legend">Nome Completo <span style="color: var(--color-red);">*</span></span>
                        <input type="text" id="name" name="name" placeholder="Qual o nome completo do associado?" required="">
                    </label>

                    <label class="label" for="surname">
                        <span class="legend">Nome pelo qual deseja ser chamado <span style="color: var(--color-red);">*</span></span>
                        <input type="text" id="surname" name="surname" placeholder="Nome pelo qual deseja ser chamado(a)?" required="">
                    </label>
                </div>

                <!-- 4. E-MAIL e SENHA -->
                <div class="label_g2">
                    <label class="label" for="email">
                        <span class="legend">E-mail <span style="color: var(--color-red);">*</span></span>
                        <input type="email" id="email" name="email" placeholder="Insira um e-mail" required="">
                    </label>

                    <label class="label" for="password">
                        <span class="legend">Senha <span style="color: var(--color-red);">*</span></span>
                        <!-- Exemplo de campo com erro (você precisará de JS para a mensagem) -->
                        <input type="password" class="error" id="password" name="password" required="">
                        <!-- Mensagem de erro manual para demonstração: -->
                        <p style="color: var(--color-red); font-size: var(--font-min); margin-top: 5px;">A senha deve conter no mínimo uma letra.</p>
                    </label>
                </div>

                <!-- 5. CONFIRMAR SENHA e DATA DE NASCIMENTO -->
                <div class="label_g2">
                    <label class="label" for="confirm-password">
                        <span class="legend">Confirme a Senha <span style="color: var(--color-red);">*</span></span>
                        <input type="password" id="confirm-password" name="confirm-password" required="">
                    </label>

                    <label class="label" for="birth">
                        <span class="legend">Data de Nascimento <span style="color: var(--color-red);">*</span></span>
                        <input type="date" id="birth" name="birth" placeholder="Data de nascimento" required="">
                    </label>
                </div>

                <!-- 6. CPF -->
                <label class="label" for="document">
                    <span class="legend">CPF <span style="color: var(--color-red);">*</span></span>
                    <input type="text" id="document" name="document" placeholder="Informe o CPF" required="" maxlength="14">
                </label>

                <!-- 7. OBSERVAÇÃO (Textarea) -->
                <label class="label" for="observation">
                    <span class="legend">Observação (opcional)</span>
                    <textarea id="observation" name="observation" rows="5" placeholder="Digite alguma observação"></textarea>
                </label>

                <!-- 8. NECESSIDADE ESPECIAL (Checkbox) -->
                <label class="label" for="special" style="margin-top: 20px;">
                    <input type="checkbox" id="special" name="special" value="1" style="width: auto; display: inline-block; margin-right: 8px;">
                    <span class="legend" style="display: inline; font-weight: normal; margin-bottom: 0;">Tem alguma necessidade especial?</span>
                </label>

                <div class="al-right">
                    <button class="btn btn-green icon-check-square-o">salvar</button>
                </div>
            </form>
        </div>
    <?php else: ?>
        <header class="dash_content_app_header">
            <h2 class="icon-pencil-square-o">Editar post #<?= $post->id; ?></h2>
            <a class="icon-link btn btn-green" href="<?= url("/blog/{$post->uri}"); ?>" target="_blank" title="">Ver no
                site</a>
        </header>

        <div class="dash_content_app_box">
            <form class="app_form" action="<?= url("/admin/blog/post/{$post->id}"); ?>" method="post">
                <!-- ACTION SPOOFING-->
                <input type="hidden" name="action" value="update"/>

                <label class="label">
                    <span class="legend">*Título:</span>
                    <input type="text" name="title" value="<?= $post->title; ?>" placeholder="A manchete do seu artigo"
                           required/>
                </label>

                <label class="label">
                    <span class="legend">*Subtítulo:</span>
                    <textarea name="subtitle" placeholder="O texto de apoio da manchete"
                              required><?= $post->subtitle; ?></textarea>
                </label>

                <label class="label">
                    <span class="legend">Capa: (1920x1080px)</span>
                    <input type="file" name="cover" placeholder="Uma imagem de capa"/>
                </label>

                <label class="label">
                    <span class="legend">Vídeo:</span>
                    <input type="text" name="video" value="<?= $post->video; ?>"
                           placeholder="O ID de um vídeo do YouTube"/>
                </label>

                <label class="label">
                    <span class="legend">*Conteúdo:</span>
                    <textarea class="mce" name="content"><?= $post->content; ?></textarea>
                </label>

                <div class="label_g2">
                    <label class="label">
                        <span class="legend">*Categoria:</span>
                        <select name="category" required>
                            <?php foreach ($categories as $category):
                                $categoryId = $post->category;
                                $select = function ($value) use ($categoryId) {
                                    return ($categoryId == $value ? "selected" : "");
                                };
                                ?>
                                <option <?= $select($category->id); ?>
                                    value="<?= $category->id; ?>"><?= $category->title; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="label">
                        <span class="legend">*Autor:</span>
                        <select name="author" required>
                            <?php foreach ($authors as $author):
                                $authorId = $post->author;
                                $select = function ($value) use ($authorId) {
                                    return ($authorId == $value ? "selected" : "");
                                };
                                ?>
                                <option <?= $select($author->id); ?>
                                    value="<?= $author->id; ?>"><?= $author->fullName(); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </div>

                <div class="label_g2">
                    <label class="label">
                        <span class="legend">*Status:</span>
                        <select name="status" required>
                            <?php
                            $status = $post->status;
                            $select = function ($value) use ($status) {
                                return ($status == $value ? "selected" : "");
                            };
                            ?>
                            <option <?= $select("post"); ?> value="post">Publicar</option>
                            <option <?= $select("draft"); ?> value="draft">Rascunho</option>
                            <option <?= $select("trash"); ?> value="trash">Lixo</option>
                        </select>
                    </label>

                    <label class="label">
                        <span class="legend">Data de publicação:</span>
                        <input class="mask-datetime" type="text" name="post_at"
                               value="<?= date_fmt($post->post_at, "d/m/Y H:i"); ?>" required/>
                    </label>
                </div>

                <div class="al-right">
                    <button class="btn btn-blue icon-pencil-square-o">Atualizar</button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</section>