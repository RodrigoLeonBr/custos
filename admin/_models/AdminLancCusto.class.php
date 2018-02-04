<?php

/**
 * AdminLancCusto.class [ MODEL ADMIN ]
 * Responável por lançamento de eventos de Custos!
 * 
 * @copyright (c) 2017, Rodrigo Leon PLANEJAMENTO SMS AMERICANA
 */
class AdminLancCusto {

    private $Data;
    private $Custo;
    private $Error;
    private $Result;

    //Nome da tabela no banco de dados
    const Entity = 'c_movcusto';

    /**
     * <b>Cadastrar o Lançamento de Custos:</b> Envelope os dados do lançamento em um array atribuitivo e execute esse método
     * para cadastrar a mesma no banco.
     * @param ARRAY $Data = Atribuitivo
     */
    public function ExeCreate(array $Data) {
        $this->Data = $Data;
        if (in_array('', $this->Data)):
            $this->Error = ["Erro ao Cadastrar Lançamento: Para cadastrar um lançamento, preencha todos os campos!", WS_ALERT];
            $this->Result = false;
        else:
            $this->checkData();
            if ($this->Result):
                $this->Create();
            endif;            
        endif;
    }

    /**
     * <b>Realiza busca de Centro de Custo:</b> Envelope os dados do lançamento em um array atribuitivo e execute esse método
     * para cadastrar a mesma no banco.
     * @param STRING $Tipo = Tudo, TotalGrupo ou TotalCentroCusto
     * @param INTEGER $Ano = Ano do Centro de Custo
     * @param INTEGER $Mes = Primeiro mes do Quadrimestre a ser visualizado
     * @param INTEGER $GICC = Grupo do Centro de Custo pode ser nulo
     * @param INTEGER $CC = Centro de Custo por Grupo pode ser nulo
     * @param INTEGER $SCC = Filtro do Relatório com único Centro de Custo pode ser nulo
     */
    public function ExeBuscaCusto($Tipo, $Ano, $Mes, $GICC = null, $Cc = null, $SCC = null) {
        
        $sqlCustos = "SELECT sum(case when mes=".$Mes." then c_movcusto.Valor else 0 end) Mes1, ";
        $sqlCustos .="sum(case when mes=".($Mes+1)." then c_movcusto.Valor else 0 end) Mes2, ";
        $sqlCustos .="sum(case when mes=".($Mes+2)." then c_movcusto.Valor else 0 end) Mes3, ";
        $sqlCustos .="sum(case when mes=".($Mes+3)." then c_movcusto.Valor else 0 end) Mes4 ";        
        if($Tipo=="Tudo"){            
            $sqlCustos .= ", c_movcusto.id_CentroCusto, ";
            $sqlCustos .= "c_tabcentrocusto.DescCentroCusto, ";
            $sqlCustos .= "c_tabitemcc.id_GrupoItemCC, ";
            $sqlCustos .= "c_tabgrupoitemcc.DescGrupoItemCC, ";
            $sqlCustos .= "c_movcusto.id_ItemCC, ";
            $sqlCustos .= "c_tabitemcc.DescItemCC ";
        }
        $sqlCustos .= "FROM c_movcusto ";
        if($Tipo=="Tudo" || $Tipo=="TotalGrupo"){
            $sqlCustos .= " INNER JOIN c_tabitemcc ON c_tabitemcc.idItemCC=c_movcusto.id_ItemCC ";
        }            
        if($Tipo=="Tudo"){
            $sqlCustos .= " INNER JOIN c_tabcentrocusto ON c_tabcentrocusto.idCentroCusto=c_movcusto.id_CentroCusto ";
            $sqlCustos .= " INNER JOIN c_tabgrupoitemcc ON c_tabgrupoitemcc.idGrupoItemCC=c_tabitemcc.id_GrupoItemCC ";
        }        
        $sqlCustos .= " WHERE Ano= ". strval($Ano);
        $sqlCustos .= " and mes >= ". strval($Mes);
        $sqlCustos .= " and mes<= ". strval($Mes+3);
        if(isset($SCC) && !empty($SCC)):
            $sqlCustos .= " and id_CentroCusto=". strval($SCC);
        endif;
        if($Tipo=="TotalGrupo" || $Tipo=="TotalCentroCusto"){
            $sqlCustos .=" and id_CentroCusto=".strval($Cc);
            if($Tipo=="TotalGrupo"){
                $sqlCustos .=" and id_GrupoItemCC=".strval($GICC);
            }
        }            
        if($Tipo=="Tudo"){
            $sqlCustos .= " GROUP BY c_movcusto.id_CentroCusto, ";
            $sqlCustos .= "c_tabcentrocusto.DescCentroCusto, ";
            $sqlCustos .= "c_tabitemcc.id_GrupoItemCC, ";
            $sqlCustos .= "c_tabgrupoitemcc.DescGrupoItemCC, ";
            $sqlCustos .= "c_movcusto.id_ItemCC, ";
            $sqlCustos .= "c_tabitemcc.DescItemCC ";        

            $sqlCustos .= "ORDER BY  c_movcusto.id_CentroCusto, ";
            $sqlCustos .= "c_tabitemcc.id_GrupoItemCC, ";
            $sqlCustos .= "c_movcusto.id_ItemCC ";
        }
        return $sqlCustos;
    }

