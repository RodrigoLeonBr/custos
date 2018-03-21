<div class="content form_create">

    <article>

        <header>
            <h1>Atualizar Centro de Custo:</h1>
        </header>

        <?php
        $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $ccid = filter_input(INPUT_GET, 'ccid', FILTER_VALIDATE_INT);

        if (!empty($data['SendPostForm'])):
            $data['StatusCC'] = ($data['SendPostForm'] == 'Atualizar' ? '0' : '1' );
            unset($data['SendPostForm']);

            require('_models/AdminCentroCusto.class.php');
            $cadastra = new AdminCentroCusto;
            $cadastra->ExeUpdate($ccid, $data);

            WSErro($cadastra->getError()[0], $cadastra->getError()[1]);

        else:
            $read = new Read;
            $read->ExeRead("c_tabcentrocusto", "WHERE idCentroCusto = :id", "id={$ccid}");
            if (!$read->getResult()):
                header('Location: painel.php?exe=centrocustos/index&empty=true');
            else:
                $data = $read->getResult()[0];                
            endif;
        endif;

        $checkCreate = filter_input(INPUT_GET, 'create', FILTER_VALIDATE_BOOLEAN);
        if ($checkCreate && empty($cadastra)):
            WSErro("O Centro de Custo <b>{$data['DescCentroCusto']}</b> foi cadastrado com sucesso no sistema!", WS_ACCEPT);
        endif;
        ?>


        <form name="PostForm" action="" method="post" enctype="multipart/form-data">

            <label class="label">
                <span class="field">Nome Centro de Custo:</span>
                <input type="text" name="DescCentroCusto" value="<?php if (isset($data['DescCentroCusto'])) echo $data['DescCentroCusto']; ?>" />
            </label>

            <div class="label_line">

                <label class="label_small">
                    <span class="field">Unidade:</span>
                    <select name="id_unidade">                        
                        <?php
                        $readUn = new Read;
                        $readUn->FullRead("SELECT idUnidade, UnDescricao FROM c_tabunidade");

                        if ($readUn->getRowCount() >= 1):
                            foreach ($readUn->getResult() as $unid):
                                echo "<option ";
                                if ($data['id_unidade'] == $unid['idUnidade']):
                                    echo "selected=\"selected\" ";
                                endif;
                                echo "value=\"{$unid['idUnidade']}\"> {$unid['UnDescricao']} </option>";
                            endforeach;
                        endif;
                        ?>
                    </select>
                </label>

                <label class="label_small">
                    <span class="field">Grupo:</span>
                    <select name="id_GrupoCC">                        
                        <?php
                        $readGrupo = new Read;
                        $readGrupo->FullRead("SELECT idGrupoCC, DescGrupoCC FROM c_tabgrupocc");

                        if ($readGrupo->getRowCount() >= 1):
                            foreach ($readGrupo->getResult() as $grupo):
                                echo "<option ";
                                if ($data['id_GrupoCC'] == $grupo['idGrupoCC']):
                                    echo "selected=\"selected\" ";
                                endif;
                                echo "value=\"{$grupo['idGrupoCC']}\"> {$grupo['DescGrupoCC']} </option>";
                            endforeach;
                        endif;
                        ?>
                    </select>
                </label>

                <label class="label_small">
                    <span class="field">SubGrupo:</span>
                    <select name="id_SubGrupoCC">                        
                        <?php
                        $readSubGrupo = new Read;
                        $readSubGrupo->FullRead("SELECT idSubGrupoCC, DescSubGrupoCC FROM c_tabsubgrupocc");

                        if ($readSubGrupo->getRowCount() >= 1):
                            foreach ($readSubGrupo->getResult() as $subgrupo):
                                echo "<option ";
                                if ($data['id_SubGrupoCC'] == $subgrupo['idSubGrupoCC']):
                                    echo "selected=\"selected\" ";
                                endif;
                                echo "value=\"{$subgrupo['idSubGrupoCC']}\"> {$subgrupo['DescSubGrupoCC']} </option>";
                            endforeach;
                        endif;
                        ?>
                    </select>
                </label>
            </div><!--/line-->
                
            <div class="label_line">    
                <label class="label_small">
                    <span class="field">Tipo de Unidade:</span>
                    <select name="TipoCC">
                        <?php
                        echo "<option ";
                        if($data['TipoCC'] == "P"):
                            echo "selected=\"selected\" ";
                        endif;
                        echo "value=\"P\"> Produção </option>";
                        
                        echo "<option ";
                        if($data['TipoCC'] == "A"):
                            echo "selected=\"selected\" ";
                        endif;
                        echo "value=\"A\"> Apoio </option>";
                        ?>
                    </select>                        
                </label>
            </div><!--/line-->
            

            <input type="submit" class="btn blue" value="Atualizar" name="SendPostForm" />
            <input type="submit" class="btn green" value="Atualizar & Publicar" name="SendPostForm" />

        </form>

    </article>

    <div class="clear"></div>
</div> <!-- content home -->