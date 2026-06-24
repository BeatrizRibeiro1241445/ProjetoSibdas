/* =========================================================
   SIBDAS - Projeto MedInventário
   Ficheiro: 1241445.S02.INSERT.sql
   Objetivo: Inserção de dados de teste da base de dados
   Nota: executar depois do ficheiro 1241445.S01.ModeloFisico.sql
   ========================================================= */

SET FOREIGN_KEY_CHECKS = 0;


/* =========================================================
   Dados da tabela CategoriaEquipamento
   ========================================================= */

INSERT INTO `CategoriaEquipamento` (`idCategoriaEquipamento`, `descricao`) VALUES
	(1, 'Monitorização'),
	(2, 'Ventilação'),
	(3, 'Infusão'),
	(4, 'Emergência'),
	(5, 'Diagnóstico');


/* =========================================================
   Dados da tabela EstadoEquipamento
   ========================================================= */

INSERT INTO `EstadoEquipamento` (`idEstadoEquipamento`, `descricao`) VALUES
	(1, 'Ativo'),
	(2, 'Em manutenção'),
	(3, 'Inativo'),
	(4, 'Em calibração'),
	(5, 'Abatido');


/* =========================================================
   Dados da tabela CriticidadeEquipamento
   ========================================================= */

INSERT INTO `CriticidadeEquipamento` (`idCriticidadeEquipamento`, `descricao`) VALUES
	(1, 'Baixa'),
	(2, 'Média'),
	(3, 'Alta'),
	(4, 'Suporte de vida');


/* =========================================================
   Dados da tabela TipoEntrada
   ========================================================= */

INSERT INTO `TipoEntrada` (`idTipoEntrada`, `descricao`) VALUES
	(1, 'Compra'),
	(2, 'Doação'),
	(3, 'Aluguer'),
	(4, 'Empréstimo');


/* =========================================================
   Dados da tabela TipoDocumento
   ========================================================= */

INSERT INTO `TipoDocumento` (`idTipoDocumento`, `descricao`) VALUES
	(1, 'Manual de utilizador'),
	(2, 'Manual de serviço'),
	(3, 'Certificado de calibração'),
	(4, 'Contrato de manutenção'),
	(5, 'Fatura'),
	(6, 'Declaração de conformidade'),
	(7, 'Relatório técnico');


/* =========================================================
   Dados da tabela Localizacao
   ========================================================= */

INSERT INTO `Localizacao` (`idLocalizacao`, `categoria`, `edificio`, `piso`, `servico`, `sala`, `observacoes`, `ativo`) VALUES
	(1, 'Área clínica crítica', 'Hospital Central', '2', 'Unidade de Cuidados Intensivos', 'Sala 1', 'Localização destinada a equipamentos críticos e de suporte de vida.', 1),
	(2, 'Área cirúrgica', 'Hospital Central', '1', 'Bloco Operatório', 'Sala 2', 'Localização destinada a equipamentos utilizados em contexto cirúrgico.', 1),
	(3, 'Área de urgência', 'Hospital Central', '0', 'Urgência', 'Sala de Reanimação', 'Localização destinada a equipamentos de emergência.', 1),
	(4, 'Área técnica', 'Hospital Central', '3', 'Cardiologia', 'Gabinete Técnico', 'Localização de apoio técnico e diagnóstico.', 1),
	(5, 'Área de imagiologia', 'Hospital Central', '0', 'Imagiologia', 'Sala de Exames', 'Localização de equipamentos de diagnóstico por imagem.', 1),
	(6, 'Área neonatal', 'Hospital Central', '2', 'Neonatologia', 'Sala 3', 'Localização de equipamentos neonatais.', 1),
	(7, 'Área de internamento', 'Hospital Central', '4', 'Internamento', 'Enfermaria A', 'Localização de equipamentos de apoio ao internamento.', 1);


/* =========================================================
   Dados da tabela Fornecedor
   ========================================================= */

