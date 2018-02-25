<div class="content list_content">

    <section class="form_pesquisao">
        <header>
            <h1>Lançamentos realizados de Folha de Pagamento - Filtro dos ítens</h1>
        </header>
        
        <?php
/**
 * BUSCA PELAS VARÁVEIS DE FOLHA DE PAGAMENTO
 */
        $empty = filter_input(INPUT_GET, 'empty', FILTER_VALIDATE_BOOLEAN);
        if ($empty):
            WSErro("Oppsss: Você tentou editar um ítem de folha de pagamento que não existe no sistema!", WS_INFOR);
        endif;

        $action = filter_input(INPUT_GET, 'action', FILTER_DEFAULT);
        if ($action):
            require ('_models/AdminFolha.class.php');

            $folhaAction = filter_input(INPUT_GET, 'folha', FILTER_VALIDATE_INT);
            $folhaUpdate = new AdminFolha();

            switch ($action):
                case 'delete':
                    $folhaUpdate->ExeDelete($folhaAction);
                    WSErro($folhaUpdate->getError()[0], $folhaUpdate->getError()[1]);
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
        if (!empty($SAno) || !empty($Smes) ):
            header('Location: painel.php?exe=auxiliares/indexfolha' . $search);
        endif;
        
/**
 * Formulário de Busca de Ítens de Folha de Pagamento
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
                    $readItemCC->FullRead("SELECT DISTINCT idItemCC, DescItemCC, Ordem from c_tabitemcc where id_GrupoItemCC=1 order by Ordem");
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
        
        $folhai = 0;
        $getPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        $Pager = new Pager('painel.php?exe=auxiliares/indexfolha'.$search.'&page=');
        $Pager->ExePager($getPage, 100);

        $readFolha = new Read;
                    
        if(!empty($SAno) && !empty($Smes) ):
            $Sql = "SELECT idFolha, c_folha.Ano Ano, c_folha.Mes Mes, c_folha.CentroCusto CC, ";
            $Sql.= "c_folha.Id_CentroCusto CentroCusto, c_tabcentrocusto.DescCentroCusto DescCentroCusto, ";
            $Sql.= "c_folha.Evento Evento, c_folha.Descricao DescEvento, ";
            $Sql.= "c_folha.id_ItemCC Movimento, c_tabitemcc.DescItemCC DescMovimento, ";
            $Sql.= "c_folha.Qtd Qtd, c_folha.Valor valor ";
            $Sql.= "FROM c_folha ";
            $Sql.= "INNER JOIN c_tabcentrocusto on c_tabcentrocusto.idCentroCusto=c_folha.Id_CentroCusto ";
            $Sql.= "INNER JOIN c_tabitemcc on c_tabitemcc.idItemCC=c_folha.id_ItemCC ";
            $Sql.= $Where;
            $Sql.= " ORDER BY c_folha.Ano, c_folha.Mes, c_folha.CentroCusto, c_folha.Id_CentroCusto, c_folha.id_ItemCC, c_folha.Evento ";
            $Sql.= " LIMIT :limit OFFSET :offset ";            
                     
            
            $readFolha->FullRead($Sql, "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}".$search);
            if ($readFolha->getResult()):
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
                        <th width=35% ><strong>Evento</strong></th>
                        <th width=3% ><strong>Qtd</strong></th>
                        <th width=5% ><strong>Valor</strong></th>
                        <th width=4.5% >Edição</th>
                    </tr>
                <?php    
                foreach ($readFolha->getResult() as $folha):
                    extract($folha);
                    
                    if(($Pano<>$Ano || $pmes<>$Mes || $pcc<>$CC) && $Sbqt<>0){
                        echo "<tr bgcolor='#FFFFFF' >";
                        echo "<td><strong>SubTotal</strong></td>";
                        echo "<td align=right><strong>".number_format($Sbqt,0, ',','.')."</strong></td>";
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
                        echo "<td align=right><strong>".number_format($Sbvl,2, ',','.')."</strong></td>";
                        echo "<td></td>";
                        echo "</tr>";                        
                        $Sbqt=0;
                        $Sbvl=0;
                    }                    
                    
                    if($Pano<>$Ano || $pmes<>$Mes || $pcc<>$CC){
                        echo "<tr bgcolor='#FFFFFF' >";
                           echo "<td colspan=4><strong>";
                           echo "Ano/Mes: ".$Ano."/".str_pad($Mes,2,'0',STR_PAD_LEFT)." - ".$CC." (".$CentroCusto."-".$DescCentroCusto.")";;
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
                    $Sbvl+=$valor;
                    $Tqt+=$Qtd;
                    $Tvl+=$valor;
                    
                    ?>
                    <tr bgcolor=<?= (($folhai++% 2)==0) ? "#ffffff": "#f2f2f2" ?>>
                        <td><?=$Evento;?>-<?=$DescEvento;?></td>
                        <td align=right><?=number_format($Qtd,0, ',','.')?></td>
                        <td align=right><?=number_format($valor,2, ',','.');?></td>
                        <td>
                            <a href="painel.php?exe=auxiliares/updatefolha&folha=<?= $idFolha; ?>" title="Editar">
                                <div class="icon"><div class="pencil"></div></div>
                            </a>
                            <a href="painel.php?exe=auxiliares/indexfolha&delete=<?= $idFolha; ?>" title="Deletar" class="user_dele">
                                <div class="icon"><div class="cross"></div></div>
                            </a>
                        </td>
                    </tr>
                    <?php
                    
                endforeach;
                
                echo "<tr bgcolor='#FFFFFF' >";
                echo "<td><strong>SubTotal</strong></td>";
                echo "<td align=right><strong>".number_format($Sbqt,0, ',','.')."</strong></td>";
                echo "<td align=right><strong>".number_format($Sbvl,2, ',','.')."</strong></td>";
                echo "<td></td>";
                echo "</tr>";
                $folhai++;
                
                echo "<tr bgcolor='#FFFFFF' >";
                echo "<td><strong>TOTAL</strong></td>";
                echo "<td align=right><strong>".number_format($Tqt,0, ',','.')."</strong></td>";
                echo "<td align=right><strong>".number_format($Tvl,2, ',','.')."</strong></td>";
                echo "<td></td>";
                echo "</tr>";
                
                echo "</table>";
            else:
                $Pager->ReturnPage();
                WSErro("Desculpe, ainda não existem ítens de folha Lançados!", WS_INFOR);
            endif;
        endif;
        ?>

        <div class="clear"></div>
    </section>


    <?php
            $Pager->ExePaginator("c_folha",$Where,$search);
            echo $Pager->getPaginator();    
    ?>

    <div class="clear"></div>
</div> <!-- content home -->