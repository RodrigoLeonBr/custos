<div class="content home">

    <aside>
        <h1 class="boxtitle">Estatísticas da Secretaria:</h1>

        <article class="sitecontent boxaside">
            <h1 class="boxsubtitle">Conteúdo:</h1>

            <?php
            //OBJETO READ
            $read = new Read;

            //Ano Inicial de Lançamento
            $read->FullRead("SELECT MIN(Ano) AS Anoi FROM c_movcusto");
            $Anoi = $read->getResult()[0]['Anoi'];

            //Mes Inicial de Lançamento
            $read->FullRead("SELECT MIN(Mes) AS Mesi FROM c_movcusto Where Ano=".$Anoi);
            $Mesi = $read->getResult()[0]['Mesi'];

            //Ano Final de Lançamento
            $read->FullRead("SELECT MAX(Ano) AS Anof FROM c_movcusto");
            $Anof = $read->getResult()[0]['Anof'];

            //Mes Final de Lançamento
            $read->FullRead("SELECT MAX(Mes) AS Mesf FROM c_movcusto Where Ano=".$Anof);
            $Mesf = $read->getResult()[0]['Mesf'];

            //DESPESAS TOTAL
            $read->FullRead("SELECT SUM(Valor) AS Total FROM c_movcusto WHERE Ano=".$Anof." AND Mes=".$Mesf);
            $Total = $read->getResult()[0]['Total'];

            //FUNCIONÁRIOS TOTAL
            $read->FullRead("SELECT SUM(Valor) AS Total FROM c_lancestrutura WHERE Ano=".$Anof." AND Mes=".$Mesf." AND id_estrutura=5");
            $Funcionario = $read->getResult()[0]['Total'];

            //Ìtens Dispensados
            $read->FullRead("SELECT SUM(QtdConsumo) AS Total FROM c_consumo WHERE Ano=".$Anof." AND Mes=".$Mesf);
            $Itens = $read->getResult()[0]['Total'];
            
            ?>

            <ul>
                <li class="view"><span><?= $Anoi."/".str_pad($Mesi,2,'0',STR_PAD_LEFT); ?></span> Custo Inicial</li>
                <li class="user"><span><?= $Anof."/".str_pad($Mesf,2,'0',STR_PAD_LEFT); ?></span> Custo Final</li>
                <li class="page"><span><?= number_format($Total,2, ',','.'); ?></span> Custo Último mês</li>
                <li class="line"></li>
                <li class="post"><span><?= number_format($Funcionario,0, ',','.'); ?></span> Funcionários</li>
                <li class="emp"><span><?= number_format($Itens,0, ',','.'); ?></span> Ítens Dispensados</li>
                <!--<li class="comm"><span>38</span> comentários</li>-->
            </ul>
            <div class="clear"></div>
        </article>

        <article class="useragent boxaside">
            <h1 class="boxsubtitle">Navegador:</h1>

            <?php
            //LE O TOTAL DE VISITAS DOS NAVEGADORES
            $read->FullRead("SELECT SUM(agent_views) AS TotalViews FROM ws_siteviews_agent");
            $TotalViews = $read->getResult()[0]['TotalViews'];

            $read->ExeRead("ws_siteviews_agent", "ORDER BY agent_views DESC LIMIT 3");
            if (!$read->getResult()):
                WSErro("Oppsss, Ainda não existem estatísticas de navegadores!", WS_INFOR);
            else:
                echo "<ul>";
                foreach ($read->getResult() as $nav):
                    extract($nav);

                    //REALIZA PORCENTAGEM DE VISITAS POR NAVEGADOR!
                    $percent = substr(( $agent_views / $TotalViews ) * 100, 0, 5);
                    ?>
                    <li>
                        <p><strong><?= $agent_name; ?>:</strong> <?= $percent; ?>%</p>
                        <span style="width: <?= $percent; ?>%"></span>
                        <p><?= $agent_views; ?> visitas</p>
                    </li>
                    <?php
                endforeach;
                echo "</ul>";
            endif;
            ?>

            <div class="clear"></div>
        </article>
    </aside>

    <section class="content_statistics">

        <h1 class="boxtitle">Informações:</h1>

        <section>
            <h1 class="boxsubtitle">Artigos Recentes:</h1>

            <?php
            $read->ExeRead("ws_posts", "ORDER BY post_date DESC LIMIT 3");
            if ($read->getResult()):
                foreach ($read->getResult() as $re):
                    extract($re);
                    ?>
                    <article>

                        <div class="img thumb_small">
                            <?= Check::Image('../uploads/' . $post_cover, $post_title, 120, 70); ?>
                        </div>

                        <h1><a target="_blank" href="../artigo/<?= $post_name; ?>" title="Ver Post"><?= Check::Words($post_title, 10) ?></a></h1>
                        <ul class="info post_actions">
                            <li><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($post_date)); ?>Hs</li>
                            <li><a class="act_view" target="_blank" href="../artigo/<?= $post_name; ?>" title="Ver no site">Ver no site</a></li>
                            <li><a class="act_edit" href="painel.php?exe=posts/update&postid=<?= $post_id; ?>" title="Editar">Editar</a></li>

                            <?php if (!$post_status): ?>
                                <li><a class="act_inative" href="painel.php?exe=posts/index&post=<?= $post_id; ?>&action=active" title="Ativar">Ativar</a></li>
                            <?php else: ?>
                                <li><a class="act_ative" href="painel.php?exe=posts/index&post=<?= $post_id; ?>&action=inative" title="Inativar">Inativar</a></li>
                            <?php endif; ?>

                            <li><a class="act_delete" href="painel.php?exe=posts/index&post=<?= $post_id; ?>&action=delete" title="Excluir">Deletar</a></li>
                        </ul>

                    </article>
                    <?php
                endforeach;
            endif;
            ?>
        </section>          


        <section>
            <h1 class="boxsubtitle">Artigos Mais Vistos:</h1>

            <?php
            $read->ExeRead("ws_posts", "ORDER BY post_views DESC LIMIT 3");
            if ($read->getResult()):
                foreach ($read->getResult() as $re):
                    extract($re);
                    ?>
                    <article>

                        <div class="img thumb_small">
                            <?= Check::Image('../uploads/' . $post_cover, $post_title, 120, 70); ?>
                        </div>

                        <h1><a target="_blank" href="../artigo/<?= $post_name; ?>" title="Ver Post"><?= Check::Words($post_title, 10) ?></a></h1>
                        <ul class="info post_actions">
                            <li><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($post_date)); ?>Hs</li>
                            <li><a class="act_view" target="_blank" href="../artigo/<?= $post_name; ?>" title="Ver no site">Ver no site</a></li>
                            <li><a class="act_edit" href="painel.php?exe=posts/update&postid=<?= $post_id; ?>" title="Editar">Editar</a></li>

                            <?php if (!$post_status): ?>
                                <li><a class="act_inative" href="painel.php?exe=posts/index&post=<?= $post_id; ?>&action=active" title="Ativar">Ativar</a></li>
                            <?php else: ?>
                                <li><a class="act_ative" href="painel.php?exe=posts/index&post=<?= $post_id; ?>&action=inative" title="Inativar">Inativar</a></li>
                            <?php endif; ?>

                            <li><a class="act_delete" href="painel.php?exe=posts/index&post=<?= $post_id; ?>&action=delete" title="Excluir">Deletar</a></li>
                        </ul>

                    </article>
                    <?php
                endforeach;
            endif;
            ?>
        </section>                           

    </section> <!-- Estatísticas -->

    <div class="clear"></div>
</div> <!-- content home -->