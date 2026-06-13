INSERT INTO CategoriaEquipamento (descricao) VALUES
('Monitorização'),
('Ventilação'),
('Infusão'),
('Emergência'),
('Diagnóstico');

INSERT INTO EstadoEquipamento (descricao) VALUES
('Ativo'),
('Em manutenção'),
('Inativo'),
('Em calibração'),
('Abatido');

INSERT INTO CriticidadeEquipamento (descricao) VALUES
('Baixa'),
('Média'),
('Alta'),
('Suporte de vida');

INSERT INTO TipoEntrada (descricao) VALUES
('Compra'),
('Doação'),
('Aluguer'),
('Empréstimo');

INSERT INTO TipoDocumento (descricao) VALUES
('Manual de utilizador'),
('Manual de serviço'),
('Certificado de calibração'),
('Contrato de manutenção'),
('Fatura'),
('Declaração de conformidade'),
('Relatório técnico');

INSERT INTO Localizacao (categoria, edificio, piso, servico, sala, observacoes, ativo) VALUES
('Área clínica crítica', 'Hospital Central', 'Piso 2', 'Unidade de Cuidados Intensivos', 'Sala 1', 'Localização destinada a equipamentos críticos e de suporte de vida.', true),
('Área cirúrgica', 'Hospital Central', 'Piso 1', 'Bloco Operatório', 'Sala 2', 'Localização destinada a equipamentos utilizados em contexto cirúrgico.', true),
('Área de urgência', 'Hospital Central', 'Piso 0', 'Urgência', 'Sala de Reanimação', 'Localização destinada a equipamentos de emergência.', true),
('Área técnica', 'Hospital Central', 'Piso 3', 'Cardiologia', 'Gabinete Técnico', 'Localização de apoio técnico e diagnóstico.', true);

INSERT INTO Fornecedor (nif, email, designacao, telefone, morada, website, pessoaContacto, telefonePessoaContacto, tipoFornecedor, observacoes, ativo) VALUES
('509000000', 'geral@medtech.pt', 'MedTech Portugal', '+351 220 000 000', 'Rua da Saúde, Porto, Portugal', 'https://www.medtech.pt', 'Ana Martins', '+351 914 000 000', 'Assistência técnica', 'Fornecedor associado a equipamentos de monitorização e manutenção hospitalar.', true),
('508111111', 'contacto@philipsmedical.pt', 'Philips Medical Systems', '+351 210 000 000', 'Avenida da Tecnologia, Lisboa, Portugal', 'https://www.philips.pt', 'Carlos Ferreira', '+351 913 000 000', 'Fabricante', 'Fabricante de equipamentos médicos de monitorização.', true),
('507222222', 'geral@hospitaldevices.pt', 'Hospital Devices S.A.', '+351 221 500 000', 'Rua dos Dispositivos Médicos, Maia, Portugal', 'https://www.hospitaldevices.pt', 'Mariana Costa', '+351 912 000 000', 'Fornecedor comercial', 'Fornecedor de equipamentos hospitalares e acessórios.', true);

INSERT INTO Utilizador (username, email, nome, passwordHash, perfil, ativo) VALUES
('admin', 'admin@medinventario.pt', 'Administrador MedInventário', '$2y$12$FQCHk/mRXZ3OH/BcYC6LAuS.AyIpJAAeYlln3GCvC6v3OYgypkiKe', 'administrador', true);

INSERT INTO ConteudoSite (chave, seccao, titulo, texto, imagem, ativo) VALUES
('hero_titulo', 'inicio', 'Sistema Web de Apoio ao Inventário Hospitalar', 'A MedInventário ajuda instituições de saúde a organizar, consultar e controlar equipamentos médicos de forma simples, centralizada e segura.', 'hospital-digital.png', true),
('quem_somos', 'quem-somos', 'Quem Somos', 'A MedInventário é uma solução digital pensada para apoiar hospitais e serviços de saúde na gestão organizada do seu parque tecnológico.', 'equipa-biomedica.png', true),
('solucao', 'solucao', 'A Nossa Solução', 'A aplicação permite melhorar a rastreabilidade, facilitar a pesquisa de informação e apoiar decisões relacionadas com manutenção, garantias e documentação.', 'solução.png', true),
('contacto_email', 'contactos', 'Email', 'geral@medinventario.pt', null, true),
('contacto_telefone', 'contactos', 'Telefone', '+351 220 000 000', null, true),
('morada', 'contactos', 'Morada', 'Porto, Portugal', null, true);