    /**
     * <b>Imprime Linha de Total do Grupo:</b> Envelope os dados do Grupo e Centro de Custo em um array 
     * e gera texto html para imprimir tabela do total do Grupo     
     * @param ARRAY $Data = Atribuitivo
     */
    public function TotalGrupo(array $Data) {
        $this->Custo = (int) $IdCusto;
        $this->Data = $Data;
        if (in_array('', $this->Data)):
            $this->Error = ["Erro ao Atualizar: Para atualizar <b>{$this->Data['id_CentroCusto']}</b>, preencha todos os campos!", WS_ALERT];
            $this->Result = false;
        else:
            $this->Update();
        endif;
    }

    /**
     * <b>Atualizar o Lançamento:</b> Envelope os dados em uma array atribuitivo e informe o id de um lançamento
     * para atualiza-la no banco de dados!
     * @param INT $IdCusto = Id do Lançamento de Custo
     * @param ARRAY $Data = Atribuitivo
     */
    public function ExeUpdate($IdCusto, array $Data) {
        $this->Custo = (int) $IdCusto;
        $this->Data = $Data;
        if (in_array('', $this->Data)):
            $this->Error = ["Erro ao Atualizar: Para atualizar <b>{$this->Data['id_CentroCusto']}</b>, preencha todos os campos!", WS_ALERT];
            $this->Result = false;
        else:
            $this->Update();
        endif;
    }

    /**
     * <b>Deleta Lançamento:</b> Informe o ID do lançamento a ser removida para que esse método realize uma
     * checagem excluinto todos os dados nessesários e removendo o prestador do banco!
     * @param INT $idCusto = Id do lançamento do custo
     */
    public function ExeDelete($idCusto) {
        $this->Custo = (int) $idCusto;

        $ReadCusto = new Read;
        $ReadCusto->ExeRead(self::Entity, "WHERE idCusto = :idcusto", "idcusto={$this->Custo}");
        if (!$ReadCusto->getResult()):
            $this->Error = ["O lançamento que você tentou deletar não existe no sistema!", WS_ERROR];
            $this->Result = false;
        else:
            $deleta = new Delete;
            $deleta->ExeDelete(self::Entity, "WHERE idCusto = :idcusto", "idcusto={$this->Custo}");

            $this->Error = ["O Lançamento foi removida com sucesso do sistema!", WS_ACCEPT];
            $this->Result = true;
        endif;
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
    
    private function checkData() {
        $this->Result = TRUE;
        if (in_array('', $this->Data)):
            $this->Error = ["Existem campos em branco. Favor preencha todos os campos!", WS_ALERT];
            $this->Result = FALSE;
        endif;
        if ($this->checkCusto()):            
            $this->Error = ["Lançamento já realizado, procure alterar o valor na pesquisa se necessário!", WS_ALERT];            
        endif;
    }
    
    private function checkCusto(){
        $ReadCusto = new Read;
        $ReadCusto->ExeRead(self::Entity, "WHERE Ano = ".$this->Data['Ano']." AND Mes = ".$this->Data['Mes']." AND id_CentroCusto = ".$this->Data['id_CentroCusto']." AND id_ItemCC = ".$this->Data['id_ItemCC']);
        if ($ReadCusto->getRowCount()):
            $this->Result= FALSE;
            $this->Error = ["Lançamento já realizado, procure alterar o valor na pesquisa se necessário!", WS_ALERT];            
        else:
            $this->Result = TRUE;
        endif;
        return $this->Result;
    }

    //Cadastra o lançamento no banco!
    private function Create() {
        $Create = new Create;
        $Create->ExeCreate(self::Entity, $this->Data);
        if ($Create->getResult()):
            $this->Result = $Create->getResult();
            $this->Error = ["O Lançamento foi cadastrada com sucesso no sistema!", WS_ACCEPT];
        endif;
    }

    //Atualiza o lançamento no banco!
    private function Update() {
        $Update = new Update;
        $Update->ExeUpdate(self::Entity, $this->Data, "WHERE idCusto = :idcusto", "idcuto={$this->Custo}");
        if ($Update->getRowCount() >= 1):
            $this->Error = ["O lançameto foi atualizada com sucesso!", WS_ACCEPT];
            $this->Result = true;
        endif;
    }

}
