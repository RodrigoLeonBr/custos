<?php

/**
 * AdminContrato.class [ MODEL ADMIN ]
 * Responável por gerenciar os Contratos do sistema no admin!
 * 
 * @copyright (c) 2018, Rodrigo A. D. Leon Planejamento SMS Americana
 */
class AdminContrato {

    private $Data;
    private $CId;
    private $Error;
    private $Result;

    //Nome da tabela no banco de dados!
    const Entity = 'c_contrato';

    /**
     * <b>Cadastrar Contrato:</b> Envelope Descriçao,  em um array atribuitivo e execute esse método
     * para cadastrar o Contrato.
     * @param ARRAY $Data = Atribuitivo
     */
    public function ExeCreate(array $Data) {
        $this->Data = $Data;

        if (in_array('', $this->Data) && !$this->checkContrato() && !$this->checkprotocolo()):
            $this->Result = false;
            $this->Error = ['<b>Erro ao cadastrar:</b> Para cadastrar um contrato, preencha todos os campos!', WS_ALERT];
        else:
            $this->setData();
            if ($this->Data['importa_arquivo']):
                $upload = new Upload;
                $upload->Excel($this->Data['importa_arquivo'], $this->Data['importa_tabela']);
            endif;

            $this->Create();
        endif;
    }

    /**
     * <b>Atualizar Contrato:</b> Envelope os dados em uma array atribuitivo e informe o id para atualiza-lo!
     * @param INT $CId = Id da grupo
     * @param ARRAY $Data = Atribuitivo
     */
    public function ExeUpdate($CId, array $Data) {
        $this->CId = (int) $CId;
        $this->Data = $Data;

        if (in_array('', $this->Data) && !$this->checkContrato() && !$this->checkprotocolo()):
            $this->Result = false;
            $this->Error = ["<b>Erro ao atualizar:</b> Para atualizar o contrato {$this->Data['contrato_prestador']}, preencha todos os campos!", WS_ALERT];
        else:
            $this->setData();
            $this->Update();
        endif;
    }

    /**
     * <b>Deleta contrato:</b> Informe o ID de um contrato para remove-lo do sistema. Esse método verifica
     * o contrato e se é permitido excluir de acordo com os registros do sistema!
     * @param INT $CId = Id do contrato
     */
    public function ExeDelete($CId) {
        $this->CId = (int) $CId;

        $read = new Read;        
        $read->ExeRead(self::Entity, "WHERE id_contrato = :delid", "delid={$this->CId}");        

        if (!$read->getResult()):
            $this->Result = false;
            $this->Error = ['Oppsss, você tentou remover um contrato que não existe no sistema!', WS_INFOR];
        else:
            extract($read->getResult()[0]);
            if (!$this->checkContrato()):
                $this->Result = false;
                $this->Error = ["O <b>Contrato da(o) {$contrato_prestador}</b> possui lançamentos cadastrados. Para deletar, antes altere ou remova os lançamentos do contrato!", WS_ALERT];
            else:
                $delete = new Delete;
                $delete->ExeDelete(self::Entity, "WHERE id_contrato = :deletaid", "deletaid={$this->CId}");

                $this->Result = true;
                $this->Error = ["O <b> contrao da(o) {$contrato_prestador}</b> foi removido com sucesso do sistema!", WS_ACCEPT];
            endif;
        endif;
    }

    /**
     * <b>Verificar Cadastro:</b> Retorna TRUE se o cadastro ou update for efetuado ou FALSE se não. Para verificar
     * erros execute um getError();
     * @return BOOL $Var = True or False
     */
    public function getResult() {
        return $this->Result;
    }

    /**
     * <b>Obter Erro:</b> Retorna um array associativo com a mensagem e o tipo de erro!
     * @return ARRAY $Error = Array associatico com o erro
     */
    public function getError() {
        return $this->Error;
    }
    
    /**
     * <b>Ativa/Inativa Contrato:</b> Informe o ID do Contrato e o status e um status sendo 1 para ativo e 0 para
     * rascunho. Esse méto ativa e inativa os Contratos!
     * @param INT $CId = Id do post
     * @param STRING $PostStatus = 1 para ativo, 0 para inativo
     */
    public function ExeStatus($CId, $CStatus) {
        $this->CId = (int) $CId;
        $this->Data['statusC'] = (string) $CStatus;
        $Update = new Update;
        $Update->ExeUpdate(self::Entity, $this->Data, "WHERE id_contrato = :id", "id={$this->CId}");
    }

    /*
     * ***************************************
     * **********  PRIVATE METHODS  **********
     * ***************************************
     */

    //Valida e cria os dados para realizar o cadastro
    private function setData() {
        $this->Data = array_map('strip_tags', $this->Data);
        
        $source = array('.', ',');
        $replace = array('', '.');
        $valor = str_replace($source, $replace, $this->Data['contrato_valor']);
        $this->Data['contrato_valor']=$valor;
        $valor = str_replace($source, $replace, $this->Data['contrato_qtd']);
        $this->Data['contrato_qtd']=$valor;
        
        $this->Data['contrato_data'] = Check::Data($this->Data['contrato_data']);        
        $this->Data['contrato_vencimento'] = Check::Data($this->Data['contrato_vencimento']);        
        $this->Data['contrato_protocolo'] = Check::Name($this->Data['contrato_protocolo']);
    }

