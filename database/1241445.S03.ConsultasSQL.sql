/* =========================================================
   SIBDAS - Projeto MedInventário
   Ficheiro: 1241445.S03.ConsultasSQL.sql
   Objetivo: Consultas SQL de teste e validação da base de dados
   ========================================================= */


/* 1. Listar todos os equipamentos ativos */
SELECT
    idEquipamento,
    codigoInterno,
    numeroSerie,
    designacao,
    marca,
    modelo,
    ativo
FROM Equipamento
WHERE ativo = true
ORDER BY codigoInterno;


/* 2. Listar equipamentos removidos/abatidos */
SELECT
    e.codigoInterno,
    e.designacao,
    e.numeroSerie,
    ee.descricao AS estado
FROM Equipamento e
INNER JOIN EstadoEquipamento ee
    ON e.idEstadoEquipamento = ee.idEstadoEquipamento
WHERE e.ativo = false
   OR ee.descricao = 'Abatido'
ORDER BY e.codigoInterno;


/* 3. Listar equipamentos com categoria, estado, criticidade e localização */
SELECT
    e.codigoInterno,
    e.designacao,
    e.numeroSerie,
    ce.descricao AS categoria,
    ee.descricao AS estado,
    cr.descricao AS criticidade,
    l.edificio,
    l.piso,
    l.servico,
    l.sala
FROM Equipamento e
INNER JOIN CategoriaEquipamento ce
    ON e.idCategoriaEquipamento = ce.idCategoriaEquipamento
INNER JOIN EstadoEquipamento ee
    ON e.idEstadoEquipamento = ee.idEstadoEquipamento
INNER JOIN CriticidadeEquipamento cr
    ON e.idCriticidadeEquipamento = cr.idCriticidadeEquipamento
INNER JOIN Localizacao l
    ON e.idLocalizacao = l.idLocalizacao
WHERE e.ativo = true
ORDER BY e.codigoInterno;


/* 4. Pesquisar equipamentos por texto na designação, marca ou modelo */
SELECT
    codigoInterno,
    designacao,
    marca,
    modelo
FROM Equipamento
WHERE ativo = true
  AND (
        designacao LIKE '%Monitor%'
     OR marca LIKE '%Monitor%'
     OR modelo LIKE '%Monitor%'
  )
ORDER BY designacao;


/* 5. Listar equipamentos de criticidade alta ou suporte de vida */
SELECT
    e.codigoInterno,
    e.designacao,
    cr.descricao AS criticidade
FROM Equipamento e
INNER JOIN CriticidadeEquipamento cr
    ON e.idCriticidadeEquipamento = cr.idCriticidadeEquipamento
WHERE e.ativo = true
  AND cr.descricao IN ('Alta', 'Suporte de vida')
ORDER BY cr.descricao DESC, e.codigoInterno;


/* 6. Listar equipamentos em manutenção, calibração ou inativos */
SELECT
    e.codigoInterno,
    e.designacao,
    ee.descricao AS estado
FROM Equipamento e
INNER JOIN EstadoEquipamento ee
    ON e.idEstadoEquipamento = ee.idEstadoEquipamento
WHERE e.ativo = true
  AND ee.descricao IN ('Em manutenção', 'Em calibração', 'Inativo')
ORDER BY ee.descricao, e.codigoInterno;


/* 7. Listar fornecedores ativos */
SELECT
    designacao,
    nif,
    email,
    telefone,
    website
FROM Fornecedor
WHERE ativo = true
ORDER BY designacao;


/* 8. Listar fornecedores associados aos equipamentos */
SELECT
    e.codigoInterno,
    e.designacao AS equipamento,
    f.designacao AS fornecedor,
    ef.tipoRelacao,
    ef.dataInicio,
    ef.dataFim,
    ef.observacoes
FROM EquipamentoFornecedor ef
INNER JOIN Equipamento e
    ON ef.idEquipamento = e.idEquipamento
INNER JOIN Fornecedor f
    ON ef.idFornecedor = f.idFornecedor
WHERE e.ativo = true
  AND f.ativo = true
ORDER BY e.codigoInterno, f.designacao, ef.tipoRelacao;


/* 9. Quantidade de equipamentos associados por fornecedor */
SELECT
    f.designacao AS fornecedor,
    COUNT(DISTINCT ef.idEquipamento) AS quantidadeEquipamentos
