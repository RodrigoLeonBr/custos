<?php

/**
 * AdminEmpresa.class [ MODEL ADMIN ]
 * Responável por gerenciar as empresas no admin do sistema!
 * 
 * @copyright (c) 2018, Rodrigo A. D. Leon Planejamento SMS Americana
 */
class AdminPrestador {

    private $Data;
    private $Prestador;
    private $Error;
    private $Result;

    //Nome da tabela no banco de dados
    const Entity = 'prestadores';

    /**
     * <b>Cadastrar o Prestador:</b> Envelope os dados do prestador em um array atribuitivo e execute esse método
     * para cadastrar a mesma no banco.
     * @param ARRAY $Data = Atribuitivo
     */
    public function ExeCreate(array $Data) {
        $this->Data = $Data;
        if (in_array('', $this->Data)):
            $this->Error = ["Erro ao Cadastrar: Para cadastrar um prestador, preencha todos os campos!", WS_ALERT];
            $this->Result = false;
        else:
            $this->setData();            
            $this->Create();
        endif;
    }

    /**
     * <b>Atualizar o Prestador:</b> Envelope os dados em uma array atribuitivo e informe o id de um prestador
     * para atualiza-la no banco de dados!
     * @param INT $PrestadorId = Id do Prestador
     * @param ARRAY $Data = Atribuitivo
     */
    public function ExeUpdate($PrestadorId, array $Data) {
        $this->Prestador = (int) $PrestadorId;
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
     * @param INT $PrestadorId = Id do prestador
     */
    public function ExeDelete($PrestadorId) {
        $this->Prestador = (int) $PrestadorId;

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
    public function ExeStatus($PrestadorId, $PrestadorStatus) {
        $this->Prestador = (int) $PrestadorId;
        $this->Data['prestador_status'] = (string) $PrestadorStatus;
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
        $this->Data['prestador_nome'] = Check::Name($this->Data['prestador_nome']);
    }

    //Verifica o NAME da empresa. Se existir adiciona um pós-fix +1
    private function setName() {
        $Where = ( isset($this->Prestador) ? "prestador_id != {$this->Prestador} AND" : '');

        $ReadName = new Read;
        $ReadName->ExeRead(self::Entity, "WHERE {$Where} prestador_nome = :t", "t={$this->Data['prestador_nome']}");
        if ($ReadName->getResult()):
            $this->Data['prestador_nome'] = $this->Data['prestador_nome'] . '-' . $ReadName->getRowCount();
        endif;
    }

    //Verifica e envia a capa da empresa para a pasta!
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
            $this->Error = ["O Prestador <b>{$this->Data['prestador_nome']}</b> foi cadastrada com sucesso no sistema!", WS_ACCEPT];
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

}
