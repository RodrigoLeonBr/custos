<?php
require_once('./simplexlsx.class.php');

$planilha = null;
$linhas   = null;
$colunas  = null;
$arquivo = 'lib2.xlsx';

$read = new Read;
$read->ExeRead('c_importa_arquivos');
$arquivo = $read->getResult()[0];

if(!empty($arquivo) && file_exists($arquivo)):
        $planilha = new SimpleXLSX($arquivo);
        list($colunas, $linhas) = $planilha->dimension();
        
        echo '<table>';
        foreach($planilha->rows() as $chave => $valor):
            
            $centrocusto     = trim($valor[0]);
            $evento          = trim($valor[1]);
            $descricao       = trim($valor[2]);
            $ccusto          = trim($valor[3]);
            $qtde            = trim($valor[4]);
            $ccvalor           = trim($valor[5]);
            
            echo '<tr><td>'.implode('</td><td>', $valor ).'</td></tr>';
            
        endforeach;
        echo '</table>';
else:
        echo 'Arquivo não encontrado!';
        exit();
endif;

 


