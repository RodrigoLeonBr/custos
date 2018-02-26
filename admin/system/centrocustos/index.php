<div class="content list_content">

    <section>

        <h1>Centro(s) de Custos(s):</h1>

        <?php
        $empty = filter_input(INPUT_GET, 'empty', FILTER_VALIDATE_BOOLEAN);
        if ($empty):
            WSErro("Oppsss: Você tentou editar um centro de custo que não existe no sistema!", WS_INFOR);
        endif;


        $action = filter_input(INPUT_GET, 'action', FILTER_DEFAULT);
        if ($action):
            require ('_models/AdminCentroCusto.class.php');

            $CCAction = filter_input(INPUT_GET, 'ccid', FILTER_VALIDATE_INT);
            $CCUpdate = new AdminCentroCusto;

            switch ($action):
                case 'active':
                    $CCUpdate->ExeStatus($CCAction, '1');
                    WSErro("O status do centro de custos foi atualizado para <b>ativo</b>. Post publicado!", WS_ACCEPT);
                    break;

                case 'inative':
                    $CCUpdate->ExeStatus($CCAction, '0');
                    WSErro("O status do centro de custos foi atualizado para <b>inativo</b>. Post agora é um rascunho!", WS_ALERT);
                    break;

                case 'delete':
                    $CCUpdate->ExeDelete($CCAction);
                    WSErro($CCUpdate->getError()[0], $CCUpdate->getError()[1]);
                    break;

                default :
                    WSErro("Ação não foi identifica pelo sistema, favor utilize os botões!", WS_ALERT);
            endswitch;
        endif;


        $CCi = 1;
        $getPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        $Pager = new Pager('painel.php?exe=centrocustos/index&page=');
        $Pager->ExePager($getPage, 20);

        $readCC = new Read;
        $readCC->ExeRead("c_tabcentrocusto", "ORDER BY id_unidade, id_GrupoCC, id_SubGrupoCC, StatusCC ASC, idCentroCusto ASC LIMIT :limit OFFSET :offset", "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}");

        /** SQL DE BUSCA DE UNIDADE*/
        $unidades = new Read;
        $unidades->FullRead("SELECT idUnidade, UnDescricao FROM c_tabunidade");
        $unidade = $unidades->getResult();

        /** SQL DE BUSCA DE GRUPO*/
        $grupoccs = new Read;
        $grupoccs->FullRead("SELECT idGrupoCC, DescGrupoCC FROM c_tabgrupocc");
        $grupocc = $grupoccs->getResult();

        /** SQL DE BUSCA DE SUBGRUPO*/
        $subgrupoccs = new Read;
        $subgrupoccs->FullRead("SELECT idSubGrupoCC, DescSubGrupoCC FROM c_tabsubgrupocc");
        $subgrupocc = $subgrupoccs->getResult();

        if ($readCC->getResult()):
            foreach ($readCC->getResult() as $CC):
                $CCi++;
                extract($CC);
                $status = (!$StatusCC ? 'style="background: #fffed8"' : '');
                ?>
                <article<?php if ($CCi % 2 == 0) echo ' class="right clear"'; ?> <?= $status; ?>>

                    <h4><strong><?=$idCentroCusto;?>-<?= Check::Words($DescCentroCusto, 20) ?></strong></h4>
                    <h5><strong>Unid.: </strong><?=$id_unidade; ?>-<?= Check::Words($unidade[array_search($id_unidade, array_column($unidade,"idUnidade"))]["UnDescricao"], 10); ?></h5>
                    <h5><strong>Grupo:</strong> <?=$id_GrupoCC; ?>-<?=$grupocc[array_search($id_GrupoCC, array_column($grupocc,"idGrupoCC"))]["DescGrupoCC"]?></h5>
                    <h5><strong>SubGrupo:</strong> <?=$id_SubGrupoCC; ?>-<?=$subgrupocc[array_search($id_SubGrupoCC, array_column($subgrupocc,"idSubGrupoCC"))]["DescSubGrupoCC"]?></h5>
                    <ul class="info post_actions">
                        <li><a class="act_edit" href="painel.php?exe=centrocustos/update&ccid=<?= $idCentroCusto; ?>" title="Editar">Editar</a></li>

                        <?php if (!$StatusCC): ?>
                            <li><a class="act_inative" href="painel.php?exe=centrocustos/index&ccid=<?= $idCentroCusto; ?>&action=active" title="Ativar">Ativar</a></li>
                        <?php else: ?>
                            <li><a class="act_ative" href="painel.php?exe=centrocustos/index&ccid=<?= $idCentroCusto; ?>&action=inative" title="Inativar">Inativar</a></li>
                        <?php endif; ?>

                        <li><a class="act_delete" href="painel.php?exe=centrocustos/index&ccid=<?= $idCentroCusto; ?>&action=delete" title="Excluir">Deletar</a></li>
                    </ul>

                </article>
                <?php
            endforeach;

        else:
            $Pager->ReturnPage();
            WSErro("Desculpe, ainda não existem Centros de Custos cadastrados!", WS_INFOR);
        endif;
        ?>

        <div class="clear"></div>
    </section>

    <?php
    $Pager->ExePaginator("c_tabcentrocusto");
    echo $Pager->getPaginator();
    ?>

    <div class="clear"></div>
</div> <!-- content home -->