<?php

/**
 * AdminEmpresa.class [ MODEL ADMIN ]
 * Responável por gerenciar as importaçãos no admin do sistema!
 * 
 * @copyright (c) 2018, Rodrigo A. D. Leon Planejamento SMS Americana
 */
class AdminFolha {

    private $Data;
    private $NomeArquivo;
    private $Error;
    private $Result;
    private $planilha = null;
    private $linhas   = null;
    private $colunas  = null;

    //Nome da tabela no banco de dados
    const Entity = 'c_importa_arquivos';

    /**
     * <b>Cadastrar o Prestador:</b> Envelope os dados do prestador em um array atribuitivo e execute esse método
     * para cadastrar a mesma no banco.
     * @param ARRAY $Data = Atribuitivo
     */
    public function ExeCreate(array $Data) {        
        require_once('_models/simplexlsx.class.php');
        
        $this->Data = $Data;
        if (in_array('', $this->Data)):
            $this->Error = ["Erro ao Cadastrar: Para cadastrar um prestador, preencha todos os campos!", WS_ALERT];
            $this->Result = false;
        else:
            $this->setData();
            if ($this->Data['importa_arquivo']):
                $upload = new Upload;
                $upload->Excel($this->Data['importa_arquivo'], $this->Data['importa_tabela']);
            endif;
            
            if (isset($upload) && $upload->getResult()):
                $this->Data['importa_arquivo'] = $upload->getResult();
                $this->Create();                
            else:
                $this->Data['importa_arquivo'] = null;
                $this->Create();
            endif;
        endif;
    }
    
    	/*
	 * <b>Importa Planilha para Folha de Pagamento:</b> Lê planilha importada 
         * testa as linhas e atualiza os custos
	 * @param $path - Caminho e nome da planilha do Excel xlsx
	 * @param $conexao - Instância da conexão PDO
	 */
	public function importaplanilha($path=null){

            if ( $xlsx = SimpleXLSX::parse($path) ) {
                $this->Error =  '<table>';
                foreach( $xlsx->rows() as $r ) {
                    $this->Error .=  '<tr><td>'.implode('</td><td>', $r ).'</td></tr>';
                }                
                $this->Error .= '</table>';
                $this->Result = TRUE;
            } else {
                $this->Error = ["Erro ao Importar provavel caminho errado ".$path, WS_ALERT];
                $this->Result = FALSE;
            }            
	}
        
	/*
	 * Método que retorna o valor do atributo $linhas
	 * @return Valor inteiro contendo a quantidade de linhas na planilha
	 */
	public function getQtdeLinhas(){
		return $this->linhas;
	}

	/*
	 * Método que retorna o valor do atributo $colunas
	 * @return Valor inteiro contendo a quantidade de colunas na planilha
	 */
	public function getQtdeColunas(){
		return $this->colunas;
	}


    /**
     * <b>Atualizar o Prestador:</b> Envelope os dados em uma array atribuitivo e informe o id de um prestador
     * para atualiza-la no banco de dados!
     * @param INT $NomeArquivo = Id do Prestador
     * @param ARRAY $Data = Atribuitivo
     */
    public function ExeUpdate($NomeArquivo, array $Data) {
        $this->Prestador = (int) $NomeArquivo;
        $this->Data = $Data;
        if (in_array('', $this->Data)):
            $this->Error = ["Erro ao Atualizar: Para atualizar <b>{$this->Data['prestador_nome']}</b>, preencha todos os campos!", WS_ALERT];
            $this->Result = false;
        else:
            $this->setName();
            $this->sendCapa();
            $this->Update();
        endif;
    }

