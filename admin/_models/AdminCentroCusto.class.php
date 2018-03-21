<?php

/**
 * AdminCentroCusto.class [ MODEL ADMIN ]
 * Responável por gerenciar os Centros de Custos do sistema no admin!
 * 
 * @copyright (c) 2018, Rodrigo A. D. Leon Planejamento SMS Americana
 */
class AdminCentroCusto {

    private $Data;
    private $CCId;
    private $Error;
    private $Result;

    //Nome da tabela no banco de dados!
    const Entity = 'c_tabcentrocusto';

    /**
     * <b>Cadastrar Centro de Custo:</b> Envelope Descriçao,  em um array atribuitivo e execute esse método
     * para cadastrar o Centro de Custo.
     * @param ARRAY $Data = Atribuitivo
     */
    public function ExeCreate(array $Data) {
        $this->Data = $Data;

        if (in_array('', $this->Data) && !$this->checkUnids() && !$this->checkname()):
            $this->Result = false;
            $this->Error = ['<b>Erro ao cadastrar:</b> Para cadastrar um centro de custo, preencha todos os campos!', WS_ALERT];
        else:
            $this->setData();            
            $this->Create();
        endif;
    }

    /**
     * <b>Atualizar Centro de Custo:</b> Envelope os dados em uma array atribuitivo e informe o id de um
     * grupo para atualiza-lo!
     * @param INT $CCId = Id da grupo
     * @param ARRAY $Data = Atribuitivo
     */
    public function ExeUpdate($CCId, array $Data) {
        $this->CCId = (int) $CCId;
        $this->Data = $Data;

        if (in_array('', $this->Data) && !$this->checkUnids() && !$this->checkname()):
            $this->Result = false;
            $this->Error = ["<b>Erro ao atualizar:</b> Para atualizar o centro de custo {$this->Data['DescCentroCusto']}, preencha todos os campos!", WS_ALERT];
        else:
            $this->setData();
            $this->Update();
        endif;
    }

    /**
     * <b>Deleta centro de custo:</b> Informe o ID de um centro de custo para remove-lo do sistema. Esse método verifica
     * o centro de custo e se é permitido excluir de acordo com os registros do sistema!
     * @param INT $CCId = Id do centro de custo
     */
    public function ExeDelete($CCId) {
        $this->CCId = (int) $CCId;

        $read = new Read;        
        $read->ExeRead(self::Entity, "WHERE idCentroCusto = :delid", "delid={$this->CCId}");        

        if (!$read->getResult()):
            $this->Result = false;
            $this->Error = ['Oppsss, você tentou remover um centro de custo que não existe no sistema!', WS_INFOR];
        else:
            extract($read->getResult()[0]);
            if (!$this->checkUnids()):
                $this->Result = false;
                $this->Error = ["O <b>SubCentro de Custo {$DescCentroCusto}</b> possui lançamentos cadastrados. Para deletar, antes altere ou remova os centros de custo!", WS_ALERT];
            else:
                $delete = new Delete;
                $delete->ExeDelete(self::Entity, "WHERE idCentroCusto = :deletaid", "deletaid={$this->CCId}");

                $this->Result = true;
                $this->Error = ["O <b> {$DescCentroCusto}</b> foi removido com sucesso do sistema!", WS_ACCEPT];
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
     * <b>Ativa/Inativa Centro de Custo:</b> Informe o ID do Centro de Custo e o status e um status sendo 1 para ativo e 0 para
     * rascunho. Esse méto ativa e inativa os Centro de Custos!
     * @param INT $CCId = Id do post
     * @param STRING $PostStatus = 1 para ativo, 0 para inativo
     */
    public function ExeStatus($CCId, $CCStatus) {
        $this->CC = (int) $CCId;
        $this->Data['StatusCC'] = (string) $CCStatus;
        $Update = new Update;
        $Update->ExeUpdate(self::Entity, $this->Data, "WHERE idCentroCusto = :id", "id={$this->CC}");
    }

    /*
     * ***************************************
     * **********  PRIVATE METHODS  **********
     * ***************************************
     */

    //Valida e cria os dados para realizar o cadastro
    private function setData() {
        $this->Data = array_map('strip_tags', $this->Data);
        $this->Data = array_map('trim', $this->Data);
        $this->Data['DescCentroCusto'] = Check::Name($this->Data['DescCentroCusto']);
    }

    //Verifica o NOME do centro de custo. Se existir adiciona um pós-fix +1
    private function checkname() {
        $readName = new Read;
        $readName->ExeRead(self::Entity, "WHERE DescCentroCusto = :t", "t={$this->Data['DescCentroCusto']}");
        if ($readName->getResult()):
            return false;
        else:
            return true;
        endif;
    }

    //Verifica grupos com Centro de Custo
    private function checkUnids() {
        $readCC = new Read;
        $readCC->FullRead("SELECT id_CentroCusto FROM c_movcusto WHERE id_CentroCusto = :parent", "parent={$this->CCId}");
        if ($readCC->getResult()):
            return false;
        else:
            return true;
        endif;
    }

    //Cadastra o centro de custo no banco!
    private function Create() {
        $Create = new Create;
        $Create->ExeCreate(self::Entity, $this->Data);
        if ($Create->getResult()):
            $this->Result = $Create->getResult();
            $this->Error = ["<b>Sucesso:</b> O centro de custo {$this->Data['DescCentroCusto']} foi cadastrado no sistema!", WS_ACCEPT];
        endif;
    }

    //Atualiza SubCentro de Custo
    private function Update() {
        $Update = new Update;
        $Update->ExeUpdate(self::Entity, $this->Data, "WHERE idCentroCusto = :ccid", "ccid={$this->CCId}");
        if ($Update->getResult()):            
            $this->Result = true;
            $this->Error = ["<b>Sucesso:</b> O centro de custo {$this->Data['DescCentroCusto']} foi atualizado no sistema!", WS_ACCEPT];
        endif;
    }
}