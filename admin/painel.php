<?php
ob_start();
session_start();
require('../_app/Config.inc.php');

$login = new Login(3);
$logoff = filter_input(INPUT_GET, 'logoff', FILTER_VALIDATE_BOOLEAN);
$getexe = filter_input(INPUT_GET, 'exe', FILTER_DEFAULT);

if (!$login->CheckLogin()):
    unset($_SESSION['userlogin']);
    header('Location: index.php?exe=restrito');
else:
    $userlogin = $_SESSION['userlogin'];
endif;

if ($logoff):
    unset($_SESSION['userlogin']);
    header('Location: index.php?exe=logoff');
endif;
?>
<!DOCTYPE html>
<html lang="pt-br">

    <head>
        <meta charset="UTF-8">
        <title>Site Admin</title>
        <!--[if lt IE 9]>
            <script src="../_cdn/html5.js"></script> 
         <![endif]-->

        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,800' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/reset.css" />
        <link rel="stylesheet" href="css/admin.css" />   
    </head>

    <body class="painel">

        <header id="navadmin">
            <div class="content">

                <h1 class="logomarca">Pro Admin</h1>

                <ul class="systema_nav radius">
                    <li class="username">Olá <?= $userlogin['user_name']; ?> <?= $userlogin['user_lastname']; ?></li>
                    <li><a class="icon profile radius" href="painel.php?exe=users/profile">Perfíl</a></li>
                    <li><a class="icon users radius" href="painel.php?exe=users/users">Usuários</a></li>
                    <li><a class="icon logout radius" href="painel.php?logoff=true">Sair</a></li>
                </ul>

                <nav>
                    <h1><a href="painel.php" title="Dasboard">Principal</a></h1>

                    <?php
                    //ATIVA MENU
                    if (isset($getexe)):
                        $linkto = explode('/', $getexe);
                    else:
                        $linkto = array();
                    endif;
                    ?>

                    <ul class="nav">

                        <li class="li<?php if (in_array('lanccustos', $linkto)) echo ' active'; ?>"><a class="opensub" onclick="return false;" href="#">Custos</a>
                            <ul class="sub">
                                <li><a href="painel.php?exe=lanccustos/create">Lançar Custos</a></li>
                                <li><a href="painel.php?exe=lanccustos/index">Listar / Editar Lançamentos</a></li>
                                <li><a href="painel.php?exe=lanccustos/relatorio&ano=2017&mes=1">Relatorio por Centro de Custo</a></li>
                            </ul>
                        </li>                        

                        <li class="li<?php if (in_array('lanccustos', $linkto)) echo ' active'; ?>"><a class="opensub" onclick="return false;" href="#">Relatorios</a>
                            <ul class="sub">
                                <li><a href="painel.php?exe=lanccustos/relatorio&ano=2017&mes=7">Relatorio por Centro de Custo</a></li>
                            </ul>
                        </li>                        

                        <li class="li<?php if (in_array('prestadores', $linkto)) echo ' active'; ?>"><a class="opensub" onclick="return false;" href="#">Prestadores</a>
                            <ul class="sub">
                                <li><a href="painel.php?exe=prestadores/create">Cadastrar Prestadores</a></li>
                                <li><a href="painel.php?exe=prestadores/index">Listar / Editar Prestadores</a></li>
                            </ul>
                        </li>                        
                        
                        <li class="li<?php if (in_array('estruturas', $linkto)) echo ' active'; ?>"><a class="opensub" onclick="return false;" href="#">Estruturas</a>
                            <ul class="sub">
                                <li><a href="painel.php?exe=estruturas/create">Cadastrar Estruturas</a></li>
                                <li><a href="painel.php?exe=estruturas/index">Listar / Editar Estruturas</a></li>
                            </ul>
                        </li>                        

                    </ul>
                </nav>

                <div class="clear"></div>
            </div><!--/CONTENT-->
        </header>

        <div id="painel">
            <?php
            //QUERY STRING
            if (!empty($getexe)):
                $includepatch = __DIR__ . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . strip_tags(trim($getexe) . '.php');
            else:
                $includepatch = __DIR__ . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'home.php';
            endif;

            if (file_exists($includepatch)):
                require_once($includepatch);
            else:
                echo "<div class=\"content notfound\">";
                WSErro("<b>Erro ao incluir tela:</b> Erro ao incluir o controller /{$getexe}.php!", WS_ERROR);
                echo "</div>";
            endif;
            ?>
        </div> <!-- painel -->

        <footer class="main_footer">
            <a href="http://www.saudeamericana.com.br/campus" target="_blank" title="Secretaria de Saúde">&copy; Secretaria de Saúde de Americana - Todos os Direitos Reservados</a>
        </footer>

    </body>

    <script src="../_cdn/jquery.js"></script>
    <script src="../_cdn/jmask.js"></script>
    <script src="../_cdn/combo.js"></script>
    <script src="__jsc/tiny_mce/tiny_mce.js"></script>
    <script src="__jsc/tiny_mce/plugins/tinybrowser/tb_tinymce.js.php"></script>
    <script src="__jsc/admin.js"></script>
</html>
<?php
ob_end_flush();
