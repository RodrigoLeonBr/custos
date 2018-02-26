<?php
if (!class_exists('Login')) :
    header('Location: ../../painel.php');
    die;
endif;
?>

<div class="content form_create">

    <article>

        <header>
            <h1>Atualizar SubGrupo:</h1>
        </header>

        <?php
        $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $subgrupoid = filter_input(INPUT_GET, 'subgrupoid', FILTER_VALIDATE_INT);

        if (!empty($data['SendPostForm'])):
            unset($data['SendPostForm']);

            require('_models/AdminSubGrupo.class.php');
            $cadastra = new AdminSubGrupo;
            $cadastra->ExeUpdate($subgrupoid, $data);

            WSErro($cadastra->getError()[0], $cadastra->getError()[1]);
        else:
            $read = new Read;
            $read->ExeRead("c_tabsubgrupocc", "WHERE idSubGrupoCC = :id", "id={$subgrupoid}");
            if (!$read->getResult()):
                header('Location: painel.php?exe=subgrupos/index&empty=true');
            else:
                $data = $read->getResult()[0];
            endif;
        endif;
        
        $checkCreate = filter_input(INPUT_GET, 'create', FILTER_VALIDATE_BOOLEAN);
        if($checkCreate && empty($cadastra)):
            WSErro("O SubGrupo <b>{$data['DescSubGrupoCC']}</b> foi cadastrado com sucesso no sistema! Continue atualizando o mesma!", WS_ACCEPT);
        endif;
        
        ?>

        <form name="PostForm" action="" method="post" enctype="multipart/form-data">
                <label class="label">
                    <span class="field">Nome:</span>
                    <input type="text" name="DescSubGrupoCC" value="<?php if (isset($data)) echo $data['DescSubGrupoCC']; ?>" />
                </label>

                <label class="label">
                    <span class="field">Conteúdo:</span>
                    <textarea name="SubGrupoConteudo" rows="5"><?php if (isset($data)) echo $data['SubGrupoConteudo']; ?></textarea>
                </label>

                <label class="label_small left">
                    <span class="field">Ordem de Exibição:</span>
                    <input type="text" name="Ordem" value="<?php if (isset($data)) echo $data['Ordem']; ?>" />
                </label>
            <br class="clear">
            <div class="gbform"></div>

            <input type="submit" class="btn blue" value="Atualizar SubGrupo" name="SendPostForm" />
        </form>

    </article>

    <div class="clear"></div>
</div> <!-- content home -->