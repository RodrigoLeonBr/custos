<div class="content cat_list">

    <section class="list_emp">

        <h1>Unidades:</h1>

        <?php
        $empty = filter_input(INPUT_GET, 'empty', FILTER_VALIDATE_BOOLEAN);
        if ($empty):
            WSErro("Você tentou editar uma unidade que não existe no sistema!", WS_INFOR);
        endif;

        $delUnid = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
        if ($delUnid):
            require ('_models/AdminUnidade.class.php');
            $deletar = new AdminUnidade;
            $deletar->ExeDelete($delUnid);
            
            WSErro($deletar->getError()[0], $deletar->getError()[1]);
        endif;

        $readUnid = new Read;
        $readUnid->ExeRead("c_tabunidade","ORDER BY idUnidade");
        if (!$readUnid->getResult()):

        else:
            foreach ($readUnid->getResult() as $unidade):
                extract($unidade);

                $readCC = new Read;
                $readCC->ExeRead("c_tabcentrocusto", "WHERE id_unidade = :parent ORDER BY id_unidade, idCentroCusto", "parent={$idUnidade}");

                $countUnidadeCC = $readCC->getRowCount();
                ?>
                <section>

                    <header>
                        <h1><?= $UnDescricao; ?>  <span>( <?= $countUnidadeCC; ?> Centro(s) de Custo(s) ) </span></h1>
                        <p class="tagline"><?= $UnConteudo; ?></p>

                        <ul class="info post_actions">
                            <li><a class="act_edit" href="painel.php?exe=unidades/update&unidid=<?= $idUnidade; ?>" title="Editar">Editar</a></li>
                            <li><a class="act_delete" href="painel.php?exe=unidades/index&delete=<?= $idUnidade; ?>" title="Excluir">Deletar</a></li>
                        </ul>
                    </header>

                    <h2>Centro(s) de Custo(s) da Unidade:</h2>

                    <?php                    
                    if (!$readCC->getResult()):

                    else:
                        $a = 0;
                        foreach ($readCC->getResult() as $CC):
                            $a++;

                            ?>
                            <article <?php if ($a % 3 == 0) echo ' class="right clear"'; ?>>
                                <hgroup>
                                    <a target="_blank" href="../centrocusto<?= $CC['DescCentroCusto']; ?>" title="Ver Centro Custo"><?=$CC['idCentroCusto'];?> - <?= $CC['DescCentroCusto']; ?></a>
                                </hgroup>
                                <ul class="info post_actions">
                                    <li><a class="act_edit" href="painel.php?exe=unidades/update&unidid=<?= $CC['idCentroCusto']; ?>" title="Editar">Editar</a></li>
                                    <li><a class="act_delete" href="painel.php?exe=unidades/index&delete=<?= $CC['idCentroCusto']; ?>" title="Excluir">Deletar</a></li>
                                </ul>
                            </article>
                            <?php
                        endforeach;
                    endif;
                    ?>

                </section>
                <?php
            endforeach;
        endif;
        ?>

        <div class="clear"></div>
    </section>

    <div class="clear"></div>
</div> <!-- content home -->