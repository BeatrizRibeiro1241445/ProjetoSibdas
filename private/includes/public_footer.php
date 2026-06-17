<?php
$footerCidadePais = 'Porto, Portugal';
$footerMorada = 'Rua ************ 000';
$footerCodigoPostal = '4249-000';

$footerHorarioSemana = 'Segunda a sexta: 09:00 - 18:00';
$footerHorarioSabado = 'Sábado: 09:00 - 13:00';
$footerHorarioDomingo = 'Domingo: encerrado';

$footerEmail = 'geral@medinventario.pt';
$footerTelefone1 = '+351 220 000 000';
$footerTelefone2 = '+351 914 000 000';

try {
    $ligacao = db_connect();

    $stmt = $ligacao->query("
        SELECT chave, texto
        FROM ConteudoSite
        WHERE ativo = true
          AND chave IN (
              'footer_localizacao',
              'footer_horario',
              'footer_contactos'
          )
    ");

    foreach ($stmt->fetchAll() as $conteudo) {
        $partes = explode('|', $conteudo->texto);

        if ($conteudo->chave === 'footer_localizacao') {
            $footerCidadePais = $partes[0] ?? $footerCidadePais;
            $footerMorada = $partes[1] ?? $footerMorada;
            $footerCodigoPostal = $partes[2] ?? $footerCodigoPostal;
        }

        if ($conteudo->chave === 'footer_horario') {
            $footerHorarioSemana = $partes[0] ?? $footerHorarioSemana;
            $footerHorarioSabado = $partes[1] ?? $footerHorarioSabado;
            $footerHorarioDomingo = $partes[2] ?? $footerHorarioDomingo;
        }

        if ($conteudo->chave === 'footer_contactos') {
            $footerEmail = $partes[0] ?? $footerEmail;
            $footerTelefone1 = $partes[1] ?? $footerTelefone1;
            $footerTelefone2 = $partes[2] ?? $footerTelefone2;
        }
    }
} catch (PDOException $e) {
}
?>

<footer class="footer-container footer-publico">

    <div class="footer-section">
        <strong>Localização</strong>
        <p><i class="fas fa-location-dot"></i> <?= e($footerCidadePais) ?></p>
        <p><?= e($footerMorada) ?></p>
        <p><?= e($footerCodigoPostal) ?></p>
    </div>

    <div class="footer-section">
        <strong>Horário</strong>
        <p><i class="fas fa-clock"></i> <?= e($footerHorarioSemana) ?></p>
        <p><?= e($footerHorarioSabado) ?></p>
        <p><?= e($footerHorarioDomingo) ?></p>
    </div>

    <div class="footer-section">
        <strong>Contactos</strong>
        <p><i class="fas fa-envelope"></i> <?= e($footerEmail) ?></p>
        <p><i class="fas fa-phone"></i> <?= e($footerTelefone1) ?></p>
        <p><i class="fas fa-phone"></i> <?= e($footerTelefone2) ?></p>
    </div>

</footer>