INSERT INTO `Fornecedor` (`idFornecedor`, `nif`, `email`, `designacao`, `telefone`, `morada`, `website`, `pessoaContacto`, `telefonePessoaContacto`, `pessoaContacto2`, `telefonePessoaContacto2`, `observacoes`, `ativo`) VALUES
	(1, '509000000', 'geral@medtech.pt', 'MedTech Portugal', '+351220000000', 'Rua da Saúde, Porto, Portugal', 'https://www.medtech.pt', 'Ana Martins', '+351914000000', NULL, NULL, 'Fornecedor associado a equipamentos de monitorização e manutenção hospitalar.', 1),
	(2, '508111111', 'contacto@philipsmedical.pt', 'Philips Medical Systems', '+351210000000', 'Avenida da Tecnologia, Lisboa, Portugal', 'https://www.philips.pt', 'Carlos Ferreira', '+351913000000', NULL, NULL, 'Fabricante de equipamentos médicos de monitorização.', 1),
	(3, '507222222', 'geral@hospitaldevices.pt', 'Hospital Devices S.A.', '+351221500001', 'Rua dos Dispositivos Médicos, Maia, Portugal', 'https://www.hospitaldevices.pt', 'Mariana Costa', '+351912000000', NULL, NULL, 'Fornecedor de equipamentos hospitalares e acessórios.', 1),
	(4, '510333444', 'geral@draeger.pt', 'Dräger Portugal', '+351219000004', 'Rua da Ventilação, Lisboa, Portugal', 'https://www.draeger.pt', 'Rui Pereira', '+351914111222', NULL, NULL, 'Fabricante de equipamentos de ventilação.', 1),
	(5, '511444555', 'info@gehealthcare.pt', 'GE Healthcare Portugal', '+351229000005', 'Avenida do Diagnóstico, Porto, Portugal', 'https://www.gehealthcare.pt', 'Sofia Andrade', '+351915333444', NULL, NULL, 'Fabricante de equipamentos de diagnóstico e monitorização.', 1),
	(6, '512555666', 'geral@bbraun.pt', 'B. Braun Medical Lda', '+351214000006', 'Rua da Infusão, Queluz, Portugal', 'https://www.bbraun.pt', 'Pedro Nunes', '+351916555666', NULL, NULL, 'Fabricante de bombas e sistemas de infusão.', 1),
	(7, '513666777', 'info@siemens-healthineers.pt', 'Siemens Healthineers Portugal', '+351210000007', 'Rua da Imagiologia, Amadora, Portugal', 'https://www.siemens-healthineers.pt', 'Inês Lopes', '+351917666777', NULL, NULL, 'Fabricante de equipamentos de imagiologia médica.', 1);


/* =========================================================
   Dados da tabela Utilizador
   ========================================================= */

INSERT INTO `Utilizador` (`idUtilizador`, `username`, `email`, `nome`, `passwordHash`, `perfil`, `ativo`, `lastLogin`, `dataFimContrato`) VALUES
	(1, 'beatriz.ribeiro', 'beatriz.ribeiro@medinventario.pt', 'Beatriz Ribeiro', '$2y$10$6GU4Ocq2NrSpomoO9/xC5.yHYgdlH8Xb9aMP8o8OnMKt6Ri5i/OA.', 'administrador', 1, '2026-06-22 02:54:36', '2026-12-31'),
	(2, 'miguel.ferreira', 'miguel.ferreira@medinventario.pt', 'Miguel Ferreira', '$2y$10$k3YbWGzEKOTnIqXLkc77P.ATebkRRgG6UCviIK0RXgdwdksVDumaC', 'tecnico', 1, '2026-06-22 00:31:56', '2026-09-30'),
	(3, 'helena.costa', 'helena.costa@medinventario.pt', 'Helena Costa', '$2y$10$7m5Z7Xx96pNfd9NYOwTch.99o.KglFHGoW88mdEBM9ZVdN2coY3Ym', 'gestor_hospitalar', 1, '2026-06-22 00:17:12', '2027-01-31'),
	(4, 'carla.santos', 'carla.santos@medinventario.pt', 'Carla Santos', '$2y$10$s/.cXFVQzabf1W8.secjS.kH9nGpt6S6R50j2q3m8UvK2Oo6hzUaq', 'profissional_saude', 1, '2026-06-20 23:14:49', '2026-07-31'),
	(5, 'joao.santos', 'joao.santos@medinventario.pt', 'João Santos', '$2y$10$bRb3sfWhsPIT9JJw2wntJO5Bl/y2XRCZjB1Lr.q6nUWcItF94NlQW', 'profissional_saude', 1, '2026-06-22 00:19:12', '2026-06-30');


/* =========================================================
   Dados da tabela ConteudoSite
   ========================================================= */

