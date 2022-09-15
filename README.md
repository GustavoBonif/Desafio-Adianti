# Desafio-Adianti: Livraria
Projeto de uma livraria para aprender a trabalhar com o framework PHP Adianti

## O objetivo deste sistema é
* Aprender a configurar o ambiente com o Adianti Framework;
* Entender o funcionamento do sistema de ORM do Framework para integração com o banco de dados, assim como seu sistema de arquivos;
* Aplicar situações descritas nos tutoriais online e discussões no fórum do Adianti Framework.

## O que foi realizado
* Criação de módulos de autores, editoras e livros;
* Permissões de CRUD atribuidas para administrator e usuário;
* No cadastro de livros há a relação ligando as entidades autores e editores.

## Primeiro impacto
* A configuração do framework é simples. Além de contar com várias discussões no fórum sobre diversos assuntos para quem vai criar seu primeiro projeto, há muito material no Youtube sobre como o framework funciona. Apesar de ter optado por utilizar o Visual Studio Code para desenvolver o sistema, utilizei da IDE indicada para trabalhar com o Adianti, o *Adianti Studio* para me auxiliar na criação de diretórios, o que me ajudou a compreender o sistema de arquivos.

## Funcionamento do Adianti Framework
* Por ser um framework com funcionamento baseado em ORM (*Object Relational Mapping*) a integração com o banco de dados, que nesse sistema foi usado o SQLite (indicado para o primeiro contato com o framework), ocorre por meio de objetos que representam das tabelas no banco de dados. As models são criadas e seus atributos são as colunas da tabela;
* A relação entre tabelas é realizada por getters e setters na model, tornando a sua aplicação de consultas que em SQL puro precisariam de JOINS e enormes clausulas WHERE muito mais simples, podendo ser resumida em pouquíssimas linhas;
* Com as models prontas, são criadas classes controladoras para realizar a interação com as models, e nesse sistema foram criadas Listas e Formulários que realizam as ações necessárias para os CRUDs de Autores, Editoras e Livros;
* O framework disponibiliza uma gestão dos programas e permissões de acessos dentro do próprio template base, o que auxilia na sua função principal, que é agilizar o processo de desenvolvimento de software, deixando a maior preocupação do programador a aplicação de regras de negócios.

## Testagem do sistema
* O usuário administrador (user: admin, senha: admin) possui permissão para o CRUD de Livros, Autores e Editoras;
* O usuário user tem acesso às listagens de registros gerados.

## Impedimentos
* Algumas questões quanto às permissões de interações de diferentes usuários na mesma tela ainda não estão claras. Por esse motivo o usuário comum não consegue criar ou editar um registro, pois essa alteração é feita na tela de formulário, mas consegue excluir, já que a exclusão ocorre na própria tela de listagem de registros.