    //Verifica o NOME do contrato. Se existir adiciona um pós-fix +1
    private function checkprotocolo() {
        $readName = new Read;
        $readName->ExeRead(self::Entity, "WHERE contrato_protocolo = :t", "t={$this->Data['contrato_protocolo']}");
        if ($readName->getResult()):
            return false;
        else:
            return true;
        endif;
    }

    //Verifica Contrato com lançamentos
    private function checkContrato() {
        $readC = new Read;
        $readC->FullRead("SELECT idcontrato FROM c_lanccontrato WHERE idcontrato = :parent", "parent={$this->CId}");
        if ($readC->getResult()):
            return false;
        else:
            return true;
        endif;
    }

    //Cadastra o contrato no banco!
    private function Create() {
        $Create = new Create;
        $Create->ExeCreate(self::Entity, $this->Data);
        if ($Create->getResult()):
            $this->Result = $Create->getResult();
            $this->Error = ["<b>Sucesso:</b> O contrato da(o) {$this->Data['contrato_prestador']} foi cadastrado no sistema!", WS_ACCEPT];
        endif;
    }

    //Atualiza SubContrato
    private function Update() {
        $readC = new Read;
        $readC->FullRead("SELECT contrato_valor, contrato_qtd FROM c_contrato WHERE id_contrato = :parent", "parent={$this->CId}");
        $cont = $readC->getResult();
        if ($readC->getResult()):
           if ($this->Data["contrato_valor"]<>$cont[0]["contrato_valor"]){
                $this->Data["contrato_saldovalor"]=$this->Data["contrato_saldovalor"]=$this->Data["contrato_valor"]-$cont[0]["contrato_valor"];
           }
           if ($this->Data["contrato_qtd"]<>$cont[0]["contrato_qtd"]){
                $this->Data["contrato_saldoqtd"]=$this->Data["contrato_saldoqtd"]=$this->Data["contrato_qtd"]-$cont[0]["contrato_qtd"];
           }
        endif;
        $Update = new Update;
        $Update->ExeUpdate(self::Entity, $this->Data, "WHERE id_contrato = :cid", "cid={$this->CId}");
        if ($Update->getResult()):            
            $this->Result = true;
            $this->Error = ["<b>Sucesso:</b> O contrato da(o) {$this->Data['contrato_prestador']} foi atualizado no sistema!", WS_ACCEPT];
        endif;
    }

    /**
     * <b>Enviar Galeria:</b> Envelope um $_FILES de um input multiple e envie junto a um postID para executar
     * o upload e o cadastro de galerias do artigo!
     * @param ARRAY $Files = Envie um $_FILES multiple
     * @param INT $PostId = Informe o ID do post
     */
    public function gbSend(array $Arquivos, $CId) {
        $this->CId = (int) $CId;
        $this->Data = $Arquivos;

        $ArquivoName = new Read;
        $ArquivoName->ExeRead(self::Entity, "WHERE id_contrato = :id", "id={$this->CId}");

        if (!$ArquivoName->getResult()):
            $this->Error = ["Erro ao enviar galeria. O índice {$this->CId} não foi encontrado no banco!", WS_ERROR];
            $this->Result = false;
        else:
            $ArquivoName = $ArquivoName->getResult()[0]['contrato_protocolo'];

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
                $ImgName = "{$ArquivoName}-gb-{$this->CId}-" . (substr(md5(time() + $i), 0, 5));
                $gbSend->Contrato($gbUpload, $ImgName, 'contrato');

                if ($gbSend->getResult()):
                    
                    $gbImage = $gbSend->getResult();
                    $gbCreate = ['idcontrato' => $this->CId, "gallery_arquivo" => $gbImage, "gallery_date" => date('Y-m-d H:i:s'), "gallery_name" =>$ArquivoName];
                    $insertGb = new Create;
                    $insertGb->ExeCreate("c_contrato_arquivos", $gbCreate);
                    $u++;
                endif;

            endforeach;

            if ($u > 1):
                $this->Error = ["Galeria Atualizada: Foram enviados {$u} arquivos para galeria deste contrato!", WS_ACCEPT];
                $this->Result = true;
            endif;
        endif;
    }

    /**
     * <b>Deletar Arquivo da galeria:</b> Informe apenas o id do contrato na galeria para que esse método leia e remova
     * o arquivo da pasta e delete o registro do banco!
     * @param INT $GbArquivoId = Id da imagem da galleria
     */
    public function gbRemove($GbArquivoId) {
        $this->CId = (int) $GbArquivoId;
        $readGb = new Read;
        $readGb->ExeRead("c_contrato_arquivos", "WHERE gallery_id = :gb", "gb={$this->CId}");
        if ($readGb->getResult()):

            $Arquivo = '../uploads/' . $readGb->getResult()[0]['gallery_arquivo'];

            if (file_exists($Arquivo) && !is_dir($Arquivo)):
                unlink($Arquivo);
            endif;

            $Deleta = new Delete;
            $Deleta->ExeDelete("c_contrato_arquivos", "WHERE gallery_id = :id", "id={$this->CId}");
            if ($Deleta->getResult()):
                $this->Error = ["O Arquivo foi removido com sucesso da galeria!", WS_ACCEPT];
                $this->Result = true;
            endif;

        endif;
    }
}