<?php
if (!class_exists('Login')) :
    header('Location: ../../painel.php');
    die;
endif;
?>

<div class="content form_create">

    <article>

        <header>
            <h1>Criar SubGrupo:</h1>
        </header>

        <?php
        $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (!empty($data['SendPostForm'])):
            unset($data['SendPostForm']);

            require('_models/AdminSubGrupo.class.php');
            $cadastra = new AdminSubGrupo;
            $cadastra->ExeCreate($data);           

            if (!$cadastra->getResult()):
                WSErro($cadastra->getError()[0], $cadastra->getError()[1]);
            else:
                header('Location: painel.php?exe=subgrupos/update&create=true&subgrupoid=' . $cadastra->getResult());
            endif;
        endif;
        $maxOrdem = new Read();
        $maxOrdem->FullRead("SELECT MAX(Ordem) Max from c_tabsubgrupocc");
        $Max = $maxOrdem->getResult()[0];
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
                <input type="text" name="Ordem" value="<?php if (isset($data)) echo $data['Ordem']; else echo ($Max['Max']+1)?>" />
            </label>
            <div class="clear"></div>
            <div class="gbform"></div>

            <input type="submit" class="btn green" value="Cadastrar SubGrupo" name="SendPostForm" />
        </form>

    </article>

    <div class="clear"></div>
</div> <!-- content home -->