<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedInventário - Nova Localização</title>

    <!-- favicon -->
    <link rel="shortcut icon" href="../../../assets/img/logo.png" type="image/png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../../assets/bootstrap/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../../assets/fontawesome/all.min.css">

    <!-- folha de estilos CSS -->
    <link rel="stylesheet" href="../../../assets/css/1241445.css">
</head>

<body class="pagina-novo-equipamento">
    <!-- Navbar -->
    <header class="bng-navbar-menu">

        <div>
            <a href="../../area_pessoal.php">
                <img src="../../../assets/img/logo.png" alt="Logo da MedInventário">
            </a>
            <h3>MedInventário</h3>
        </div>

        <div>
            <button id="btn-utilizador" type="button" onclick="mostrarAreaUtilizador()">
                <i class="fas fa-user-circle"></i>
                Utilizador
            </button>
        </div>

    </header>

    <!-- Sidebar -->
    <aside class="sidebar">
        <h4>Menu</h4>

        <nav>
            <a href="../equipamentos/lista.php">
                <i class="fas fa-laptop-medical"></i> Equipamentos
            </a>

            <a href="../fornecedores/lista.php">
                <i class="fas fa-truck-medical"></i> Fornecedores
            </a>

            <a href="../localizacoes/lista.php" class="active">
                <i class="fas fa-location-dot"></i> Localizações
            </a>

            <a href="../gestao_conteudos/gestao_conteudos.php">
                <i class="fas fa-pen-to-square"></i> Conteúdos do site
            </a>

            <a href="../dashboard/dashboard.php">
                <i class="fas fa-chart-bar"></i> Dashboard
            </a>
        </nav>
    </aside>

    <!-- Conteúdo Principal -->
    <main class="content">
        <section>

            <div class="actions-top">
                <h2>
                    <strong>
                        <i class="fas fa-plus"></i> Inserir Nova Localização
                    </strong>
                </h2>

                <a href="lista.php" class="btn btn-outline-secondary botao-anterior" title="Voltar à lista">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

            <hr>

            <form action="#" method="post" class="formulario-equipamento">

                <div class="card mb-4">
                    <div class="card-body">

                        <h3>
                            <i class="fas fa-location-dot"></i> Dados da localização
                        </h3>

                        <div class="row mb-3">

                            <div class="col-12 col-md-6">
                                <label for="categoria" class="form-label">Categoria</label>
                                <input type="text" class="form-control campo-obrigatorio-localizacao" id="categoria"
                                    name="categoria"
                                    placeholder="Ex.: Área clínica crítica, Área cirúrgica, Área técnica">
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="edificio" class="form-label">Edifício</label>
                                <input type="text" class="form-control campo-obrigatorio-localizacao" id="edificio"
                                    name="edificio" placeholder="Ex.: Hospital Central">
                            </div>

                        </div>

                        <div class="row mb-3">

                            <div class="col-12 col-md-4">
                                <label for="piso" class="form-label">Piso</label>
                                <input type="text" class="form-control campo-obrigatorio-localizacao" id="piso"
                                    name="piso" placeholder="Ex.: Piso 2">
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="servico" class="form-label">Serviço / Departamento</label>
                                <input type="text" class="form-control campo-obrigatorio-localizacao" id="servico"
                                    name="servico" placeholder="Ex.: Unidade de Cuidados Intensivos">
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="sala" class="form-label">Sala / Gabinete</label>
                                <input type="text" class="form-control campo-obrigatorio-localizacao" id="sala"
                                    name="sala" placeholder="Ex.: Sala 1">
                            </div>

                        </div>

                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">

                        <h3>
                            <i class="fas fa-circle-info"></i> Observações
                        </h3>

                        <div class="mb-3">
                            <label for="observacoes" class="form-label">Observações</label>
                            <textarea class="form-control" id="observacoes" name="observacoes" rows="4"
                                placeholder="Observações adicionais sobre esta localização."></textarea>
                        </div>

                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">

                    <a href="lista.php" class="btn btn-outline-secondary botao-anterior">
                        Cancelar
                    </a>

                    <button type="button" class="btn btn-primary" onclick="validarLocalizacao()">
                        Guardar localização
                    </button>

                </div>

            </form>

            <p id="mensagem-formulario"></p>

        </section>
    </main>

    <!-- Bootstrap JS -->
    <script src="../../../assets/bootstrap/bootstrap.bundle.min.js"></script>

    <!-- JavaScript -->
    <script src="../../../assets/js/1241445.js"></script>

</body>

</html>