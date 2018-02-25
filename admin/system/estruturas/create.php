<?php
if (!class_exists('Login')) :
    header('Location: ../../painel.php');
    die;
endif;
?>

<div class="content form_create">

    <article>

        <header>
            <h1>Criar Estrutura:</h1>
        </header>

        <?php
        $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (!empty($data['SendPostForm'])):
            unset($data['SendPostForm']);

            require('_models/AdminEstruturas.class.php');
            $cadastra = new AdminEstrutura;
            $cadastra->ExeCreate($data);

            if (!$cadastra->getResult()):
                WSErro($cadastra->getError()[0], $cadastra->getError()[1]);
            else:
                header('Location: painel.php?exe=estruturas/update&create=true&estid=' . $cadastra->getResult());
            endif;
        endif;
        ?>

        <form name="PostForm" action="" method="post" enctype="multipart/form-data">


            <label class="label">
                <span class="field">Descrição:</span>
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
                                echo "<option value=\"{$est['estrutura']}\" ";

                                if ($est['idestrutura'] == $data['estrutura_grupo']):
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

            <input type="submit" class="btn green" value="Cadastrar Estrutura" name="SendPostForm" />
        </form>

    </article>

    <div class="clear"></div>
</div> <!-- content home -->