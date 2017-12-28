<div class="content list_content">

    <section class="list_emp">

        <h1>Prestadores</h1>      

        <?php
        $empty = filter_input(INPUT_GET, 'empty', FILTER_VALIDATE_BOOLEAN);
        if ($empty):
            WSErro("Oppsss: Você tentou editar um prestador que não existe no sistema!", WS_INFOR);
        endif;

        $action = filter_input(INPUT_GET, 'action', FILTER_DEFAULT);
        if ($action):
            require ('_models/AdminPrestador.class.php');

            $prestAction = filter_input(INPUT_GET, 'prest', FILTER_VALIDATE_INT);
            $prestUpdate = new AdminPrestador();

            switch ($action):
                case 'active':
                    $prestUpdate->ExeStatus($prestAction, '1');
                    WSErro("O status do prestador foi atualizado para <b>ativo</b>.", WS_ACCEPT);
                    break;

                case 'inative':
                    $prestUpdate->ExeStatus($prestAction, '0');
                    WSErro("O status do prestador foi atualizado para <b>inativo</b>.", WS_ALERT);
                    break;

                case 'delete':
                    $prestUpdate->ExeDelete($prestAction);
                    WSErro($prestUpdate->getError()[0], $prestUpdate->getError()[1]);
                    break;

                default :
                    WSErro("Ação não foi identifica pelo sistema, favor utilize os botões!", WS_ALERT);
            endswitch;
        endif;

        $presti = 0;
        $getPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        $Pager = new Pager('painel.php?exe=prestadores/index&page=');
        $Pager->ExePager($getPage, 10);

        $readPrest = new Read;

        $readPrest->ExeRead("prestadores", "ORDER BY prestador_status ASC, prestador_nome ASC LIMIT :limit OFFSET :offset", "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}");
        if ($readPrest->getResult()):
            foreach ($readPrest->getResult() as $prest):
                $presti++;
                extract($prest);
                $status = (!$prestador_status ? 'style="background: #fffed8"' : '');

                ?>
                <article<?php if ($presti % 2 == 0) echo ' class="right"'; ?> <?= $status; ?>>
                    <header>

                        <div class="img thumb_small">
                            <?= Check::Image('../uploads/' . $prestador_capa, $prestador_nome, 120, 70); ?>
                        </div>
                        
                        <hgroup>
                            <h1><a target="_blank" href="../prestador/<?= $prestador_nome; ?>" title="Ver Empresa"><?= $prestador_nome; ?></a></h1>
                        </hgroup>

                    </header>
                    <ul class="info post_actions">
                        <li><a class="act_view" target="_blank" href="../prestador/<?= $prestador_nome; ?>" title="Ver no site">Ver no site</a></li>
                        <li><a class="act_edit" href="painel.php?exe=prestadores/update&prest=<?= $prestador_id; ?>" title="Editar">Editar</a></li>

                        <?php if (!$prestador_status): ?>
                            <li><a class="act_inative" href="painel.php?exe=prestadores/index&prest=<?= $prestador_id; ?>&action=active" title="Ativar">Ativar</a></li>
                        <?php else: ?>
                            <li><a class="act_ative" href="painel.php?exe=prestadores/index&prest=<?= $prestador_id; ?>&action=inative" title="Inativar">Inativar</a></li>
                        <?php endif; ?>

                        <li><a class="act_delete" href="painel.php?exe=prestadores/index&prest=<?= $prestador_id; ?>&action=delete" title="Excluir">Deletar</a></li>
                    </ul>
                </article>
                <?php
            endforeach;
        else:
            $Pager->ReturnPage();
            WSErro("Desculpe, ainda não existem prestadores cadastradas!", WS_INFOR);
        endif;
        ?>

        <div class="clear"></div>
    </section>


    <?php
    $Pager->ExePaginator("prestadores");
    echo $Pager->getPaginator();
    ?>

    <div class="clear"></div>
</div> <!-- content home -->