README - Projeto MedInventário

Nome do projeto:
MedInventário

Estudante:
Beatriz Ribeiro

Número de estudante:
1241445

Unidade curricular:
SIBDAS

Descrição da aplicação:
O MedInventário é uma aplicação web criada para apoiar a gestão do inventário hospitalar de equipamentos médicos. O projeto tem
uma área pública, onde é apresentada a solução e as suas funcionalidades, e uma área privada, onde os utilizadores autenticados
podem gerir os dados do sistema.

Na área reservada é possível registar, consultar, editar e remover equipamentos, fornecedores e localizações. Cada equipamento
pode ter informação sobre categoria, estado, criticidade, localização, fornecedores associados, documentos PDF e dados de
garantias ou contratos. A aplicação inclui ainda gestão de utilizadores, gestão dos conteúdos apresentados no site público,
exportação de dados, registo de eventos do sistema e um dashboard com indicadores e gráficos estatísticos.

O objetivo principal foi criar uma aplicação simples, organizada e funcional, que centralize a informação dos equipamentos
médicos e facilite a consulta, pesquisa e acompanhamento do estado do inventário hospitalar.

A aplicação foi pensada para simular uma situação real numa unidade hospitalar, em que é necessário consultar rapidamente onde
se encontra um equipamento, qual o seu estado, que documentação tem associada, que fornecedor está relacionado com esse equipamento
e se existem garantias ou contratos a acompanhar. Desta forma, o sistema ajuda a organizar informação que, de outra forma, poderia
estar dispersa por folhas de cálculo, pastas físicas ou documentos separados.

Estrutura de diretorias adotada:

medinventario/
├── assets/                              # Recursos utilizados pela aplicação
│   ├── bootstrap/                       # Ficheiros locais da biblioteca Bootstrap
│   ├── chartjs/                         # Ficheiro local da biblioteca Chart.js
│   ├── css/
│   │   └── 1241445.css                  # Folha de estilos própria da estudante
│   ├── fontawesome/                     # Ficheiros locais da biblioteca Font Awesome
│   ├── img/                             # Imagens usadas na área pública e privada
│   ├── js/
│   │   └── 1241445.js                   # Scripts próprios da estudante
│   └── uploads/
│       └── documentos/                  # PDFs associados à documentação dos equipamentos
│
├── config/
│   └── config.php                       # Configurações gerais da aplicação e da base de dados
│
├── database/                            # Ficheiros de base de dados e documentação técnica
│   ├── 1241445.BD-export.sql            # Exportação completa da base de dados
│   ├── 1241445.S01.ModeloFisico.sql     # Script de criação da estrutura física da BD
│   ├── 1241445.S02.INSERT.sql           # Script de inserção dos dados de teste
│   ├── 1241445.S03.ConsultasSQL.sql     # Consultas SQL de teste e validação
│   ├── 1241445.medinventario.dbml       # Representação da base de dados em DBML
│   ├── 1241445.modelo-relacional.drawio # Modelo relacional editável em notação Crow's Foot
│   └── 1241445.modelo-relacional.png    # Imagem do modelo relacional
│
├── private/                             # Área reservada da aplicação
│   ├── area_pessoal.php                 # Página inicial da área privada
│   ├── alterar_password.php             # Alteração da palavra-passe do utilizador autenticado
│   ├── includes/                        # Ficheiros reutilizáveis da área privada
│   │   ├── footer.php                   # Rodapé comum
│   │   ├── funcoes.php                  # Funções reutilizáveis, ligação à BD, segurança e logs
│   │   ├── header.php                   # Cabeçalho comum
│   │   ├── nav.php                      # Barra superior da área privada
│   │   ├── public_footer.php            # Rodapé da área pública
│   │   ├── public_nav.php               # Navegação da área pública
│   │   └── sidebar.php                  # Menu lateral da área privada
│   └── views/                           # Módulos funcionais da aplicação
│       ├── dashboard/                   # Dashboard com indicadores e gráficos estatísticos
│       ├── equipamentos/                # Gestão de equipamentos
│       ├── fornecedores/                # Gestão de fornecedores
│       ├── gestao_conteudos/            # Gestão dos conteúdos da área pública
│       ├── localizacoes/                # Gestão de localizações
│       ├── logs/                        # Consulta do registo de eventos do sistema
│       └── utilizadores/                # Gestão de utilizadores
│
├── public/                              # Área pública da aplicação
│   ├── index.php                        # Página pública principal
│   ├── login.php                        # Página de login
│   ├── logout.php                       # Terminar sessão
│   └── processa_login.php               # Processamento do login
│
├── index.php                            # Redireciona a raiz do projeto para public/index.php
├── README.txt                           # Instruções de instalação, execução, testes e credenciais
└── commits.txt                          # Lista de commits do projeto

