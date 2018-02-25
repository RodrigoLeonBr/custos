<?php
if (!class_exists('Login')) :
    header('Location: ../../painel.php');
    die;
endif;
?>

<div class="content form_create">

    <article>

        <header>
            <h1>Atualizar Unidade:</h1>
        </header>

        <?php
        $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $unidid = filter_input(INPUT_GET, 'unidid', FILTER_VALIDATE_INT);

        if (!empty($data['SendPostForm'])):
            unset($data['SendPostForm']);

            require('_models/AdminUnidade.class.php');
            $cadastra = new AdminUnidade;
            $cadastra->ExeUpdate($unidid, $data);

            WSErro($cadastra->getError()[0], $cadastra->getError()[1]);
        else:
            $read = new Read;
            $read->ExeRead("c_tabunidade", "WHERE idUnidade = :id", "id={$unidid}");
            if (!$read->getResult()):
                header('Location: painel.php?exe=unidades/index&empty=true');
            else:
                $data = $read->getResult()[0];
            endif;
        endif;
        
        $checkCreate = filter_input(INPUT_GET, 'create', FILTER_VALIDATE_BOOLEAN);
        if($checkCreate && empty($cadastra)):
            WSErro("A Unidade <b>{$data['UnDescricao']}</b> foi cadastrada com sucesso no sistema! Continue atualizando a mesma!", WS_ACCEPT);
        endif;
        
        ?>

        <form name="PostForm" action="" method="post" enctype="multipart/form-data">


            <label class="label">
                <span class="field">Nome:</span>
                <input type="text" name="UnDescricao" value="<?php if (isset($data)) echo $data['UnDescricao']; ?>" />
            </label>

            <label class="label">
                <span class="field">Conteúdo:</span>
                <textarea name="UnConteudo" rows="5"><?php if (isset($data)) echo $data['UnConteudo']; ?></textarea>
            </label>

            <div class="gbform"></div>

            <input type="submit" class="btn blue" value="Atualizar Unidade" name="SendPostForm" />
        </form>

    </article>

    <div class="clear"></div>
</div> <!-- content home -->