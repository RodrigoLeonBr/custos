<div class="content cat_list">

    <section class="list_emp">

        <h1>SubGrupos:</h1>

        <?php
        $empty = filter_input(INPUT_GET, 'empty', FILTER_VALIDATE_BOOLEAN);
        if ($empty):
            WSErro("Você tentou editar um SubGrupo que não existe no sistema!", WS_INFOR);
        endif;

        $delSubGrupo = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
        if ($delSubGrupo):
            require ('_models/AdminSubGrupo.class.php');
            $deletar = new AdminSubGrupo;
            $deletar->ExeDelete($delSubGrupo);
            
            WSErro($deletar->getError()[0], $deletar->getError()[1]);
        endif;

        $readSubGrupo = new Read;
        $readSubGrupo->ExeRead("c_tabsubgrupocc");
        if (!$readSubGrupo->getResult()):

        else:
            foreach ($readSubGrupo->getResult() as $subgrupo):
                extract($subgrupo);

                $readCC = new Read;
                $readCC->ExeRead("c_tabcentrocusto", "WHERE id_SubGrupoCC = :parent ORDER BY id_SubGrupoCC, idCentroCusto", "parent={$idSubGrupoCC}");

                $countSubGrupoCC = $readCC->getRowCount();
                ?>
                <section>

                    <header>
                        <h1><?= $idSubGrupoCC;?>-<?= $DescSubGrupoCC; ?>  <span>( <?= $countSubGrupoCC; ?> Centro(s) de Custo(s) ) - Ordem: <?= $Ordem;?></span></h1>
                        <p class="tagline"><?= $SubGrupoConteudo; ?></p>

                        <ul class="info post_actions">
                            <li><a class="act_edit" href="painel.php?exe=subgrupos/update&subgrupoid=<?= $idSubGrupoCC; ?>" title="Editar">Editar</a></li>
                            <li><a class="act_delete" href="painel.php?exe=subgrupos/index&delete=<?= $idSubGrupoCC; ?>" title="Excluir">Deletar</a></li>
                        </ul>
                    </header>

                    <h2>Centro(s) de Custo(s) do SubGrupo:</h2>

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
                                    <li><a class="act_edit" href="painel.php?exe=unidades/update&subgrupoid=<?= $CC['idCentroCusto']; ?>" title="Editar">Editar</a></li>
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