    /**
     * <b>Deleta Prestadores:</b> Informe o ID do prestador a ser removida para que esse método realize uma
     * checagem excluinto todos os dados nessesários e removendo o prestador do banco!
     * @param INT $NomeArquivo = Id do prestador
     */
    public function ExeDelete($NomeArquivo) {
        $this->Prestador = (int) $NomeArquivo;

        $ReadPrest = new Read;
        $ReadPrest->ExeRead(self::Entity, "WHERE prestador_id = :prest", "prest={$this->Prestador}");
        if (!$ReadPrest->getResult()):
            $this->Error = ["O prestador que você tentou deletar não existe no sistema!", WS_ERROR];
            $this->Result = false;
        else:
            $PrestDelete = $ReadPrest->getResult()[0];
            if (file_exists('../uploads/' . $PrestDelete['prestador_capa']) && !is_dir('../uploads/' . $PrestDelete['prestador_capa'])):
                unlink('../uploads/' . $PrestDelete['prestador_capa']);
            endif;

            $deleta = new Delete;
            $deleta->ExeDelete(self::Entity, "WHERE prestador_id = :prest", "prest={$this->Prestador}");

            $this->Error = ["O prestador <b>{$PrestDelete['prestador_nome']}</b> foi removida com sucesso do sistema!", WS_ACCEPT];
            $this->Result = true;
        endif;
    }

    /**
     * <b>Ativa/Inativa Prestador:</b> Informe o ID do prestador e o status e um status sendo 1 para ativo e 0 para
     * rascunho. Esse méto ativa e inativa o prestador!
     * @param INT $PostId = Id do post
     * @param STRING $PostStatus = 1 para ativo, 0 para inativo
     */
    public function ExeStatus($NomeArquivo, $FolhaStatus) {
        $this->Prestador = (int) $NomeArquivo;
        $this->Data['prestador_status'] = (string) $FolhaStatus;
        $Update = new Update;
        $Update->ExeUpdate(self::Entity, $this->Data, "WHERE prestador_id = :id", "id={$this->Prestador}");
    }

    /**
     * <b>Verificar Ação:</b> Retorna TRUE se ação for efetuada ou FALSE se não. Para verificar erros
     * execute um getError();
     * @return BOOL $Var = True or False
     */
    public function getResult() {
        return $this->Result;
    }

    /**
     * <b>Obter Erro:</b> Retorna um array associativo com um erro e um tipo.
     * @return ARRAY $Error = Array associativo com o erro
     */
    public function getError() {
        return $this->Error;
    }

    /*
     * ***************************************
     * **********  PRIVATE METHODS  **********
     * ***************************************
     */

    //Valida e cria os dados para realizar o cadastro. Realiza Upload da Capa!
    private function setData() {
        $this->Data['importa_tabela'] = Check::Name($this->Data['importa_tabela']);
    }

    //Verifica o NAME da importação. Se existir adiciona um pós-fix +1
    private function setName() {
        $Where = ( isset($this->Prestador) ? "prestador_id != {$this->Prestador} AND" : '');

        $ReadName = new Read;
        $ReadName->ExeRead(self::Entity, "WHERE {$Where} prestador_nome = :t", "t={$this->Data['prestador_nome']}");
        if ($ReadName->getResult()):
            $this->Data['prestador_nome'] = $this->Data['prestador_nome'] . '-' . $ReadName->getRowCount();
        endif;
    }

    //Verifica e envia a capa da importação para a pasta!
    private function sendCapa() {
        if (!empty($this->Data['prestador_capa']['tmp_name'])):
            list($w, $h) = getimagesize($this->Data['prestador_capa']['tmp_name']);

            if ($w != '578' || $h != '288'):
                $this->Error = ['Capa Inválida: A Capa do prestador deve ter exatamente 578x288px do tipo .JPG, .PNG ou .GIF!', WS_INFOR];
                $this->Result = false;
            else:
                $this->checkCover();
                $Upload = new Upload;
                $Upload->Image($this->Data['prestador_capa'], $this->Data['prestador_nome'], 578, 'prestadores');

                if ($Upload->getError()):
                    $this->Error = $Upload->getError();
                    $this->Result = false;
                else:
                    $this->Data['prestador_capa'] = $Upload->getResult();
                    $this->Result = true;
                endif;
            endif;
        endif;
    }

    //Verifica se já existe uma capa, se sim deleta para enviar outra!
    private function checkCover() {
        $readCapa = new Read;
        $readCapa->FullRead("SELECT prestador_capa FROM prestadores WHERE prestador_id = :id", "id={$this->Prestador}");

        if ($readCapa->getRowCount()):
            $delCapa = $readCapa->getResult()[0]['prestador_capa'];
            if (file_exists("../uploads/{$delCapa}") && !is_dir("../uploads/{$delCapa}")):
                unlink("../uploads/{$delCapa}");
            endif;
        endif;
    }

