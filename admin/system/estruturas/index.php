<div class="content cat_list">

    <section>

        <h1>Estruturas:</h1>

        <?php
        $empty = filter_input(INPUT_GET, 'empty', FILTER_VALIDATE_BOOLEAN);
        if ($empty):
            WSErro("Você tentou editar uma estrutura que não existe no sistema!", WS_INFOR);
        endif;

        $delEst = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
        if ($delEst):
            require ('_models/AdminEstruturas.class.php');
            $deletar = new AdminEstrutura;
            $deletar->ExeDelete($delEst);
            
            WSErro($deletar->getError()[0], $deletar->getError()[1]);
        endif;


        $readEst = new Read;
        $readEst->ExeRead("estruturas", "WHERE estrutura_grupo IS NULL and estrutura_subgrupo IS NULL ORDER BY estrutura_descricao ASC");
        if (!$readEst->getResult()):

        else:
            foreach ($readEst->getResult() as $grupo):
                extract($grupo);

                $readEsts = new Read;
                $readEsts->ExeRead("estruturas", "WHERE estrutura_grupo = :parent and estrutura_subgrupo IS NULL", "parent={$estrutura_id}");

                $readSubs = new Read;
                $readSubs->ExeRead("estruturas", "WHERE estrutura_grupo = '".$estrutura_id."' and estrutura_subgrupo = '".$estrutura_grupo."'");

                $countGrupoEst = $readEsts->getRowCount();
                $countGrupoSub = $readSubs->getRowCount();
                ?>
                <section>

                    <header>
                        <h1><?= $estrutura_descricao; ?>  <span> ( <?= $countGrupoEst; ?> Grupos )( <?= $countGrupoSub; ?> Sub Grupos )</span></h1>
                        <p class="tagline"><?= $estrutura_apoio2; ?></p>

                        <ul class="info post_actions">
                            <li><strong>Valor </strong> <?= $estrutura_apoio1; ?></li>
                            <li><a class="act_view" target="_blank" href="../estruturas/<?= $estrutura_descricao; ?>" title="Ver no site">Ver no site</a></li>
                            <li><a class="act_edit" href="painel.php?exe=estruturas/update&catid=<?= $estrutura_id; ?>" title="Editar">Editar</a></li>
                            <li><a class="act_delete" href="painel.php?exe=estruturas/index&delete=<?= $estrutura_id; ?>" title="Excluir">Deletar</a></li>
                        </ul>
                    </header>
                
                    <h2>Grupos:</h2>
                    
                    <?php
                    $readGrupo = new Read;
                    $readGrupo->ExeRead("estruturas", "WHERE estrutura_grupo = :grupo","grupo={$estrutura_id}");
                    if (!$readGrupo->getResult()):

                    else:
                        $a = 0;
                        foreach ($readGrupo->getResult() as $sub):
                            $a++;

                            $readEstSub = new Read;
                            $readEstSub->ExeRead("estruturas", "WHERE estrutura_grupo = '".$estrutura_id."' and estrutura_subgrupo = '".$sub['estrutura_grupo']."'");
                            ?>
                            <article<?php if ($a % 3 == 0) echo ' class="right"'; ?>>
                                <h1><a target="_blank" href="../estruturas/<?= $sub['estrutura_apoio2']; ?>" title="Ver Grupo"><?= $sub['estrutura_descricao']; ?></a>  ( <?= $readEstSub->getRowCount(); ?> subgrupos )</h1>

                                <ul class="info post_actions">
                                    <li><a class="act_view" target="_blank" href="../estruturas/<?= $sub['estrutura_apoio2']; ?>" title="Ver no site">Ver no site</a></li>
                                    <li><a class="act_edit" href="painel.php?exe=estruturas/update&estid=<?= $sub['estrutura_id']; ?>" title="Editar">Editar</a></li>
                                    <li><a class="act_delete" href="painel.php?exe=estruturas/index&delete=<?= $sub['estrutura_id']; ?>" title="Excluir">Deletar</a></li>
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