<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2012 Vereniging Website At School, Amsterdam, <info@websiteatschool.eu>
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

/** /program/languages/pt/loginlib.php
 *
 * Language: pt (Português)
 * Release:  0.90.3 / 2012041700 (2012-04-17)
 *
 * @author Rita Valente Ribeiro da Silva <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2012 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_pt
 * @version $Id: loginlib.php,v 1.1 2012/04/17 15:20:14 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = 'Iniciar sessão';
$string['translatetool_description'] = 'Este arquivo contém traduções relacionadas com iniciar/encerrar sessão';
$string['access_denied'] = 'Acesso negado';
$string['change_password'] = 'Alterar palavra-passe';
$string['change_password_confirmation_message'] = 'A sua palavra-passe foi alterada. 

O pedido para alteração da palavra-passe foi recebido 
através do endereço {REMOTE_ADDR} a {DATETIME}. 

Com os melhores cumprimentos, 

O seu webmaster automático.';
$string['change_password_confirmation_subject'] = 'A sua palavra-passe foi alterada com êxito';
$string['contact_webmaster_for_new_password'] = 'Por favor contactar webmaster para alterar a sua palavra-passe.';
$string['do_you_want_to_try_forgot_password_procedure'] = 'Credenciais inválidas. Pretende tentar o procedimento \'palavra-passe esquecida\' ?';
$string['email_address'] = 'Correio eletrónico';
$string['failure_sending_laissez_passer_mail'] = 'Falhou o envio da mensagem por correio eletrónico com o código único Por favor tente novamente ou contacte o webmaster se o problema persistir.';
$string['failure_sending_temporary_password'] = 'Falhou o envio da mensagem por correio eletrónico com a palavra-passe temporária. Por favor tente novamente ou contacte o webmaster se o problema persistir.';
$string['forgot_password'] = 'Esqueceu a sua palavra-passe?';
$string['forgotten_password_mailmessage1'] = 'Aqui poderá encontrar uma hiperligação com um código único que permite o pedido de uma nova palavra-passe temporária. Copie a hiperligação localizada em baixo para a barra de navegação no seu motor de busca e clique [Enter]: 

{AUTO_URL} 

Alternativamente, poderá visitar a seguinte hiperligação : 

{MANUAL_URL} 

uma vez aí, deverá inserir o seu nome de usuário e o seguinte código único: 

{LAISSEZ_PASSER} 

Informamos que este código é válido somente por {INTERVAL} minutos. 

O pedido para o envio deste código único foi recibido através do seguinte endereço: 

{REMOTE_ADDR} 

Boa sorte! 

O seu webmaster automático';
$string['forgotten_password_mailmessage2'] = 'A sua palavra-passe é a seguinte: 

{PASSWORD} 

Informamos que esta palavra-passe é válida somente por {INTERVAL} minutos. 

O pedido para o envio desta palavra-passe foi recebido através do seguinte endereço: 

{REMOTE_ADDR} 

Boa sorte! 

O seu webmaster automático';
$string['home_page'] = ' (início)';
$string['invalid_credentials_please_retry'] = 'Credenciais inválidas, por favor tente novamente.';
$string['invalid_laissez_passer_please_retry'] = 'Códico-único inválido, por favor tente novamente.';
$string['invalid_new_passwords'] = 'A sua palavra-passe não foi aceite. Causas possíveis: 
a primeira palavra-passe não corresponde à segunda ; 
a nova palavra-passe não é suficientemente longa (minimal {MIN_LENGTH}), 
não existiam suficientes letras minúsculas (minimal {MIN_LOWER}), maiúsculas (minimal {MIN_UPPER}) ou dígitos (minimal {MIN_DIGIT}), na sua nova palavra-passe. 
ou a sua nova palavra-passe é a mesma que a anterior. 
Por favor escolha uma nova palavra-passe e tente novamente.';
$string['invalid_username_email_please_retry'] = 'Nome de usuário e endereço eletrónico inválidos, por favor tente novamente.';
$string['laissez_passer'] = 'Código-único';
$string['login'] = 'Iniciar sessão';
$string['logout_forced'] = 'Será forcado a encerrar a sessão.';
$string['logout_successful'] = 'A sua sessão foi encerrada com êxito.';
$string['message_box'] = 'Caixa de mensagens';
$string['must_change_password'] = 'Deverá alterar de imediato a sua palavra-passe.';
$string['new_password1'] = 'Nova palavra-passe';
$string['new_password2'] = 'Comfirme a sua nova palavra-passe';
$string['OK'] = 'OK';
$string['password'] = 'Palavra-passe';
$string['password_changed'] = 'A sua palavra-passe foi alterada com êxito.';
$string['please_enter_new_password_twice'] = 'Introduza, por favor, nome de usuário, palavra-passe, nova palavra-passe duas vezes e pressione o botão';
$string['please_enter_username_email'] = 'Introduza, por favor, nome de usuário e endereço eletrónico e pressione o botão.';
$string['please_enter_username_laissez_passer'] = 'Introduza, por favor, nome de usuário e código-único e pressione o botão.';
$string['please_enter_username_password'] = 'Introduza, por favor, nome de usuário e palavra-passe e pressione o botão.';
$string['request_bypass'] = 'Solicitar palavra-passe temporária';
$string['request_laissez_passer'] = 'Solicitar código-único de login';
$string['see_mail_for_further_instructions'] = 'Por favor verifique o seu correio eletrónico para mais instruções.';
$string['see_mail_for_new_temporary_password'] = 'Por favor verifique no seu endereço eletrónico a sua nova palavra-passe temporária.';
$string['too_many_change_password_attempts'] = 'Demasiadas tentativas para a alterar a palavra-passe.';
$string['too_many_login_attempts'] = 'Demasiadas tentativas para iniciar a sessão.';
$string['username'] = 'Nome de usuário';
$string['your_forgotten_password_subject1'] = 'Ref: Pedido de lo código-único';
$string['your_forgotten_password_subject2'] = 'Referente: Petición de contraseña temporaria';
?>