INSERT INTO `ConteudoSite` (`idConteudoSite`, `chave`, `seccao`, `titulo`, `texto`, `imagem`, `ativo`) VALUES
	(2, 'quem_somos', 'quem_somos', 'Quem Somos', 'A MedInventário é uma solução digital pensada para apoiar hospitais e serviços de saúde na gestão organizada do seu parque tecnológico.\r\n\r\nA plataforma centraliza informação essencial sobre equipamentos, fornecedores, localizações, documentação, garantias e contratos, facilitando o acesso rápido aos dados.', 'assets/img/equipa-biomedica.png', 1),
	(3, 'solucao', 'solucao', 'A Nossa Solução', 'O objetivo da MedInventário é disponibilizar uma plataforma organizada para apoiar o ciclo de vida dos equipamentos médicos, desde o registo inicial até à sua consulta, atualização ou desativação.\r\n\r\nA aplicação permite melhorar a rastreabilidade, facilitar a pesquisa de informação e apoiar decisões relacionadas com manutenção, garantias e documentação.', 'assets/img/solução.png', 1),
	(7, 'footer_localizacao', 'rodape', 'Localização', 'Porto, Portugal|Rua da Saúde 100|4249-000', NULL, 1),
	(8, 'footer_horario', 'rodape', 'Horário', 'Segunda a sexta: 09:00 - 18:00|Sábado: 09:00 - 13:00|Domingo: encerrado', NULL, 1),
	(9, 'footer_contactos', 'rodape', 'Contactos', 'geral@medinventario.pt|+351220000000|+351914000000', NULL, 1),
	(10, 'site_nome', 'navbar', 'MedInventário', 'MedInventário', 'assets/img/logo.png', 1),
	(11, 'nav_inicio', 'navbar', 'Início', 'Início', NULL, 1),
	(12, 'nav_quem_somos', 'navbar', 'Quem Somos', 'Quem Somos', NULL, 1),
	(13, 'nav_solucao', 'navbar', 'Solução', 'Solução', NULL, 1),
	(14, 'nav_funcionalidades', 'navbar', 'Funcionalidades', 'Funcionalidades', NULL, 1),
	(15, 'inicio', 'inicio', 'Sistema Web de Apoio ao Inventário Hospitalar', 'A MedInventário ajuda instituições de saúde a organizar, consultar e controlar equipamentos médicos de forma simples, centralizada e segura.', 'assets/img/hospital-digital.png', 1),
	(16, 'funcionalidades_intro', 'funcionalidades', 'Funcionalidades', 'A MedInventário organiza os principais módulos necessários para uma gestão clara, simples e centralizada do inventário hospitalar.', NULL, 1),
	(23, 'dashboard_publico', 'dashboard_publico', 'Informação centralizada para melhor decisão', 'Através de indicadores e alertas, a solução permite identificar equipamentos críticos, garantias próximas do fim, documentação em falta e estados de funcionamento.\r\n\r\nEsta informação ajuda os serviços técnicos e administrativos a acompanhar o inventário de forma mais rápida e estruturada.', 'assets/img/dashboard.png', 1);


/* =========================================================
   Dados da tabela Equipamento
   (distribuídos por categoria, estado, criticidade e serviço
    para o dashboard apresentar dados variados)
   ========================================================= */

