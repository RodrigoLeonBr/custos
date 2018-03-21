<div class="content cat_list">

    <section class="list_emp">

        <h1>Grupos:</h1>

        <?php
        $empty = filter_input(INPUT_GET, 'empty', FILTER_VALIDATE_BOOLEAN);
        if ($empty):
            WSErro("Você tentou editar um Grupo que não existe no sistema!", WS_INFOR);
        endif;

        $delGrupo = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
        if ($delGrupo):
            require ('_models/AdminGrupo.class.php');
            $deletar = new AdminGrupo;
            $deletar->ExeDelete($delGrupo);
            
            WSErro($deletar->getError()[0], $deletar->getError()[1]);
        endif;

        $readGrupo = new Read;
        $readGrupo->ExeRead("c_tabgrupocc","ORDER BY Ordem");
        
        $exp = filter_input(INPUT_GET, 'exp', FILTER_DEFAULT);        
        if ($exp):            
            // Nome do Arquivo do Excel que será gerado
            
            $arquivo = 'grupos.csv';
        
            $df=fopen("grupos.csv", 'w');
            fprintf($df, chr(0xEF).chr(0xBB).chr(0xBF));
            // Criamos uma tabela HTML com o formato da planilha para excel
            
            $tabela = array('Id','Nome','Ordem','Conteudo');
            fputcsv($df,$tabela);
            

            $resultados = $readGrupo;
            $resultados->ExeRead("c_tabgrupocc","ORDER BY Ordem");

            foreach ($resultados->getResult() as $result):
                fputcsv($df,$result);
            endforeach;
            fclose($df);                       
        
        endif;      

        
        if (!$readGrupo->getResult()):

        else:
            foreach ($readGrupo->getResult() as $grupo):
                extract($grupo);

                $readCC = new Read;
                $readCC->ExeRead("c_tabcentrocusto", "WHERE id_GrupoCC = :parent ORDER BY id_GrupoCC, idCentroCusto", "parent={$idGrupoCC}");

                $countGrupoCC = $readCC->getRowCount();
                ?>
                <section>

                    <header>
                        <h1><?= $DescGrupoCC; ?>  <span>( <?= $countGrupoCC; ?> Centro(s) de Custo(s) ) - Ordem: <?=$Ordem;?> </span></h1>
                        <p class="tagline"><?= $GrupoConteudo; ?></p>

                        <ul class="info post_actions">
                            <li><a class="act_edit" href="painel.php?exe=grupos/update&grupoid=<?= $idGrupoCC; ?>" title="Editar">Editar</a></li>
                            <li><a class="act_delete" href="painel.php?exe=grupos/index&delete=<?= $idGrupoCC; ?>" title="Excluir">Deletar</a></li>
                        </ul>
                    </header>

                    <h2>Centro(s) de Custo(s) do Grupo:</h2>

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
                                    <li><a class="act_edit" href="painel.php?exe=centrocustos/update&ccid=<?= $CC['idCentroCusto']; ?>" title="Editar">Editar</a></li>
                                    <li><a class="act_delete" href="painel.php?exe=centrocustos/index&delete=<?= $CC['idCentroCusto']; ?>" title="Excluir">Deletar</a></li>
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
        <br>
        <span><a class="btn green" href="painel.php?exe=grupos/index&exp=true" title="Exporta">Exporta Excel</a></span>
        
        <span><a class="btn green" href="grupos.csv" download="grupos.csv" title="Exporta">Baixar arquivo</a></span>
        
        <div class="clear"></div>
    </section>

    <div class="clear"></div>
</div> <!-- content home -->