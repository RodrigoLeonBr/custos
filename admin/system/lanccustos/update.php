<div class="content form_create">

    <article>

        <header>
            <h1>Atualizar Prestador:</h1>
        </header>

        <?php
        $prestador = filter_input(INPUT_GET, 'prest', FILTER_VALIDATE_INT);
        $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if ($data && $data['SendPostForm']):
            $data['prestador_status'] = ($data['SendPostForm'] == 'Atualizar' ? '0' : '1');

            unset($data['SendPostForm']);
            require('_models/AdminPrestador.class.php');
            $cadastra = new AdminPrestador;
            $cadastra->ExeUpdate($prestador, $data);

            WSErro($cadastra->getError()[0], $cadastra->getError()[1]);
        else:
            $readPrest = new Read;
            $readPrest->ExeRead("prestadores", "WHERE prestador_id = :prest", "prest={$prestador}");
            if (!$readPrest->getResult()):
                header('Location: painel.php?exe=prestadores/index&empty=true');
            else:
                $data = $readPrest->getResult()[0];
            endif;
        endif;

        $checkCreate = filter_input(INPUT_GET, 'create', FILTER_VALIDATE_BOOLEAN);
        if ($checkCreate && empty($cadastra)):
            WSErro("O prestador <b>{$data['prestador_nome']}</b> foi cadastrada com sucesso no sistema!", WS_ACCEPT);
        endif;
        ?>

        <form name="PostForm" action="" method="post" enctype="multipart/form-data">

           <div class="label_line">
            <label class="label_small">
                <span class="field">CNES do Prestador:</span>
                <input type="text" name="prestador_cnes" value="<?php if (isset($data['prestador_cnes'])) echo $data['prestador_cnes']; ?>" />
            </label>
           </div>

            <label class="label">
                <span class="field">Nome do Prestador:</span>
                <input type="text" name="prestador_nome" value="<?php if (isset($data['prestador_nome'])) echo $data['prestador_nome']; ?>" />
            </label>
            
            <div class="label_line">

                <label class="label_small">
                    <span class="field">Tipo de Relatório:</span>
                    <select name="prestador_tipo">
                        <option value="" selected> Indique Tipo de Relatório </option>
                        <option value="P" <?php if (isset($data['prestador_tipo']) && $data['prestador_tipo'] == 'P') echo 'selected'; ?>> Particular/Individual </option>
                        <option value="U" <?php if (isset($data['prestador_tipo']) && $data['prestador_tipo'] == 'U') echo 'selected'; ?>> Unidade Básica </option>
                        <option value="M" <?php if (isset($data['prestador_tipo']) && $data['prestador_tipo'] == 'M') echo 'selected'; ?>> Hospital Municipal </option>                        
                    </select>
                </label>

                <label class="label_small">
                    <span class="field">Tipo de Organização:</span>
                    <select name="prestador_organizacao">
                        <option value="" selected> Indique Tipo de Organização </option>
                        <option value="M" <?php if (isset($data['prestador_organizacao']) && $data['prestador_organizacao'] == 'M') echo 'selected'; ?>> Municipal </option>
                        <option value="P" <?php if (isset($data['prestador_organizacao']) && $data['prestador_organizacao'] == 'P') echo 'selected'; ?>> Particular </option>
                        <option value="F" <?php if (isset($data['prestador_organizacao']) && $data['prestador_organizacao'] == 'F') echo 'selected'; ?>> Filantrópico </option>
                        <option value="E" <?php if (isset($data['prestador_organizacao']) && $data['prestador_organizacao'] == 'E') echo 'selected'; ?>> Estadual </option>
                    </select>                    
                </label>

                <label class="label_small">
                    <span class="field">Tipo de Relatório Quadrimestral:</span>
                    <select name="prestador_relatorio">
                        <option value="" selected> Indique Tipo de Relatório Quadrimestral </option>
                        <option value="Atenção Básica, Urgência e Emergência" <?php if (isset($data['prestador_relatorio']) && $data['prestador_relatorio'] == 'Atenção Básica, Urgência e Emergência') echo 'selected'; ?>> Atenção Básica, Urgência e Emergência </option>
                        <option value="Atenção Ambulatorial Especializada" <?php if (isset($data['prestador_relatorio']) && $data['prestador_relatorio'] == 'Atenção Ambulatorial Especializada') echo 'selected'; ?>> Atenção Ambulatorial Especializada </option>
                        <option value="Hospitalar" <?php if (isset($data['prestador_relatorio']) && $data['prestador_relatorio'] == 'Hospitalar') echo 'selected'; ?>> Hospitalar </option>
                        <option value="Atenção Psicossocial" <?php if (isset($data['prestador_relatorio']) && $data['prestador_relatorio'] == 'Atenção Psicossocial') echo 'selected'; ?>> Atenção Psicossocial </option>
                    </select>
                </label>
            </div><!--/tipo relatório-->            

            <div class="label_line">
            <label class="label_small">
                <span class="field">CNPJ:</span>
                <input type="text" name="prestador_cnpj" value="<?php if (isset($data['prestador_cnpj'])) echo $data['prestador_cnpj']; ?>" />
            </label>

            <label class="label_small">
                <span class="field">Área de Planejamento:</span>
                <input type="text" name="prestador_area" value="<?php if (isset($data['prestador_area'])) echo $data['prestador_area']; ?>" />
            </label>
            </div><!--/CNPJ - Área-->            

            <div class="gbform"></div>

            <input type="submit" class="btn blue" value="Atualizar" name="SendPostForm" />
            <input type="submit" class="btn green" value="Atualizar & Publicar" name="SendPostForm" />
        </form>

    </article>

    <div class="clear"></div>
</div> <!-- content form- -->