INSERT INTO `Equipamento` (`idEquipamento`, `codigoInterno`, `numeroSerie`, `idCategoriaEquipamento`, `idEstadoEquipamento`, `idCriticidadeEquipamento`, `idTipoEntrada`, `idLocalizacao`, `designacao`, `marca`, `modelo`, `fabricante`, `dataAquisicao`, `anoFabrico`, `custoAquisicao`, `observacoes`, `ativo`) VALUES
	(1, '004.002.00', 'MP5-2022-45873', 1, 1, 4, 1, 1, 'Monitor Multiparamétrico', 'Philips', 'IntelliVue MP5', 'Philips Medical Systems', '2022-03-15', 2022, 4500.00, 'Equipamento de monitorização de sinais vitais utilizado em UCI.', 1),
	(2, '003.001.00', 'EV500-2021-55210', 2, 1, 3, 1, 3, 'Ventilador Pulmonar', 'Dräger', 'Evita V500', 'Dräger Medical', '2021-06-10', 2021, 18500.00, 'Equipamento utilizado em suporte ventilatório.', 1),
	(3, '007.001.00', 'INF-2020-88321', 3, 2, 3, 1, 1, 'Bomba de Infusão', 'B. Braun', 'Infusomat Space', 'B. Braun Medical', '2020-11-05', 2020, 2300.00, 'Equipamento em manutenção preventiva.', 1),
	(4, '006.001.00', 'DESF-2022-11980', 4, 1, 4, 1, 3, 'Desfibrilhador', 'Zoll', 'R Series', 'Zoll Medical', '2022-01-20', 2022, 7800.00, 'Equipamento de emergência localizado na sala de reanimação.', 1),
	(5, '004.003.00', 'MX700-2023-10122', 1, 1, 4, 1, 1, 'Monitor Multiparamétrico', 'Philips', 'IntelliVue MX700', 'Philips Medical Systems', '2023-02-10', 2023, 6200.00, 'Monitorização avançada em UCI.', 1),
	(6, '004.004.00', 'B450-2021-77810', 1, 1, 2, 1, 4, 'Monitor de Transporte', 'GE Healthcare', 'B450', 'GE Healthcare', '2021-09-01', 2021, 3100.00, 'Monitor utilizado no transporte de doentes.', 1),
	(7, '004.005.00', 'VS9-2019-33410', 1, 4, 2, 2, 5, 'Monitor de Sinais Vitais', 'Mindray', 'VS-9', 'Mindray', '2019-05-15', 2019, 2800.00, 'Equipamento em calibração programada.', 1),
	(8, '004.006.00', 'BSM-2024-99001', 1, 1, 1, 1, 7, 'Monitor de Sinais Vitais', 'Nihon Kohden', 'BSM-3000', 'Nihon Kohden', '2024-01-12', 2024, 2600.00, 'Monitorização em enfermaria.', 1),
	(9, '003.002.00', 'C6-2023-22019', 2, 1, 4, 1, 1, 'Ventilador Pulmonar', 'Hamilton', 'Hamilton-C6', 'Hamilton Medical', '2023-04-20', 2023, 21000.00, 'Ventilação invasiva em UCI.', 1),
	(10, '003.003.00', 'VN500-2020-55120', 2, 2, 4, 1, 6, 'Ventilador Neonatal', 'Dräger', 'Babylog VN500', 'Dräger Medical', '2020-07-08', 2020, 24000.00, 'Equipamento em manutenção corretiva.', 1),
	(11, '003.004.00', 'AST-2018-11200', 2, 3, 3, 2, 7, 'Ventilador Portátil', 'ResMed', 'Astral 150', 'ResMed', '2018-03-30', 2018, 9000.00, 'Equipamento inativo, a aguardar decisão técnica.', 1),
	(12, '007.002.00', 'PSP-2023-44120', 3, 1, 3, 1, 1, 'Bomba de Infusão', 'B. Braun', 'Perfusor Space', 'B. Braun Medical', '2023-06-15', 2023, 2400.00, 'Infusão controlada em UCI.', 1),
	(13, '007.003.00', 'AGL-2022-77810', 3, 1, 2, 1, 7, 'Bomba de Infusão', 'Fresenius', 'Agilia VP', 'Fresenius Kabi', '2022-10-02', 2022, 2200.00, 'Infusão em enfermaria.', 1),
	(14, '007.004.00', 'INJ-2019-22330', 3, 4, 2, 1, 2, 'Bomba de Seringa', 'Fresenius', 'Injectomat', 'Fresenius Kabi', '2019-11-20', 2019, 1900.00, 'Equipamento em calibração.', 1),
	(15, '006.002.00', 'MRX-2023-66200', 4, 1, 4, 1, 3, 'Desfibrilhador', 'Philips', 'HeartStart MRx', 'Philips Medical Systems', '2023-03-05', 2023, 8200.00, 'Emergência na sala de reanimação.', 1),
	(16, '006.003.00', 'AED-2021-31199', 4, 1, 3, 2, 7, 'Desfibrilhador Automático', 'Zoll', 'AED Plus', 'Zoll Medical', '2021-12-12', 2021, 1800.00, 'Desfibrilhador automático externo em enfermaria.', 1),
	(17, '006.004.00', 'VAR-2020-90021', 4, 2, 2, 1, 3, 'Aspirador de Secreções', 'Medela', 'Vario 18', 'Medela', '2020-02-18', 2020, 950.00, 'Equipamento em manutenção.', 1),
	(18, '005.001.00', 'S70-2022-50012', 5, 1, 3, 1, 4, 'Ecógrafo', 'GE Healthcare', 'Vivid S70', 'GE Healthcare', '2022-05-25', 2022, 42000.00, 'Diagnóstico cardiológico por imagem.', 1),
	(19, '005.002.00', 'MIRA-2019-77120', 5, 1, 3, 1, 5, 'Aparelho de Raio-X Portátil', 'Siemens', 'Mobilett Mira', 'Siemens Healthineers', '2019-08-14', 2019, 55000.00, 'Imagiologia de apoio à urgência.', 1),
	(20, '005.003.00', 'FT1-2024-11220', 5, 1, 1, 1, 4, 'Eletrocardiógrafo', 'Schiller', 'Cardiovit FT-1', 'Schiller', '2024-02-28', 2024, 3200.00, 'Realização de ECG em cardiologia.', 1),
	(21, '005.004.00', 'OLD-2017-00110', 5, 5, 1, 2, 5, 'Monitor de Diagnóstico Antigo', 'Generic', 'OldScan 1', 'Generic Medical', '2017-01-10', 2017, 1200.00, 'Equipamento abatido por fim de vida útil.', 0),
	(22, '002.001.00', 'ACC-2018-44550', 5, 5, 1, 2, 7, 'Glicómetro de Bancada', 'Roche', 'Accu-Chek', 'Roche', '2018-06-01', 2018, 800.00, 'Equipamento abatido.', 0);


