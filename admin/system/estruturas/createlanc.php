<div class="content form_create">

    <article>

        <header>
            <h1>Lançamento de Custos:</h1>
        </header>

        <?php
        $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if ($data && $data['SendPostForm']):
            unset($data['SendPostForm']);
            require('_models/AdminLancCusto.class.php');
            $cadastra = new AdminLancCusto;
            $cadastra->ExeCreate($data);

            if (!$cadastra->getResult()):
                WSErro($cadastra->getError()[0], $cadastra->getError()[1]);
            else:
                WSErro($cadastra->getError()[0], $cadastra->getError()[1]);
//                header("Location:painel.php?exe=lanccustos/create");
            endif;
        endif;
        ?>

        <form name="PostForm" action="" method="post" enctype="multipart/form-data">

            <div class="label_line">
                <label class="label_small">
                    <span class="field">Ano:</span>
                    <input type="text" name="Ano" value="<?php if (isset($data['Ano'])) echo $data['Ano']; ?>" />
                </label>
                
                <label class="label_small left">
                    <span class="field">Mês:</span>
                    <input type="text" name="Mes" value="<?php if (isset($data['Mes'])) echo $data['Mes']; ?>" />
                </label>    
            </div><!--/Ano e Mês do Lançamento-->
            
            <div class="label_line">
                <label class="label_medium">
                    <span class="field">Centro de Custo:</span>
                    <select name="id_CentroCusto">
                        <option value="null"> Selecione Centro de Custo: </option>
                        <?php
                        $readCc = new Read;
                        $readCc->FullRead("SELECT idCentroCusto, DescCentroCusto FROM c_tabcentrocusto order by DescCentroCusto");
                        if (!$readCc->getResult()):
                            echo '<option disabled="disabled" value="null"> Cadastre antes uma seção! </option>';
                        else:
                            foreach ($readCc->getResult() as $cc):
                                echo "<option value=\"{$cc['idCentroCusto']}\" ";
                                echo "> {$cc['DescCentroCusto']} </option>";
                            endforeach;
                        endif;
                        ?>
                    </select>
                </label>

                <label class="label_medium">
                    <span class="field">Ítem de Lançamento:</span>
                    <select name="id_ItemCC">
                        <option value="null"> Selecione Ítem de Lançamento: </option>
                        <?php
                        $readIcc = new Read;
                        $readIcc->FullRead("SELECT idItemCC, DescItemCC FROM c_tabitemcc");
                        if (!$readIcc->getResult()):
                            echo '<option disabled="disabled" value="null"> Cadastre antes uma seção! </option>';
                        else:
                            foreach ($readIcc->getResult() as $icc):
                                echo "<option value=\"{$icc['idItemCC']}\" ";
                                echo "> {$icc['DescItemCC']} </option>";
                            endforeach;
                        endif;
                        ?>
                    </select>                    
                </label>                
            </div><!--/Centro de Custo e Ítem de Lançamento-->

            <div class="label_line">
                <label class="label_small">
                    <span class="field">Valor:</span>
                    <input type="text" name="Valor" value="<?php if (isset($data['Valor'])) echo $data['Valor']; ?>" />
                </label>
            </div><!--/Valor do Lançamento-->

            
            <input type="submit" class="btn blue" value="Cadastrar" name="SendPostForm" />
        </form>

    </article>

    <div class="clear"></div>
</div> <!-- content form- -->