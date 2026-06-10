/* =========================================================
   SIBDAS - Projeto MedInventário
   Ficheiro: 1241445.S03.ConsultasSQL.sql
   Objetivo: Consultas SQL de teste e validação da BD
   ========================================================= */


/* 1. Listar todos os equipamentos registados */
SELECT *
FROM Equipamento;


/* 2. Listar todos os fornecedores registados */
SELECT *
FROM Fornecedor;


/* 3. Listar todas as localizações registadas */
SELECT *
FROM Localizacao;


/* 4. Projeção: listar apenas os principais dados dos equipamentos */
SELECT
    codigoInterno,
    designacao,
    numeroSerie,
    marca,
    modelo
FROM Equipamento
ORDER BY codigoInterno;


/* 5. Seleção: listar apenas equipamentos ativos */
SELECT
    codigoInterno,
    designacao,
    numeroSerie
FROM Equipamento
WHERE ativo = true
ORDER BY designacao;


/* 6. Seleção: listar equipamentos em manutenção */
SELECT
    e.codigoInterno,
    e.designacao,
    e.numeroSerie
FROM Equipamento e
JOIN EstadoEquipamento ee
    ON e.idEstadoEquipamento = ee.idEstadoEquipamento
WHERE ee.descricao = 'Em manutenção';


/* 7. Consulta com junção de tabelas:
      listar equipamentos com categoria, estado, criticidade e localização */
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
JOIN CategoriaEquipamento ce
    ON e.idCategoriaEquipamento = ce.idCategoriaEquipamento
JOIN EstadoEquipamento ee
    ON e.idEstadoEquipamento = ee.idEstadoEquipamento
JOIN CriticidadeEquipamento cr
    ON e.idCriticidadeEquipamento = cr.idCriticidadeEquipamento
JOIN Localizacao l
    ON e.idLocalizacao = l.idLocalizacao
WHERE e.ativo = true
ORDER BY e.codigoInterno;


/* 8. Consulta com junção:
      listar fornecedores associados a cada equipamento */
SELECT
    e.codigoInterno,
    e.designacao AS equipamento,
    f.designacao AS fornecedor,
    ef.tipoRelacao,
    ef.dataInicio,
    ef.dataFim
FROM EquipamentoFornecedor ef
JOIN Equipamento e
    ON ef.idEquipamento = e.idEquipamento
JOIN Fornecedor f
    ON ef.idFornecedor = f.idFornecedor
ORDER BY e.codigoInterno, f.designacao;


/* 9. Consulta com junção:
      listar documentação associada aos equipamentos */
SELECT
    e.codigoInterno,
    e.designacao AS equipamento,
    d.nomeDocumento,
    td.descricao AS tipoDocumento,
    d.dataDocumento,
    d.dataValidade,
    f.designacao AS fornecedorAssociado
FROM Documento d
JOIN Equipamento e
    ON d.idEquipamento = e.idEquipamento
JOIN TipoDocumento td
    ON d.idTipoDocumento = td.idTipoDocumento
LEFT JOIN Fornecedor f
    ON d.idFornecedor = f.idFornecedor
WHERE d.ativo = true
ORDER BY e.codigoInterno, td.descricao;


/* 10. Consulta com subconsulta:
       listar equipamentos ativos sem documentação ativa associada */
SELECT
    e.codigoInterno,
    e.designacao,
    e.numeroSerie
FROM Equipamento e
WHERE e.ativo = true
AND e.idEquipamento NOT IN (
    SELECT d.idEquipamento
    FROM Documento d
    WHERE d.ativo = true
);


/* 11. Consulta com subconsulta:
       listar equipamentos cuja criticidade é superior ou igual à criticidade Alta */
SELECT
    e.codigoInterno,
    e.designacao,
    e.numeroSerie
FROM Equipamento e
WHERE e.idCriticidadeEquipamento IN (
    SELECT c.idCriticidadeEquipamento
    FROM CriticidadeEquipamento c
    WHERE c.descricao IN ('Alta', 'Suporte de vida')
)
ORDER BY e.codigoInterno;


/* 12. Dashboard: total de equipamentos ativos */
SELECT
    COUNT(*) AS totalEquipamentosAtivos
FROM Equipamento
WHERE ativo = true;


/* 13. Dashboard: quantidade de equipamentos por estado */
SELECT
    ee.descricao AS estado,
    COUNT(*) AS quantidade
FROM Equipamento e
JOIN EstadoEquipamento ee
    ON e.idEstadoEquipamento = ee.idEstadoEquipamento
WHERE e.ativo = true
GROUP BY ee.descricao
ORDER BY quantidade DESC;


/* 14. Dashboard: quantidade de equipamentos por criticidade */
SELECT
    cr.descricao AS criticidade,
    COUNT(*) AS quantidade
FROM Equipamento e
JOIN CriticidadeEquipamento cr
    ON e.idCriticidadeEquipamento = cr.idCriticidadeEquipamento
WHERE e.ativo = true
GROUP BY cr.descricao
ORDER BY quantidade DESC;


/* 15. Dashboard: quantidade de equipamentos por localização */
SELECT
    l.edificio,
    l.piso,
    l.servico,
    l.sala,
    COUNT(e.idEquipamento) AS quantidadeEquipamentos
FROM Localizacao l
LEFT JOIN Equipamento e
    ON l.idLocalizacao = e.idLocalizacao
GROUP BY
    l.edificio,
    l.piso,
    l.servico,
    l.sala
ORDER BY quantidadeEquipamentos DESC;


/* 16. Garantias ou contratos próximos do fim ou já expirados */
SELECT
    e.codigoInterno,
    e.designacao AS equipamento,
    gc.tipo,
    gc.numeroContrato,
    gc.dataInicio,
    gc.dataFim,
    f.designacao AS fornecedorResponsavel
FROM GarantiaContrato gc
JOIN Equipamento e
    ON gc.idEquipamento = e.idEquipamento
LEFT JOIN Fornecedor f
    ON gc.idFornecedorResponsavel = f.idFornecedor
WHERE gc.ativo = true
ORDER BY gc.dataFim;