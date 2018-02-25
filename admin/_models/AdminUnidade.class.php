<?php

/**
 * AdminUnidade.class [ MODEL ADMIN ]
 * Responável por gerenciar as unidades do sistema no admin!
 * 
 * @copyright (c) 2018, Rodrigo A. D. Leon Planejamento SMS Americana
 */
class AdminUnidade {

    private $Data;
    private $UnidId;
    private $Error;
    private $Result;

    //Nome da tabela no banco de dados!
    const Entity = 'c_tabunidade';

    /**
     * <b>Cadastrar Unidade:</b> Envelope Descriçao,  em um array atribuitivo e execute esse método
     * para cadastrar a unidade. 
     * @param ARRAY $Data = Atribuitivo
     */
    public function ExeCreate(array $Data) {
        $this->Data = $Data;

        if (in_array('', $this->Data) && !$this->checkUnids() && !$this->checkname()):
            $this->Result = false;
            $this->Error = ['<b>Erro ao cadastrar:</b> Para cadastrar uma unidade, preencha todos os campos!', WS_ALERT];
        else:
            $this->setData();            
            $this->Create();
        endif;
    }

    /**
     * <b>Atualizar Unidade:</b> Envelope os dados em uma array atribuitivo e informe o id de uma
     * unidade para atualiza-la!
     * @param INT $UnidadeId = Id da unidade
     * @param ARRAY $Data = Atribuitivo
     */
    public function ExeUpdate($UnidadeId, array $Data) {
        $this->UnidId = (int) $UnidadeId;
        $this->Data = $Data;

        if (in_array('', $this->Data) && !$this->checkUnids() && !$this->checkname()):
            $this->Result = false;
            $this->Error = ["<b>Erro ao atualizar:</b> Para atualizar a unidade {$this->Data['UnDescricao']}, preencha todos os campos!", WS_ALERT];
        else:
            $this->setData();
            $this->Update();
        endif;
    }

    /**
     * <b>Deleta unidade:</b> Informe o ID de uma unidade para remove-la do sistema. Esse método verifica
     * a unidade e se é permitido excluir de acordo com os registros do sistema!
     * @param INT $UnidadeId = Id da unidade
     */
    public function ExeDelete($UnidadeId) {
        $this->UnidId = (int) $UnidadeId;

        $read = new Read;        
        $read->ExeRead(self::Entity, "WHERE idUnidade = :delid", "delid={$this->UnidId}");        

        if (!$read->getResult()):
            $this->Result = false;
            $this->Error = ['Oppsss, você tentou remover uma unidade que não existe no sistema!', WS_INFOR];
        else:
            extract($read->getResult()[0]);
            if (!$this->checkUnids()):
                $this->Result = false;
                $this->Error = ["A <b>Unidade {$UnDescricao}</b> possui centro de custos cadastradas. Para deletar, antes altere ou remova os centros de custo!", WS_ALERT];            
            else:
                $delete = new Delete;
                $delete->ExeDelete(self::Entity, "WHERE idUnidade = :deletaid", "deletaid={$this->UnidId}");

                $this->Result = true;
                $this->Error = ["A <b> {$UnDescricao}</b> foi removida com sucesso do sistema!", WS_ACCEPT];
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

    /*
     * ***************************************
     * **********  PRIVATE METHODS  **********
     * ***************************************
     */

    //Valida e cria os dados para realizar o cadastro
    private function setData() {
        $this->Data = array_map('strip_tags', $this->Data);
        $this->Data = array_map('trim', $this->Data);
        $this->Data['UnDescricao'] = Check::Name($this->Data['UnDescricao']);
    }

    //Verifica o NOME da unidade. Se existir adiciona um pós-fix +1
    private function checkname() {
        $readName = new Read;
        $readName->ExeRead(self::Entity, "WHERE UnDescricao = :t", "t={$this->Data['UnDescricao']}");
        if ($readName->getResult()):
            return false;
        else:
            return true;
        endif;
    }

    //Verifica unidades com Centro de Custo
    private function checkUnids() {
        $readCC = new Read;
        $readCC->FullRead("SELECT id_unidade FROM c_tabcentrocusto WHERE id_unidade = :parent", "parent={$this->UnidId}");
        if ($readCC->getResult()):
            return false;
        else:
            return true;
        endif;
    }

    //Cadastra a unidade no banco!
    private function Create() {
        $Create = new Create;
        $Create->ExeCreate(self::Entity, $this->Data);
        if ($Create->getResult()):
            $this->Result = $Create->getResult();
            $this->Error = ["<b>Sucesso:</b> A unidade {$this->Data['UnDescricao']} foi cadastrada no sistema!", WS_ACCEPT];
        endif;
    }

    //Atualiza Unidade
    private function Update() {
        $Update = new Update;
        $Update->ExeUpdate(self::Entity, $this->Data, "WHERE idUnidade = :unidid", "unidid={$this->UnidId}");
        if ($Update->getResult()):            
            $this->Result = true;
            $this->Error = ["<b>Sucesso:</b> A  Unidade {$this->Data['UnDescricao']} foi atualizada no sistema!", WS_ACCEPT];
        endif;
    }
}