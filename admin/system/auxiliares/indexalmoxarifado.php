<div class="content list_content">

    <section class="form_pesquisao">
        <header>
            <h1>Lançamentos realizados de Consumo/Amoxarifado - Filtro dos ítens</h1>
        </header>
        
        <?php
/**
 * BUSCA PELAS VARÁVEIS DE ALMOXARIFADO
 */
        $empty = filter_input(INPUT_GET, 'empty', FILTER_VALIDATE_BOOLEAN);
        if ($empty):
            WSErro("Oppsss: Você tentou editar um prestador que não existe no sistema!", WS_INFOR);
        endif;

        $action = filter_input(INPUT_GET, 'action', FILTER_DEFAULT);
        if ($action):
            require ('_models/AdminAlmoxarifado.class.php');

            $almoxAction = filter_input(INPUT_GET, 'prest', FILTER_VALIDATE_INT);
            $almoxUpdate = new AdminAlmoxarifado();

            switch ($action):
                case 'delete':
                    $almoxUpdate->ExeDelete($almoxAction);
                    WSErro($almoxUpdate->getError()[0], $almoxUpdate->getError()[1]);
                    break;

                default :
                    WSErro("Ação não foi identifica pelo sistema, favor utilize os botões!", WS_ALERT);
            endswitch;
        endif;

            
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
            $Where.=(empty($Where) ? "Where Id_ItemCC=:itemcc " : " and Id_ItemCC=:itemcc ");
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
                    foreach ($readMes->getResult() as $mes):
                        echo "<option value=\"{$mes['Mes']}\" ";
                        if ($Smes == $mes['Mes']): echo 'selected';
                        endif;
                        echo "> {$mes['Mes']} </option>";
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
        $search = "";
        $Where = "";

        $SAno = filter_input(INPUT_GET, 'ano', FILTER_DEFAULT);
        if (!empty($SAno)):
            $SAno = strip_tags(trim(urlencode($SAno)));
            $search.="&ano=" . $SAno;
            $Where.=(empty($Where) ? "Where Ano=:ano " : " and Ano=:ano ");
        endif;
        $Smes = filter_input(INPUT_GET, 'mes', FILTER_DEFAULT);
        if (!empty($Smes)):
            $Smes = strip_tags(trim(urlencode($Smes)));
            $search.="&mes=" . $Smes;
            $Where.=(empty($Where) ? "Where Mes=:mes " : " and Mes=:mes ");
        endif;
        
        $SCC = filter_input(INPUT_GET, 'cc', FILTER_DEFAULT);
        if (!empty($SCC)):
            $SCC = strip_tags(trim(urlencode($SCC)));
            $search.="&cc=" . $SCC;
            $Where.=(empty($Where) ? "Where Id_CentroCusto=:cc " : " and Id_CentroCusto=:cc ");
        endif;
        
        $ItemCC = filter_input(INPUT_GET, 'itemcc', FILTER_DEFAULT);
        if (!empty($ItemCC)):
            $SCC = strip_tags(trim(urlencode($SCC)));
            $search.="&itemcc=" . $ItemCC;
            $Where.=(empty($Where) ? "Where Id_ItemCC=:itemcc " : " and Id_ItemCC=:itemcc ");
        endif;
        
        $almoxi = 0;
        $getPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        $Pager = new Pager('painel.php?exe=auxiliares/indexalmoxarifado'.$search.'&page=');
        $Pager->ExePager($getPage, 100);

        $readAlmox = new Read;
                    
        if(!empty($SAno) && !empty($Smes) ):
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
            $Sql.= " ORDER BY c_consumo.Ano, c_consumo.Mes, c_consumo.CC, c_consumo.Id_CentroCusto, c_consumo.id_ItemCC, c_itemestoque.DescricaoItem ";
            $Sql.= " LIMIT :limit OFFSET :offset ";            
                     
            
            $readAlmox->FullRead($Sql, "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}".$search);
            if ($readAlmox->getResult()):
                $Pano=0;
                $pmes=0;
                $pcc=0;
                $pmoc=0;
                $Sbqt=0;
                $Sbvl=0;
                $Tqt=0;
                $Tvl=0;
            
                ?>
            
                <table width="100%" border="0" cellspacing="1" cellpadding="0" bgcolor="#CCCCCC" >
                    <tr bgcolor="#FFFFFF">
                        <th width=35% ><strong>Ítem</strong></th>
                        <th width=3% ><strong>Qtd</strong></th>
                        <th width=4% ><strong>Vl Uni</strong></th>
                        <th width=5% ><strong>Vl Total</strong></th>
                        <th width=4.5% >Edição</th>
                    </tr>
                <?php    
                foreach ($readAlmox->getResult() as $almox):
                    extract($almox);
                    
                    if(($Pano<>$Ano || $pmes<>$Mes || $pcc<>$CC) && $Sbqt<>0){
                        echo "<tr bgcolor='#FFFFFF' >";
                        echo "<td><strong>SubTotal</strong></td>";
                        echo "<td align=right><strong>".number_format($Sbqt,0, ',','.')."</strong></td>";
                        echo "<td><strong></strong></td>";
                        echo "<td align=right><strong>".number_format($Sbvl,2, ',','.')."</strong></td>";
                        echo "<td></td>";
                        echo "</tr>";
                        
                        $Sbqt=0;
                        $Sbvl=0;
                    }
                    if($pmoc<>$Movimento && $Sbqt<>0){
                        echo "<tr bgcolor='#FFFFFF' >";
                        echo "<td><strong>SubTotal</strong></td>";
                        echo "<td align=right><strong>".number_format($Sbqt,0, ',','.')."</strong></td>";
                        echo "<td><strong></strong></td>";
                        echo "<td align=right><strong>".number_format($Sbvl,2, ',','.')."</strong></td>";
                        echo "<td></td>";
                        echo "</tr>";                        
                        $Sbqt=0;
                        $Sbvl=0;
                    }                    
                    if($Pano<>$Ano || $pmes<>$Mes || $pcc<>$CC){
                        echo "<tr bgcolor='#FFFFFF' >";
                           echo "<td colspan=5><strong>";
                           echo "Ano/Mes: ".$Ano."/".str_pad($Mes,2,'0',STR_PAD_LEFT)." - ".$DescCC." (".$CentroCusto."-".$DescCentroCusto.")";
                           echo "</strong></td>";
                        echo "</tr>";
                        $Pano=$Ano;
                        $pmes=$Mes;
                        $pcc=$CC;                        
                    }
                    if($pmoc<>$Movimento){
                        echo "<tr bgcolor='#FFFFFF' >";
                           echo "<td colspan=5><strong>";
                           echo "Lançamento: ".$Movimento." - ".$DescMovimento;
                           echo "</strong></td>";
                        echo "</tr>";
                        $pmoc=$Movimento;                        
                    }
                    $Sbqt+=$Qtd;
                    $Sbvl+=$VlTotal;
                    $Tqt+=$Qtd;
                    $Tvl+=$VlTotal;
                    
                    ?>
                    <tr bgcolor=<?= (($almoxi++% 2)==0) ? "#ffffff": "#f2f2f2" ?>>
                        <td><?=$Item;?>-<?=$DescItem;?></td>
                        <td align=right><?=number_format($Qtd,0, ',','.')?></td>
                        <td align=right><?=number_format($VlUni,2, ',','.');?></td>
                        <td align=right><?=number_format($VlTotal,2, ',','.');?></td>
                        <td>
                            <a href="painel.php?exe=auxiliares/updatealmoxarifado&almox=<?= $idConsumo; ?>" title="Editar">
                                <div class="icon"><div class="pencil"></div></div>
                            </a>
                            <a href="painel.php?exe=auxiliares/indexalmoxarifado&delete=<?= $idConsumo; ?>" title="Deletar" class="user_dele">
                                <div class="icon"><div class="cross"></div></div>
                            </a>
                        </td>
                    </tr>
                    <?php
                    
                endforeach;
                
                echo "<tr bgcolor='#FFFFFF' >";
                echo "<td><strong>SubTotal</strong></td>";
                echo "<td align=right><strong>".number_format($Sbqt,0, ',','.')."</strong></td>";
                echo "<td><strong></strong></td>";
                echo "<td align=right><strong>".number_format($Sbvl,2, ',','.')."</strong></td>";
                echo "<td></td>";
                echo "</tr>";
                $almoxi++;
                
                echo "<tr bgcolor='#FFFFFF' >";
                echo "<td><strong>TOTAL</strong></td>";
                echo "<td align=right><strong>".number_format($Tqt,0, ',','.')."</strong></td>";
                echo "<td><strong></strong></td>";
                echo "<td align=right><strong>".number_format($Tvl,2, ',','.')."</strong></td>";
                echo "<td></td>";
                echo "</tr>";
                
                echo "</table>";
            else:
                $Pager->ReturnPage();
                WSErro("Desculpe, ainda não existem Ítens de Consumo Lançado!", WS_INFOR);
            endif;
        endif;
        ?>

        <div class="clear"></div>
    </section>


    <?php
            $Pager->ExePaginator("c_consumo",$Where,$search);
            echo $Pager->getPaginator();    
    ?>

    <div class="clear"></div>
</div> <!-- content home -->