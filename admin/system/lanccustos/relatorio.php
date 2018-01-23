<div class="content list_content">

    <section class="rel_custo">

        <?php
        $empty = filter_input(INPUT_GET, 'empty', FILTER_VALIDATE_BOOLEAN);
        if ($empty):
            WSErro("Oppsss: Você tentou exibir um período que não possui lançamentos!", WS_INFOR);
        endif;

        $action = filter_input(INPUT_GET, 'action', FILTER_DEFAULT);
        if ($action):
            require ('_models/AdminLancCusto.class.php');

            $relcusAction = filter_input(INPUT_GET, 'custo', FILTER_VALIDATE_INT);
            $relcusAction = new AdminLancCusto();
            
            /*
             * 
            switch ($action):
                case 'itemcc':
                   $relTipo=$action;
                   break;
                default :
                    WSErro("Ação não foi identifica pelo sistema, favor utilize os botões!", WS_ALERT);
            endswitch;
             * 
             */
        endif;
        $SAno = filter_input(INPUT_GET, 'ano', FILTER_VALIDATE_INT);
        if(is_int($SAno)==FALSE):
            WSErro("Oppsss: Ano incorreto, utilize o formulário", WS_INFOR);
            header('Location: painel.php?exe=lanccustos/relatorio');
        endif;
        $Smes = filter_input(INPUT_GET, 'mes', FILTER_VALIDATE_INT);
        if(is_int($Smes)==FALSE):
            WSErro("Oppsss: Mês incorreto, utilize o formulário", WS_INFOR);
            header('Location: painel.php?exe=lanccustos/relatorio');
        endif;
            
            $readCustoTotal = new Read;
            $readCustoDireto = new Read;
            $readCusto = new Read;
 
            $sqlMes = "SELECT sum(case when mes=".$Smes." then c_movcusto.Valor else 0 end) Mes1, ";
            $sqlMes .="sum(case when mes=".($Smes+1)." then c_movcusto.Valor else 0 end) Mes2, ";
            $sqlMes .="sum(case when mes=".($Smes+2)." then c_movcusto.Valor else 0 end) Mes3, ";
            $sqlMes .= "sum(case when mes=".($Smes+3)." then c_movcusto.Valor else 0 end) Mes4 ";
            $sqlCustos = $sqlMes.", ";
            $sqlCustos .= "c_movcusto.id_CentroCusto, ";
            $sqlCustos .= "c_tabcentrocusto.DescCentroCusto, ";
            $sqlCustos .= "c_tabitemcc.id_GrupoItemCC, ";
            $sqlCustos .= "c_tabgrupoitemcc.DescGrupoItemCC, ";
            $sqlCustos .= "c_movcusto.id_ItemCC, ";
            $sqlCustos .= "c_tabitemcc.DescItemCC ";


            $sqlCustos .= "FROM c_movcusto ";
            $sqlCustos .= "INNER JOIN c_tabcentrocusto ON c_tabcentrocusto.idCentroCusto=c_movcusto.id_CentroCusto ";
            $sqlCustos .= "INNER JOIN c_tabitemcc ON c_tabitemcc.idItemCC=c_movcusto.id_ItemCC ";
            $sqlCustos .= "INNER JOIN c_tabgrupoitemcc ON c_tabgrupoitemcc.idGrupoItemCC=c_tabitemcc.id_GrupoItemCC ";
            $sqlCustos .= " WHERE Ano= ". strval($SAno);
            $sqlCustos .= " and mes >= ". strval($Smes);
            $sqlCustos .= " and mes<= ". strval($Smes+3);

            $sqlCustos .= " GROUP BY c_movcusto.id_CentroCusto, ";
            $sqlCustos .= "c_tabcentrocusto.DescCentroCusto, ";
            $sqlCustos .= "c_tabitemcc.id_GrupoItemCC, ";
            $sqlCustos .= "c_tabgrupoitemcc.DescGrupoItemCC, ";
            $sqlCustos .= "c_movcusto.id_ItemCC, ";
            $sqlCustos .= "c_tabitemcc.DescItemCC ";


            $sqlCustos .= "ORDER BY  c_movcusto.id_CentroCusto, ";
            $sqlCustos .= "c_tabitemcc.id_GrupoItemCC, ";
            $sqlCustos .= "c_movcusto.id_ItemCC ";	
            
            $readCusto->FullRead($sqlCustos);

            if ($readCusto->getResult()):
                /** CABEÇALHO*/

            ?>

            <div class='container'>
                <tbody>
                <div class="row text-center">
                    <div class="col-md-8">
                        <img src="brasao.png" width="50%">
                        <p style="line-height: 22px; font-size: 170%"><strong>Secretaria Municipal de Saúde - Americana/SP</strong></p>
                        <p style="line-height: 22px; font-size: 150%"><strong>Prefeitura Municipal de Americana</strong></p>
                        <p style="line-height: 22px; font-size: 150%"><strong>Estado de São Paulo</strong></p>
                        <p style="line-height: 22px; font-size: 150%"><strong>Unidade de Planejamento</strong></p>
                        <p style="line-height: 22px; font-size: 130%"><strong>Custos da Secretaria de Saúde - Custo por Setor</strong></p> 
                    </div>

                </div>

            <table class="table table-condensed table-hover">

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
                          echo "<tr><td>&nbsp;</td></tr>";

                        }
                        echo "<tr>";
                            echo "<td colspan=3><strong>Centro de Custo: ". $rel["id_CentroCusto"]." - ".$rel["DescCentroCusto"] ."</strong></td>";
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
                            echo "<td><b>CUSTOS DIRETOS </b></td>";
                            echo "<td align=right><b>Valor</b></td>";
                            echo "<td align=right><b>%</b></td>";
                            echo "<td align=right><b>Valor</b></td>";
                            echo "<td align=right><b>%</b></td>";
                            echo "<td align=right><b>Valor</b></td>";
                            echo "<td align=right><b>%</b></td>";
                            echo "<td align=right><b>Valor</b></td>";
                            echo "<td align=right><b>%</b></td>";
                        echo "</tr>";
                        echo "<tr><td>&nbsp;</td></tr>";

                        $SCustoTotal = $sqlMes;                        
                        $SCustoTotal .="FROM c_movcusto ";
                        $SCustoTotal .= "INNER JOIN c_tabitemcc ON c_tabitemcc.idItemCC=c_movcusto.id_ItemCC ";
                        $SCustoTotal .="WHERE Ano= ". strval($SAno);
                        $SCustoTotal .=" and mes >= ". strval($Smes);
                        $SCustoTotal .=" and mes<= ". strval($Smes+3);
                        $SCustoTotal .=" and id_CentroCusto=".strval($rel["id_CentroCusto"]);
                        $SCustoTotal .=" and id_GrupoItemCC=".strval($rel["id_GrupoItemCC"]);

                        $readCustoTotal->FullRead($SCustoTotal);
                        $Rtot = $readCustoTotal->getResult();

                        $SCustoDireto = $sqlMes;                        
                        $SCustoDireto .="FROM c_movcusto ";
                        $SCustoDireto .="WHERE Ano= ". strval($SAno);
                        $SCustoDireto .=" and mes >= ". strval($Smes);
                        $SCustoDireto .=" and mes<= ". strval($Smes+3);
                        $SCustoDireto .=" and id_CentroCusto=".strval($rel["id_CentroCusto"]);

                        $readCustoDireto->FullRead($SCustoDireto);
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

                          $SCustoTotal = $sqlMes;
                          $SCustoTotal .="FROM c_movcusto ";
                          $SCustoTotal .= "INNER JOIN c_tabitemcc ON c_tabitemcc.idItemCC=c_movcusto.id_ItemCC ";
                          $SCustoTotal .="WHERE Ano= ". strval($SAno);
                          $SCustoTotal .=" and mes >= ". strval($Smes);
                          $SCustoTotal .=" and mes<= ". strval($Smes+3);
                          $SCustoTotal .=" and id_CentroCusto=".strval($rel["id_CentroCusto"]);
                          $SCustoTotal .=" and id_GrupoItemCC=".strval($rel["id_GrupoItemCC"]);

                          $readCustoTotal->FullRead($SCustoTotal);
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
              endif;           
          ?>

        </div>        
        <div class="clear"></div>
    </section>

    <div class="clear"></div>
</div> <!-- content home -->