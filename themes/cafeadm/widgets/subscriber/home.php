<?php $this->layout("_admin"); ?>
<?php $this->insert("widgets/subscriber/sidebar"); ?>

<div class="dash_content_app">

    <div class="dash_content_app_box">
        <div class="app_control_home">
            <section class="app_control_home_stats">
                <article class="radius">
                    <h4 class="icon-user">associados</h4>
                    <p><?= str_pad($stats->subscriptions, 5, 0, 0); ?></p>
                </article>

                <article class="radius">
                    <h4 class="icon-user-plus">Por 30 dias</h4>
                    <p><?= str_pad($stats->subscriptionsMonth, 5, 0, 0); ?></p>
                </article>

                <article class="radius">
                    <h4 class="icon-calendar-check-o">Este mês:</h4>
                    <p>R$ <?= str_price($stats->recurrenceMonth); ?></p>
                </article>

                <article class="radius">
                    <h4 class="icon-retweet">Recorrência:</h4>
                    <p>R$ <?= str_price($stats->recurrence); ?></p>
                </article>
            </section>
            <hr>
            <div class="app_filter">
                <div class="app_filter_fields">
                    <label class="label legend" for="filter_name">
                        <input type="text" id="filter_name" placeholder="nome do associado">
                    </label>

                    <label class="label legend" for="filter_status">
                        <select id="filter_status">
                            <option value="">todos</option>
                            <option value="active">ativo</option>
                            <option value="inactive">inativo</option>
                        </select>
                    </label>

                    <div class="app_filter_actions">
                        <button class="btn btn-blue" type="button"><i class="icon-search radius"></i> filtrar</button>
                    </div>
                </div>
            </div>


            <!-- DEMO 2: PADRONIZAÇÃO DE CARD (.app_card) e TABELA (.app_table) -->
            <div class="app_card">
                <div class="app_card_header">
                    <h2>lista de associados</h2>
                    <a href="<?= url("/admin/subscriber/create")?>" class="btn btn-green"><i class="icon-plus-circle"></i> novo associado</a>
                </div>

                <?php if (!$subscriptions): ?>
                    <div class="message info icon-info">Ainda não existem associados em seu APP, assim que eles
                        começarem a chegar você verá os mais recentes aqui. Esperamos que seja em breve :)
                    </div>
                <?php else: ?>

                    <div class="app_table_container">
                        <table class="app_table">
                            <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Documento</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                        <?php foreach ($subscriptions as $subscription): ?>
                            <tr>
                                <td data-label="Nome">João da Silva</td>
                                <td data-label="Documento">123.456.789-00</td>
                                <td data-label="Email">joao@exemplo.com</td>
                                <td data-label="Status"><span class="message success"
                                                              style="padding: 5px 10px; border: none; font-size: 0.7em; background: var(--color-green);">Ativo</span>
                                </td>
                                <td class="actions" data-label="Ações">
                                    <!-- Botões de Ação com Ícones -->
                                    <a href="#" class="btn btn-blue" title="Editar"><i class="icon-pencil-square-o"></i>
                                        editar</a>
                                    <a href="#" class="btn btn-red" title="Excluir"><i class="icon-trash-o"></i> excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>


        </div>
    </div>


</div>
