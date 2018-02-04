<?php
if (!class_exists('Login')) :
    header('Location: ../../painel.php');
    die;
endif;
?>

<div class="content form_create">

    <article>

        <header>
            <h1>Atualizar Estrutura:</h1>
        </header>

        <?php
        $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $estid = filter_input(INPUT_GET, 'estid', FILTER_VALIDATE_INT);

        if (!empty($data['SendPostForm'])):
            unset($data['SendPostForm']);

            require('_models/AdminEstruturas.class.php');
            $cadastra = new AdminEstrutura;
            $cadastra->ExeUpdate($estid, $data);

            WSErro($cadastra->getError()[0], $cadastra->getError()[1]);
        else:
            $read = new Read;
            $read->ExeRead("c_estruturas", "WHERE id_estrutura = :id", "id={$estid}");
            if (!$read->getResult()):
                header('Location: painel.php?exe=estruturas/index&empty=true');
            else:
                $data = $read->getResult()[0];
            endif;
        endif;
        
        $checkCreate = filter_input(INPUT_GET, 'create', FILTER_VALIDATE_BOOLEAN);
        if($checkCreate && empty($cadastra)):
            $tipo = ( empty($data['estrutura_grupo']) ? 'seção' : 'categoria');
            WSErro("A {$tipo} <b>{$data['estrutura_descricao']}</b> foi cadastrada com sucesso no sistema! Continue atualizando a mesma!", WS_ACCEPT);
        endif;
        
        ?>

        <form name="PostForm" action="" method="post" enctype="multipart/form-data">


            <label class="label">
                <span class="field">Descricao:</span>
                <input type="text" name="estrutura_descricao" value="<?php if (isset($data)) echo $data['estrutura_descricao']; ?>" />
            </label>

            <label class="label">
                <span class="field">Apoio Texto:</span>
                <input type="text" name="estrutura_apoio2" value="<?php if (isset($data)) echo $data['estrutura_apoio2']; ?>" />
            </label>

            <div class="label_line">            
                <label class="label_small">
                    <span class="field">Apoio Valor:</span>
                    <input type="text" name="estrutura_apoio1" value="<?php if (isset($data)) echo $data['estrutura_apoio1']; ?>" />
                </label>
            
            
                <label class="label_small left">
                    <span class="field">Seção:</span>
                    <select name="estrutura_grupo">
                        <option value="null"> Selecione a Estrutura Principal: </option>
                        <?php
                        $readEst = new Read;
                        $readEst->ExeRead("c_estruturas", "WHERE estrutura_grupo IS NULL ORDER BY estrutura_descricao ASC");
                        if (!$readEst->getResult()):
                            echo '<option disabled="disabled" value="null"> Cadastre antes uma estrutura! </option>';
                        else:
                            foreach ($readEst->getResult() as $est):
                                echo "<option value=\"{$est['id_estrutura']}\" ";

                                if ($est['id_estrutura'] == $data['estrutura_grupo']):
                                    echo ' selected="selected" ';
                                endif;

                                echo "> {$est['estrutura_descricao']} </option>";
                            endforeach;
                        endif;
                        ?>
                    </select>
                </label>
            </div>

            <div class="gbform"></div>

            <input type="submit" class="btn blue" value="Atualizar Categoria" name="SendPostForm" />
        </form>

    </article>

    <div class="clear"></div>
</div> <!-- content home -->