    //Cadastra o prestador no banco!
    private function Create() {
        $Create = new Create;
        $Create->ExeCreate(self::Entity, $this->Data);
        if ($Create->getResult()):
            $this->Result = $Create->getResult();
            $this->Error = ["A importação de <b>{$this->Data['importa_tabela']}</b> foi cadastrada com sucesso no sistema!", WS_ACCEPT];
        endif;
    }

    //Atualiza o prestador no banco!
    private function Update() {
        $Update = new Update;
        $Update->ExeUpdate(self::Entity, $this->Data, "WHERE prestador_id = :id", "id={$this->Prestador}");
        if ($Update->getRowCount() >= 1):
            $this->Error = ["O Prestador <b>{$this->Data['prestador_nome']}</b> foi atualizada com sucesso!", WS_ACCEPT];
            $this->Result = true;
        endif;
    }

    /**
     * <b>Enviar Galeria:</b> Envelope um $_FILES de um input multiple e envie junto a um postID para executar
     * o upload e o cadastro de galerias do artigo!
     * @param ARRAY $Files = Envie um $_FILES multiple
     * @param INT $PostId = Informe o ID do post
     */
    public function gbSend(array $Excel, $NomeArquivo, $NomeTabela) {
        $this->FolhaId = (int) $NomeArquivo;
        $this->Data = $Excel;

        $ExcelName = new Read;
        $ExcelName->ExeRead(c_importa_arquivos, "WHERE importa_name = :name", "name={$NomeArquivo}");

        if ($ExcelName->getResult()):
            $this->Error = ["Erro ao enviar excel. O índice {$this->FolhaId} já foi importado no banco!", WS_ERROR];
            $this->Result = false;
        else:
            
            $gbFiles = array();
            $gbCount = count($this->Data['tmp_name']);
            $gbKeys = array_keys($this->Data);

            for ($gb = 0; $gb < $gbCount; $gb++):
                foreach ($gbKeys as $Keys):
                    $gbFiles[$gb][$Keys] = $this->Data[$Keys][$gb];
                endforeach;
            endfor;

            $gbSend = new Upload;
            $i = 0;
            $u = 0;

            foreach ($gbFiles as $gbUpload):
                $i++;
                $ExName = "{$ExcelName}-gb-" . (substr(md5(time() + $i), 0, 5));
                $gbSend->Excel($gbUpload, $ExName);

                if ($gbSend->getResult()):
                    $gbArquivo = $gbSend->getResult();
                    $gbCreate = ['importa_tabela' => $NomeTabela, "importa_arquivo" => $gbArquivo, "importa_date" => date('Y-m-d H:i:s'), "importa_name" => $NomeArquivo];
                    $insertGb = new Create;
                    $insertGb->ExeCreate("c_importa_arquivos", $gbCreate);
                    $u++;
                endif;

            endforeach;

            if ($u > 1):
                $this->Error = ["Arquivos Atualizados: Foram enviadas {$u} arquivos de excel para galeria!", WS_ACCEPT];
                $this->Result = true;
            endif;
        endif;
    }

    /**
     * <b>Deletar Imagem da galeria:</b> Informe apenas o id da imagem na galeria para que esse método leia e remova
     * a imagem da pasta e delete o registro do banco!
     * @param INT $GbImageId = Id da imagem da galleria
     */
    public function gbRemove($GbImageId) {
        $this->FolhaId = (int) $GbImageId;
        $readGb = new Read;
        $readGb->ExeRead("ws_posts_gallery", "WHERE gallery_id = :gb", "gb={$this->FolhaId}");
        if ($readGb->getResult()):

            $Imagem = '../uploads/' . $readGb->getResult()[0]['gallery_image'];

            if (file_exists($Imagem) && !is_dir($Imagem)):
                unlink($Imagem);
            endif;

            $Deleta = new Delete;
            $Deleta->ExeDelete("ws_posts_gallery", "WHERE gallery_id = :id", "id={$this->FolhaId}");
            if ($Deleta->getResult()):
                $this->Error = ["A imagem foi removida com sucesso da galeria!", WS_ACCEPT];
                $this->Result = true;
            endif;

        endif;
    }
    
}
