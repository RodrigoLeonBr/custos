<div class="content list_content">

    <section class="form_pesquisao">
        <header>
            <h1>Lançamentos realziados de Consumo/Amoxarifado - Filtro dos ítens</h1>
        </header>
        
        <?php
/**
 * BUSCA PELAS VARÁVEIS DE ALMOXARIFADO
 */
        $search = "";
        $Where = "";
        $SAno = filter_input(INPUT_POST, 'ano', FILTER_DEFAULT);
        if (!empty($SAno)):
            $SAno = strip_tags(trim(urlencode($SAno)));
            $search.="&ano=" . $SAno;
            $Where=" Where Ano=:ano ";
        endif;        
        $Smes = filter_input(INPUT_POST, 'mes', FILTER_DEFAULT);
        if (!empty($Smes)):
            $Smes = strip_tags(trim(urlencode($Smes)));
            $search.="&mes=" . $Smes;
            $Where.=(empty($Where) ? "Where Mes=:mes " : " and Mes=:mes ");
        endif;
        $SCC = filter_input(INPUT_POST, 'cc', FILTER_DEFAULT);
        if (!empty($SCC)):
            $SCC = strip_tags(trim(urlencode($SCC)));
            $search.="&cc=" . $SCC;
            $Where.=(empty($Where) ? "Where Id_CentroCusto=:cc " : " and Id_CentroCusto=:cc ");
        endif;
        $SItemCC = filter_input(INPUT_POST, 'itemcc', FILTER_DEFAULT);
        if (!empty($SItemCC)):
            $SItemCC = strip_tags(trim(urlencode($SItemCC)));
            $search.="&itemcc=" . $SItemCC;
            $Where.=(empty($Where) ? "Where Id_ItemCC=:itemcc " : " and Id_CentroCusto=:itemcc ");
        endif;
        if (!empty($SAno) || !empty($Smes)):
            header('Location: painel.php?exe=auxiliares/indexalmoxarifado' . $search);
        endif;
        