FROM Fornecedor f
LEFT JOIN EquipamentoFornecedor ef
    ON f.idFornecedor = ef.idFornecedor
LEFT JOIN Equipamento e
    ON ef.idEquipamento = e.idEquipamento
   AND e.ativo = true
WHERE f.ativo = true
GROUP BY f.idFornecedor, f.designacao
ORDER BY quantidadeEquipamentos DESC, f.designacao;


/* 10. Listar localizações ativas e quantidade de equipamentos */
SELECT
    l.edificio,
    l.piso,
    l.servico,
    l.sala,
    COUNT(e.idEquipamento) AS quantidadeEquipamentos
FROM Localizacao l
LEFT JOIN Equipamento e
    ON l.idLocalizacao = e.idLocalizacao
   AND e.ativo = true
WHERE l.ativo = true
GROUP BY
    l.idLocalizacao,
    l.edificio,
    l.piso,
    l.servico,
    l.sala
ORDER BY quantidadeEquipamentos DESC, l.edificio, l.piso, l.servico, l.sala;


/* 11. Histórico de movimentações dos equipamentos */
SELECT
    e.codigoInterno,
    e.designacao,
    me.dataLocalizacao,
    l.edificio,
    l.piso,
    l.servico,
    l.sala,
    me.responsavel,
    me.motivo
FROM MovimentacaoEquipamento me
INNER JOIN Equipamento e
    ON me.idEquipamento = e.idEquipamento
INNER JOIN Localizacao l
    ON me.idLocalizacao = l.idLocalizacao
WHERE me.ativo = true
ORDER BY e.codigoInterno, me.dataLocalizacao;


/* 12. Listar documentação ativa com PDF associado */
SELECT
    e.codigoInterno,
    e.designacao AS equipamento,
    d.nomeDocumento,
    td.descricao AS tipoDocumento,
    d.dataDocumento,
    d.dataValidade,
    d.nomeFicheiro,
    d.caminhoFicheiro
FROM Documento d
INNER JOIN Equipamento e
    ON d.idEquipamento = e.idEquipamento
INNER JOIN TipoDocumento td
    ON d.idTipoDocumento = td.idTipoDocumento
WHERE d.ativo = true
  AND e.ativo = true
  AND d.nomeFicheiro IS NOT NULL
  AND d.nomeFicheiro <> ''
  AND d.caminhoFicheiro IS NOT NULL
  AND d.caminhoFicheiro <> ''
ORDER BY e.codigoInterno, td.descricao, d.nomeDocumento;


/* 13. Listar equipamentos ativos sem documentação PDF ativa */
SELECT
    e.codigoInterno,
    e.designacao,
    e.numeroSerie
FROM Equipamento e
WHERE e.ativo = true
  AND NOT EXISTS (
        SELECT 1
        FROM Documento d
        WHERE d.idEquipamento = e.idEquipamento
          AND d.ativo = true
          AND d.nomeFicheiro IS NOT NULL
          AND d.nomeFicheiro <> ''
          AND d.caminhoFicheiro IS NOT NULL
          AND d.caminhoFicheiro <> ''
  )
ORDER BY e.codigoInterno;


/* 14. Documentos expirados */
SELECT
    e.codigoInterno,
    e.designacao AS equipamento,
    d.nomeDocumento,
    d.dataValidade
FROM Documento d
INNER JOIN Equipamento e
    ON d.idEquipamento = e.idEquipamento
WHERE d.ativo = true
  AND e.ativo = true
  AND d.dataValidade IS NOT NULL
  AND d.dataValidade <= CURDATE()
ORDER BY d.dataValidade ASC;


/* 15. Documentos a expirar nos próximos 30 dias */
SELECT
    e.codigoInterno,
    e.designacao AS equipamento,
    d.nomeDocumento,
    d.dataValidade
FROM Documento d
INNER JOIN Equipamento e
    ON d.idEquipamento = e.idEquipamento
WHERE d.ativo = true
  AND e.ativo = true
  AND d.dataValidade IS NOT NULL
  AND d.dataValidade BETWEEN DATE_ADD(CURDATE(), INTERVAL 1 DAY)
                         AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
ORDER BY d.dataValidade ASC;


