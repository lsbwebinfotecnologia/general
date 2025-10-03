<?php $this->layout("_admin"); ?>
<?php $this->insert("widgets/school/sidebar"); ?>


<div class="dash_content_app">

    <!-- DEMO 1: PADRONIZAÇÃO DE FILTRO MULTI-CAMPO (.app_filter) -->
    <div class="app_filter">
        <div class="app_filter_fields">

            <label class="label legend" for="filter_name">nome escola
                <input type="text" id="filter_name" placeholder="nome da escola">
            </label>

            <label class="label legend" for="filter_status">status
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

    <hr>

    <!-- DEMO 2: PADRONIZAÇÃO DE CARD (.app_card) e TABELA (.app_table) -->
    <div class="app_card">
        <div class="app_card_header">
            <h2>lista de escolas</h2>
            <a href="#" class="btn btn-green"><i class="icon-plus-circle"></i> nova escola</a>
        </div>

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
                <tr>
                    <td data-label="Nome">João da Silva</td>
                    <td data-label="Documento">123.456.789-00</td>
                    <td data-label="Email">joao@exemplo.com</td>
                    <td data-label="Status"><span class="message success"
                                                  style="padding: 5px 10px; border: none; font-size: 0.7em; background: var(--color-green);">Ativo</span>
                    </td>
                    <td class="actions" data-label="Ações">
                        <!-- Botões de Ação com Ícones -->
                        <a href="#" class="btn btn-blue" title="Editar"><i class="icon-pencil-square-o"></i> editar</a>
                        <a href="#" class="btn btn-red" title="Excluir"><i class="icon-trash-o"></i> excluir</a>
                    </td>
                </tr>
                <tr>
                    <td data-label="Nome">Maria Oliveira</td>
                    <td data-label="Documento">987.654.321-00</td>
                    <td data-label="Email">maria@exemplo.com</td>
                    <td data-label="Status"><span class="message warning"
                                                  style="padding: 5px 10px; border: none; font-size: 0.7em; background: var(--color-yellow);">Pendente</span>
                    </td>
                    <td class="actions" data-label="Ações">
                        <!-- Botões de Ação com Ícones -->
                        <a href="#" class="btn btn-blue" title="Editar"><i class="icon-pencil-square-o"></i> editar</a>
                        <a href="#" class="btn btn-red" title="Excluir"><i class="icon-trash-o"></i> excluir</a>
                    </td>
                </tr>
                <tr>
                    <td data-label="Nome">Pedro Souza</td>
                    <td data-label="Documento">111.222.333-44</td>
                    <td data-label="Email">pedro@exemplo.com</td>
                    <td data-label="Status"><span class="message error"
                                                  style="padding: 5px 10px; border: none; font-size: 0.7em; background: var(--color-red);">Inativo</span>
                    </td>
                    <td class="actions" data-label="Ações">
                        <!-- Botões de Ação com Ícones -->
                        <a href="#" class="btn btn-blue" title="Editar"><i class="icon-pencil-square-o"></i> editar</a>
                        <a href="#" class="btn btn-red" title="Excluir"><i class="icon-trash-o"></i> excluir</a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