/**
 * Formulário de Busca de Ítens de Consumo/Almoxarifado
 */
        ?>
        <form name="search" action="" method="post" enctype="multipart/form-data">
            <label class="label_small">
                <span class="field"> Ano:</span>
                <select name="ano" >                    
                    <?php
                    $readAno = new Read;
                    $readAno->FullRead("SELECT DISTINCT Ano from c_movcusto");
                    foreach ($readAno->getResult() as $ano):
                        echo "<option value=\"{$ano['Ano']}\" ";
                        if (isset($data['ano']) && $data['ano'] == $ano['Ano']): echo 'selected';
                        endif;
                        echo "> {$ano['Ano']} </option>";
                    endforeach;
                    ?>                        
                </select>
                
                <span class="field"> Mês:</span>
                <select name="mes" >
                    <?php
                    $readMes = new Read;
                    $readMes->FullRead("SELECT DISTINCT Mes from c_movcusto");
                    foreach ($readMes->getResult() as $pmes):
                        echo "<option value=\"{$pmes['Mes']}\" ";
                        if ($Smes == $pmes['Mes']): echo 'selected';
                        endif;
                        echo "> {$pmes['Mes']} </option>";
                    endforeach;
                    ?>                        
                </select>
                
                <span class="field">Centro de Custo:</span>
                <select name="cc" >
                    <option value="" selected> Selecione Setor </option>
                    <?php
                    $readCC = new Read;
                    $readCC->FullRead("SELECT DISTINCT idCentroCusto, DescCentroCusto from c_tabcentrocusto order by idCentroCusto");
                    foreach ($readCC->getResult() as $pcc):
                        echo "<option value=\"{$pcc['idCentroCusto']}\" ";                        
                        echo "> {$pcc['DescCentroCusto']} </option>";
                    endforeach;
                    ?>                        
                </select>

                <span class="field">Ítem de Movimento:</span>
                <select name="itemcc" >
                    <option value="" selected> Selecione Ítem de Movimento </option>
                    <?php
                    $readItemCC = new Read;
                    $readItemCC->FullRead("SELECT DISTINCT idItemCC, DescItemCC, Ordem from c_tabitemcc where id_GrupoItemCC=2 order by Ordem");
                    foreach ($readItemCC->getResult() as $pitemcc):
                        echo "<option value=\"{$pitemcc['idItemCC']}\" ";                        
                        echo "> {$pitemcc['DescItemCC']} </option>";
                    endforeach;
                    ?>                        
                </select>
            </label>         
            <input type="submit" class="btn blue" name="sendsearch" value="Pesq"/>            
        </form>

        <?php
        $action = filter_input(INPUT_GET, 'action', FILTER_DEFAULT);
        require ('_models/AdminAlmoxarifado.class.php');
        $relAlmoxAction = new AdminAlmoxarifado();

        $search = "";
        $Where = "";
        $SAno = filter_input(INPUT_POST, 'ano', FILTER_DEFAULT);
        if (!empty($SAno)):
            $SAno = strip_tags(trim(urlencode($SAno)));
            $search.="&ano=" . $SAno;
            $Where=" Where Ano=:ano ";
        endif;        
        $Smes = filter_input(INPUT_POST, 'mes', FILTER_DEFAULT);
        if (!empty($Smes)):
            $Smes = strip_tags(trim(urlencode($Smes)));
            $search.="&mes=" . $Smes;
            $Where.=(empty($Where) ? "Where Mes=:mes " : " and Mes=:mes ");
        endif;
        $SCC = filter_input(INPUT_POST, 'cc', FILTER_DEFAULT);
        if (!empty($SCC)):
            $SCC = strip_tags(trim(urlencode($SCC)));
            $search.="&cc=" . $SCC;
            $Where.=(empty($Where) ? "Where Id_CentroCusto=:cc " : " and Id_CentroCusto=:cc ");
        endif;
        $SItemCC = filter_input(INPUT_POST, 'itemcc', FILTER_DEFAULT);
        if (!empty($SItemCC)):
            $SItemCC = strip_tags(trim(urlencode($SItemCC)));
            $search.="&itemcc=" . $SItemCC;
            $Where.=(empty($Where) ? "Where Id_ItemCC=:itemcc " : " and Id_CentroCusto=:itemcc ");
        endif;
        if (!empty($SAno) || !empty($Smes) || !empty($SCC)):
            header('Location: painel.php?exe=auxiliares/indexalmoxarifado' . $search);
        endif;
        
        $almoxi = 0;
        $getPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        $Pager = new Pager('painel.php?exe=auxiliares/indexalmoxarifado'.$search.'&page=');
        $Pager->ExePager($getPage, 50);

        $readAlmox = new Read;
                    
        if(!empty($SAno) && !empty($Smes) && !empty($SCC)):
            $Sql = "SELECT idConsumo, c_consumo.Ano Ano, c_consumo.Mes Mes, c_consumo.CC CC, c_consumo.DescCC DescCC, ";
            $Sql.= "c_consumo.Id_CentroCusto CentroCusto, c_tabcentrocusto.DescCentroCusto DescCentroCusto, ";
            $Sql.= "c_consumo.idItem Item, c_itemestoque.DescricaoItem DescItem, ";
            $Sql.= "c_consumo.id_ItemCC Movimento, c_tabitemcc.DescItemCC DescMovimento, ";
            $Sql.= "c_consumo.QtdConsumo Qtd, c_consumo.vlUnitConsumo VlUni, c_consumo.vlTotalConsumo VlTotal ";
            $Sql.= "FROM c_consumo ";
            $Sql.= "INNER JOIN c_tabcentrocusto on c_tabcentrocusto.idCentroCusto=c_consumo.Id_CentroCusto ";
            $Sql.= "INNER JOIN c_itemestoque on c_itemestoque.idItem=c_consumo.idItem ";
            $Sql.= "INNER JOIN c_tabitemcc on c_tabitemcc.idItemCC=c_consumo.id_ItemCC ";
            $Sql.= $Where;
            $Sql.= " LIMIT :limit OFFSET :offset ";
            
            
            $readAlmox->FullRead($Sql, "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}".$search);
            if ($readAlmox->getResult()):
                foreach ($readAlmox->getResult() as $almox):
                    $almoxi++;
                    extract($almox);
                    $status = (!$prestador_status ? 'style="background: #fffed8"' : '');
                    ?>
                    <article<?php if ($presti % 2 == 0) echo ' class="right"'; ?> <?= $status; ?>>
                        <header>
                            <hgroup>
                                <h1>Ano: <?= $Ano; ?> Mes: <?= $Mes; ?> Centro de Custo:<?=$DescCC; ?></h1>
                            </hgroup>
                        </header>
                        <ul class="info post_actions">
                            <li><a class="act_edit" href="painel.php?exe=prestadores/update&prest=<?= $idConsumo; ?>" title="Editar">Editar</a></li>
                            <li><a class="act_delete" href="painel.php?exe=prestadores/index&prest=<?= $idConsumo; ?>&action=delete" title="Excluir">Deletar</a></li>
                        </ul>
                    </article>
                    <?php
                endforeach;
            else:
                $Pager->ReturnPage();
                WSErro("Desculpe, ainda não existem prestadores cadastradas!", WS_INFOR);
            endif;
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