<?php
# This file is part of Website@School, a Content Management System especially designed for schools.
# Copyright (C) 2008-2011 Vereniging Website At School, Amsterdam, <info@websiteatschool.eu>
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

/** /program/install/languages/es/install.php
 *
 * Language: es (Español)
 * Release:  0.90.1 / 2011050500 (2011-05-05)
 *
 * @author Hanna Tulleken <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2011 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_es
 * @version $Id: install.php,v 1.4 2011/05/05 07:27:20 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = 'Instalar';
$string['translatetool_description'] = 'Esta cuenta contiene las traducciónes para el programa de la instalación';
$string['websiteatschool_install'] = 'Instalación Website@School ';
$string['websiteatschool_logo'] = 'logo Website@School';
$string['help_name'] = 'ayuda ';
$string['help_description'] = 'Ayuda (se abre en una ventana nueva)';
$string['next'] = 'Siguiente';
$string['next_accesskey'] = 'S';
$string['next_title'] = 'Usar  [Alt-S] o [Cmnd-S] como un acceso directo al teclado para este bottón';
$string['previous'] = 'Anterior';
$string['previous_accesskey'] = 'A';
$string['previous_title'] = 'Usar [Alt-A] o [Cmnd-A] como acceso directo teclado para este botton';
$string['cancel'] = 'Cancelar';
$string['cancel_accesskey'] = 'C';
$string['cancel_title'] = 'Usar  [Alt-C] o [Cmnd-C] como acceso directo teclado para este botton';
$string['ok'] = 'OK';
$string['ok_accesskey'] = 'K';
$string['ok_title'] = 'Usar [Alt-K] o [Cmnd-K] como acceso directo teclado para este botton';
$string['yes'] = 'Si';
$string['no'] = 'No';
$string['language_name'] = 'Español';
$string['dialog_language'] = 'Idioma';
$string['dialog_language_title'] = 'Seleccionar el idioma por la  instalación';
$string['dialog_language_explanation'] = 'Por favor seleccionar el idioma para usar durante el procedimiento de la instalación';
$string['language_label'] = 'Idioma';
$string['language_help'] = '';
$string['dialog_installtype'] = 'Tipo de instalación';
$string['dialog_installtype_title'] = 'Elijir entre una instalación Normal y Personalizar  ';
$string['dialog_installtype_explanation'] = 'Por favor elijir de la lista aquí abajo el escenario de la instalación ';
$string['installtype_label'] = 'El escenario de la instalación';
$string['installtype_help'] = 'Por favor seleccionar el escenario de la instalación apropeada.<br><strong>Estándar</strong> significa una instalación directemente con un mínimo de preguntas para contestar,<br><strong>Personalizar</strong> da control completo de todas las opciónes de la instalación';
$string['installtype_option_standard'] = 'Estándar ';
$string['installtype_option_custom'] = 'Personalizar ';
$string['high_visibility_label'] = 'Alta-visibilidad';
$string['high_visibility_help'] = 'Verificar la casilla para usar un texto-solo usuario interfaz durante la instalación.';
$string['dialog_license'] = 'Licencia';
$string['dialog_license_title'] = 'Leer y aceptar la licencia para este software';
$string['dialog_license_explanation'] = 'Este software tiene la licencia para usted cuando y solo cuando se lee, entienda y está de acuerdo con los siguientes términos y condiciónes. Tenga en cuenta la versión Inglés de este acuerdo de licencia se aplica, aun así  las programas que usted instala usando el idioma diferente';
$string['dialog_license_i_agree'] = 'Convengo';
$string['dialog_license_you_must_accept'] = ' Usted tiene que aceptar el acuerdo de la licencia en escribir  "<b>{IAGREE}</b>" (menos las cotizaciones) en la casilla aquí abajo';
$string['dialog_database'] = 'La base de datos';
$string['dialog_database_title'] = 'Entrar la información del servidor de base de datos';
$string['dialog_database_explanation'] = 'Por favor entrar las propiedades de vuestro servidor de base de datos en las casillas aquí abajo';
$string['db_type_label'] = 'Tipo';
$string['db_type_help'] = 'Elijir uno de los tipos de bases de datos disponibles';
$string['db_type_option_mysql'] = 'MySQL';
$string['db_server_label'] = 'Servidor';
$string['db_server_help'] = 'Esto es la dirección del servidor de base de datos, generalmente <strong>localhost</strong>. Otros ejemplos: <strong>mysql.ejemplo.org</strong> o <strong>ejemplo.dbserver.provider.net:3306</strong>';
$string['db_username_label'] = 'Usuario';
$string['db_username_help'] = 'Una combinación usuario/contraseña válida tiene necesidad para conectar el base de datos-servidor. Por favor no usar la cuenta raíz de base de datos-servidor (root account) pero un database-servidor menos importante, por ejemplo: <strong>wasuser</strong> o <strong>example_wwwa</strong>';
$string['db_password_label'] = 'Contraseña ';
$string['db_password_help'] = 'Una combinación usuario/contraseña válida tiene necesidad para conectar con el servidor de base de datos.';
$string['db_name_label'] = 'Nombre de la base de datos ';
$string['db_name_help'] = 'Este es el nombre de la base de datos para usar. Tenga en cuenta que este base de datos tendría que existir ya; este programa de instalación no esta diseñado para crear bases de datos ( por razónes de seguridad). Ejemplos: <strong>www</strong> o <strong>example_www</strong>';
$string['db_prefix_label'] = 'Prefijo';
$string['db_prefix_help'] = 'Todos los nombres de tablas en la base de datos comienzan con este prefijo. Esto permite instalaciónes múltiples en la misma  base de datos. Tenga en cuenta que el prefijo tiene que comenzar con una letra. Ejamplos: <strong>was_</strong> o <strong>cms2_</strong>';
$string['dialog_cms'] = 'Página de Internet';
$string['dialog_cms_title'] = 'Entrar las informaciones escenciales  de la página de Internet.';
$string['dialog_cms_explanation'] = 'Por favor entra las informaciones escenciales de la página de Internet en las casillas aquí abajo';
$string['cms_title_label'] = 'Título de la página de Internet';
$string['cms_title_help'] = 'El nombre de su página de Internet ';
$string['cms_website_from_address_label'] = 'Desde: la dirección de correo electrónico ';
$string['cms_website_from_address_help'] = 'Esta dirección de correo electrónico es usado por el correo de salida, por ejemplo alertas y recuerdos de contraseña  ';
$string['cms_website_replyto_address_label'] = 'Respuesta a: dirección de correo electrónico   ';
$string['cms_website_replyto_address_help'] = 'Esta dirección de correo electrónico esta agregado al correo de salidas y puede ser usado para especifiquar un buzón a donde las respuestas son leído en efectivo (por ti) y no son descartados (por el software del servidor web)';
$string['cms_dir_label'] = 'Directorio sitio web';
$string['cms_dir_help'] = 'Este es la ruta hacía el directorio donde se puede encontrar los archivos como index.php y config.php, por ejemplo <strong>/home/httpd/htdocs</strong> o <strong>C:\Program Files\Apache Group\Apache\htdocs</strong>';
$string['cms_www_label'] = 'Sitio Web URL';
$string['cms_www_help'] = 'Este es el URL-principal que conduce hacía su sitio web o bien el sitio donde se puede visitar el index.php. Ejemplos son: <strong>http://www.example.org</strong> o <strong>https://example.org:443/schoolsite</strong>.';
$string['cms_progdir_label'] = 'Programa-directorio';
$string['cms_progdir_help'] = 'Este es la ruta hacía el directorio donde se puede encontrar los Website@School archivos de programa (generalmente el subdirectorio <strong>program</strong> de sitio web-directorio) Ejemplos: <strong>/home/httpd/htdocs/program</strong> o <strong>C:\Program Files\Apache Group\Apache\htdocs\program</strong>';
$string['cms_progwww_label'] = 'Programa URL';
$string['cms_progwww_help'] = 'Este es el URL que dirige al directorio del programa (generalmente el sitio web URL seguido a <strong>/programa</strong>). Ejemplos son: <strong>http://www.example.org/program</strong> o <strong>https://example.org:443/schoolsite/program</strong>';
$string['cms_datadir_label'] = 'Datos de directorio';
$string['cms_datadir_help'] = 'Este es un directorio que sostiene archivos cargados y otros archivos de datos. Es muy importante que este directorio está situado fuera de la raíz del documento, es decir no es directamente accesible con un navegador. Tenga en cuenta que el web server debe tener suficiente permiso para leer, para crear y para escribir archivos aquí. Los ejemplos son: <strong>/home/httpd/wasdata</strong> o <strong>C:\Program Files\Apache Group\Apache\wasdata</strong>';
$string['cms_demodata_label'] = 'Llenar la base de datos';
$string['cms_demodata_help'] = 'Verificar esta casilla si usted quiere comenzar con su nuevo Sitio Web  usando datos de demostración';
$string['cms_demodata_password_label'] = 'Contraseña de demostración  ';
$string['cms_demodata_password_help'] = 'La misma contraseña de demostración será asignada a <em>todas</em>cuentas de usuario de demostración.  Por favor elijir una buena contraseña: escojer por lo menos 8 carácteres de letras mayúsculas, de letras minúsculas y cifras. Usted puede dejar este espacio en blanco del campo si usted no comprobó la casilla \' Llenar la base de datos\' aquí arriba';
$string['dialog_user'] = ' Cuenta de usuario';
$string['dialog_user_title'] = 'Crear la primera cuenta';
$string['dialog_user_explanation'] = 'Incorpore por favor la información para el primer usuario explicando este nuevo sitio web. Tenga en cuenta que esta cuenta tendrá privilegios completos del administrador y todos los permisos posibles así que cualquier persona con acceso a esta cuenta puede hacer lo que sea             ';
$string['user_full_name_label'] = 'Nombre completo';
$string['user_full_name_help'] = 'Por favor incorporar su propio nombre o, si usted prefiere, otro nombre (funcional), por ejemplo: <strong>Wilhelmina Bladergroen</strong> o <strong>Web Principal</strong>';
$string['user_username_label'] = ' Nombre de usuario';
$string['user_username_help'] = 'Por favor incorporar el nombre de usuario que usted quiere utilizar para esta cuenta. Usted necesita escribir este nombre cada vez que usted quiere abrir una sesión. Ejemplos:<strong> wblade</strong> o <strong>webmaster</strong>';
$string['user_password_label'] = 'Contraseña ';
$string['user_password_help'] = 'Por favor elijir  una buena contraseña: escojer por lo menos 8 caracteres de letras mayúsculas, de letras minúsculas, cifras y de carácteres especiales como % (pecentaje), = (los iguales), / (raya vertical) y. (punto). No comparte su contraseña con otros, pero en lugar crear cuentas adicionales para sus colegas ';
$string['user_email_label'] = 'Dirección de correo electrónico';
$string['user_email_help'] = ' Por favor entrar aquí su dirección de correo electronico. Se necesita esta dirección siempre cuando se necesita pedir una nueva contraseña. Estar seguro de que solo usted tenga acceso a este buzón (no utilice un buzón compartido). Ejemplos:<strong>wilhelmina.bladergroen@example.org</strong> o <strong>webmaster@example.org</strong>';
$string['dialog_compatibility'] = 'Compatibilidad ';
$string['dialog_compatibility_title'] = 'Comprobar la compatibilidad ';
$string['dialog_compatibility_explanation'] = 'Aquí abajo hay una descripción de ajustes requeridos y deseados. Usted necesita estar seguro de que los requisitos están satisfechos antes de continuar                                                     Hieronder staat een overzicht van noodzakelijke en gewenste instellingen. Overtuig uzelf ervan dat aan alle vereisten is voldaan vooordat u op [Volgende] drukt.';
$string['compatibility_label'] = 'Prueba';
$string['compatibility_value'] = 'Valor';
$string['compatibility_result'] = 'Resultado';
$string['compatibility_ok'] = 'OK';
$string['compatibility_warning'] = 'AVISO';
$string['compatibility_websiteatschool_version_label'] = 'Website@School';
$string['compatibility_websiteatschool_version_check'] = '(verificar)';
$string['compatibility_websiteatschool_version_value'] = 'versión {RELEASE} ({VERSION}) {RELEASE_DATE}';
$string['compatibility_websiteatschool_version_check_title'] = 'Verificar  Website@School versiónes nuevas';
$string['compatibility_phpversion_label'] = 'PHP versión';
$string['compatibility_phpversion_obsolete'] = 'PHP versión es envejecido';
$string['compatibility_phpversion_too_old'] = 'PHP versión es demasiado vieja: el mínimo es {MIN_VERSION}';
$string['compatibility_php_safemode_label'] = 'PHP Modo seguro';
$string['compatibility_php_safemode_warning'] = 'El modo seguro está en marcha.  Por favor apagarlo en php.ini ';
$string['compatibility_webserver_label'] = 'Web server ';
$string['compatibility_autostart_session_label'] = 'Inicio sección automatico';
$string['compatibility_autostart_session_fail'] = 'Inicio automatico de la sesión está en marcha.  Por favor apagarlo en php.ini ';
$string['compatibility_file_uploads_label'] = 'Archivo cargados';
$string['compatibility_file_uploads_fail'] = 'Archivos cargados está Off. Por favor gambiar a On en php.ini';
$string['compatibility_database_label'] = 'Servidor de base de datos';
$string['compatibility_clamscan_label'] = 'Antivirus de Clamscan ';
$string['compatibility_clamscan_not_available'] = '(no disponible)';
$string['compatibility_gd_support_label'] = 'Ayuda de GD';
$string['compatibility_gd_support_none'] = 'GD no está apoyado';
$string['compatibility_gd_support_gif_readonly'] = 'Leer solo';
$string['compatibility_gd_support_details'] = '{VERSION} (GIF: {GIF}, JPG: {JPG}, PNG: {PNG})';
$string['dialog_confirm'] = 'Confirmación';
$string['dialog_confirm_title'] = 'Confirme los ajustes ';
$string['dialog_confirm_explanation'] = 'Usted está a punto de instalar su nuevo Sitio Web. Comprobar cuidadosamente los ajustes de la configuración aquí abajo y a seguir hacer clic [Siguiente] para comenzar el proceso de instalación actual. Esto puede tardar un rato';
$string['dialog_confirm_printme'] = 'Consejo: imprimir esta página y guardar la impresión para la futura referencia';
$string['dialog_cancelled'] = 'Cancelado';
$string['dialog_cancelled_title'] = '';
$string['dialog_cancelled_explanation'] = 'La instalación de Website@School ha sido cancelada. Hacer clic en el bottón aquí abajo para recomprobar o para hacer clic en el bottón de ayuda para leer el manual';
$string['dialog_finish'] = 'Terminar';
$string['dialog_finish_title'] = 'Terminar el procedimiento de la instalación';
$string['dialog_finish_explanation_0'] = 'La instalación de Website@School {VERSION}  ahora está casi completa.<p> Hay dos cosas que quedan para hacer:<ol><li>Usted ahora tiene que {AHREF}cargar{A} el archivo config.php, y<li> Usted tiene que colocar el archivo config.php adentro <tt><strong>{CMS_DIR}</strong></tt>.</ol>Una vez que config.php está en el lugar, usted puede cerrar el instalador hacer clic en el bottón [OK] aquí abajo';
$string['dialog_finish_explanation_1'] = 'La instalación de Website@School {VERSION} ahora esta lista.<p>Usted puede cerrar el instalador haciendo clic el bottón [OK] aquí abajo';
$string['dialog_finish_check_for_updates'] = 'Si usted desea, usted puede seguir el enlace aquí abajo para verificar si hay actualizaciones (el enlace se abre en una ventana nueva)';
$string['dialog_finish_check_for_updates_anchor'] = 'Verificar para actualizaciones de Website@School.';
$string['dialog_finish_check_for_updates_title'] = 'verificar el estado de su versión de Website@School ';
$string['jump_label'] = 'Ir a';
$string['jump_help'] = 'Seleccionar la localización en donde usted quiere ir después de hacer clic el bottón [OK] aquí abajo';
$string['dialog_download'] = 'Cargar config.php';
$string['dialog_download_title'] = 'Cargar config.php hacía su ordenador';
$string['dialog_unknown'] = 'Desconocido';
$string['error_already_installed'] = 'Error: Website@School está instalado ya ';
$string['error_wrong_version'] = 'Error: número de versión incorrecto. ¿Usted ha cargado una nueva versión durante la instalación?';
$string['error_fatal'] = 'Error fatal {ERROR}: por favor entrar en contacto con <{EMAIL}>para ayuda ';
$string['error_php_obsolete'] = 'Error: esta versión de PHP es demasiado viejo ';
$string['error_php_too_old'] = 'Error: la versión de PHP ({VERSION}) es demasiado viejo: utilice por lo menos la versión {MIN_VERSION}';
$string['error_not_dir'] = 'Error: {FIELD}: el directorio no existe: {DIRECTORY}';
$string['warning_switch_to_custom'] = 'Aviso: cambiar la instalación adaptada así que los errores pueden ser corregidos ';
$string['error_not_create_dir'] = 'Error: {FIELD}: el directorio no puede ser creado: {DIRECTORY}';
$string['error_db_unsupported'] = 'Error: la base de datos {DATABASE} no se apoya actualmente ';
$string['error_db_cannot_connect'] = 'Error: no se puede establecer la conexión con el servidor de la base de datos ';
$string['error_db_cannot_select_db'] = 'Error: no se puede abrir la base de datos ';
$string['error_invalid_db_prefix'] = 'Error: {FIELD}: se debe comenzar con una letra, solo puede contener letras, cifras o rayas ';
$string['error_db_prefix_in_use'] = 'Error: {FIELD}: ya está funcionando: {PREFIX}';
$string['error_time_out'] = 'Error fatal: descanso ';
$string['error_db_parameter_empty'] = 'Error: los parámetros de la base de datos vacía no son aceptables';
$string['error_db_forbidden_name'] = 'Error: {FIELD}: este nombre no es aceptable: {NAME}';
$string['error_too_short'] = 'Error: {FIELD}: la cadena es demasiado corta (mimimum = {MIN})';
$string['error_too_long'] = 'Error: {FIELD}: la cadena es demasiado larga (máximo = {MAX})';
$string['error_invalid'] = 'Error: {FIELD}: valor inválido ';
$string['error_bad_password'] = 'Error: {FIELD}: valor no aceptable; los requisitos mínimos son: cifras: {MIN_DIGIT}, minúsculo: {MIN_LOWER}, mayúsculo: {MIN_UPPER}';
$string['error_bad_data'] = '{MENU_ITEM}:los errores son detectados, por favor corrigir estos primeros (vía el menú) ';
$string['error_file_not_found'] = 'Error: no se puede encontrar el archivo: {FILENAME}';
$string['error_create_table'] = 'Error: no se puede crear la tabla: {TABLENAME} ({ERRNO}/{ERROR})';
$string['error_insert_into_table'] = 'Error: no se puede insertar datos en la tabla: {TABLENAME} ({ERRNO}/{ERROR})';
$string['error_update_config'] = 'Error: no se puede actualizar la configuración: {CONFIG} ({ERRNO}/{ERROR})';
$string['warning_no_manifest'] = 'Aviso: vaciar manifesto o ningun manifesto para {ITEM}';
$string['error_install_demodata'] = 'Error: no se puede instalar datos de demostración';
$string['error_directory_exists'] = 'Error: {FIELD}: directorio ya  existe : {DIRECTORY}';
$string['error_nameclash'] = 'Error: {FIELD}: por favor cambiar el nombre {USERNAME}; se utiliza ya como cuenta de usuario de demostración';
$string['warning_mysql_obsolete'] = 'Aviso: MySQL versión {VERSION} es envejecido (it does not support UTF-8. Please upgrade MySQL)';
?>