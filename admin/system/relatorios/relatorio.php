<div class="content list_content">

    <section class="form_pesquisao">
        <header>
            <h1>Filtro de Relatório por Centro de Custo:</h1>
        </header>
        
        <?php
        
        $search = "";
        $SAno = filter_input(INPUT_POST, 'ano', FILTER_DEFAULT);
        if (!empty($SAno)):
            $SAno = strip_tags(trim(urlencode($SAno)));
            $search.="&ano=" . $SAno;
        endif;        
        $Smes = filter_input(INPUT_POST, 'mes', FILTER_DEFAULT);
        if (!empty($Smes)):
            $Smes = strip_tags(trim(urlencode($Smes)));
            $search.="&mes=" . $Smes;
        endif;
        $SCC = filter_input(INPUT_POST, 'cc', FILTER_DEFAULT);
        if (!empty($SCC)):
            $SCC = strip_tags(trim(urlencode($SCC)));
            $search.="&cc=" . $SCC;
        endif;
        if (!empty($SAno) || !empty($Smes)):
            header('Location: painel.php?exe=relatorios/relatorio' . $search);
        endif;
        ?>

        <form name="search" action="" method="post" enctype="multipart/form-data">
            <label class="label_small">
                <span class="field">Ano:</span>
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
            </label>         
            <input type="submit" class="btn blue" name="sendsearch" value="Pesq"/>            
        </form>

        <?php
        $action = filter_input(INPUT_GET, 'action', FILTER_DEFAULT);
        require ('_models/AdminLancCusto.class.php');
        $relcusAction = new AdminLancCusto();
        
        
        $SAno = filter_input(INPUT_GET, 'ano', FILTER_DEFAULT);
        if (!empty($SAno)):
            $SAno = strip_tags(trim(urlencode($SAno)));
            $search.="&ano=" . $SAno;            
        endif;
        $Smes = filter_input(INPUT_GET, 'mes', FILTER_DEFAULT);
        if (!empty($Smes)):
            $Smes = strip_tags(trim(urlencode($Smes)));
            $search.="&mes=" . $Smes;            
        endif;
        
        $SCC = filter_input(INPUT_GET, 'cc', FILTER_DEFAULT);
        if (!empty($SCC)):
            $SCC = strip_tags(trim(urlencode($SCC)));
            $search.="&cc=" . $SCC;
        endif;
        
        if(!empty($SAno) && !empty($Smes)):
            $readCustoTotal = new Read;
            $readCustoDireto = new Read;
            $readCusto = new Read;
          
            $readCusto->FullRead($relcusAction->ExeBuscaCusto("Tudo",$SAno, $Smes, NULL , NULL , $SCC));

            if ($readCusto->getResult()):
                /** CABEÇALHO*/

            ?>

            <div class='container'>
                <tbody>
                <table class="table table-condensed table-hover">                
                <tr><th colspan=9 style="line-height: 22px; font-size: 170%"><strong>Secretaria Municipal de Saúde - Americana/SP</strong></th></tr>
                <tr><th colspan=9 style="line-height: 22px; font-size: 150%"><strong>Prefeitura Municipal de Americana</strong></th></tr>
                <tr><th colspan=9 style="line-height: 22px; font-size: 150%"><strong>Estado de São Paulo</strong></th></tr>
                <tr><th colspan=9 style="line-height: 22px; font-size: 150%"><strong>Unidade de Planejamento</strong></th></tr>
                <tr><th colspan=9 style="line-height: 22px; font-size: 130%"><strong>Custos da Secretaria de Saúde - Custo por Setor</strong></th></tr>
                <tr><th>&nbsp;</th></tr>

                <?php
                $CentrodeCusto=0;
                $GrupoItemCusto=0;
                $ItemCC=0;
                $MesCusto=0;
                $DescItemCusto=0;
                $Mes=$Smes;
                switch ($Mes){
                    case 1:
                        $M1="Jan";
                        $M2="Fev";
                        $M3="Mar";
                        $M4="Abr";
                        break;
                    case 2:
                        $M1="Fev";
                        $M2="Mar";
                        $M3="Abr";
                        $M4="Mai";
                        break;
                    case 3:
                        $M1="Mar";
                        $M2="Abr";
                        $M3="Mai";
                        $M4="Jun";
                        break;
                    case 4:
                        $M1="Abr";
                        $M2="Mai";
                        $M3="Jun";
                        $M4="Jul";
                        break;
                    case 5:
                        $M1="Mai";
                        $M2="Jun";
                        $M3="Jul";
                        $M4="Ago";
                        break;
                    case 6:
                        $M1="Jun";
                        $M2="Jul";
                        $M3="Ago";
                        $M4="Set";
                        break;
                    case 7:
                        $M1="Jul";
                        $M2="Ago";
                        $M3="Set";
                        $M4="Out";
                        break;
                    case 8:
                        $M1="Ago";
                        $M2="Set";
                        $M3="Out";
                        $M4="Nov";
                        break;
                    case 9:
                        $M1="Set";
                        $M2="Out";
                        $M3="Nov";
                        $M4="Dez";
                        break;
                    case 10:
                        $M1="Out";
                        $M2="Nov";
                        $M3="Dez";
                        $M4=" ";
                        break;
                    case 11:
                        $M1="Nov";
                        $M2="Dez";
                        $M3=" ";
                        $M4=" ";
                        break;
                    case 12:
                        $M1="Dez";
                        $M2=" ";
                        $M3=" ";
                        $M4=" ";
                        break;
                    default:
                        $M1=" ";
                        $M2=" ";
                        $M3=" ";
                        $M4=" ";                    
                }

                  foreach ($readCusto->getResult() as $rel): 
                      /** TOTAL CENTRO DE CUSTO */
                      if($CentrodeCusto <> $rel["id_CentroCusto"]){

                        if($CentrodeCusto<>0){
                          echo "<tr>";
                              echo "<td align=left><b>TOTAL: </b></td>";
                              echo "<td align=right><b>".number_format($Rtot[0]["Mes1"],2, ',','.')."</b></td>";
                              if($Rtot[0]["Mes1"]>0 && $Rdir[0]["Mes1"]>0){
                                echo "<td align=right><b> ".number_format(($Rtot[0]["Mes1"]/$Rdir[0]["Mes1"]*100),2, ',','.')."</b></td>";
                              }else{
                                echo "<td align=right> ".number_format(0,2, ',','.')."</td>";
                              }
                              echo "<td align=right><b>".number_format($Rtot[0]["Mes2"],2, ',','.')."</b></td>";
                              if($Rtot[0]["Mes2"]>0 & $Rdir[0]["Mes2"]>0){
                                echo "<td align=right><b> ".number_format(($Rtot[0]["Mes2"]/$Rdir[0]["Mes2"]*100),2, ',','.')."</b></td>";
                              }else{
                                echo "<td align=right> ".number_format(0,2, ',','.')."</td>";
                              }
                              echo "<td align=right><b>".number_format($Rtot[0]["Mes3"],2, ',','.')."</b></td>";
                              if($Rtot[0]["Mes3"]>0 & $Rdir[0]["Mes3"]>0){
                                echo "<td align=right><b> ".number_format(($Rtot[0]["Mes3"]/$Rdir[0]["Mes3"]*100),2, ',','.')."</b></td>";
                              }else{
                                echo "<td align=right> ".number_format(0,2, ',','.')."</td>";
                              }
                              echo "<td align=right><b>".number_format($Rtot[0]["Mes4"],2, ',','.')."</b></td>";
                              if($Rtot[0]["Mes4"]>0 & $Rdir[0]["Mes4"]>0){
                                echo "<td align=right><b> ".number_format(($Rtot[0]["Mes4"]/$Rdir[0]["Mes4"]*100),2, ',','.')."</b></td>";
                              }else{
                                echo "<td align=right> ".number_format(0,2, ',','.')."</td>";
                              }
                          echo "</tr>";

                          echo "<tr>";
                              echo "<td align=left><b>TOTAL CUSTOS DIRETOS: </b></td>";                          
                              echo "<td align=right><b>".number_format($Rdir[0]["Mes1"],2, ',','.')."</b></td>";
                              echo "<td align=right></td>";
                              echo "<td align=right><b>".number_format($Rdir[0]["Mes2"],2, ',','.')."</b></td>";
                              echo "<td align=right></td>";
                              echo "<td align=right><b>".number_format($Rdir[0]["Mes3"],2, ',','.')."</b></td>";
                              echo "<td align=right></td>";
                              echo "<td align=right><b>".number_format($Rdir[0]["Mes4"],2, ',','.')."</b></td>";
                              echo "<td align=right></td>";
                          echo "</tr>";
                          echo "<tr><td colspan=9 style='border-top: 1px solid black;'>&nbsp;</td></tr>";

                        }
                        echo "<tr>";
                            echo "<td colspan=9 style='border-bottom: 1px solid black;'><strong>CENTRO DE CUSTO: ". $rel["id_CentroCusto"]." - ".$rel["DescCentroCusto"] ."</strong></td>";
                        echo "</tr>";

                        echo "<tr>";
                            echo "<td></td>";
                            echo "<td align=right><b>".$M1."/".$SAno."</b></td>";
                            echo "<td align=right></td>";
                            echo "<td align=right><b>".$M2."/".$SAno."</b></td>";
                            echo "<td align=right></td>";
                            echo "<td align=right><b>".$M3."/".$SAno."</b></td>";
                            echo "<td align=right></td>";
                            echo "<td align=right><b>".$M4."/".$SAno."</b></td>";
                            echo "<td align=right></td>";
                        echo "</tr>";

                        echo "<tr>";
                            echo "<td style='width: 30%'><b>CUSTOS DIRETOS </b></td>";
                            echo "<td style='width: 12%' align=right><b>Valor</b></td>";
                            echo "<td style='width: 5%' align=right><b>%</b></td>";
                            echo "<td style='width: 12%' align=right><b>Valor</b></td>";
                            echo "<td style='width: 5%' align=right><b>%</b></td>";
                            echo "<td style='width: 12%' align=right><b>Valor</b></td>";
                            echo "<td style='width: 5%' align=right><b>%</b></td>";
                            echo "<td style='width: 12%' align=right><b>Valor</b></td>";
                            echo "<td style='width: 5%' align=right><b>%</b></td>";
                        echo "</tr>";
                        echo "<tr><td>&nbsp;</td></tr>";

                        $readCustoTotal->FullRead($relcusAction->ExeBuscaCusto("TotalGrupo",$SAno, $Smes, $rel["id_GrupoItemCC"], $rel["id_CentroCusto"], $SCC));
                        $Rtot = $readCustoTotal->getResult();
                        
                        $readCustoDireto->FullRead($relcusAction->ExeBuscaCusto("TotalCentroCusto",$SAno, $Smes, $rel["id_GrupoItemCC"], $rel["id_CentroCusto"], $SCC));
                        $Rdir = $readCustoDireto->getResult();
                        $CentrodeCusto = $rel["id_CentroCusto"];                    
                      }

                      /** TOTAL GRUPO DE ITEM */
                      if($GrupoItemCusto <> $rel["id_GrupoItemCC"]){

                          if($GrupoItemCusto <>0){
                            echo "<tr>";
                                echo "<td align=left><b>TOTAL: </b></td>";
                                echo "<td align=right><b>".number_format($Rtot[0]["Mes1"],2, ',','.')."</b></td>";
                                if($Rtot[0]["Mes1"]>0 && $Rdir[0]["Mes1"]>0){
                                  echo "<td align=right><b> ".number_format(($Rtot[0]["Mes1"]/$Rdir[0]["Mes1"]*100),2, ',','.')."</b></td>";
                                }else{
                                  echo "<td align=right> ".number_format(0,2, ',','.')."</td>";
                                }
                                echo "<td align=right><b>".number_format($Rtot[0]["Mes2"],2, ',','.')."</b></td>";
                                if($Rtot[0]["Mes2"]>0 & $Rdir[0]["Mes2"]>0){
                                  echo "<td align=right><b> ".number_format(($Rtot[0]["Mes2"]/$Rdir[0]["Mes2"]*100),2, ',','.')."</b></td>";
                                }else{
                                  echo "<td align=right> ".number_format(0,2, ',','.')."</td>";
                                }
                                echo "<td align=right><b>".number_format($Rtot[0]["Mes3"],2, ',','.')."</b></td>";
                                if($Rtot[0]["Mes3"]>0 & $Rdir[0]["Mes3"]>0){
                                  echo "<td align=right><b> ".number_format(($Rtot[0]["Mes3"]/$Rdir[0]["Mes3"]*100),2, ',','.')."</b></td>";
                                }else{
                                  echo "<td align=right> ".number_format(0,2, ',','.')."</td>";
                                }
                                echo "<td align=right><b>".number_format($Rtot[0]["Mes4"],2, ',','.')."</b></td>";
                                if($Rtot[0]["Mes4"]>0 & $Rdir[0]["Mes4"]>0){
                                  echo "<td align=right><b> ".number_format(($Rtot[0]["Mes4"]/$Rdir[0]["Mes4"]*100),2, ',','.')."</b></td>";
                                }else{
                                  echo "<td align=right> ".number_format(0,2, ',','.')."</td>";
                                }
                            echo "</tr>";                      
                          }
                          echo "<tr>";
                          echo "<td><b>".$rel["DescGrupoItemCC"]."</b></td>";
                          echo "</tr>";

                          $readCustoTotal->FullRead($relcusAction->ExeBuscaCusto("TotalGrupo",$SAno, $Smes, $rel["id_GrupoItemCC"], $rel["id_CentroCusto"], $SCC));
                          $Rtot = $readCustoTotal->getResult();

                          $GrupoItemCusto=$rel["id_GrupoItemCC"];
                      }

                      echo "<tr>";

                        echo "<td>".$rel["DescItemCC"]."</td>";
                        echo "<td align=right>".number_format($rel["Mes1"],2, ',','.')."</td>";
                        if($rel["Mes1"]>0 & $Rtot[0]["Mes1"]>0){
                            echo "<td align=right> ".number_format(($rel["Mes1"]/$Rtot[0]["Mes1"]*100),2, ',','.')."</td>";
                        }else{
                            echo "<td align=right> ".number_format(0,2, ',','.')."</td>";
                        }                    
                        echo "<td align=right>".number_format($rel["Mes2"],2, ',','.')."</td>";
                        if(($rel["Mes2"]>0 and $Rtot[0]["Mes2"]>0)){
                            echo "<td align=right> ".number_format(($rel["Mes2"]/$Rtot[0]["Mes2"]*100),2, ',','.')."</td>";
                        }else{
                            echo "<td align=right> ".number_format(0,2, ',','.')."</td>";
                        }
                        echo "<td align=right>".number_format($rel["Mes3"],2, ',','.')."</td>";
                        if($rel["Mes3"]>0 & $Rtot[0]["Mes3"]>0){
                            echo "<td align=right> ".number_format(($rel["Mes3"]/$Rtot[0]["Mes3"]*100),2, ',','.')."</td>";
                        }else{
                            echo "<td align=right> ".number_format(0,2, ',','.')."</td>";
                        }
                        echo "<td align=right>".number_format($rel["Mes4"],2, ',','.')."</td>";
                        if($rel["Mes4"]>0 & $Rtot[0]["Mes4"]>0){
                            echo "<td align=right> ".number_format(($rel["Mes4"]/$Rtot[0]["Mes4"]*100),2, ',','.')."</td>";
                        }else{
                            echo "<td align=right> ".number_format(0,2, ',','.')."</td>";
                        }
                      echo "</tr>";


                      $CentrodeCusto = $rel["id_CentroCusto"];
                      $GrupoItemCusto = $rel["id_GrupoItemCC"];

                  endforeach;
                  /*
                   * Total e Total Custo Direto Final
                   */
                  echo "<tr>";
                     echo "<td align=left><b>TOTAL: </b></td>";
                     echo "<td align=right><b>".number_format($Rtot[0]["Mes1"],2, ',','.')."</b></td>";
                     if($Rtot[0]["Mes1"]>0 && $Rdir[0]["Mes1"]>0){
                       echo "<td align=right><b> ".number_format(($Rtot[0]["Mes1"]/$Rdir[0]["Mes1"]*100),2, ',','.')."</b></td>";
                     }else{
                       echo "<td align=right> ".number_format(0,2, ',','.')."</td>";
                     }
                     echo "<td align=right><b>".number_format($Rtot[0]["Mes2"],2, ',','.')."</b></td>";
                     if($Rtot[0]["Mes2"]>0 & $Rdir[0]["Mes2"]>0){
                       echo "<td align=right><b> ".number_format(($Rtot[0]["Mes2"]/$Rdir[0]["Mes2"]*100),2, ',','.')."</b></td>";
                     }else{
                       echo "<td align=right> ".number_format(0,2, ',','.')."</td>";
                     }
                     echo "<td align=right><b>".number_format($Rtot[0]["Mes3"],2, ',','.')."</b></td>";
                     if($Rtot[0]["Mes3"]>0 & $Rdir[0]["Mes3"]>0){
                       echo "<td align=right><b> ".number_format(($Rtot[0]["Mes3"]/$Rdir[0]["Mes3"]*100),2, ',','.')."</b></td>";
                     }else{
                       echo "<td align=right> ".number_format(0,2, ',','.')."</td>";
                     }
                     echo "<td align=right><b>".number_format($Rtot[0]["Mes4"],2, ',','.')."</b></td>";
                     if($Rtot[0]["Mes4"]>0 & $Rdir[0]["Mes4"]>0){
                       echo "<td align=right><b> ".number_format(($Rtot[0]["Mes4"]/$Rdir[0]["Mes4"]*100),2, ',','.')."</b></td>";
                     }else{
                       echo "<td align=right> ".number_format(0,2, ',','.')."</td>";
                     }
                 echo "</tr>";
                 echo "<tr>";
                     echo "<td align=left><b>TOTAL CUSTOS DIRETOS: </b></td>";                          
                     echo "<td align=right><b>".number_format($Rdir[0]["Mes1"],2, ',','.')."</b></td>";
                     echo "<td align=right></td>";
                     echo "<td align=right><b>".number_format($Rdir[0]["Mes2"],2, ',','.')."</b></td>";
                     echo "<td align=right></td>";
                     echo "<td align=right><b>".number_format($Rdir[0]["Mes3"],2, ',','.')."</b></td>";
                     echo "<td align=right></td>";
                     echo "<td align=right><b>".number_format($Rdir[0]["Mes4"],2, ',','.')."</b></td>";
                     echo "<td align=right></td>";
                 echo "</tr>";
                 

              endif;
        endif;
    ?>

        </div>        
        <div class="clear"></div>
    </section>

    <div class="clear"></div>
</div> <!-- content home -->