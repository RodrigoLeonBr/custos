<div class="content list_content">

    <section>

        <h1><strong>Contratos:</strong></h1>

        <?php
        $empty = filter_input(INPUT_GET, 'empty', FILTER_VALIDATE_BOOLEAN);
        if ($empty):
            WSErro("Oppsss: Você tentou editar um contrato que não existe no sistema!", WS_INFOR);
        endif;


        $action = filter_input(INPUT_GET, 'action', FILTER_DEFAULT);
        if ($action):
            require ('_models/AdminContrato.class.php');

            $CAction = filter_input(INPUT_GET, 'cid', FILTER_VALIDATE_INT);
            $CUpdate = new AdminContrato;

            switch ($action):
                case 'active':
                    $CUpdate->ExeStatus($CAction, '1');
                    WSErro("O status do centro de custos foi atualizado para <b>ativo</b>. Contrato Ativo!", WS_ACCEPT);
                    break;

                case 'inative':
                    $CUpdate->ExeStatus($CAction, '0');
                    WSErro("O status do centro de custos foi atualizado para <b>inativo</b>. Contato encerrado!", WS_ALERT);
                    break;

                case 'delete':
                    $CUpdate->ExeDelete($CAction);
                    WSErro($CUpdate->getError()[0], $CUpdate->getError()[1]);
                    break;

                default :
                    WSErro("Ação não foi identifica pelo sistema, favor utilize os botões!", WS_ALERT);
            endswitch;
        endif;


        $Ci = 1;
        $getPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        $Pager = new Pager('painel.php?exe=contratos/index&page=');
        $Pager->ExePager($getPage, 20);

        $readC = new Read;
        $readC->ExeRead("c_contrato", "ORDER BY statusC desc,contrato_vencimento LIMIT :limit OFFSET :offset", "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}");

        if ($readC->getResult()):
            foreach ($readC->getResult() as $CC):
                $Ci++;
                extract($CC);
                $status = (!$statusC ? 'style="background: #fffed8"' : '');
                ?>
                <article<?php if ($Ci % 2 == 0) echo ' class="right clear"'; ?> <?= $status; ?>>

                    <h4><strong><?=$contrato_protocolo;?>-<?= Check::Words($contrato_prestador, 20) ?></strong></h4>
                    <h4><strong>CNES: </strong><?=$contrato_cnes; ?><strong> Dt Venc: </strong><?= date('d/m/Y', strtotime($contrato_vencimento)); ?></h4>
                    <h4><strong>Valor Saldo:</strong> <?=number_format($contrato_saldovalor,2, ',','.'); ?><strong> Qtd Saldo: </strong><?=number_format($contrato_saldoqtd,0, ',','.');?></h4>
                    
                    <ul class="info post_actions">
                        <li><a class="act_edit" href="painel.php?exe=contratos/update&cid=<?= $id_contrato; ?>" title="Editar">Editar</a></li>

                        <?php if (!$statusC): ?>
                            <li><a class="act_inative" href="painel.php?exe=contratos/index&cid=<?= $id_contrato; ?>&action=active" title="Ativar">Ativar</a></li>
                        <?php else: ?>
                            <li><a class="act_ative" href="painel.php?exe=contratos/index&cid=<?= $id_contrato; ?>&action=inative" title="Inativar">Inativar</a></li>
                        <?php endif; ?>

                        <li><a class="act_delete" href="painel.php?exe=contratos/index&cid=<?= $id_contrato; ?>&action=delete" title="Excluir">Deletar</a></li>
                    </ul>

                </article>
                <?php
            endforeach;

        else:
            $Pager->ReturnPage();
            WSErro("Desculpe, ainda não existem Contratos cadastrados!", WS_INFOR);
        endif;
        ?>

        <div class="clear"></div>
    </section>

    <?php
    $Pager->ExePaginator("c_contrato");
    echo $Pager->getPaginator();
    ?>

    <div class="clear"></div>
</div> <!-- content home -->