<?php

/**
 * AdminCategory.class [ MODEL ADMIN ]
 * Responável por gerenciar as categorias do sistema no admin!
 * 
 * @copyright (c) 2014, Robson V. Leite UPINSIDE TECNOLOGIA
 */
class AdminEstrutura {

    private $Data;
    private $EstId;
    private $Error;
    private $Result;

    //Nome da tabela no banco de dados!
    const Entity = 'estruturas';

    /**
     * <b>Cadastrar Estrutura:</b> Envelope titulo, descrição, data e sessão em um array atribuitivo e execute esse método
     * para cadastrar a categoria. Case seja uma sessão, envie o category_parent como STRING null.
     * @param ARRAY $Data = Atribuitivo
     */
    public function ExeCreate(array $Data) {
        $this->Data = $Data;

        if ( $this->Data['estrutura_descricao']==''):
            $this->Result = false;
            $this->Error = ['<b>Erro ao cadastrar:</b> Para cadastrar uma estrutura, preencha a descrição', WS_ALERT];
        else:
            $this->setData();
            $this->setName();
            $this->Create();
        endif;
    }

    /**
     * 
     * <b>Atualizar Estrutura:</b> Envelope os dados em uma array atribuitivo e informe o id de uma
     * categoria para atualiza-la!
     * @param INT $CategoryId = Id da categoria
     * @param ARRAY $Data = Atribuitivo
     */
    public function ExeUpdate($EstruturaId, array $Data) {
        $this->EstId = (int) $EstruturaId;
        $this->Data = $Data;

        if (in_array('', $this->Data)):
            $this->Result = false;
            $this->Error = ["<b>Erro ao atualizar:</b> Para atualizar a estrutura {$this->Data['estrutura_descricao']}, preencha todos os campos!", WS_ALERT];
        else:
            $this->setData();
            $this->setName();
            $this->Update();
        endif;
    }

    /**
     * <b>Deleta categoria:</b> Informe o ID de uma categoria para remove-la do sistema. Esse método verifica
     * o tipo de categoria e se é permitido excluir de acordo com os registros do sistema!
     * @param INT $CategoryId = Id da categoria
     */
    public function ExeDelete($EstruruaId) {
        $this->EstId = (int) $EstruruaId;

        $read = new Read;
        $read->ExeRead(self::Entity, "WHERE estrutura_id = :delid", "delid={$this->EstId}");

        if (!$read->getResult()):
            $this->Result = false;
            $this->Error = ['Oppsss, você tentou remover uma estrutura que não existe no sistema!', WS_INFOR];
        else:
            extract($read->getResult()[0]);
            if (!$estrutura_grupo && !$this->checkEst()):
                $this->Result = false;
                $this->Error = ["A <b>seção {$estrutura_descricao}</b> possui estruturas cadastradas. Para deletar, antes altere ou remova as estruturas filhas!", WS_ALERT];
            elseif ($estrutura_subgrupo && !$this->checkSubGrupo()):
                $this->Result = false;
                $this->Error = ["O <b>grupo {$estrutura_descricao} </b> possui Sub Grupos cadastrados. Para deletar, antes altere ou remova todos os Sub Grupos deste grupo!", WS_ALERT];
            else:
                $delete = new Delete;
                $delete->ExeDelete(self::Entity, "WHERE estrutura_id = :deletaid", "deletaid={$this->EstId}");

                $tipo = ( empty($estrutura_grupo) ? 'campo' : 'estrutura' );
                $this->Result = true;
                $this->Error = ["A <b>{$tipo} {$estrutura_descricao}</b> foi removida com sucesso do sistema!", WS_ACCEPT];
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
        $this->Data['estrutura_descricao'] = Check::Name($this->Data['estrutura_descricao']);
        $this->Data['estrutura_grupo'] = ($this->Data['estrutura_grupo'] == 'null' ? null : $this->Data['estrutura_grupo']);
    }

    //Verifica o NAME da categoria. Se existir adiciona um pós-fix +1
    private function setName() {
        $Where = (!empty($this->EstId) ? "estrutura_id != {$this->EstId} AND" : '' );

        $readName = new Read;
        $readName->ExeRead(self::Entity, "WHERE {$Where} estrutura_descricao = :t", "t={$this->Data['estrutura_descricao']}");
        if ($readName->getResult()):
            $this->Data['estrutura_apoio2'] = $this->Data['estrutura_apoio2'] . '-' . $readName->getRowCount();
        endif;
    }

    //Verifica categorias da seção
    private function checkEst() {
        $readSub = new Read;
        $readSub->ExeRead(self::Entity, "WHERE estrutura_grupo = :parent", "parent={$this->EstId}");
        if ($readSub->getResult()):
            return false;
        else:
            return true;
        endif;
    }

    //Verifica Sub-grupos
    private function checkSubGrupo($grupo) {
        $readSub = new Read;
        $readSub->ExeRead(self::Entity, "WHERE estrutura_grupo = '".$this->EstId."' and estrutura_subgrupo='".$grupo."'");
        if ($readSub->getResult()):
            return false;
        else:
            return true;
        endif;
    }

    //Cadastra a categoria no banco!
    private function Create() {
        $Create = new Create;
        $Create->ExeCreate(self::Entity, $this->Data);
        if ($Create->getResult()):
            $this->Result = $Create->getResult();
            $this->Error = ["<b>Sucesso:</b> A estrutura {$this->Data['estrutura_descricao']} foi cadastrada no sistema!", WS_ACCEPT];
        endif;
    }

    //Atualiza Categoria
    private function Update() {
        $Update = new Update;
        $Update->ExeUpdate(self::Entity, $this->Data, "WHERE estrutura_id = :estid", "estid={$this->EstId}");
        if ($Update->getResult()):
            $tipo = ( empty($this->Data['grupo']) ? 'campo' : 'estrutura' );
            $this->Result = true;
            $this->Error = ["<b>Sucesso:</b> A {$tipo} {$this->Data['estrutura_descricao']} foi atualizada no sistema!", WS_ACCEPT];
        endif;
    }

}