/* =========================================================
   Dados da tabela EquipamentoFornecedor
   ========================================================= */

INSERT INTO `EquipamentoFornecedor` (`idEquipamentoFornecedor`, `idEquipamento`, `idFornecedor`, `tipoRelacao`, `dataInicio`, `dataFim`, `observacoes`) VALUES
	(1, 1, 1, 'Assistência técnica', '2022-03-15', NULL, 'Fornecedor responsável pela assistência técnica do monitor.'),
	(2, 1, 2, 'Fabricante', '2022-03-15', NULL, 'Fabricante do equipamento.'),
	(3, 2, 4, 'Fabricante', '2021-06-10', NULL, 'Fabricante do ventilador.'),
	(4, 2, 3, 'Fornecedor comercial', '2021-06-10', NULL, 'Fornecedor responsável pelo fornecimento do ventilador.'),
	(5, 3, 6, 'Fabricante', '2020-11-05', NULL, 'Fabricante da bomba de infusão.'),
	(6, 3, 1, 'Assistência técnica', '2020-11-05', NULL, 'Fornecedor associado à manutenção da bomba de infusão.'),
	(7, 4, 3, 'Fornecedor comercial', '2022-01-20', NULL, 'Fornecedor responsável pelo fornecimento do desfibrilhador.'),
	(8, 5, 2, 'Fabricante', '2023-02-10', NULL, 'Fabricante do monitor multiparamétrico.'),
	(9, 6, 5, 'Fabricante', '2021-09-01', NULL, 'Fabricante do monitor de transporte.'),
	(10, 9, 4, 'Fabricante', '2023-04-20', NULL, 'Fabricante do ventilador Hamilton.'),
	(11, 10, 4, 'Fabricante', '2020-07-08', NULL, 'Fabricante do ventilador neonatal.'),
	(12, 12, 6, 'Fabricante', '2023-06-15', NULL, 'Fabricante da bomba de infusão.'),
	(13, 15, 2, 'Fornecedor comercial', '2023-03-05', NULL, 'Fornecedor responsável pelo desfibrilhador.'),
	(14, 18, 5, 'Fabricante', '2022-05-25', NULL, 'Fabricante do ecógrafo.'),
	(15, 19, 7, 'Fabricante', '2019-08-14', NULL, 'Fabricante do aparelho de raio-X.'),
	(16, 20, 7, 'Fornecedor comercial', '2024-02-28', NULL, 'Fornecedor responsável pelo eletrocardiógrafo.');


/* =========================================================
   Dados da tabela MovimentacaoEquipamento
   (localização atual de cada equipamento)
   ========================================================= */