Nota sobre o ficheiro index.php da raiz:
O ficheiro index.php colocado na raiz do projeto serve para encaminhar automaticamente o utilizador para a página pública principal,
localizada em public/index.php.
Este ficheiro permite que o projeto abra corretamente quando é acedido através do endereço obrigatório:
http://127.0.0.1/sibdas/1241445/medinventario

Sem este ficheiro, seria necessário escrever manualmente /public/index.php no endereço do browser. Assim, ao abrir a raiz do
projeto, a aplicação redireciona automaticamente para a página inicial pública.

Tecnologias e bibliotecas utilizadas:
* HTML5: estrutura das páginas.
* CSS3: estilos personalizados da aplicação.
* JavaScript: interatividade e funcionalidades do lado do cliente.
* PHP: processamento no servidor e lógica da aplicação.
* MySQL: armazenamento dos dados.
* PDO: ligação e comunicação segura com a base de dados.
* Bootstrap: layout responsivo e componentes visuais.
* Font Awesome: ícones utilizados na interface.
* Chart.js: gráficos estatísticos apresentados no dashboard.

As bibliotecas externas foram guardadas localmente na pasta assets, para que o projeto funcione sem depender de ligações externas/CDN.

Separação de responsabilidades:
* Os ficheiros PHP/HTML encontram-se nas pastas public e private.
* Os estilos próprios encontram-se em assets/css/1241445.css.
* Os scripts próprios encontram-se em assets/js/1241445.js.
* As bibliotecas externas usadas localmente encontram-se em assets/bootstrap, assets/fontawesome e assets/chartjs.
* Os ficheiros PDF associados à documentação dos equipamentos ficam em assets/uploads/documentos.
* Os ficheiros relativos à base de dados encontram-se na pasta database.

Instalação e execução:
1. Instalar o Laragon ou outro ambiente local compatível com PHP e MySQL.

2. Copiar a pasta do projeto para a estrutura pedida:
   www/sibdas/1241445/medinventario

3. Confirmar que o ficheiro config/config.php tem o BASE_URL definido como:
   /sibdas/1241445/medinventario

4. Confirmar que a aplicação fica acessível através do endereço:
   http://127.0.0.1/sibdas/1241445/medinventario

5. A aplicação usa a base de dados MySQL configurada no ficheiro config/config.php.

6. Caso seja necessário recriar a base de dados, use os ficheiros da pasta database:
   * 1241445.S01.ModeloFisico.sql para criação da estrutura;
   * 1241445.S02.INSERT.sql para inserção dos dados de teste;
   * 1241445.BD-export.sql como exportação completa da base de dados;
   * 1241445.S03.ConsultasSQL.sql para consultas SQL de teste;
   * 1241445.medinventario.dbml para representação DBML;
   * 1241445.modelo-relacional.drawio e 1241445.modelo-relacional.png para o modelo relacional.

7. Confirmar que as bibliotecas estão disponíveis localmente nas seguintes pastas:
   * assets/bootstrap
   * assets/fontawesome
   * assets/chartjs

8. Confirmar que a pasta de uploads de documentos existe:
   assets/uploads/documentos

Credenciais de acesso à aplicação:
Perfil: Administrador
Utilizador: beatriz.ribeiro
Palavra-passe: 111111

Perfil: Técnico
Utilizador: miguel.ferreira
Palavra-passe: miguel1234

Perfil: Gestor Hospitalar
Utilizador: helena.costa
Palavra-passe: helena1234

Perfil: Profissional de Saúde
Utilizador: carla.santos
Palavra-passe: carla11234

Nota:
As palavras-passe encontram-se guardadas na base de dados através de password_hash().
No formulário de login deve ser usada a palavra-passe original indicada acima.

Manual de utilização resumido:
1. Aceder à aplicação através de:
   http://127.0.0.1/sibdas/1241445/medinventario

2. Entrar na área reservada através da opção de login.

3. Iniciar sessão com uma das credenciais de teste indicadas neste ficheiro.

4. Depois do login, utilizar o menu lateral para aceder aos módulos disponíveis de acordo com o perfil do utilizador:
   * Equipamentos;
   * Fornecedores;
   * Localizações;
   * Utilizadores;
   * Conteúdos do site;
   * Registo de eventos;
   * Dashboard.

5. Nas listagens, usar os campos de pesquisa e filtros para encontrar os registos pretendidos.

6. Usar os botões de ação para criar, editar, consultar, remover ou restaurar registos, conforme as permissões do perfil.

7. No módulo de equipamentos, é possível associar fornecedores, documentação PDF, garantias/contratos e localização ao equipamento.

