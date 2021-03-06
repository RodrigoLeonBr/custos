<?php
if (!class_exists('Login')) :
    header('Location: ../../painel.php');
    die;
endif;
?>

<div class="content form_create">

    <article>

        <header>
            <h1>Criar Grupo:</h1>
        </header>

        <?php
        $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (!empty($data['SendPostForm'])):
            unset($data['SendPostForm']);

            require('_models/AdminGrupo.class.php');
            $cadastra = new AdminGrupo;
            $cadastra->ExeCreate($data);

            if (!$cadastra->getResult()):
                WSErro($cadastra->getError()[0], $cadastra->getError()[1]);
            else:
                header('Location: painel.php?exe=grupos/update&create=true&grupoid=' . $cadastra->getResult());
            endif;
        endif;
        $maxOrdem = new Read();
        $maxOrdem->FullRead("SELECT MAX(Ordem) Max from c_tabgrupocc");
        $Max = $maxOrdem->getResult()[0];
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
                <input type="text" name="Ordem" value="<?php if (isset($data)) echo $data['Ordem']; else echo ($Max['Max']+1)?>" />
            </label>
            <br class="clear">
            <div class="gbform"></div>

            <input type="submit" class="btn green" value="Cadastrar Grupo" name="SendPostForm" />
        </form>

    </article>

    <div class="clear"></div>
</div> <!-- content home -->