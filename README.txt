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
exportação de dados e um dashboard com indicadores e gráficos estatísticos.
O objetivo principal foi criar uma aplicação simples, organizada e funcional, que centralize a informação dos equipamentos 
médicos e facilite a consulta, pesquisa e acompanhamento do estado do inventário hospitalar.

Estrutura principal do projeto:
medinventario/
├── assets/
│   ├── bootstrap/
│   ├── chartjs/
│   ├── css/
│   ├── fontawesome/
│   ├── img/
│   ├── js/
│   └── uploads/
├── config/
├── database/
├── private/
│   ├── includes/
│   └── views/
├── public/
├── index.php
└── README.txt

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

Tecnologias utilizadas:
* PHP
* MySQL
* HTML5
* CSS3
* JavaScript
* Bootstrap
* Font Awesome
* Chart.js
* PDO para ligação segura à base de dados

Instalação e execução:
1. Copiar a pasta do projeto para a estrutura pedida:
   www/sibdas/1241445/medinventario

2. Confirmar que o ficheiro config/config.php tem o BASE_URL definido como:
   /sibdas/1241445/medinventario

3. Confirmar que a aplicação fica acessível através do endereço:
   http://127.0.0.1/sibdas/1241445/medinventario

4. A aplicação usa a base de dados MySQL configurada no ficheiro config/config.php.

5. Caso seja necessário recriar a base de dados, use os ficheiros da pasta database:
   * 1241445.S01.ModeloFisico.sql para criação da estrutura;
   * 1241445.S02.INSERT.sql para inserção dos dados de teste;
   * 1241445.BD-export.sql como exportação completa da base de dados;
   * 1241445.S03.ConsultasSQL.sql para consultas SQL de teste;
   * 1241445.medinventario.dbml para representação DBML;
   * 1241445.modelo-relacional.drawio e 1241445.modelo-relacional.png para o modelo relacional.

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
Palavra-passe: carla1234

Nota:
As palavras-passe encontram-se guardadas na base de dados através de password_hash(). 
No formulário de login deve ser usada a 
palavra-passe original indicada acima.

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

9. Teste do dashboard:

   * Confirmar que os indicadores são apresentados.
   * Confirmar que os gráficos estatísticos carregam corretamente.
   * Confirmar que os dados apresentados vêm da base de dados.
   * Confirmar que os alertas de garantias, documentação e criticidade são coerentes.

10. Teste de exportação:

* Testar a exportação de dados nas listagens onde a funcionalidade está disponível.
* Confirmar que o ficheiro exportado contém os dados apresentados na lista.

Informação adicional:
A aplicação foi organizada de forma modular, separando ficheiros de configuração, funções reutilizáveis, layouts comuns, 
páginas públicas, páginas privadas, recursos visuais e scripts próprios. 
A base de dados encontra-se normalizada e representada através de SQL, DBML e modelo relacional. 
O projeto utiliza Bootstrap, Font Awesome e Chart.js localmente, evitando dependências externas durante a execução.