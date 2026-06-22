/* =========================================================
   SIBDAS - Projeto MedInventário
   Ficheiro: 1241445.S01.ModeloFisico.sql
   Objetivo: Criação da estrutura física da base de dados
   ========================================================= */

CREATE DATABASE IF NOT EXISTS `db1241445` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `db1241445`;

SET FOREIGN_KEY_CHECKS = 0;


DROP TABLE IF EXISTS `LogSistema`;
DROP TABLE IF EXISTS `GarantiaContrato`;
DROP TABLE IF EXISTS `Documento`;
DROP TABLE IF EXISTS `MovimentacaoEquipamento`;
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

/* =========================================================
   Tabela CategoriaEquipamento
   ========================================================= */
CREATE TABLE `CategoriaEquipamento` (
  `idCategoriaEquipamento` int NOT NULL AUTO_INCREMENT,
  `descricao` varchar(80) NOT NULL,
  PRIMARY KEY (`idCategoriaEquipamento`),
  UNIQUE KEY `descricao` (`descricao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/* =========================================================
   Tabela EstadoEquipamento
   ========================================================= */
CREATE TABLE `EstadoEquipamento` (
  `idEstadoEquipamento` int NOT NULL AUTO_INCREMENT,
  `descricao` varchar(80) NOT NULL,
  PRIMARY KEY (`idEstadoEquipamento`),
  UNIQUE KEY `descricao` (`descricao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/* =========================================================
   Tabela CriticidadeEquipamento
   ========================================================= */
CREATE TABLE `CriticidadeEquipamento` (
  `idCriticidadeEquipamento` int NOT NULL AUTO_INCREMENT,
  `descricao` varchar(80) NOT NULL,
  PRIMARY KEY (`idCriticidadeEquipamento`),
  UNIQUE KEY `descricao` (`descricao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/* =========================================================
   Tabela TipoEntrada
   ========================================================= */
CREATE TABLE `TipoEntrada` (
  `idTipoEntrada` int NOT NULL AUTO_INCREMENT,
  `descricao` varchar(80) NOT NULL,
  PRIMARY KEY (`idTipoEntrada`),
  UNIQUE KEY `descricao` (`descricao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/* =========================================================
   Tabela Localizacao
   ========================================================= */
CREATE TABLE `Localizacao` (
  `idLocalizacao` int NOT NULL AUTO_INCREMENT,
  `categoria` varchar(100) NOT NULL,
  `edificio` varchar(100) NOT NULL,
  `piso` varchar(50) NOT NULL,
  `servico` varchar(120) NOT NULL,
  `sala` varchar(80) NOT NULL,
  `observacoes` text,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idLocalizacao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/* =========================================================
   Tabela Fornecedor
   ========================================================= */
CREATE TABLE `Fornecedor` (
  `idFornecedor` int NOT NULL AUTO_INCREMENT,
  `nif` varchar(20) NOT NULL,
  `email` varchar(120) NOT NULL,
  `designacao` varchar(150) NOT NULL,
  `telefone` varchar(30) NOT NULL,
  `morada` varchar(200) DEFAULT NULL,
  `website` varchar(150) DEFAULT NULL,
  `pessoaContacto` varchar(120) NOT NULL,
  `telefonePessoaContacto` varchar(30) NOT NULL,
  `pessoaContacto2` varchar(120) DEFAULT NULL,
  `telefonePessoaContacto2` varchar(30) DEFAULT NULL,
  `observacoes` text,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idFornecedor`),
  UNIQUE KEY `nif` (`nif`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `uq_Fornecedor_Telefone` (`telefone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/* =========================================================
   Tabela TipoDocumento
   ========================================================= */
CREATE TABLE `TipoDocumento` (
  `idTipoDocumento` int NOT NULL AUTO_INCREMENT,
  `descricao` varchar(100) NOT NULL,
  PRIMARY KEY (`idTipoDocumento`),
  UNIQUE KEY `descricao` (`descricao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/* =========================================================
   Tabela Utilizador
   ========================================================= */
CREATE TABLE `Utilizador` (
  `idUtilizador` int NOT NULL AUTO_INCREMENT,
  `username` varchar(80) NOT NULL,
  `email` varchar(120) NOT NULL,
  `nome` varchar(120) NOT NULL,
  `passwordHash` varchar(255) NOT NULL,
  `perfil` varchar(50) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `lastLogin` datetime DEFAULT NULL,
  `dataFimContrato` date DEFAULT NULL,
  PRIMARY KEY (`idUtilizador`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  CONSTRAINT `ck_Utilizador_Perfil`
    CHECK (`perfil` IN ('administrador', 'tecnico', 'gestor_hospitalar', 'profissional_saude'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/* =========================================================
   Tabela ConteudoSite
   ========================================================= */
CREATE TABLE `ConteudoSite` (
  `idConteudoSite` int NOT NULL AUTO_INCREMENT,
  `chave` varchar(100) NOT NULL,
  `seccao` varchar(100) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `texto` text NOT NULL,
  `imagem` varchar(150) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idConteudoSite`),
  UNIQUE KEY `chave` (`chave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/* =========================================================
   Tabela Equipamento
   ========================================================= */
CREATE TABLE `Equipamento` (
  `idEquipamento` int NOT NULL AUTO_INCREMENT,
  `codigoInterno` varchar(30) NOT NULL,
  `numeroSerie` varchar(80) NOT NULL,
  `idCategoriaEquipamento` int NOT NULL,
  `idEstadoEquipamento` int NOT NULL,
  `idCriticidadeEquipamento` int NOT NULL,
  `idTipoEntrada` int NOT NULL,
  `idLocalizacao` int NOT NULL,
  `designacao` varchar(150) NOT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `fabricante` varchar(120) DEFAULT NULL,
  `dataAquisicao` date DEFAULT NULL,
  `anoFabrico` int DEFAULT NULL,
  `custoAquisicao` decimal(10,2) DEFAULT NULL,
  `observacoes` text,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idEquipamento`),
  UNIQUE KEY `codigoInterno` (`codigoInterno`),
  UNIQUE KEY `numeroSerie` (`numeroSerie`),
  KEY `fk_Equipamento_CategoriaEquipamento` (`idCategoriaEquipamento`),
  KEY `fk_Equipamento_EstadoEquipamento` (`idEstadoEquipamento`),
  KEY `fk_Equipamento_CriticidadeEquipamento` (`idCriticidadeEquipamento`),
  KEY `fk_Equipamento_TipoEntrada` (`idTipoEntrada`),
  KEY `fk_Equipamento_Localizacao` (`idLocalizacao`),
  CONSTRAINT `fk_Equipamento_CategoriaEquipamento` FOREIGN KEY (`idCategoriaEquipamento`) REFERENCES `CategoriaEquipamento` (`idCategoriaEquipamento`),
  CONSTRAINT `fk_Equipamento_CriticidadeEquipamento` FOREIGN KEY (`idCriticidadeEquipamento`) REFERENCES `CriticidadeEquipamento` (`idCriticidadeEquipamento`),
  CONSTRAINT `fk_Equipamento_EstadoEquipamento` FOREIGN KEY (`idEstadoEquipamento`) REFERENCES `EstadoEquipamento` (`idEstadoEquipamento`),
  CONSTRAINT `fk_Equipamento_Localizacao` FOREIGN KEY (`idLocalizacao`) REFERENCES `Localizacao` (`idLocalizacao`),
  CONSTRAINT `fk_Equipamento_TipoEntrada` FOREIGN KEY (`idTipoEntrada`) REFERENCES `TipoEntrada` (`idTipoEntrada`),
  CONSTRAINT `ck_Equipamento_AnoFabrico` CHECK (((`anoFabrico` is null) or (`anoFabrico` >= 1900))),
  CONSTRAINT `ck_Equipamento_CustoAquisicao` CHECK (((`custoAquisicao` is null) or (`custoAquisicao` >= 0)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/* =========================================================
   Tabela EquipamentoFornecedor
   ========================================================= */
CREATE TABLE `EquipamentoFornecedor` (
  `idEquipamentoFornecedor` int NOT NULL AUTO_INCREMENT,
  `idEquipamento` int NOT NULL,
  `idFornecedor` int NOT NULL,
  `tipoRelacao` varchar(80) NOT NULL,
  `dataInicio` date DEFAULT NULL,
  `dataFim` date DEFAULT NULL,
  `observacoes` text,
  PRIMARY KEY (`idEquipamentoFornecedor`),
  UNIQUE KEY `uq_EquipamentoFornecedor_Tipo` (`idEquipamento`,`idFornecedor`,`tipoRelacao`),
  KEY `fk_EquipamentoFornecedor_Fornecedor` (`idFornecedor`),
  CONSTRAINT `fk_EquipamentoFornecedor_Equipamento` FOREIGN KEY (`idEquipamento`) REFERENCES `Equipamento` (`idEquipamento`),
  CONSTRAINT `fk_EquipamentoFornecedor_Fornecedor` FOREIGN KEY (`idFornecedor`) REFERENCES `Fornecedor` (`idFornecedor`),
  CONSTRAINT `ck_EquipamentoFornecedor_Datas` CHECK (((`dataFim` is null) or (`dataInicio` is null) or (`dataFim` >= `dataInicio`)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/* =========================================================
   Tabela MovimentacaoEquipamento
   ========================================================= */
CREATE TABLE `MovimentacaoEquipamento` (
  `idMovimentacaoEquipamento` int NOT NULL AUTO_INCREMENT,
  `idEquipamento` int NOT NULL,
  `idLocalizacao` int NOT NULL,
  `dataLocalizacao` date NOT NULL,
  `responsavel` varchar(120) NOT NULL,
  `motivo` varchar(200) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idMovimentacaoEquipamento`),
  KEY `fk_movimentacao_equipamento` (`idEquipamento`),
  KEY `fk_movimentacao_localizacao` (`idLocalizacao`),
  CONSTRAINT `fk_movimentacao_equipamento` FOREIGN KEY (`idEquipamento`) REFERENCES `Equipamento` (`idEquipamento`),
  CONSTRAINT `fk_movimentacao_localizacao` FOREIGN KEY (`idLocalizacao`) REFERENCES `Localizacao` (`idLocalizacao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/* =========================================================
   Tabela Documento
   ========================================================= */
CREATE TABLE `Documento` (
  `idDocumento` int NOT NULL AUTO_INCREMENT,
  `idEquipamento` int NOT NULL,
  `idTipoDocumento` int NOT NULL,
  `idFornecedor` int DEFAULT NULL,
  `nomeDocumento` varchar(150) NOT NULL,
  `dataDocumento` date DEFAULT NULL,
  `dataValidade` date DEFAULT NULL,
  `nomeFicheiro` varchar(150) DEFAULT NULL,
  `caminhoFicheiro` varchar(255) DEFAULT NULL,
  `observacoes` text,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idDocumento`),
  KEY `fk_Documento_Equipamento` (`idEquipamento`),
  KEY `fk_Documento_TipoDocumento` (`idTipoDocumento`),
  KEY `fk_Documento_Fornecedor` (`idFornecedor`),
  CONSTRAINT `fk_Documento_Equipamento` FOREIGN KEY (`idEquipamento`) REFERENCES `Equipamento` (`idEquipamento`),
  CONSTRAINT `fk_Documento_Fornecedor` FOREIGN KEY (`idFornecedor`) REFERENCES `Fornecedor` (`idFornecedor`),
  CONSTRAINT `fk_Documento_TipoDocumento` FOREIGN KEY (`idTipoDocumento`) REFERENCES `TipoDocumento` (`idTipoDocumento`),
  CONSTRAINT `ck_Documento_DataValidade` CHECK (((`dataValidade` is null) or (`dataDocumento` is null) or (`dataValidade` >= `dataDocumento`)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/* =========================================================
   Tabela GarantiaContrato
   ========================================================= */
CREATE TABLE `GarantiaContrato` (
  `idGarantiaContrato` int NOT NULL AUTO_INCREMENT,
  `idEquipamento` int NOT NULL,
  `idFornecedorResponsavel` int DEFAULT NULL,
  `tipo` varchar(80) NOT NULL,
  `numeroContrato` varchar(80) DEFAULT NULL,
  `dataInicio` date NOT NULL,
  `dataFim` date NOT NULL,
  `periodicidade` varchar(80) DEFAULT NULL,
  `observacoes` text,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idGarantiaContrato`),
  KEY `fk_GarantiaContrato_Equipamento` (`idEquipamento`),
  KEY `fk_GarantiaContrato_Fornecedor` (`idFornecedorResponsavel`),
  CONSTRAINT `fk_GarantiaContrato_Equipamento` FOREIGN KEY (`idEquipamento`) REFERENCES `Equipamento` (`idEquipamento`),
  CONSTRAINT `fk_GarantiaContrato_Fornecedor` FOREIGN KEY (`idFornecedorResponsavel`) REFERENCES `Fornecedor` (`idFornecedor`),
  CONSTRAINT `ck_GarantiaContrato_Datas` CHECK ((`dataFim` >= `dataInicio`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/* =========================================================
   Tabela LogSistema
   ========================================================= */
CREATE TABLE `LogSistema` (
  `idLogSistema` INT NOT NULL AUTO_INCREMENT,
  `idUtilizador` INT NULL,
  `username` VARCHAR(80) NULL,
  `perfil` VARCHAR(50) NULL,
  `tipoEvento` VARCHAR(80) NOT NULL,
  `descricao` TEXT NULL,
  `ip` VARCHAR(45) NULL,
  `dataHora` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idLogSistema`),
  KEY `idx_LogSistema_DataHora` (`dataHora`),
  KEY `idx_LogSistema_TipoEvento` (`tipoEvento`),
  KEY `idx_LogSistema_IdUtilizador` (`idUtilizador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

