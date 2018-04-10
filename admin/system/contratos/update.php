<div class="content form_create">
    <script language="javascript"> 
        //-----------------------------------------------------
        //Funcao: MascaraMoeda
        //Sinopse: Mascara de preenchimento de moeda
        //Parametro:
        //   objTextBox : Objeto (TextBox)
        //   SeparadorMilesimo : Caracter separador de milésimos
        //   SeparadorDecimal : Caracter separador de decimais
        //   e : Evento
        //Retorno: Booleano
        //Autor: Gabriel Fróes - www.codigofonte.com.br
        //-----------------------------------------------------
        function MascaraMoeda(objTextBox, SeparadorMilesimo, SeparadorDecimal, e){
            var sep = 0;
            var key = '';
            var i = j = 0;
            var len = len2 = 0;
            var strCheck = '0123456789';
            var aux = aux2 = '';
            var whichCode = (window.Event) ? e.which : e.keyCode;
            if (whichCode == 13) return true;
            key = String.fromCharCode(whichCode); // Valor para o código da Chave
            if (strCheck.indexOf(key) == -1) return false; // Chave inválida
            len = objTextBox.value.length;
            for(i = 0; i < len; i++)
                if ((objTextBox.value.charAt(i) != '0') && (objTextBox.value.charAt(i) != SeparadorDecimal)) break;
            aux = '';
            for(; i < len; i++)
                if (strCheck.indexOf(objTextBox.value.charAt(i))!=-1) aux += objTextBox.value.charAt(i);
            aux += key;
            len = aux.length;
            if (len == 0) objTextBox.value = '';
            if (len == 1) objTextBox.value = '0'+ SeparadorDecimal + '0' + aux;
            if (len == 2) objTextBox.value = '0'+ SeparadorDecimal + aux;
            if (len > 2) {
                aux2 = '';
                for (j = 0, i = len - 3; i >= 0; i--) {
                    if (j == 3) {
                        aux2 += SeparadorMilesimo;
                        j = 0;
                    }
                    aux2 += aux.charAt(i);
                    j++;
                }
                objTextBox.value = '';
                len2 = aux2.length;
                for (i = len2 - 1; i >= 0; i--)
                objTextBox.value += aux2.charAt(i);
                objTextBox.value += SeparadorDecimal + aux.substr(len - 2, len);
            }
            return false;
        }       
    </script>    

    <article>

        <header>
            <h1>Atualizar Contrato:</h1>
        </header>

        <?php
        $contrato = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $cid = filter_input(INPUT_GET, 'cid', FILTER_VALIDATE_INT);

        if (isset($contrato) && $contrato['SendContratoForm']):
            $contrato['statusC'] = ($contrato['SendContratoForm'] == 'Atualizar' ? '0' : '1' );            
            unset($contrato['SendContratoForm']);

            require('_models/AdminContrato.class.php');
            $cadastra = new AdminContrato;
            $cadastra->ExeUpdate($cid, $contrato);

            WSErro($cadastra->getError()[0], $cadastra->getError()[1]);

            if (!empty($_FILES['gallery_arquivos']['tmp_name'])):
                $sendGallery = new AdminContrato;
                $sendGallery->gbSend($_FILES['gallery_arquivos'], $cid);
            endif;

        else:
            $read = new Read;
            $read->ExeRead("c_contrato", "WHERE id_contrato = :id", "id={$cid}");
            if (!$read->getResult()):
                header('Location: painel.php?exe=contratos/index&empty=true');
            else:
                $contrato = $read->getResult()[0];
                $contrato['contrato_data'] = date('d/m/Y', strtotime($contrato['contrato_data']));
                $contrato['contrato_vencimento'] = date('d/m/Y', strtotime($contrato['contrato_vencimento']));
                
                $contrato['contrato_valor']=number_format($contrato['contrato_valor'], 2, ',', '.');
                $contrato['contrato_qtd']=number_format($contrato['contrato_qtd'], 2, ',', '.');
            endif;
        endif;

        $checkCreate = filter_input(INPUT_GET, 'create', FILTER_VALIDATE_BOOLEAN);
        if ($checkCreate && empty($cadastra)):
            WSErro("O contrato <b>{$contrato['post_protocolo']}</b> foi cadastrado com sucesso no sistema!", WS_ACCEPT);
        endif;
        ?>


        <form name="ContratoForm" action="" method="post" enctype="multipart/form-data">

            <div class="label_line">
                <label class="label_small">
                    <span class="field">Protocolo:</span>
                    <input type="text" name="contrato_protocolo" value="<?php if (isset($contrato['contrato_protocolo'])) echo $contrato['contrato_protocolo']; ?>" />
                </label>

                <label class="label_small">
                    <span class="field">CNES:</span>
                    <input type="text" name="contrato_cnes" value="<?php if (isset($contrato['contrato_cnes'])) echo $contrato['contrato_cnes']; ?>" />
                </label>

                <label class="label_small">
                    <span class="field">CNPJ:</span>
                    <input type="text" name="contrato_cnpj" value="<?php if (isset($contrato['contrato_cnpj'])) echo $contrato['contrato_cnpj']; ?>" />
                </label>
            </div>

            <div class="label_line">     
                <label class="label">
                    <span class="field">Prestador:</span>
                    <input type="text" name="contrato_prestador" value="<?php if (isset($contrato['contrato_prestador'])) echo $contrato['contrato_prestador']; ?>" />
                </label>
                
                <label class="label">
                    <span class="field">Observação:</span>
                    <input type="text" name="contrato_obs" value="<?php if (isset($contrato['contrato_obs'])) echo $contrato['contrato_obs']; ?>" />
                </label>
                
                <label class="label">
                    <span class="field">Histórico:</span>
                    <textarea name="contrato_historico" rows="5"><?php if (isset($data)) echo $data['contrato_historico']; ?></textarea>
                </label>
            </div>

            <label class="label">
                <span class="field">Objeto do Contrato/Covênio:</span>
                <textarea name="contrato_objeto" rows="10"><?php if (isset($contrato['contrato_objeto'])) echo htmlspecialchars($contrato['contrato_objeto']); ?></textarea>
            </label>

            <div class="label_line">

                <label class="label_small left">
                    <span class="field">Data Contrato:</span>
                    <input type="text" class="formData center" name="contrato_data" value="<?php
                    if (isset($contrato['contrato_data'])): echo $contrato['contrato_data'];
                    else: echo date('d/m/Y');
                    endif;
                    ?>" />
                </label>

                <label class="label_small left">
                    <span class="field">Data Vencimento:</span>
                    <input type="text" class="formData center" name="contrato_vencimento" value="<?php
                    if (isset($contrato['contrato_vencimento'])): echo $contrato['contrato_vencimento'];
                    else: echo date('d/m/Y');
                    endif;
                    ?>" />
                </label>
            </div>

            <div class="label_line">

                <label class="label_small left">
                    <span class="field">Valor:</span>
                    <input type="text" name="contrato_valor" value="<?php if (isset($contrato['contrato_valor'])) echo $contrato['contrato_valor']; ?>" onkeypress="return(MascaraMoeda(this,'.',',',event))"/>
                </label>

                <label class="label_small left">
                    <span class="field">Quantidade:</span>
                    <input type="text" name="contrato_qtd" value="<?php if (isset($contrato['contrato_qtd'])) echo $contrato['contrato_qtd']; ?>" onkeypress="return(MascaraMoeda(this,'.',',',event))"/>
                </label>
                
            </div><!--/line-->

            <div class="label gbform" id="gbfoco">

                <label class="label">             
                    <span class="field">Enviar Arquivos:</span>
                    <input type="file" multiple name="gallery_arquivos[]" />
                </label>

                <?php
                $delGb = filter_input(INPUT_GET, 'gbdel', FILTER_VALIDATE_INT);
                if ($delGb):
                    require_once('_models/AdminContrato.class.php');
                    $DelGallery = new AdminContrato;
                    $DelGallery->gbRemove($delGb);

                    WSErro($DelGallery->getError()[0], $DelGallery->getError()[1]);

                endif;
                ?>

                <ul class="gallery">
                    <?php
                    $gbi = 0;
                    $Gallery = new Read;
                    $Gallery->ExeRead("c_contrato_arquivos", "WHERE idcontrato = :id", "id={$cid}");
                    if ($Gallery->getResult()):
                        foreach ($Gallery->getResult() as $gb):
                            $gbi++;
                            ?>
                            <li<?php if ($gbi % 5 == 0) echo ' class="right"'; ?>>
                                <div class="img thumb_small">
                                    <?= Check::Image('../uploads/' . $gb['gallery_image'], $gbi, 146, 100); ?>
                                </div>
                                <a href="painel.php?exe=contratos/update&cid=<?= $cid; ?>&gbdel=<?= $gb['gallery_id']; ?>#gbfoco" class="del">Deletar</a>
                            </li>
                            <?php
                        endforeach;
                    endif;
                    ?>
                </ul>                
            </div>

            <input type="submit" class="btn blue" value="Atualizar" name="SendContratoForm" />
            <input type="submit" class="btn green" value="Atualizar & Publicar" name="SendContratoForm" />

        </form>

    </article>

    <div class="clear"></div>
</div> <!-- content home -->