INSERT INTO `MovimentacaoEquipamento` (`idMovimentacaoEquipamento`, `idEquipamento`, `idLocalizacao`, `dataLocalizacao`, `responsavel`, `motivo`, `ativo`) VALUES
	(1, 1, 1, '2022-03-15', 'Registo existente', 'Localização atual', 1),
	(2, 2, 3, '2021-06-10', 'Registo existente', 'Localização atual', 1),
	(3, 3, 1, '2020-11-05', 'Registo existente', 'Localização atual', 1),
	(4, 4, 3, '2022-01-20', 'Registo existente', 'Localização atual', 1),
	(5, 5, 1, '2023-02-10', 'Registo existente', 'Localização atual', 1),
	(6, 6, 4, '2021-09-01', 'Registo existente', 'Localização atual', 1),
	(7, 7, 5, '2019-05-15', 'Registo existente', 'Localização atual', 1),
	(8, 8, 7, '2024-01-12', 'Registo existente', 'Localização atual', 1),
	(9, 9, 1, '2023-04-20', 'Registo existente', 'Localização atual', 1),
	(10, 10, 6, '2020-07-08', 'Registo existente', 'Localização atual', 1),
	(11, 11, 7, '2018-03-30', 'Registo existente', 'Localização atual', 1),
	(12, 12, 1, '2023-06-15', 'Registo existente', 'Localização atual', 1),
	(13, 13, 7, '2022-10-02', 'Registo existente', 'Localização atual', 1),
	(14, 14, 2, '2019-11-20', 'Registo existente', 'Localização atual', 1),
	(15, 15, 3, '2023-03-05', 'Registo existente', 'Localização atual', 1),
	(16, 16, 7, '2021-12-12', 'Registo existente', 'Localização atual', 1),
	(17, 17, 3, '2020-02-18', 'Registo existente', 'Localização atual', 1),
	(18, 18, 4, '2022-05-25', 'Registo existente', 'Localização atual', 1),
	(19, 19, 5, '2019-08-14', 'Registo existente', 'Localização atual', 1),
	(20, 20, 4, '2024-02-28', 'Registo existente', 'Localização atual', 1),
	(21, 21, 5, '2017-01-10', 'Registo existente', 'Localização atual', 1),
	(22, 22, 7, '2018-06-01', 'Registo existente', 'Localização atual', 1);


/* =========================================================
   Dados da tabela Documento
   (alguns equipamentos com documentos e outros sem;
    validades vencidas e a vencer para alimentar os alertas)
   Nota: os ficheiros apontam para os PDF existentes na pasta
         assets/uploads/documentos/ para os links funcionarem.
   ========================================================= */

INSERT INTO `Documento` (`idDocumento`, `idEquipamento`, `idTipoDocumento`, `idFornecedor`, `nomeDocumento`, `dataDocumento`, `dataValidade`, `nomeFicheiro`, `caminhoFicheiro`, `observacoes`, `ativo`) VALUES
	(1, 1, 1, 2, 'Manual de utilizador do Monitor Multiparamétrico', '2022-03-15', NULL, 'manual_ventilador.pdf', 'assets/uploads/documentos/manual_ventilador.pdf', 'Manual de utilizador fornecido pelo fabricante.', 1),
	(2, 1, 3, 1, 'Certificado de calibração do Monitor Multiparamétrico', '2025-06-10', '2026-06-10', 'certificado_calibracao_monitor.pdf', 'assets/uploads/documentos/certificado_calibracao_monitor.pdf', 'Certificado de calibração já fora de validade.', 1),
	(3, 3, 7, 1, 'Relatório técnico da Bomba de Infusão', '2024-04-12', NULL, 'manual_ventilador.pdf', 'assets/uploads/documentos/manual_ventilador.pdf', 'Relatório associado à manutenção preventiva.', 1),
	(4, 4, 3, 3, 'Certificado de verificação do Desfibrilhador', '2025-02-01', '2026-02-01', 'certificado_calibracao_monitor.pdf', 'assets/uploads/documentos/certificado_calibracao_monitor.pdf', 'Certificado de verificação fora de validade.', 1),
	(5, 2, 1, 3, 'Manual de utilizador do Ventilador Pulmonar', '2021-06-10', NULL, 'manual_ventilador.pdf', 'assets/uploads/documentos/manual_ventilador.pdf', 'Manual de utilizador do ventilador.', 1),
	(6, 5, 3, 2, 'Certificado de calibração do Monitor MX700', '2025-12-01', '2026-07-05', 'certificado_calibracao_monitor.pdf', 'assets/uploads/documentos/certificado_calibracao_monitor.pdf', 'Certificado com validade próxima do fim.', 1),
	(7, 9, 1, 4, 'Manual de utilizador do Ventilador Hamilton', '2023-04-20', NULL, 'manual_ventilador.pdf', 'assets/uploads/documentos/manual_ventilador.pdf', 'Manual de utilizador do ventilador Hamilton.', 1),
	(8, 12, 3, 6, 'Certificado de calibração da Bomba de Infusão', '2025-07-18', '2026-07-18', 'certificado_calibracao_monitor.pdf', 'assets/uploads/documentos/certificado_calibracao_monitor.pdf', 'Certificado com validade próxima do fim.', 1),
	(9, 15, 6, 2, 'Declaração de conformidade do Desfibrilhador', '2023-03-05', NULL, 'manual_ventilador.pdf', 'assets/uploads/documentos/manual_ventilador.pdf', 'Declaração de conformidade CE.', 1),
	(10, 18, 3, 5, 'Certificado de calibração do Ecógrafo', '2025-05-15', '2026-05-15', 'certificado_calibracao_monitor.pdf', 'assets/uploads/documentos/certificado_calibracao_monitor.pdf', 'Certificado de calibração fora de validade.', 1),
	(11, 19, 3, 7, 'Certificado de calibração do Aparelho de Raio-X', '2024-11-01', '2025-11-01', 'certificado_calibracao_monitor.pdf', 'assets/uploads/documentos/certificado_calibracao_monitor.pdf', 'Certificado de calibração fora de validade.', 1);