INSERT INTO Equipamento (
  codigoInterno,
  numeroSerie,
  idCategoriaEquipamento,
  idEstadoEquipamento,
  idCriticidadeEquipamento,
  idTipoEntrada,
  idLocalizacao,
  designacao,
  marca,
  modelo,
  fabricante,
  dataAquisicao,
  anoFabrico,
  custoAquisicao,
  observacoes,
  ativo
) VALUES
('004.002.00', 'MP5-2022-45873', 1, 1, 4, 1, 1, 'Monitor Multiparamétrico', 'Philips', 'IntelliVue MP5', 'Philips Medical Systems', '2022-03-15', 2022, 4500.00, 'Equipamento de monitorização de sinais vitais utilizado em UCI.', true),
('003.001.00', 'EV500-2021-55210', 2, 1, 3, 1, 1, 'Ventilador Pulmonar', 'Dräger', 'Evita V500', 'Dräger Medical', '2021-06-10', 2021, 18500.00, 'Equipamento utilizado em suporte ventilatório.', true),
('007.001.00', 'INF-2020-88321', 3, 2, 3, 1, 1, 'Bomba de Infusão', 'B. Braun', 'Infusomat Space', 'B. Braun Medical', '2020-11-05', 2020, 2300.00, 'Equipamento em manutenção preventiva.', true),
('006.001.00', 'DESF-2022-11980', 4, 1, 4, 1, 3, 'Desfibrilhador', 'Zoll', 'R Series', 'Zoll Medical', '2022-01-20', 2022, 7800.00, 'Equipamento de emergência localizado na sala de reanimação.', true);

INSERT INTO EquipamentoFornecedor (idEquipamento, idFornecedor, tipoRelacao, dataInicio, dataFim, observacoes) VALUES
(1, 1, 'Assistência técnica', '2022-03-15', null, 'Fornecedor responsável pela assistência técnica do monitor.'),
(1, 2, 'Fabricante', '2022-03-15', null, 'Fabricante do equipamento.'),
(2, 3, 'Fornecedor comercial', '2021-06-10', null, 'Fornecedor responsável pelo fornecimento do ventilador.'),
(3, 1, 'Manutenção', '2020-11-05', null, 'Fornecedor associado à manutenção da bomba de infusão.'),
(4, 3, 'Fornecedor comercial', '2022-01-20', null, 'Fornecedor responsável pelo fornecimento do desfibrilhador.');

INSERT INTO Documento (
  idEquipamento,
  idTipoDocumento,
  idFornecedor,
  nomeDocumento,
  dataDocumento,
  dataValidade,
  nomeFicheiro,
  caminhoFicheiro,
  observacoes,
  ativo
) VALUES
(1, 1, 2, 'Manual de utilizador do Monitor Multiparamétrico', '2022-03-15', null, 'manual_monitor_multiparametrico.pdf', 'uploads/documentos/manual_monitor_multiparametrico.pdf', 'Manual de utilizador fornecido pelo fabricante.', true),
(1, 3, 1, 'Certificado de calibração do Monitor Multiparamétrico', '2024-01-10', '2025-01-10', 'certificado_calibracao_monitor.pdf', 'uploads/documentos/certificado_calibracao_monitor.pdf', 'Certificado de calibração anual.', true),
(2, 1, 3, 'Manual de utilizador do Ventilador Pulmonar', '2021-06-10', null, 'manual_ventilador.pdf', 'uploads/documentos/manual_ventilador.pdf', 'Manual técnico do ventilador pulmonar.', true),
(3, 7, 1, 'Relatório técnico da Bomba de Infusão', '2024-04-12', null, 'relatorio_bomba_infusao.pdf', 'uploads/documentos/relatorio_bomba_infusao.pdf', 'Relatório associado à manutenção preventiva.', true),
(4, 3, 3, 'Certificado de verificação do Desfibrilhador', '2024-02-01', '2025-02-01', 'certificado_desfibrilhador.pdf', 'uploads/documentos/certificado_desfibrilhador.pdf', 'Certificado de verificação funcional.', true);

INSERT INTO GarantiaContrato (
  idEquipamento,
  idFornecedorResponsavel,
  tipo,
  numeroContrato,
  dataInicio,
  dataFim,
  periodicidade,
  observacoes,
  ativo
) VALUES
(1, 1, 'Contrato de manutenção', 'CM-2022-004', '2022-03-15', '2026-03-14', 'Anual', 'Contrato de manutenção preventiva e corretiva do monitor multiparamétrico.', true),
(2, 3, 'Garantia', 'GAR-2021-003', '2021-06-10', '2024-06-09', null, 'Garantia inicial do ventilador pulmonar.', true),
(3, 1, 'Contrato de manutenção', 'CM-2020-007', '2020-11-05', '2025-11-04', 'Semestral', 'Contrato de manutenção da bomba de infusão.', true),
(4, 3, 'Garantia', 'GAR-2022-006', '2022-01-20', '2025-01-19', null, 'Garantia do desfibrilhador.', true);

