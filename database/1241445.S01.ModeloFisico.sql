SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `GarantiaContrato`;
DROP TABLE IF EXISTS `Documento`;
DROP TABLE IF EXISTS `EquipamentoFornecedor`;
DROP TABLE IF EXISTS `Equipamento`;
DROP TABLE IF EXISTS `ConteudoSite`;
DROP TABLE IF EXISTS `Utilizador`;
DROP TABLE IF EXISTS `TipoDocumento`;
DROP TABLE IF EXISTS `Fornecedor`;
DROP TABLE IF EXISTS `Localizacao`;
DROP TABLE IF EXISTS `TipoEntrada`;
DROP TABLE IF EXISTS `CriticidadeEquipamento`;
DROP TABLE IF EXISTS `EstadoEquipamento`;
DROP TABLE IF EXISTS `CategoriaEquipamento`;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `CategoriaEquipamento` (
  `idCategoriaEquipamento` INT PRIMARY KEY AUTO_INCREMENT,
  `descricao` VARCHAR(80) UNIQUE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `EstadoEquipamento` (
  `idEstadoEquipamento` INT PRIMARY KEY AUTO_INCREMENT,
  `descricao` VARCHAR(80) UNIQUE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `CriticidadeEquipamento` (
  `idCriticidadeEquipamento` INT PRIMARY KEY AUTO_INCREMENT,
  `descricao` VARCHAR(80) UNIQUE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `TipoEntrada` (
  `idTipoEntrada` INT PRIMARY KEY AUTO_INCREMENT,
  `descricao` VARCHAR(80) UNIQUE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `Localizacao` (
  `idLocalizacao` INT PRIMARY KEY AUTO_INCREMENT,
  `categoria` VARCHAR(100) NOT NULL,
  `edificio` VARCHAR(100) NOT NULL,
  `piso` VARCHAR(50) NOT NULL,
  `servico` VARCHAR(120) NOT NULL,
  `sala` VARCHAR(80) NOT NULL,
  `observacoes` TEXT,
  `ativo` BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `Fornecedor` (
  `idFornecedor` INT PRIMARY KEY AUTO_INCREMENT,
  `nif` VARCHAR(20) UNIQUE NOT NULL,
  `email` VARCHAR(120) UNIQUE NOT NULL,
  `designacao` VARCHAR(150) NOT NULL,
  `telefone` VARCHAR(30),
  `morada` VARCHAR(200),
  `website` VARCHAR(150),
  `pessoaContacto` VARCHAR(120),
  `telefonePessoaContacto` VARCHAR(30),
  `tipoFornecedor` VARCHAR(80),
  `observacoes` TEXT,
  `ativo` BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `TipoDocumento` (
  `idTipoDocumento` INT PRIMARY KEY AUTO_INCREMENT,
  `descricao` VARCHAR(100) UNIQUE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `Utilizador` (
  `idUtilizador` INT PRIMARY KEY AUTO_INCREMENT,
  `username` VARCHAR(80) UNIQUE NOT NULL,
  `email` VARCHAR(120) UNIQUE NOT NULL,
  `nome` VARCHAR(120) NOT NULL,
  `passwordHash` VARCHAR(255) NOT NULL,
  `perfil` VARCHAR(50) NOT NULL,
  `ativo` BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `ConteudoSite` (
  `idConteudoSite` INT PRIMARY KEY AUTO_INCREMENT,
  `chave` VARCHAR(100) UNIQUE NOT NULL,
  `seccao` VARCHAR(100) NOT NULL,
  `titulo` VARCHAR(150) NOT NULL,
  `texto` TEXT NOT NULL,
  `imagem` VARCHAR(150),
  `ativo` BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `Equipamento` (
  `idEquipamento` INT PRIMARY KEY AUTO_INCREMENT,
  `codigoInterno` VARCHAR(30) UNIQUE NOT NULL,
  `numeroSerie` VARCHAR(80) UNIQUE NOT NULL,
  `idCategoriaEquipamento` INT NOT NULL,
  `idEstadoEquipamento` INT NOT NULL,
  `idCriticidadeEquipamento` INT NOT NULL,
  `idTipoEntrada` INT NOT NULL,
  `idLocalizacao` INT NOT NULL,
  `designacao` VARCHAR(150) NOT NULL,
  `marca` VARCHAR(100),
  `modelo` VARCHAR(100),
  `fabricante` VARCHAR(120),
  `dataAquisicao` DATE,
  `anoFabrico` INT,
  `custoAquisicao` DECIMAL(10,2),
  `observacoes` TEXT,
  `ativo` BOOLEAN NOT NULL DEFAULT TRUE,

  CONSTRAINT `fk_Equipamento_CategoriaEquipamento`
    FOREIGN KEY (`idCategoriaEquipamento`)
    REFERENCES `CategoriaEquipamento` (`idCategoriaEquipamento`),

  CONSTRAINT `fk_Equipamento_EstadoEquipamento`
    FOREIGN KEY (`idEstadoEquipamento`)
    REFERENCES `EstadoEquipamento` (`idEstadoEquipamento`),

  CONSTRAINT `fk_Equipamento_CriticidadeEquipamento`
    FOREIGN KEY (`idCriticidadeEquipamento`)
    REFERENCES `CriticidadeEquipamento` (`idCriticidadeEquipamento`),

  CONSTRAINT `fk_Equipamento_TipoEntrada`
    FOREIGN KEY (`idTipoEntrada`)
    REFERENCES `TipoEntrada` (`idTipoEntrada`),

  CONSTRAINT `fk_Equipamento_Localizacao`
    FOREIGN KEY (`idLocalizacao`)
    REFERENCES `Localizacao` (`idLocalizacao`),

  CONSTRAINT `ck_Equipamento_AnoFabrico`
    CHECK (`anoFabrico` IS NULL OR `anoFabrico` >= 1900),

  CONSTRAINT `ck_Equipamento_CustoAquisicao`
    CHECK (`custoAquisicao` IS NULL OR `custoAquisicao` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `EquipamentoFornecedor` (
  `idEquipamentoFornecedor` INT PRIMARY KEY AUTO_INCREMENT,
  `idEquipamento` INT NOT NULL,
  `idFornecedor` INT NOT NULL,
  `tipoRelacao` VARCHAR(80) NOT NULL,
  `dataInicio` DATE,
  `dataFim` DATE,
  `observacoes` TEXT,

  CONSTRAINT `fk_EquipamentoFornecedor_Equipamento`
    FOREIGN KEY (`idEquipamento`)
    REFERENCES `Equipamento` (`idEquipamento`),

  CONSTRAINT `fk_EquipamentoFornecedor_Fornecedor`
    FOREIGN KEY (`idFornecedor`)
    REFERENCES `Fornecedor` (`idFornecedor`),

  CONSTRAINT `uq_EquipamentoFornecedor_Tipo`
    UNIQUE (`idEquipamento`, `idFornecedor`, `tipoRelacao`),

  CONSTRAINT `ck_EquipamentoFornecedor_Datas`
    CHECK (`dataFim` IS NULL OR `dataInicio` IS NULL OR `dataFim` >= `dataInicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `Documento` (
  `idDocumento` INT PRIMARY KEY AUTO_INCREMENT,
  `idEquipamento` INT NOT NULL,
  `idTipoDocumento` INT NOT NULL,
  `idFornecedor` INT,
  `nomeDocumento` VARCHAR(150) NOT NULL,
  `dataDocumento` DATE,
  `dataValidade` DATE,
  `nomeFicheiro` VARCHAR(150),
  `caminhoFicheiro` VARCHAR(255),
  `observacoes` TEXT,
  `ativo` BOOLEAN NOT NULL DEFAULT TRUE,

  CONSTRAINT `fk_Documento_Equipamento`
    FOREIGN KEY (`idEquipamento`)
    REFERENCES `Equipamento` (`idEquipamento`),

  CONSTRAINT `fk_Documento_TipoDocumento`
    FOREIGN KEY (`idTipoDocumento`)
    REFERENCES `TipoDocumento` (`idTipoDocumento`),

  CONSTRAINT `fk_Documento_Fornecedor`
    FOREIGN KEY (`idFornecedor`)
    REFERENCES `Fornecedor` (`idFornecedor`),

  CONSTRAINT `ck_Documento_DataValidade`
    CHECK (`dataValidade` IS NULL OR `dataDocumento` IS NULL OR `dataValidade` >= `dataDocumento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `GarantiaContrato` (
  `idGarantiaContrato` INT PRIMARY KEY AUTO_INCREMENT,
  `idEquipamento` INT NOT NULL,
  `idFornecedorResponsavel` INT,
  `tipo` VARCHAR(80) NOT NULL,
  `numeroContrato` VARCHAR(80),
  `dataInicio` DATE NOT NULL,
  `dataFim` DATE NOT NULL,
  `periodicidade` VARCHAR(80),
  `observacoes` TEXT,
  `ativo` BOOLEAN NOT NULL DEFAULT TRUE,

  CONSTRAINT `fk_GarantiaContrato_Equipamento`
    FOREIGN KEY (`idEquipamento`)
    REFERENCES `Equipamento` (`idEquipamento`),

  CONSTRAINT `fk_GarantiaContrato_Fornecedor`
    FOREIGN KEY (`idFornecedorResponsavel`)
    REFERENCES `Fornecedor` (`idFornecedor`),

  CONSTRAINT `ck_GarantiaContrato_Datas`
    CHECK (`dataFim` >= `dataInicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;