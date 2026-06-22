-- --------------------------------------------------------
-- Anfitrião:                    vsgate-s1.dei.isep.ipp.pt
-- Versão do servidor:           8.0.45 - MySQL Community Server - GPL
-- SO do servidor:               Linux
-- HeidiSQL Versão:              12.17.0.7270
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- A despejar estrutura da base de dados para db1241445
CREATE DATABASE IF NOT EXISTS `db1241445` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `db1241445`;

-- A despejar estrutura para tabela db1241445.CategoriaEquipamento
CREATE TABLE IF NOT EXISTS `CategoriaEquipamento` (
  `idCategoriaEquipamento` int NOT NULL AUTO_INCREMENT,
  `descricao` varchar(80) NOT NULL,
  PRIMARY KEY (`idCategoriaEquipamento`),
  UNIQUE KEY `descricao` (`descricao`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela db1241445.CategoriaEquipamento: ~5 rows (aproximadamente)
INSERT INTO `CategoriaEquipamento` (`idCategoriaEquipamento`, `descricao`) VALUES
	(1, 'Monitorização'),
	(2, 'Ventilação'),
	(3, 'Infusão'),
	(4, 'Emergência'),
	(5, 'Diagnóstico');

-- A despejar estrutura para tabela db1241445.ConteudoSite
CREATE TABLE IF NOT EXISTS `ConteudoSite` (
  `idConteudoSite` int NOT NULL AUTO_INCREMENT,
  `chave` varchar(100) NOT NULL,
  `seccao` varchar(100) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `texto` text NOT NULL,
  `imagem` varchar(150) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idConteudoSite`),
  UNIQUE KEY `chave` (`chave`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela db1241445.ConteudoSite: ~23 rows (aproximadamente)
INSERT INTO `ConteudoSite` (`idConteudoSite`, `chave`, `seccao`, `titulo`, `texto`, `imagem`, `ativo`) VALUES
	(2, 'quem_somos', 'quem_somos', 'Quem Somos', 'A MedInventário é uma solução digital pensada para apoiar hospitais e serviços de saúde na gestão organizada do seu parque tecnológico.\r\n\r\nA plataforma centraliza informação essencial sobre equipamentos, fornecedores, localizações, documentação, garantias e contratos, facilitando o acesso rápido aos dados.', 'assets/img/equipa-biomedica.png', 1),
	(3, 'solucao', 'solucao', 'A Nossa Solução', 'O objetivo da MedInventário é disponibilizar uma plataforma organizada para apoiar o ciclo de vida dos equipamentos médicos, desde o registo inicial até à sua consulta, atualização ou desativação.\r\n\r\nA aplicação permite melhorar a rastreabilidade, facilitar a pesquisa de informação e apoiar decisões relacionadas com manutenção, garantias e documentação.', 'assets/img/solução.png', 1),
	(7, 'footer_localizacao', 'rodape', 'Localização', 'Porto, Portugal|Rua ************ 000|4249-000', NULL, 1),
	(8, 'footer_horario', 'rodape', 'Horário', 'Segunda a sexta: 09:00 - 18:00|Sábado: 09:00 - 13:00|Domingo: encerrado', NULL, 1),
	(9, 'footer_contactos', 'rodape', 'Contactos', 'geral@medinventario.pt|+351220000000|+351914000000', NULL, 1),
	(10, 'site_nome', 'navbar', 'MedInventário', 'MedInventário', 'assets/img/logo.png', 1),
	(11, 'nav_inicio', 'navbar', 'Início', 'Início', NULL, 1),
	(12, 'nav_quem_somos', 'navbar', 'Quem Somos', 'Quem Somos', NULL, 1),
	(13, 'nav_solucao', 'navbar', 'Solução', 'Solução', NULL, 1),
	(14, 'nav_funcionalidades', 'navbar', 'Funcionalidades', 'Funcionalidades', NULL, 1),
	(15, 'inicio', 'inicio', 'Sistema Web de Apoio ao Inventário Hospitalar', 'A MedInventário ajuda instituições de saúde a organizar, consultar e controlar equipamentos médicos de forma simples, centralizada e segura.', 'assets/img/hospital-digital.png', 1),
	(16, 'funcionalidades_intro', 'funcionalidades', 'Funcionalidades', 'A MedInventário organiza os principais módulos necessários para uma gestão clara, simples e centralizada do inventário hospitalar.', NULL, 1),
	(17, 'funcionalidade_1', 'funcionalidades', 'Equipamentos', 'Registo, consulta e atualização dos equipamentos médicos existentes.', 'fas fa-laptop-medical', 1),
	(18, 'funcionalidade_2', 'funcionalidades', 'Localizações', 'Associação dos equipamentos a edifícios, pisos, serviços e salas.', 'fas fa-location-dot', 1),
	(19, 'funcionalidade_3', 'funcionalidades', 'Fornecedores', 'Gestão de empresas, contactos e associações aos equipamentos.', 'fas fa-truck-medical', 1),
	(20, 'funcionalidade_4', 'funcionalidades', 'Documentação', 'Organização de manuais, certificados e documentos técnicos.', 'fas fa-file-medical', 1),
	(21, 'funcionalidade_5', 'funcionalidades', 'Garantias', 'Consulta de garantias, contratos e entidades responsáveis.', 'fas fa-file-contract', 1),
	(22, 'funcionalidade_6', 'funcionalidades', 'Dashboard', 'Indicadores, alertas e resumo do estado do inventário.', 'fas fa-chart-simple', 1),
	(23, 'dashboard_publico', 'dashboard_publico', 'Informação centralizada para melhor decisão', 'Através de indicadores e alertas, a solução permite identificar equipamentos críticos, garantias próximas do fim, documentação em falta e estados de funcionamento.\r\n\r\nEsta informação ajuda os serviços técnicos e administrativos a acompanhar o inventário de forma mais rápida e estruturada.', 'assets/img/dashboard.png', 1);

-- A despejar estrutura para tabela db1241445.CriticidadeEquipamento
CREATE TABLE IF NOT EXISTS `CriticidadeEquipamento` (
  `idCriticidadeEquipamento` int NOT NULL AUTO_INCREMENT,
  `descricao` varchar(80) NOT NULL,
  PRIMARY KEY (`idCriticidadeEquipamento`),
  UNIQUE KEY `descricao` (`descricao`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela db1241445.CriticidadeEquipamento: ~4 rows (aproximadamente)
INSERT INTO `CriticidadeEquipamento` (`idCriticidadeEquipamento`, `descricao`) VALUES
	(1, 'Baixa'),
	(2, 'Média'),
	(3, 'Alta'),
	(4, 'Suporte de vida');

-- A despejar estrutura para tabela db1241445.Documento
CREATE TABLE IF NOT EXISTS `Documento` (
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela db1241445.Documento: ~8 rows (aproximadamente)
INSERT INTO `Documento` (`idDocumento`, `idEquipamento`, `idTipoDocumento`, `idFornecedor`, `nomeDocumento`, `dataDocumento`, `dataValidade`, `nomeFicheiro`, `caminhoFicheiro`, `observacoes`, `ativo`) VALUES
	(1, 1, 1, 2, 'Manual de utilizador do Monitor Multiparamétrico', '2022-03-15', NULL, 'manual_monitor_multiparametrico.pdf', 'assets/uploads/documentos/manual_monitor_multiparametrico.pdf', 'Manual de utilizador fornecido pelo fabricante.', 1),
	(2, 1, 3, 1, 'Certificado de calibração do Monitor Multiparamétrico', '2024-01-10', '2025-01-10', 'certificado_calibracao_monitor.pdf', 'assets/uploads/documentos/certificado_calibracao_monitor.pdf', 'Certificado de calibração anual.', 1),
	(4, 3, 7, 1, 'Relatório técnico da Bomba de Infusão', '2024-04-12', NULL, 'relatorio_bomba_infusao.pdf', 'assets/uploads/documentos/relatorio_bomba_infusao.pdf', 'Relatório associado à manutenção preventiva.', 1),
	(5, 4, 3, 3, 'Certificado de verificação do Desfibrilhador', '2024-02-01', '2025-02-01', 'certificado_desfibrilhador.pdf', 'assets/uploads/documentos/certificado_desfibrilhador.pdf', 'Certificado de verificação funcional.', 1),
	(8, 2, 1, 3, 'Manual de utilizador do Ventilador Pulmonar', '2021-06-10', NULL, 'manual_ventilador.pdf', 'assets/uploads/documentos/manual_ventilador.pdf', NULL, 1);

-- A despejar estrutura para tabela db1241445.Equipamento
CREATE TABLE IF NOT EXISTS `Equipamento` (
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela db1241445.Equipamento: ~4 rows (aproximadamente)
INSERT INTO `Equipamento` (`idEquipamento`, `codigoInterno`, `numeroSerie`, `idCategoriaEquipamento`, `idEstadoEquipamento`, `idCriticidadeEquipamento`, `idTipoEntrada`, `idLocalizacao`, `designacao`, `marca`, `modelo`, `fabricante`, `dataAquisicao`, `anoFabrico`, `custoAquisicao`, `observacoes`, `ativo`) VALUES
	(1, '004.002.00', 'MP5-2022-45873', 1, 1, 4, 1, 1, 'Monitor Multiparamétrico', 'Philips', 'IntelliVue MP5', 'Philips Medical Systems', '2022-03-15', 2022, 4500.00, 'Equipamento de monitorização de sinais vitais utilizado em UCI.', 1),
	(2, '003.001.00', 'EV500-2021-55210', 2, 1, 3, 1, 3, 'Ventilador Pulmonar', 'Dräger', 'Evita V500', 'Dräger Medical', '2021-06-10', 2021, 18500.00, 'Equipamento utilizado em suporte ventilatório.', 1),
	(3, '007.001.00', 'INF-2020-88321', 3, 2, 3, 1, 1, 'Bomba de Infusão', 'B. Braun', 'Infusomat Space', 'B. Braun Medical', '2020-11-05', 2020, 2300.00, 'Equipamento em manutenção preventiva.', 1),
	(4, '006.001.00', 'DESF-2022-11980', 4, 1, 4, 1, 3, 'Desfibrilhador', 'Zoll', 'R Series', 'Zoll Medical', '2022-01-20', 2022, 7800.00, 'Equipamento de emergência localizado na sala de reanimação.', 1);

-- A despejar estrutura para tabela db1241445.EquipamentoFornecedor
CREATE TABLE IF NOT EXISTS `EquipamentoFornecedor` (
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela db1241445.EquipamentoFornecedor: ~5 rows (aproximadamente)
INSERT INTO `EquipamentoFornecedor` (`idEquipamentoFornecedor`, `idEquipamento`, `idFornecedor`, `tipoRelacao`, `dataInicio`, `dataFim`, `observacoes`) VALUES
	(1, 1, 1, 'Assistência técnica', '2022-03-15', NULL, 'Fornecedor responsável pela assistência técnica do monitor.'),
	(2, 1, 2, 'Fabricante', '2022-03-15', NULL, 'Fabricante do equipamento.'),
	(4, 3, 1, 'Assistência técnica', '2020-11-05', NULL, 'Fornecedor associado à manutenção da bomba de infusão.'),
	(5, 4, 3, 'Fornecedor comercial', '2022-01-20', NULL, 'Fornecedor responsável pelo fornecimento do desfibrilhador.'),
	(8, 2, 3, 'Fornecedor comercial', '2021-06-10', NULL, 'Fornecedor responsável pelo fornecimento do ventilador.');

-- A despejar estrutura para tabela db1241445.EstadoEquipamento
CREATE TABLE IF NOT EXISTS `EstadoEquipamento` (
  `idEstadoEquipamento` int NOT NULL AUTO_INCREMENT,
  `descricao` varchar(80) NOT NULL,
  PRIMARY KEY (`idEstadoEquipamento`),
  UNIQUE KEY `descricao` (`descricao`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela db1241445.EstadoEquipamento: ~5 rows (aproximadamente)
INSERT INTO `EstadoEquipamento` (`idEstadoEquipamento`, `descricao`) VALUES
	(1, 'Ativo'),
	(2, 'Em manutenção'),
	(3, 'Inativo'),
	(4, 'Em calibração'),
	(5, 'Abatido');

-- A despejar estrutura para tabela db1241445.Fornecedor
CREATE TABLE IF NOT EXISTS `Fornecedor` (
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela db1241445.Fornecedor: ~5 rows (aproximadamente)
INSERT INTO `Fornecedor` (`idFornecedor`, `nif`, `email`, `designacao`, `telefone`, `morada`, `website`, `pessoaContacto`, `telefonePessoaContacto`, `pessoaContacto2`, `telefonePessoaContacto2`, `observacoes`, `ativo`) VALUES
	(1, '509000000', 'geral@medtech.pt', 'MedTech Portugal', '+351220000000', 'Rua da Saúde, Porto, Portugal', 'https://www.medtech.pt', 'Ana Martins', '+351914000000', NULL, NULL, 'Fornecedor associado a equipamentos de monitorização e manutenção hospitalar.', 1),
	(2, '508111111', 'contacto@philipsmedical.pt', 'Philips Medical Systems', '+351210000000', 'Avenida da Tecnologia, Lisboa, Portugal', 'https://www.philips.pt', 'Carlos Ferreira', '+351913000000', NULL, NULL, 'Fabricante de equipamentos médicos de monitorização.', 1),
	(3, '507222222', 'geral@hospitaldevices.pt', 'Hospital Devices S.A.', '+351221500001', 'Rua dos Dispositivos Médicos, Maia, Portugal', 'https://www.hospitaldevices.pt', 'Mariana Costa', '+351912000000', NULL, NULL, 'Fornecedor de equipamentos hospitalares e acessórios.', 1),
	(4, '509888777', 'testeclinico@fornecedor.pt', 'Teste Clínico Lda', '+351220123123', 'Rua de Teste, Porto', 'https://www.testeclinico.pt', 'Ana Silva', '+351910111111', 'João Costa', '+351911222222', 'Fornecedor criado para teste da Ficha 12', 1);

-- A despejar estrutura para tabela db1241445.GarantiaContrato
CREATE TABLE IF NOT EXISTS `GarantiaContrato` (
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela db1241445.GarantiaContrato: ~4 rows (aproximadamente)
INSERT INTO `GarantiaContrato` (`idGarantiaContrato`, `idEquipamento`, `idFornecedorResponsavel`, `tipo`, `numeroContrato`, `dataInicio`, `dataFim`, `periodicidade`, `observacoes`, `ativo`) VALUES
	(1, 1, 1, 'Contrato de manutenção', 'CM-2022-004', '2022-03-15', '2026-03-14', 'Anual', 'Contrato de manutenção preventiva e corretiva do monitor multiparamétrico.', 1),
	(2, 2, 3, 'Garantia', 'GAR-2021-003', '2021-06-10', '2024-06-09', 'Pontual', 'Garantia inicial do ventilador pulmonar.', 1),
	(3, 3, 1, 'Contrato de manutenção', 'CM-2020-007', '2020-11-05', '2025-11-04', 'Semestral', 'Contrato de manutenção da bomba de infusão.', 1),
	(4, 4, 3, 'Garantia', 'GAR-2022-006', '2022-01-20', '2025-01-19', NULL, 'Garantia do desfibrilhador.', 1);

-- A despejar estrutura para tabela db1241445.Localizacao
CREATE TABLE IF NOT EXISTS `Localizacao` (
  `idLocalizacao` int NOT NULL AUTO_INCREMENT,
  `categoria` varchar(100) NOT NULL,
  `edificio` varchar(100) NOT NULL,
  `piso` varchar(50) NOT NULL,
  `servico` varchar(120) NOT NULL,
  `sala` varchar(80) NOT NULL,
  `observacoes` text,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idLocalizacao`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela db1241445.Localizacao: ~6 rows (aproximadamente)
INSERT INTO `Localizacao` (`idLocalizacao`, `categoria`, `edificio`, `piso`, `servico`, `sala`, `observacoes`, `ativo`) VALUES
	(1, 'Área clínica crítica', 'Hospital Central', '2', 'Unidade de Cuidados Intensivos', 'Sala 1', 'Localização destinada a equipamentos críticos e de suporte de vida.', 1),
	(2, 'Área cirúrgica', 'Hospital Central', '1', 'Bloco Operatório', 'Sala 2', 'Localização destinada a equipamentos utilizados em contexto cirúrgico.', 1),
	(3, 'Área de urgência', 'Hospital Central', '0', 'Urgência', 'Sala de Reanimação', 'Localização destinada a equipamentos de emergência.', 1),
	(4, 'Área técnica', 'Hospital Central', '3', 'Cardiologia', 'Gabinete Técnico', 'Localização de apoio técnico e diagnóstico.', 1),
	(5, 'Área Clinica', 'Hospital Central', '2', 'Unidade de Cuidados Intensivos', 'Sala 1', NULL, 1),
	(6, 'A', 'A', '3', 'A', 'Sala 2', NULL, 0);

-- A despejar estrutura para tabela db1241445.MovimentacaoEquipamento
CREATE TABLE IF NOT EXISTS `MovimentacaoEquipamento` (
  `idMovimentacaoEquipamento` int NOT NULL AUTO_INCREMENT,
  `idEquipamento` int NOT NULL,
  `idLocalizacao` int NOT NULL,
  `dataLocalizacao` date NOT NULL,
  `responsavel` varchar(120) COLLATE utf8mb4_bin NOT NULL,
  `motivo` varchar(200) COLLATE utf8mb4_bin NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idMovimentacaoEquipamento`),
  KEY `fk_movimentacao_equipamento` (`idEquipamento`),
  KEY `fk_movimentacao_localizacao` (`idLocalizacao`),
  CONSTRAINT `fk_movimentacao_equipamento` FOREIGN KEY (`idEquipamento`) REFERENCES `Equipamento` (`idEquipamento`),
  CONSTRAINT `fk_movimentacao_localizacao` FOREIGN KEY (`idLocalizacao`) REFERENCES `Localizacao` (`idLocalizacao`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- A despejar dados para tabela db1241445.MovimentacaoEquipamento: ~4 rows (aproximadamente)
INSERT INTO `MovimentacaoEquipamento` (`idMovimentacaoEquipamento`, `idEquipamento`, `idLocalizacao`, `dataLocalizacao`, `responsavel`, `motivo`, `ativo`) VALUES
	(1, 1, 1, '2022-03-15', 'Registo existente', 'Localização atual', 1),
	(2, 2, 3, '2021-06-10', 'Registo existente', 'Localização atual', 1),
	(3, 3, 1, '2020-11-05', 'Registo existente', 'Localização atual', 1),
	(4, 4, 3, '2022-01-20', 'Registo existente', 'Localização atual', 1);

-- A despejar estrutura para tabela db1241445.TipoDocumento
CREATE TABLE IF NOT EXISTS `TipoDocumento` (
  `idTipoDocumento` int NOT NULL AUTO_INCREMENT,
  `descricao` varchar(100) NOT NULL,
  PRIMARY KEY (`idTipoDocumento`),
  UNIQUE KEY `descricao` (`descricao`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela db1241445.TipoDocumento: ~7 rows (aproximadamente)
INSERT INTO `TipoDocumento` (`idTipoDocumento`, `descricao`) VALUES
	(1, 'Manual de utilizador'),
	(2, 'Manual de serviço'),
	(3, 'Certificado de calibração'),
	(4, 'Contrato de manutenção'),
	(5, 'Fatura'),
	(6, 'Declaração de conformidade'),
	(7, 'Relatório técnico');

-- A despejar estrutura para tabela db1241445.TipoEntrada
CREATE TABLE IF NOT EXISTS `TipoEntrada` (
  `idTipoEntrada` int NOT NULL AUTO_INCREMENT,
  `descricao` varchar(80) NOT NULL,
  PRIMARY KEY (`idTipoEntrada`),
  UNIQUE KEY `descricao` (`descricao`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela db1241445.TipoEntrada: ~4 rows (aproximadamente)
INSERT INTO `TipoEntrada` (`idTipoEntrada`, `descricao`) VALUES
	(1, 'Compra'),
	(2, 'Doação'),
	(3, 'Aluguer'),
	(4, 'Empréstimo');

-- A despejar estrutura para tabela db1241445.Utilizador
CREATE TABLE IF NOT EXISTS `Utilizador` (
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
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- A despejar dados para tabela db1241445.Utilizador: ~7 rows (aproximadamente)
INSERT INTO `Utilizador` (`idUtilizador`, `username`, `email`, `nome`, `passwordHash`, `perfil`, `ativo`, `lastLogin`, `dataFimContrato`) VALUES
	(1, 'beatriz.ribeiro', 'beatriz.ribeiro@medinventario.pt', 'Beatriz Ribeiro', '111111', 'administrador', 1, '2026-06-22 02:54:36', '2026-12-31'),
	(2, 'miguel.ferreira', 'miguel.ferreira@medinventario.pt', 'Miguel Ferreira', 'miguel1234', 'tecnico', 1, '2026-06-22 00:31:56', '2026-09-30'),
	(3, 'helena.costa', 'helena.costa@medinventario.pt', 'Helena Costa', 'helena1234', 'gestor_hospitalar', 1, '2026-06-22 00:17:12', '2027-01-31'),
	(4, 'carla.santos', 'carla.santos@medinventario.pt', 'Carla Santos', 'carla11234', 'profissional_saude', 1, '2026-06-20 23:14:49', '2026-07-31'),
	(5, 'joao.santos', 'joao.santos@medinventario.pt', 'João Santos', 'joao1234', 'profissional_saude', 1, '2026-06-22 00:19:12', '2026-06-30');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