INSERT INTO ConteudoSite (chave, seccao, titulo, texto, imagem, ativo) VALUES
('site_nome', 'navbar', 'MedInventário', 'MedInventário', 'logo.png', true),

('nav_inicio', 'navbar', 'Início', 'Início', NULL, true),
('nav_quem_somos', 'navbar', 'Quem Somos', 'Quem Somos', NULL, true),
('nav_solucao', 'navbar', 'Solução', 'Solução', NULL, true),
('nav_funcionalidades', 'navbar', 'Funcionalidades', 'Funcionalidades', NULL, true),

('inicio', 'inicio', 'Sistema Web de Apoio ao Inventário Hospitalar',
'A MedInventário ajuda instituições de saúde a organizar, consultar e controlar equipamentos médicos de forma simples, centralizada e segura.',
'assets/img/hospital-digital.png', true),

('quem_somos', 'quem_somos', 'Quem Somos',
'A MedInventário é uma solução digital pensada para apoiar hospitais e serviços de saúde na gestão organizada do seu parque tecnológico.

A plataforma centraliza informação essencial sobre equipamentos, fornecedores, localizações, documentação, garantias e contratos, facilitando o acesso rápido aos dados.',
'assets/img/equipa-biomedica.png', true),

('solucao', 'solucao', 'A Nossa Solução',
'O objetivo da MedInventário é disponibilizar uma plataforma organizada para apoiar o ciclo de vida dos equipamentos médicos, desde o registo inicial até à sua consulta, atualização ou desativação.

A aplicação permite melhorar a rastreabilidade, facilitar a pesquisa de informação e apoiar decisões relacionadas com manutenção, garantias e documentação.',
'assets/img/solução.png', true),

('funcionalidades_intro', 'funcionalidades', 'Funcionalidades',
'A MedInventário organiza os principais módulos necessários para uma gestão clara, simples e centralizada do inventário hospitalar.',
NULL, true),

('funcionalidade_1', 'funcionalidades', 'Equipamentos',
'Registo, consulta e atualização dos equipamentos médicos existentes.',
'fas fa-laptop-medical', true),

('funcionalidade_2', 'funcionalidades', 'Localizações',
'Associação dos equipamentos a edifícios, pisos, serviços e salas.',
'fas fa-location-dot', true),

('funcionalidade_3', 'funcionalidades', 'Fornecedores',
'Gestão de empresas, contactos e associações aos equipamentos.',
'fas fa-truck-medical', true),

('funcionalidade_4', 'funcionalidades', 'Documentação',
'Organização de manuais, certificados e documentos técnicos.',
'fas fa-file-medical', true),

('funcionalidade_5', 'funcionalidades', 'Garantias',
'Consulta de garantias, contratos e entidades responsáveis.',
'fas fa-file-contract', true),

('funcionalidade_6', 'funcionalidades', 'Dashboard',
'Indicadores, alertas e resumo do estado do inventário.',
'fas fa-chart-simple', true),

('dashboard_publico', 'dashboard_publico', 'Informação centralizada para melhor decisão',
'Através de indicadores e alertas, a solução permite identificar equipamentos críticos, garantias próximas do fim, documentação em falta e estados de funcionamento.

Esta informação ajuda os serviços técnicos e administrativos a acompanhar o inventário de forma mais rápida e estruturada.',
'assets/img/dashboard.png', true),

('footer_localizacao', 'rodape', 'Localização',
'Porto, Portugal|Rua ************ 000|4249-000',
NULL, true),

('footer_horario', 'rodape', 'Horário',
'Segunda a sexta: 09:00 - 18:00|Sábado: 09:00 - 13:00|Domingo: encerrado',
NULL, true),

('footer_contactos', 'rodape', 'Contactos',
'geral@medinventario.pt|+351 220 000 000|+351 914 000 000',
NULL, true)
ON DUPLICATE KEY UPDATE
    seccao = VALUES(seccao),
    titulo = VALUES(titulo),
    texto = VALUES(texto),
    imagem = VALUES(imagem),
    ativo = VALUES(ativo);