/* 16. Garantias ou contratos expirados */
SELECT
    e.codigoInterno,
    e.designacao AS equipamento,
    gc.tipo,
    gc.numeroContrato,
    gc.dataInicio,
    gc.dataFim,
    f.designacao AS fornecedorResponsavel
FROM GarantiaContrato gc
INNER JOIN Equipamento e
    ON gc.idEquipamento = e.idEquipamento
LEFT JOIN Fornecedor f
    ON gc.idFornecedorResponsavel = f.idFornecedor
WHERE gc.ativo = true
  AND e.ativo = true
  AND gc.dataFim <= CURDATE()
ORDER BY gc.dataFim ASC;


/* 17. Garantias ou contratos a expirar nos próximos 30 dias */
SELECT
    e.codigoInterno,
    e.designacao AS equipamento,
    gc.tipo,
    gc.numeroContrato,
    gc.dataInicio,
    gc.dataFim,
    f.designacao AS fornecedorResponsavel
FROM GarantiaContrato gc
INNER JOIN Equipamento e
    ON gc.idEquipamento = e.idEquipamento
LEFT JOIN Fornecedor f
    ON gc.idFornecedorResponsavel = f.idFornecedor
WHERE gc.ativo = true
  AND e.ativo = true
  AND gc.dataFim BETWEEN DATE_ADD(CURDATE(), INTERVAL 1 DAY)
                     AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
ORDER BY gc.dataFim ASC;


/* 18. Dashboard: total de equipamentos ativos */
SELECT
    COUNT(*) AS totalEquipamentosAtivos
FROM Equipamento
WHERE ativo = true;


/* 19. Dashboard: distribuição de equipamentos por estado, incluindo abatidos */
SELECT
    ee.descricao AS estado,
    COUNT(e.idEquipamento) AS quantidade
FROM EstadoEquipamento ee
LEFT JOIN Equipamento e
    ON ee.idEstadoEquipamento = e.idEstadoEquipamento
GROUP BY ee.idEstadoEquipamento, ee.descricao
ORDER BY quantidade DESC, ee.descricao;


/* 20. Dashboard: distribuição de equipamentos ativos por criticidade */
SELECT
    cr.descricao AS criticidade,
    COUNT(e.idEquipamento) AS quantidade
FROM CriticidadeEquipamento cr
LEFT JOIN Equipamento e
    ON cr.idCriticidadeEquipamento = e.idCriticidadeEquipamento
   AND e.ativo = true
GROUP BY cr.idCriticidadeEquipamento, cr.descricao
ORDER BY quantidade DESC, cr.descricao;


/* 21. Dashboard: distribuição de equipamentos ativos por serviço */
SELECT
    l.servico,
    COUNT(e.idEquipamento) AS quantidadeEquipamentos
FROM Localizacao l
LEFT JOIN Equipamento e
    ON l.idLocalizacao = e.idLocalizacao
   AND e.ativo = true
WHERE l.ativo = true
GROUP BY l.servico
ORDER BY quantidadeEquipamentos DESC, l.servico;


/* 22. Utilizadores ativos por perfil */
SELECT
    perfil,
    COUNT(*) AS quantidadeUtilizadores
FROM Utilizador
WHERE ativo = true
GROUP BY perfil
ORDER BY perfil;


/* 23. Utilizadores com contrato a terminar nos próximos 60 dias */
SELECT
    nome,
    username,
    email,
    perfil,
    dataFimContrato
FROM Utilizador
WHERE ativo = true
  AND dataFimContrato IS NOT NULL
  AND dataFimContrato BETWEEN CURDATE()
                          AND DATE_ADD(CURDATE(), INTERVAL 60 DAY)
ORDER BY dataFimContrato ASC;


/* 24. Conteúdos ativos do site público */
SELECT
    chave,
    seccao,
    titulo,
    imagem
FROM ConteudoSite
WHERE ativo = true
ORDER BY seccao, chave;


/* 25. Exemplo com união: equipamentos ativos e removidos numa única listagem */
SELECT
    codigoInterno,
    designacao,
    'Ativo na aplicação' AS situacao
FROM Equipamento
WHERE ativo = true
UNION
SELECT
    codigoInterno,
    designacao,
    'Removido da aplicação' AS situacao
FROM Equipamento
WHERE ativo = false
ORDER BY codigoInterno;