8. No módulo Registo de eventos, disponível apenas para administrador, é possível consultar eventos importantes registados automaticamente pela aplicação, como login, logout, criação, edição, remoção e restauro de equipamentos.

9. No dashboard, consultar os indicadores principais, alertas de gestão e gráficos estatísticos baseados nos dados registados na base de dados.

Principais testes a realizar:
1. Teste da área pública:
   * Aceder a http://127.0.0.1/sibdas/1241445/medinventario.
   * Confirmar que a página pública carrega corretamente.
   * Confirmar que a navegação por secções funciona.
   * Confirmar que os conteúdos apresentados vêm da base de dados.

2. Teste de autenticação:
   * Aceder à página de login.
   * Testar login com credenciais inválidas.
   * Testar login com cada um dos perfis existentes.
   * Confirmar que o logout termina corretamente a sessão.

3. Teste de permissões:
   * Confirmar que páginas privadas não abrem sem login.
   * Confirmar que a gestão de utilizadores só está disponível para administrador.
   * Confirmar que o registo de eventos só está disponível para administrador.
   * Confirmar que os perfis apresentam apenas as opções permitidas no menu.

4. Teste do módulo de equipamentos:
   * Listar equipamentos.
   * Pesquisar e filtrar equipamentos.
   * Criar novo equipamento.
   * Editar equipamento existente.
   * Remover equipamento.
   * Confirmar que equipamentos removidos deixam de aparecer na lista principal.
   * Restaurar equipamento removido.
   * Confirmar que documentação PDF associada abre corretamente.

5. Teste do módulo de fornecedores:
   * Listar fornecedores.
   * Criar fornecedor.
   * Editar fornecedor.
   * Validar campos como NIF, email, telefone e website.
   * Remover fornecedor.

6. Teste do módulo de localizações:
   * Listar localizações.
   * Criar localização com perfil autorizado.
   * Editar localização com perfil autorizado.
   * Remover localização com perfil autorizado.
   * Confirmar que perfis sem permissões não conseguem alterar dados.

7. Teste do módulo de utilizadores:
   * Aceder como administrador.
   * Criar novo utilizador.
   * Confirmar validações de username, email, perfil, palavra-passe e data de fim de contrato.
   * Remover utilizador antigo/inativo.

8. Teste da gestão de conteúdos:
   * Aceder ao backoffice de conteúdos.
   * Alterar textos ou informações da área pública.
   * Confirmar que a página pública reflete as alterações.

9. Teste do registo de eventos:
   * Fazer login com dados incorretos.
   * Fazer login com dados corretos.
   * Criar, editar, remover e restaurar um equipamento.
   * Entrar como administrador no módulo Registo de eventos.
   * Confirmar que os eventos foram registados na tabela LogSistema.

10. Teste do dashboard:
   * Confirmar que os indicadores são apresentados.
   * Confirmar que os gráficos estatísticos carregam corretamente.
   * Confirmar que os dados apresentados vêm da base de dados.
   * Confirmar que os alertas de garantias, documentação e criticidade são coerentes.

11. Teste de exportação:
   * Testar a exportação de dados nas listagens onde a funcionalidade está disponível.
   * Confirmar que o ficheiro exportado contém os dados apresentados na lista.

Informação adicional:
A aplicação foi organizada de forma modular, separando ficheiros de configuração, funções reutilizáveis, layouts comuns,
páginas públicas, páginas privadas, recursos visuais e scripts próprios.
A base de dados encontra-se normalizada e representada através de SQL, DBML e modelo relacional em notação Crow's Foot.
O projeto utiliza Bootstrap, Font Awesome e Chart.js localmente, evitando dependências externas durante a execução.

Informação adicional para avaliação:
O projeto inclui uma área pública e uma área privada, cumprindo a separação entre front office e back office.
A área pública apresenta a solução MedInventário e os seus conteúdos podem ser geridos através da área reservada.
A área privada permite gerir os principais dados do inventário hospitalar, incluindo equipamentos, fornecedores, localizações, documentação, garantias/contratos, utilizadores, registo de eventos e indicadores de gestão.

Foram implementadas funcionalidades adicionais de valorização, nomeadamente:
* upload real de documentos PDF;
* histórico de movimentações de equipamentos;
* exportação de dados;
* dashboard com gráficos estatísticos;
* equipamentos removidos com possibilidade de restauro;
* gestão dinâmica dos conteúdos públicos;
* registo de eventos do sistema através da tabela LogSistema;
* consulta de eventos no backoffice pelo administrador;
* preparação estrutural para futura manutenção preventiva.

Observação final:
Caso a pasta do projeto seja colocada noutro caminho, é necessário ajustar a constante BASE_URL no ficheiro config/config.php.