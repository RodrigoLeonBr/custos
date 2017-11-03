--
-- Estrutura da tabela `c_tabunidade`
--

DROP TABLE IF EXISTS c_tabunidade;
CREATE TABLE c_tabunidade (
  idUnidade int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  UnDescricao varchar(30) NOT NULL
);


--
-- Estrutura da tabela `c_tabgrupocc`
--

DROP TABLE IF EXISTS c_tabgrupocc;
CREATE TABLE c_tabgrupocc (
  idGrupoCc int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  DescGrupoCc varchar(60) NOT NULL
);


--
-- Estrutura da tabela `c_tabsubgrupocc`
--

DROP TABLE IF EXISTS c_tabsubgrupocc;
CREATE TABLE c_tabsubgrupocc (
  idSubGrupoCC int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  DescSubGrupoCC varchar(60) NOT NULL
);;


--
-- Estrutura da tabela `c_tabcentrocusto`
--

DROP TABLE IF EXISTS c_tabcentrocusto;
CREATE TABLE c_tabcentrocusto (
  idCentroCusto int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  DescCentroCusto varchar(60) NOT NULL,
  id_Unidade int(11) NOT NULL,
  id_GrupoCC int(11) NOT NULL,
  id_SubGrupoCC int(11) NOT NULL,
  TipoCC char(1) NOT NULL
);
 
ALTER TABLE c_tabcentrocusto ADD CONSTRAINT FK_tabunidade_tabcentrocusto
FOREIGN KEY(id_Unidade) REFERENCES c_tabunidade(idUnidade); 

ALTER TABLE c_tabcentrocusto ADD CONSTRAINT FK_tabgrupocc_tabcentrocusto
FOREIGN KEY(id_GrupoCC) REFERENCES c_tabgrupocc(idGrupoCc); 

ALTER TABLE c_tabcentrocusto ADD CONSTRAINT FK_tabsubgrupocc_tabcentrocusto
FOREIGN KEY(id_SubGrupoCC) REFERENCES c_tabsubgrupocc(idSubGrupoCC); 

--
-- Estrutura da tabela `c_tabgrupoitemcc`
--

DROP TABLE IF EXISTS c_tabgrupoitemcc;
CREATE TABLE c_tabgrupoitemcc (
  idGrupoItemCC int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  DescGrupoItemCC varchar(30) NOT NULL
);


--
-- Estrutura da tabela `c_tabitemcc`
--

DROP TABLE IF EXISTS c_tabitemcc;
CREATE TABLE c_tabitemcc (
  idItemCC int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  DescItemCC varchar(60) NOT NULL,
  id_GrupoItemCC int(11) NOT NULL
);

ALTER TABLE c_tabitemcc ADD CONSTRAINT FK_tabgrupoitemcc_tabitemcc
FOREIGN KEY(id_GrupoItemCC) REFERENCES c_tabgrupoitemcc(idGrupoItemCC); 


