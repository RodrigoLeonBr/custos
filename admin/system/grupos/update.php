<?php
if (!class_exists('Login')) :
    header('Location: ../../painel.php');
    die;
endif;
?>

<div class="content form_create">

    <article>

        <header>
            <h1>Atualizar Grupo:</h1>
        </header>

        <?php
        $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $grupoid = filter_input(INPUT_GET, 'grupoid', FILTER_VALIDATE_INT);

        if (!empty($data['SendPostForm'])):
            unset($data['SendPostForm']);

            require('_models/AdminGrupo.class.php');
            $cadastra = new AdminGrupo;
            $cadastra->ExeUpdate($grupoid, $data);

            WSErro($cadastra->getError()[0], $cadastra->getError()[1]);
        else:
            $read = new Read;
            $read->ExeRead("c_tabgrupocc", "WHERE idGrupoCC = :id", "id={$grupoid}");
            if (!$read->getResult()):
                header('Location: painel.php?exe=grupos/index&empty=true');
            else:
                $data = $read->getResult()[0];
            endif;
        endif;
        
        $checkCreate = filter_input(INPUT_GET, 'create', FILTER_VALIDATE_BOOLEAN);
        if($checkCreate && empty($cadastra)):
            WSErro("O Grupo <b>{$data['DescGrupoCC']}</b> foi cadastrado com sucesso no sistema! Continue atualizando o mesma!", WS_ACCEPT);
        endif;
        
        ?>

        <form name="PostForm" action="" method="post" enctype="multipart/form-data">


            <label class="label">
                <span class="field">Nome:</span>
                <input type="text" name="DescGrupoCC" value="<?php if (isset($data)) echo $data['DescGrupoCC']; ?>" />
            </label>

            <label class="label">
                <span class="field">Conteúdo:</span>
                <textarea name="GrupoConteudo" rows="5"><?php if (isset($data)) echo $data['GrupoConteudo']; ?></textarea>
            </label>

            <label class="label_small left clear">
                <span class="field">Ordem de Exibição:</span>
                <input type="text" name="Ordem" value="<?php if (isset($data)) echo $data['Ordem']; ?>" />
            </label>
            <br class="clear">
            <div class="gbform"></div>

            <input type="submit" class="btn blue" value="Atualizar Grupo" name="SendPostForm" />
        </form>

    </article>

    <div class="clear"></div>
</div> <!-- content home -->