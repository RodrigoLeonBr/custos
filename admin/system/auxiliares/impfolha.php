<div class="content list_content">

    <section class="form_pesquisao">
        <header>
            <h1><strong>Importa Folha de Pagamento por Mês e Ano</strong></h1>
            <h3>Estrura por Campo: Ano, Mes, CentroCusto, Evento, Descricao, Quantidade, Valor</h3>
            <br>
        </header>
        <?php
            $folha = filter_input_array(INPUT_POST, FILTER_DEFAULT);
            if (isset($folha) && $folha['SendFolhaForm']):
                $folha['importa_arquivo'] = ( $_FILES['importa_arquivo']['tmp_name'] ? $_FILES['importa_arquivo'] : null );
                unset($folha['SendFolhaForm']);
                
                require('_models/AdminFolha.class.php');

                $cadastra = new AdminFolha;
                $cadastra->ExeCreate($folha);               
                
                if ($cadastra->getResult()):
                    echo $cadastra->getError()[0];
                    header('Location: /admin/system/importa/importafolha&folha='.$cadastra->getError()[0]);
                else:
                    WSErro($cadastra->getError()[0], $cadastra->getError()[1]);
                endif;
            endif;
        ?>        

        <form name="ImportaFolha" action="" method="post" enctype="multipart/form-data">
            
            <div class="label_line">
                <label class="label_small">
                    <span class="field">Nome Importacao Folha (Ano/Mes):</span>
                    <input type="text" name="importa_tabela" value="<?php if (isset($contrato['importa_tabela'])) echo $contrato['importa_tabela']; ?>" />
                </label>
            </div>    
            
            <div class="label gbform">
                <label class="label">
                    <span class="field">Enviar Folha de Pagamento:</span>
                    <input type="file" name="importa_arquivo" />
                </label>
            </div>

            <input type="submit" class="btn green" name="SendFolhaForm" value="Importa Tabela"/>            
        </form>
        
    </section>
    
</div>