/* =========================================================
   Dados da tabela GarantiaContrato
   (garantias/contratos vencidos, a vencer e válidos)
   ========================================================= */

INSERT INTO `GarantiaContrato` (`idGarantiaContrato`, `idEquipamento`, `idFornecedorResponsavel`, `tipo`, `numeroContrato`, `dataInicio`, `dataFim`, `periodicidade`, `observacoes`, `ativo`) VALUES
	(1, 1, 1, 'Contrato de manutenção', 'CM-2022-004', '2022-03-15', '2026-03-14', 'Anual', 'Contrato de manutenção do monitor multiparamétrico (já terminado).', 1),
	(2, 2, 3, 'Garantia', 'GAR-2021-003', '2021-06-10', '2024-06-09', 'Pontual', 'Garantia inicial do ventilador pulmonar (terminada).', 1),
	(3, 3, 1, 'Contrato de manutenção', 'CM-2020-007', '2020-11-05', '2025-11-04', 'Semestral', 'Contrato de manutenção da bomba de infusão (terminado).', 1),
	(4, 4, 3, 'Garantia', 'GAR-2022-006', '2022-01-20', '2025-01-19', NULL, 'Garantia do desfibrilhador (terminada).', 1),
	(5, 6, 5, 'Garantia', 'GAR-2021-006', '2021-09-01', '2025-09-01', NULL, 'Garantia do monitor de transporte (terminada).', 1),
	(6, 19, 7, 'Contrato de manutenção', 'CM-2019-019', '2019-08-14', '2026-05-14', 'Anual', 'Contrato de manutenção do aparelho de raio-X (terminado).', 1),
	(7, 5, 2, 'Garantia', 'GAR-2023-010', '2023-02-10', '2026-07-10', 'Pontual', 'Garantia do monitor MX700, próxima do fim.', 1),
	(8, 12, 6, 'Garantia', 'GAR-2023-012', '2023-06-15', '2026-07-05', 'Pontual', 'Garantia da bomba de infusão, próxima do fim.', 1),
	(9, 15, 2, 'Garantia', 'GAR-2023-015', '2023-03-05', '2026-07-20', 'Pontual', 'Garantia do desfibrilhador, próxima do fim.', 1),
	(10, 9, 4, 'Contrato de manutenção', 'CM-2023-009', '2023-04-20', '2027-04-19', 'Anual', 'Contrato de manutenção do ventilador Hamilton (válido).', 1),
	(11, 18, 5, 'Contrato de manutenção', 'CM-2022-018', '2022-05-25', '2027-05-24', 'Anual', 'Contrato de manutenção do ecógrafo (válido).', 1),
	(12, 20, 7, 'Garantia', 'GAR-2024-020', '2024-02-28', '2028-02-27', NULL, 'Garantia do eletrocardiógrafo (válida).', 1);


SET FOREIGN_KEY_CHECKS = 1;
