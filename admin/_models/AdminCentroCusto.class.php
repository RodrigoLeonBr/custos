<?php

/**
 * AdminCentroCusto.class [ MODEL ADMIN ]
 * Respnsável por gerenciar os centros de custos no Admin do sistema!
 * 
 * @copyright (c) 2018, Rodrigo A. D. Leon Planejamento SMS Americana
 */
class AdminCentroCusto {

    private $Data;
    private $CC;
    private $Error;
    private $Result;

    //Nome da tabela no banco de dados
    const Entity = 'c_tabcentrocusto';

    /**
     * <b>Cadastrar o Centro de Custo:</b> Envelope os dados do Centro de Custo em um array atribuitivo e execute esse método
     * para cadastrar o Centro de Custo. 
     * @param ARRAY $Data = Atribuitivo
     */
    public function ExeCreate(array $Data) {
        $this->Data = $Data;

        if (in_array('', $this->Data)):
            $this->Error = ["Erro ao cadastrar: Para criar um Centro de Custo, favor preencha todos os campos!", WS_ALERT];
            $this->Result = false;
        else:
            $this->setData();
            $this->setName();
        endif;
    }

    /**
     * <b>Atualizar Centro de Custo:</b> Envelope os dados em uma array atribuitivo e informe o id de um 
     * Centro de Custo para atualiza-lo na tabela!
     * @param INT $CCId = Id do Centro de Custo
     * @param ARRAY $Data = Atribuitivo
     */
    public function ExeUpdate($CCId, array $Data) {
        $this->CC = (int) $CCId;
        $this->Data = $Data;

        if (in_array('', $this->Data)):
            $this->Error = ["Para atualizar este centro de custo, preencha todos os campos ( Capa não precisa ser enviada! )", WS_ALERT];
            $this->Result = false;
        else:
            $this->setData();
        endif;
    }

    /**
     * <b>Deleta Centro de Custo:</b> Informe o ID do Centro de Custo a ser removido
     * @param INT $CCId = Id do Centro de Custo
     */
    public function ExeDelete($CCId) {
        $this->CC = (int) $CCId;

        $ReadCC = new Read;
        $ReadCC->ExeRead(self::Entity, "WHERE idCentroCusto = :cc", "cc={$this->CC}");

        if (!$ReadCC->getResult()):
            $this->Error = ["O Centro de Custo que você tentou deletar não existe no sistema!", WS_ERROR];
            $this->Result = false;
        else:
            $deleta = new Delete;
            $deleta->ExeDelete(self::Entity, "WHERE idCentroCusto = :ccid", "ccid={$this->CC}");

            $this->Error = ["O Centro de Custo <b>{$ReadCC['DescCentroCusto']}</b> foi removido com sucesso do sistema!", WS_ACCEPT];
            $this->Result = true;

        endif;
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

    /**
     * <b>Verificar Cadastro:</b> Retorna ID do registro se o cadastro for efetuado ou FALSE se não.
     * Para verificar erros execute um getError();
     * @return BOOL $Var = InsertID or False
     */
    public function getResult() {
        return $this->Result;
    }

    /**
     * <b>Obter Erro:</b> Retorna um array associativo com uma mensagem e o tipo de erro.
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
    }

    //Cadastra o post no banco!
    private function Create() {
        $cadastra = new Create;
        $cadastra->ExeCreate(self::Entity, $this->Data);
        if ($cadastra->getResult()):
            $this->Error = ["O Centro de Custo {$this->Data['DescCentroCusto']} foi cadastrado com sucesso no sistema!", WS_ACCEPT];
            $this->Result = $cadastra->getResult();
        endif;
    }

    //Atualiza o post no banco!
    private function Update() {
        $Update = new Update;
        $Update->ExeUpdate(self::Entity, $this->Data, "WHERE idCentroCusto = :id", "id={$this->CC}");
        if ($Update->getResult()):
            $this->Error = ["O Centro de Custo <b>{$this->Data['DescCentroCusto']}</b> foi atualizado com sucesso no sistema!", WS_ACCEPT];
            $this->Result = true;
        endif;
    }

}