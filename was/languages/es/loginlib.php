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

/** /program/languages/es/loginlib.php
 *
 * Language: es (Español)
 * Release:  0.90.3 / 2012041700 (2012-04-17)
 *
 * @author Anouk Coumans <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2012 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_es
 * @version $Id: loginlib.php,v 1.5 2012/04/17 14:52:07 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = 'iniciar sesión';
$string['translatetool_description'] = 'Este archivo contiene las traducciónes tratando de iniciar /cerrar sesión.';
$string['access_denied'] = 'Acceso denegado';
$string['change_password'] = 'Cambiar contraseña';
$string['change_password_confirmation_message'] = 'Su contraseña ha sido cambiado.

La solicitud para cambiar la contraseña se ha recibido.
de la dirección {REMOTE_ADDR}  el {DATETIME}.

Un saludo atento,

Su administrador del web automático.';
$string['change_password_confirmation_subject'] = 'Su contraseña  ha sido cambiado con éxito.';
$string['contact_webmaster_for_new_password'] = 'Por favor tomar contacto con el administrador del web para cambiar su contraseña.';
$string['do_you_want_to_try_forgot_password_procedure'] = 'Credenciales inválidas. ¿Quiere Usted procesar con  \'contraseña olvidado\' ?';
$string['email_address'] = 'Dirección correo electrónico';
$string['failure_sending_laissez_passer_mail'] = 'Fallo en mandar el mensaje electrónico  con el código-único. Si el problema persiste por favor intentar nuevamente o tomar contacto con el administrador del web';
$string['failure_sending_temporary_password'] = 'Fallo mandar un mensaje electrónico con la contraseña temporánea. Si este problema persiste, por favor intentar nuevamente o tomar contacto con el administrador del web';
$string['forgot_password'] = '¿olvidado su contraseña?';
$string['forgotten_password_mailmessage1'] = 'Aquí hay un enlace con un código-único con la cuál usted puede pedir  una contraseña temporal. Cópiar el enlace abajo hacía la barra de direcciones en su navegador y hacer clic [Enter]:

    {AUTO_URL}

También Usted puede ir a este alternativo:

    {MANUAL_URL}

una vez ahí entrar su nombre de usuario y este código-único:

    {LAISSEZ_PASSER}

Tenga en cuenta que este código es válido por solo {INTERVAL} minutos.

La solicitud para el envío de este código-único fue recibido desde esta dirección:

    {REMOTE_ADDR}

¡Suerte!

Su administrador del web automático';
$string['forgotten_password_mailmessage2'] = 'Aquí está su contraseña provisional:

    {PASSWORD}

Tenga en cuenta que esta contraseña es válida solo {INTERVAL}        minutos

La solicitud para el envio de esta contraseña temporaria ha sido recibido desde esta direccion: 

{REMOTE_ADDR}

!Suerte!

Su administrador del web automatico
';
$string['home_page'] = ' Início';
$string['invalid_credentials_please_retry'] = 'Credenciales inválidas, por favor intenta nuevamente.';
$string['invalid_laissez_passer_please_retry'] = 'Códico-único inválido, por favor intenta nuevamente.';
$string['invalid_new_passwords'] = 'Su nuevo contraseña no es acceptable. Possibles causas:
la primera contraseña no era iguál a la segunda contraseña;
la nueva contraseña no era suficiente largo (minimal {MIN_LENGTH}),
no habían suficiente letras minúsculas (minimal {MIN_LOWER}),
mayúsculas (minimal {MIN_UPPER}) o cifras (minimal {MIN_DIGIT})       en su nueva contraseña.
o su nueva contraseña era la misma que su vieja contraseña.
por favor intenta de crear una contraseña buena y nueva y intenta nuevamente.';
$string['invalid_username_email_please_retry'] = 'Nombre de usuario y dirección electrónico invalido, por favor intentar de nuevamente';
$string['laissez_passer'] = 'Código-único';
$string['login'] = 'Iniciar sesión';
$string['logout_forced'] = 'Usted es forzado cerrar la sesión';
$string['logout_successful'] = 'Usted ha cerrado la sesión con éxito';
$string['message_box'] = 'Casilla de mensajes.';
$string['must_change_password'] = 'Usted tiene que cambiar su contraseña ahora.';
$string['new_password1'] = 'Contraseña nueva';
$string['new_password2'] = 'Confirma contraseña nueva';
$string['OK'] = 'OK';
$string['password'] = 'Contraseña';
$string['password_changed'] = 'Su contraseña ha sido cambiado con éxito.';
$string['please_enter_new_password_twice'] = 'Por favor entrar su nombre de usuario y contraseña y también entra su nueva contrseña dos veces y hacer clic en el botton';
$string['please_enter_username_email'] = 'Entrar su nombre de usuario y dirección de correo electrónico y hacer clic en el botton';
$string['please_enter_username_laissez_passer'] = 'Por favor entrar su nombre usuario y código-único y hacer clic en el botton';
$string['please_enter_username_password'] = 'Por favor entrar su nombre de usuario y contraseña y hacer clic en el botton';
$string['request_bypass'] = 'Pedir una contraseña temporaria';
$string['request_laissez_passer'] = 'Pedir un código-único de iniciar sesión';
$string['see_mail_for_further_instructions'] = 'Por favor ver su correo electrónico para más instucciones';
$string['see_mail_for_new_temporary_password'] = 'Por favor ver su correo electronico para su nueva contraseña temporáneo';
$string['too_many_change_password_attempts'] = 'Demasiados intentos para cambiar la contraseña';
$string['too_many_login_attempts'] = 'Demasiados intentos para inicíar sesión';
$string['username'] = 'Nombre usuarío';
$string['your_forgotten_password_subject1'] = 'Referente: Petición de código-único';
$string['your_forgotten_password_subject2'] = 'Referente: Petición de contraseña temporaria';
?>