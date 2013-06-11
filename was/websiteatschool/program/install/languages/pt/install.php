<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam, <info@websiteatschool.eu>
#
# This program is free software: you can redistribute it and/or modify it under
# the terms of the GNU Affero General Public License version 3 as published by
# the Free Software Foundation supplemented with the Additional Terms, as set
# forth in the License Agreement for Website@School (see /program/license.html).
#
# This program is distributed in the hope that it will be useful, but
# WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
# FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License
# for more details.
#
# You should have received a copy of the License Agreement for Website@School
# along with this program. If not, see http://websiteatschool.eu/license.html

/** /program/install/languages/pt/install.php
 *
 * Language: pt (Português)
 * Release:  0.90.3 / 2012041700 (2012-04-17)
 *
 * @author Rita Valente Ribeiro da Silva <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_pt
 * @version $Id: install.php,v 1.2 2013/06/11 11:26:03 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = 'Instalar';
$string['translatetool_description'] = 'Este ficheiro contém traduções do programa de instalação';
$string['websiteatschool_install'] = 'Instalação de Website@School';
$string['websiteatschool_logo'] = 'Logotipo Website@School';
$string['help_name'] = 'ajuda';
$string['help_description'] = 'Ajuda (abre em nova janela)';
$string['next'] = 'Seguinte';
$string['next_accesskey'] = 'S';
$string['next_title'] = 'Usar [Alt-S] o [Cmnd-S] como atalho de teclado para este botão';
$string['previous'] = 'Anterior';
$string['previous_accesskey'] = 'A';
$string['previous_title'] = 'Usar [Alt-A] o [Cmnd-A] como atalho de teclado para este botão';
$string['cancel'] = 'Cancelar';
$string['cancel_accesskey'] = 'C';
$string['cancel_title'] = 'Usar  [Alt-C] o [Cmnd-C] como atalho de teclado para este botão';
$string['ok'] = 'OK';
$string['ok_accesskey'] = 'K';
$string['ok_title'] = 'Usar [Alt-K] o [Cmnd-K] como atalho de teclado para este botão';
$string['yes'] = 'Sim';
$string['no'] = 'Não';
$string['language_name'] = 'Português';
$string['dialog_language'] = 'Idioma';
$string['dialog_language_title'] = 'Selecione o idioma de instalação';
$string['dialog_language_explanation'] = 'Por favor, selecionar o idioma a usar durante o processo de instalação.';
$string['language_label'] = 'Idioma';
$string['language_help'] = '';
$string['dialog_installtype'] = 'Tipo de Instalação';
$string['dialog_installtype_title'] = 'Escolher entre a instalação Padrão ou Personalizada';
$string['dialog_installtype_explanation'] = 'Por favor, escolher o cenário de instalação a partir da lista abaixo';
$string['installtype_label'] = 'Cenário de Instalação';
$string['installtype_help'] = 'Por favor, selecionar o cenário de instalação mais apropriado. <br> <strong> Padrão </strong> significa uma instalação simples, com um mínimo de perguntas a responder, <br> <strong> Personalizado </ strong> permite o controlo total de todas as opções de instalação.';
$string['installtype_option_standard'] = 'Padrão';
$string['installtype_option_custom'] = 'Personalizado';
$string['high_visibility_label'] = 'Elevada Visibilidade';
$string['high_visibility_help'] = 'Selecione a caixa para usar um interface de texto durante a instalação';
$string['dialog_license'] = 'Licença';
$string['dialog_license_title'] = 'Ler e aceitar a licença para este software';
$string['dialog_license_explanation'] = 'Poderá obter a licença para este programa apenas se ler, compreender e concordar com os seguintes termos e condições Informamos que se aplica a versão inglesa deste contrato, mesmo tendo instalado outro idioma.';
$string['dialog_license_i_agree'] = 'Concordo';
$string['dialog_license_you_must_accept'] = 'É necessário aceitar o acordo de licenciamento, digitando "<b> {iagree} </ b>" (sem as aspas) na caixa abaixo.';
$string['dialog_database'] = 'Base de dados';
$string['dialog_database_title'] = 'Insira as informações sobre o servidor de base de dados';
$string['dialog_database_explanation'] = 'Por favor introduza as propriedades do servidor da base de dados nos campos abaixo.';
$string['db_type_label'] = 'Tipo';
$string['db_type_help'] = 'Selecionar um dos tipos de base de dados disponíveis.';
$string['db_type_option_mysql'] = 'MySQL';
$string['db_server_label'] = 'Servidor';
$string['db_server_help'] = 'Este é o endereço do servidor da base de dados geralmente <strong> localhost </ strong>. Outros exemplos: <strong> mysql.example.org </ strong> ou <strong> example.dbserver.provider.net: 3306 </ strong>.';
$string['db_username_label'] = 'Nome de Usuário';
$string['db_username_help'] = 'É necessário um nome de usuário/ palavra passe válidos para ser conectado ao servidor da base de dados. Por favor, não utilizar a conta "root" do servidor da base de dados do mas sim uma conta menos privilegiada,, ex. <strong>wasuser</strong> ou  <strong>example_wwwa</strong>.';
$string['db_password_label'] = 'Palavra passe';
$string['db_password_help'] = 'É necessário um nome de usuário/ palavra passe válidos para ser conectado ao servidor da base de dados';
$string['db_name_label'] = 'Nome da base de dados';
$string['db_name_help'] = 'Este é o nome da base de dados a usar. Note que esta base de dados já existe; este programa de instalação não foi concebido para criar bases de dados (por razões de segurança). Exemplos: <strong>www</strong>ou<strong> example_www </strong>.';
$string['db_prefix_label'] = 'Prefixo';
$string['db_prefix_help'] = 'Todos os nomes de tabelas na base de dados começam com este prefixo. Isto permite múltiplas instalações na mesma base de dados. Note que o prefixo deve começar com uma letra. Exemplos: <strong> was_ </ strong> ou <strong> cms2_ </ strong>.';
$string['dialog_cms'] = 'Sítio';
$string['dialog_cms_title'] = 'Introduzir as informações essenciais sítio';
$string['dialog_cms_explanation'] = 'Por favor introduzir as informações essenciais do sítio nos campos abaixo.';
$string['cms_title_label'] = 'Título do sítio';
$string['cms_title_help'] = 'O nome do seu sítio.';
$string['cms_website_from_address_label'] = 'De: endereço';
$string['cms_website_from_address_help'] = 'Este endereço de eletrónico é usado para correio a  enviar, por exemplo, alertas e lembretes de palavra passe.';
$string['cms_website_replyto_address_label'] = 'Responder a: endereço';
$string['cms_website_replyto_address_help'] = 'Este endereço de correio electrónico é adicionado ao correio de saída e pode ser usado para especificar uma caixa de correio onde as respostas serão realmente lidas (pelo usuário) e não rejeitadas (pelo software de servidor de rede).';
$string['cms_dir_label'] = 'Diretório do sítio';
$string['cms_dir_help'] = 'Este é o caminho de arquivo para o diretório que contém index.php e config.php, por exemplo, <strong>/home/httpd/htdocs</strong> ou <strong>C:\Program Files\Apache Group\Apache\htdocs</strong>.';
$string['cms_www_label'] = 'URL do sítio';
$string['cms_www_help'] = 'Esta é a URL principal que conduz ao seu sítio isto é, o lugar onde index.php pode ser visitado. Exemplos: <strong>http://www.example.org</strong> ou <strong>https://example.org:443/schoolsite</strong>.';
$string['cms_progdir_label'] = 'Diretório do programa';
$string['cms_progdir_help'] = 'Este é o caminho para o diretório que mantém  ficheiros do programa  Website@School (geralmente o <strong> programa </strong> subdiretório do diretório do site). Exemplos: <strong>/home/httpd/htdocs/program</strong> ou <strong>C:\Program Files\Apache Group\Apache\htdocs\program</strong>.';
$string['cms_progwww_label'] = 'Programa URL';
$string['cms_progwww_help'] = 'Esta é a URL que conduz ao diretório do programa (geralmente o URL do site seguido é por <strong> /programa </strong>). Exemplos:<strong>http://www.example.org/program</strong> ou <strong>https://example.org:443/schoolsite/program</strong>.';
$string['cms_datadir_label'] = 'Diretório de dados';
$string['cms_datadir_help'] = 'Este é um diretório que contém os ficheiros carregados e outros ficheiros de dados. É muito importante que este diretório seja localizado fora da "root" do documento, ou seja, não é acessível diretamente através de um navegador. Note que o servidor deve ter permissão suficiente para ler, criar e gravar ficheiros aqui. Exemplos:<strong>/home/httpd/wasdata</strong> ou <strong>C:\Program Files\Apache Group\Apache\wasdata</strong>.';
$string['cms_demodata_label'] = 'Preencher base de dados';
$string['cms_demodata_help'] = 'Selecionar esta caixa de seleção se pretende iniciar com o seu novo sítio usando dados da demonstração.';
$string['cms_demodata_password_label'] = 'Palavra passe da demonstração';
$string['cms_demodata_password_help'] = 'Serão atribuídas a <em> todas </ em> as contas de usuário de demonstração a mesma  palavra passe de demonstração. Por favor, escolher uma palavra passe segura: escolher no mínimo 8 carateres  de letras maiúsculas, letras minúsculas e números. Pode deixar este campo em branco se não tiver selecionado a caixa de seleção  "Preencher base de dados \'acima.';
$string['dialog_user'] = 'Conta de Usuário';
$string['dialog_user_title'] = 'Criar a primeira conta';
$string['dialog_user_explanation'] = 'Por favor introduzir as informações para a primeira conta de usuário para este novo sítio Web. Note que esta conta terá os privilégios de administrador e todas as permissões possíveis. Desta forma, qualquer pessoa com acesso a esta conta pode fazer o que pretender.';
$string['user_full_name_label'] = 'Nome completo';
$string['user_full_name_help'] = 'Favor introduza o seu nome próprio ou, se preferir, um outro nome (funcional), por exemplo,<strong>Wilhelmina Bladergroen</strong> ou <strong>Gestor Web</strong>';
$string['user_username_label'] = 'Nome de Usuário';
$string['user_username_help'] = 'Por favor, introduza o nome de login que pretende utilizar para esta conta. É necessário digitar este nome sempre que pretender iniciar a sessão Exemplos: <strong>wblade</strong> ou <strong>Gestor de Rede</strong>.';
$string['user_password_label'] = 'Palavra Passe';
$string['user_password_help'] = 'Por favor, escolher palavra passe segura: escolher no mínimo 8 carateres de letras maiúsculas, letras minúsculas, números e carateres especiais, como % (por cento), = (igual), / (barra) e . (ponto). Não partilhar a palavra passe com outros, criar, como alternativa, contas adicionais para outras pessoas.';
$string['user_email_label'] = 'Endereço de correio eletrónico';
$string['user_email_help'] = 'Por favor introduzir o endereço eletrónico aqui. Este endereço é necessário sempre que precisar de solicitar uma nova palavra passe. Certifique-se que é a única pessoa a ter acesso a esta caixa de correio (não usar uma caixa de correio partilhada). Exemplos:<strong>wilhelmina.bladergroen@example.org</strong> ou <strong>webmaster@example.org</strong>.';
$string['dialog_compatibility'] = 'Compatibilidade';
$string['dialog_compatibility_title'] = 'Verificar compatibilidade';
$string['dialog_compatibility_explanation'] = 'Abaixo está um resumo das definições necessárias e desejadas. É necessário ter a certeza de que as exigências se encontram satisfeitas antes de continuar.';
$string['compatibility_label'] = 'Teste';
$string['compatibility_value'] = 'Valor';
$string['compatibility_result'] = 'Resultado';
$string['compatibility_ok'] = 'OK';
$string['compatibility_warning'] = 'AVISO';
$string['compatibility_websiteatschool_version_label'] = 'Website@School';
$string['compatibility_websiteatschool_version_check'] = '(verificar)';
$string['compatibility_websiteatschool_version_value'] = 'versão {RELEASE} ({VERSION}) {RELEASE_DATE}';
$string['compatibility_websiteatschool_version_check_title'] = 'Verificar se há versões mais recentes do Website@School';
$string['compatibility_phpversion_label'] = 'versão em PHP';
$string['compatibility_phpversion_obsolete'] = ' A versão em PHP está obsoleta';
$string['compatibility_phpversion_too_old'] = 'Versão em PHP é muito antiga: versão mínima é {MIN_VERSION}';
$string['compatibility_php_safemode_label'] = 'PHP Modo seguro';
$string['compatibility_php_safemode_warning'] = 'Modo de Segurança está Ativado. Por favor, desligar em php.ini';
$string['compatibility_webserver_label'] = 'Servidor de rede';
$string['compatibility_autostart_session_label'] = 'Início de sessão automático';
$string['compatibility_autostart_session_fail'] = 'Início de sessão automático está Ativado. Por favor, desligar em php.ini';
$string['compatibility_file_uploads_label'] = 'Carregamento de ficheiros';
$string['compatibility_file_uploads_fail'] = 'O carregamento de ficheiros está Desativado. Por favor activar em php.ini';
$string['compatibility_database_label'] = 'Servidor de base de dados';
$string['compatibility_clamscan_label'] = 'Antivírus  Clamscan';
$string['compatibility_clamscan_not_available'] = '(indisponível)';
$string['compatibility_gd_support_label'] = 'Ajuda GD';
$string['compatibility_gd_support_none'] = 'GD não é compatível';
$string['compatibility_gd_support_gif_readonly'] = 'Somente para leitura';
$string['compatibility_gd_support_details'] = '{VERSION} (GIF: {GIF}, JPG: {JPG}, PNG: {PNG})';
$string['dialog_confirm'] = 'Confirmação';
$string['dialog_confirm_title'] = 'Confirmar definições';
$string['dialog_confirm_explanation'] = 'Está prestes a instalar o seu novo sítio. Verificar cuidadosamente as configurações abaixo e em seguida pressione [Próximo] para iniciar o processo de instalação. Este processo pode demorar alguns minutos.';
$string['dialog_confirm_printme'] = 'Dica: imprimir esta página e manter a cópia para referência futura.';
$string['dialog_cancelled'] = 'Cancelado';
$string['dialog_cancelled_title'] = '';
$string['dialog_cancelled_explanation'] = 'A instalação do sítio Website@School foi cancelada. Pressionar o botão abaixo para tentar novamente ou clicar no botão de ajuda para ler o manual.';
$string['dialog_finish'] = 'Terminar';
$string['dialog_finish_title'] = 'Terminar o processo de instalação';
$string['dialog_finish_explanation_0'] = 'A instalação do sítio Website@School {VERSION} está praticamente concluída. <p> No entanto, é necessário: <ol> <li>  {AHREF} carregar {A} o ficheiro config.php, e <li> colocar o ficheiro config.php em<tt><strong>{CMS_DIR}</strong></tt>.</ol> Quando o ficheiro config.php  estiver implementado, o programa de instalação pode ser fechado ao pressionar o botão [OK] abaixo.';
$string['dialog_finish_explanation_1'] = 'A instalação  do sítio Website@School {VERSION} está completa. <p>O programa de instalação pode ser fechado ao pressionar  o botão [OK] abaixo.';
$string['dialog_finish_check_for_updates'] = 'Se desejar, pode seguir a hiperligação abaixo para verificar se há atualizações (abre numa nova janela).';
$string['dialog_finish_check_for_updates_anchor'] = 'Verificar se há atualizações do sítio Website@School.';
$string['dialog_finish_check_for_updates_title'] = 'Verificar o estado da versão do sítio Website@School';
$string['jump_label'] = 'Ir para';
$string['jump_help'] = 'Selecionar a localização onde pretende ir depois de pressionar o botão [OK] abaixo';
$string['dialog_download'] = 'Carregar config.php';
$string['dialog_download_title'] = 'Carregar config.php para o seu computador';
$string['dialog_unknown'] = 'Desconhecido';
$string['error_already_installed'] = 'Erro:o sítio Website@School já está instalado';
$string['error_wrong_version'] = 'Error: número de versão errado. Será que carregou a nova versão durante a instalação?';
$string['error_fatal'] = 'Erro fatal {ERROR}: por favor contactar <{EMAIL}>para assistência';
$string['error_php_obsolete'] = 'Erro: a versão de PHP é demasiado antiga';
$string['error_php_too_old'] = 'Erro: a versão de PHP ({VERSION}) é demasiado antiga: utilizar pelo menos a versão {MIN_VERSION}';
$string['error_not_dir'] = 'Erro: {FIELD}: diretório não existe: {DIRECTORY}';
$string['warning_switch_to_custom'] = 'Aviso: mudar para instalação personalizada para que os erros possam ser corrigidos';
$string['error_not_create_dir'] = 'Erro: {FIELD}: diretório não pode ser criado: {DIRECTORY}';
$string['error_db_unsupported'] = 'Erro: base de dados {DATABASE} atualmente não é compatível';
$string['error_db_cannot_connect'] = 'Erro: não é possível estabelecer ligação com o servidor da base de dados';
$string['error_db_cannot_select_db'] = 'Erro: não é possível abrir base de dados';
$string['error_invalid_db_prefix'] = 'Erro: {FIELD}: deve iniciar com uma letra, poderá conter apenas letras, números ou sublinhados';
$string['error_db_prefix_in_use'] = 'Erro: {FIELD}: já em uso: {PREFIX}';
$string['error_time_out'] = 'Erro fatal: tempo limite';
$string['error_db_parameter_empty'] = 'Erro: parâmetros vazios na base de dados não são aceitáveis';
$string['error_db_forbidden_name'] = 'Erro: {FIELD}: este nome não é aceitável: {NAME}';
$string['error_too_short'] = 'Erro: {FIELD}: cadeia de carateres é muito curta (mimimum = {MIN})';
$string['error_too_long'] = 'Erro: {FIELD}: cadeia de carateres é demasiado longa  (máximo = {MAX})';
$string['error_invalid'] = 'Erro: {FIELD}: valor inválido';
$string['error_bad_password'] = 'Erro: {FIELD}: valor não aceitável; requisitos mínimos são: dígitos: {MIN_DIGIT}, minúsculas: {MIN_LOWER}, letras maiúsculas: {MIN_UPPER}';
$string['error_bad_data'] = '{MENU_ITEM}: erros detetados, por favor corrigi-los primeiro (através do menu)';
$string['error_file_not_found'] = 'Erro: não é possível localizar o ficheiro: {FILENAME}';
$string['error_create_table'] = 'Erro: não é possível criar tabela: {TABLENAME} ({ERRNO}/{ERROR})';
$string['error_insert_into_table'] = 'Erro: não é possível inserir dados na tabela: {TABLENAME} ({ERRNO}/{ERROR})';
$string['error_update_config'] = 'Erro: não é possível atualizar configuração: {CONFIG} ({ERRNO}/{ERROR})';
$string['warning_no_manifest'] = 'Aviso: manifesto vazio ou não há manifesto para {ITEM}';
$string['error_install_demodata'] = 'Erro: não é possível instalar os dados de demonstração';
$string['error_directory_exists'] = 'Erro: {FIELD}: diretório já existente : {DIRECTORY}';
$string['error_nameclash'] = 'Erro {FIELD}: por favor, altere o nome {USERNAME}, já é utilizado na conta de usuário de demonstração';
$string['warning_mysql_obsolete'] = 'Aviso: versão \'{VERSION}\' de MySQL está obsoleta e não suporta UTF-8. Por favor, atualizar MySQL.';
?>