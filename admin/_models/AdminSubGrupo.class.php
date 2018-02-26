<?php

/**
 * AdminSubGrupo.class [ MODEL ADMIN ]
 * Responável por gerenciar as grupos do sistema no admin!
 * 
 * @copyright (c) 2018, Rodrigo A. D. Leon Planejamento SMS Americana
 */
class AdminSubGrupo {

    private $Data;
    private $SubGrupoId;
    private $Error;
    private $Result;

    //Nome da tabela no banco de dados!
    const Entity = 'c_tabsubgrupocc';

    /**
     * <b>Cadastrar Grupo:</b> Envelope Descriçao,  em um array atribuitivo e execute esse método
     * para cadastrar o grupo. 
     * @param ARRAY $Data = Atribuitivo
     */
    public function ExeCreate(array $Data) {
        $this->Data = $Data;

        if (in_array('', $this->Data) && !$this->checkUnids() && !$this->checkname()):
            $this->Result = false;
            $this->Error = ['<b>Erro ao cadastrar:</b> Para cadastrar um subgrupo, preencha todos os campos!', WS_ALERT];
        else:
            $this->setData();            
            $this->Create();
        endif;
    }

    /**
     * <b>Atualizar Grupo:</b> Envelope os dados em uma array atribuitivo e informe o id de um
     * grupo para atualiza-lo!
     * @param INT $SubGrupoId = Id da grupo
     * @param ARRAY $Data = Atribuitivo
     */
    public function ExeUpdate($SubGrupoId, array $Data) {
        $this->SubGrupoId = (int) $SubGrupoId;
        $this->Data = $Data;

        if (in_array('', $this->Data) && !$this->checkUnids() && !$this->checkname()):
            $this->Result = false;
            $this->Error = ["<b>Erro ao atualizar:</b> Para atualizar o subgrupo {$this->Data['DescSubGrupoCC']}, preencha todos os campos!", WS_ALERT];
        else:
            $this->setData();
            $this->Update();
        endif;
    }

    /**
     * <b>Deleta subgrupo:</b> Informe o ID de um subgrupo para remove-lo do sistema. Esse método verifica
     * o subgrupo e se é permitido excluir de acordo com os registros do sistema!
     * @param INT $SubGrupoId = Id do subgrupo
     */
    public function ExeDelete($SubGrupoId) {
        $this->SubGrupoId = (int) $SubGrupoId;

        $read = new Read;        
        $read->ExeRead(self::Entity, "WHERE idSubGrupoCC = :delid", "delid={$this->SubGrupoId}");        

        if (!$read->getResult()):
            $this->Result = false;
            $this->Error = ['Oppsss, você tentou remover um subgrupo que não existe no sistema!', WS_INFOR];
        else:
            extract($read->getResult()[0]);
            if (!$this->checkUnids()):
                $this->Result = false;
                $this->Error = ["O <b>SubGrupo {$DescSubGrupoCC}</b> possui centro de custos cadastrados. Para deletar, antes altere ou remova os centros de custo!", WS_ALERT];
            else:
                $delete = new Delete;
                $delete->ExeDelete(self::Entity, "WHERE idSubGrupoCC = :deletaid", "deletaid={$this->SubGrupoId}");

                $this->Result = true;
                $this->Error = ["O <b> {$DescSubGrupoCC}</b> foi removido com sucesso do sistema!", WS_ACCEPT];
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
        $this->Data['DescSubGrupoCC'] = Check::Name($this->Data['DescSubGrupoCC']);
    }

    //Verifica o NOME do grupo. Se existir adiciona um pós-fix +1
    private function checkname() {
        $readName = new Read;
        $readName->ExeRead(self::Entity, "WHERE DescSubGrupoCC = :t", "t={$this->Data['DescSubGrupoCC']}");
        if ($readName->getResult()):
            return false;
        else:
            return true;
        endif;
    }

    //Verifica grupos com Centro de Custo
    private function checkUnids() {
        $readCC = new Read;
        $readCC->FullRead("SELECT id_SubGrupoCC FROM c_tabcentrocusto WHERE id_SubGrupoCC = :parent", "parent={$this->SubGrupoId}");
        if ($readCC->getResult()):
            return false;
        else:
            return true;
        endif;
    }

    //Cadastra o grupo no banco!
    private function Create() {
        $Create = new Create;
        $Create->ExeCreate(self::Entity, $this->Data);
        if ($Create->getResult()):
            $this->Result = $Create->getResult();
            $this->Error = ["<b>Sucesso:</b> O subgrupo {$this->Data['DescSubGrupoCC']} foi cadastrado no sistema!", WS_ACCEPT];
        endif;
    }

    //Atualiza SubGrupo
    private function Update() {
        $Update = new Update;
        $Update->ExeUpdate(self::Entity, $this->Data, "WHERE idSubGrupoCC = :subgrupoid", "subgrupoid={$this->SubGrupoId}");
        if ($Update->getResult()):            
            $this->Result = true;
            $this->Error = ["<b>Sucesso:</b> O  subgrupo {$this->Data['DescSubGrupoCC']} foi atualizado no sistema!", WS_ACCEPT];
        endif;
    }
}