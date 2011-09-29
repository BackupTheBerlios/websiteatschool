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

/** /program/install/languages/fr/install.php
 *
 * Language: fr (Français)
 * Release:  0.90.2 / 2011092900 (2011-09-29)
 *
 * @author Jean Peyratout <translators@websiteatschool.eu> <jean.peyratout@abul.org>
 * @copyright Copyright (C) 2008-2011 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_fr
 * @version $Id: install.php,v 1.3 2011/09/29 19:06:20 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = 'Installation';
$string['translatetool_description'] = 'Ce fichier contient  les traductions du programme d\'installation';
$string['websiteatschool_install'] = 'Installation de Website@School';
$string['websiteatschool_logo'] = 'Logo Website@School';
$string['help_name'] = 'Aide';
$string['help_description'] = 'Aide (ouvre une nouvelle fenêtre)';
$string['next'] = 'Suivant';
$string['next_accesskey'] = 'S';
$string['next_title'] = 'Utiliser [Alt-S] ou [Cmnd-S] comme raccourci clavier pour ce bouton';
$string['previous'] = 'Précédent';
$string['previous_accesskey'] = 'P';
$string['previous_title'] = 'Utiliser [Alt-P] ou [Cmnd-P] comme raccourci clavier pour ce bouton';
$string['cancel'] = 'Annuler';
$string['cancel_accesskey'] = 'A';
$string['cancel_title'] = 'Utiliser [Alt-A] ou [Cmnd-A] comme raccourci clavier pour ce bouton';
$string['ok'] = 'OK';
$string['ok_accesskey'] = 'K';
$string['ok_title'] = 'Utiliser [Alt-K] ou [Cmnd-K] comme raccourci clavier pour ce bouton';
$string['yes'] = 'Oui';
$string['no'] = 'Non';
$string['language_name'] = 'Français';
$string['dialog_language'] = 'Langue';
$string['dialog_language_title'] = 'Choisir la langue d\'installation';
$string['dialog_language_explanation'] = 'Choisir la langue à utiliser durant la procédure d\'installation';
$string['language_label'] = 'Langue';
$string['language_help'] = '';
$string['dialog_installtype'] = 'Type d\'installation ';
$string['dialog_installtype_title'] = 'Choisir entre installation standard ou personnalisée';
$string['dialog_installtype_explanation'] = 'Choisir le scénario d\'installation dans la liste ci-dessous';
$string['installtype_label'] = 'Scénario d\'installation';
$string['installtype_help'] = 'Choisir le scénario d\'installation approprié.<br/><strong>Standard</strong> signifie une installation simple avec un minimum de questions,<br/><strong>Personnalisée</strong> vous donne un contrôle complet  de toutes les options d\'installation.';
$string['installtype_option_standard'] = 'Standard';
$string['installtype_option_custom'] = 'Personnalisée';
$string['high_visibility_label'] = 'Haute visibilité';
$string['high_visibility_help'] = 'Cocher la case pour utiliser une interface texte seulement durant l\'installation.';
$string['dialog_license'] = 'Licence';
$string['dialog_license_title'] = 'Lire et accepter la licence de ce logiciel';
$string['dialog_license_explanation'] = 'L\'utilisation de ce logiciel sous licence vous est autorisée si et seulement si vous lisez, comprenez et êtes d\'accord avec les termes et conditions qui suivent.  Notez que la version en anglais de cet accord de licence s\'applique, même si vous installez le logiciel en utilisant une autre langue.';
$string['dialog_license_i_agree'] = 'Je suis d\'accord';
$string['dialog_license_you_must_accept'] = 'Vous devez accepter l\'ccord de licence en saisissant &quot;<b>{IAGREE}</b>&quot; (sans les guillemets) dans la boîte ci-dessous.';
$string['dialog_database'] = 'Base de données';
$string['dialog_database_title'] = 'Saisir les informations relatives au serveur de base de données';
$string['dialog_database_explanation'] = 'Saisir les informations relatives à votre serveur de base de données dans les champs ci-dessous.';
$string['db_type_label'] = 'Type';
$string['db_type_help'] = 'Choisir l\'un des types de base de données disponible.';
$string['db_type_option_mysql'] = 'MySQL';
$string['db_server_label'] = 'Serveur';
$string['db_server_help'] = 'C\'est l\'adresse du serveur de base de données, habituellement <strong>localhost</strong>. Autres exemples : <strong>mysql.example.org</strong> ou <strong>exemple.dbserver.fournisseur.net:3306</strong>.';
$string['db_username_label'] = 'Nom d\'utilisateur';
$string['db_username_help'] = 'Une combinaison correcte nom d\'utilisateur/ mot de passe est requise pour se connecter au serveur de base de données. N\'utiisez pas le compte root du sereur de base de données mais un compte avec moins de droits, par ex. <strong>wasutilisateur</strong> ou <strong>exemple_wwwa</strong>.';
$string['db_password_label'] = 'Mot de passe';
$string['db_password_help'] = 'Une combinaison correcte nom d\'utilisateur/ mot de passe est requise pour se connecter au serveur de base de données.';
$string['db_name_label'] = 'Nom de la base';
$string['db_name_help'] = 'C\'est le nom de la base de données à utiliser. Notez que cette base de données doit déjà exister ; ce programme d\'installation n\'est pour d\'évidentes raisons de sécurité pas conçu pour créer des bases de données. Exemples : <strong>www</strong> or <strong>exemple_www</strong>.';
$string['db_prefix_label'] = 'Préfixe';
$string['db_prefix_help'] = 'Tous les noms de tables de la base de données commencent par ce préfixe. Cela permet des installations multiples dans la même base de données. Notez que le préfixe doit commencer par une lettre. Exemples : <strong>was_</strong> ou <strong>cms2_</strong>.';
$string['dialog_cms'] = 'Site Web';
$string['dialog_cms_title'] = 'Saisir les informations essentielles relatives au site Web';
$string['dialog_cms_explanation'] = 'Saisir les informations essentielles relatives au site Web dans les champs ci-dessous.';
$string['cms_title_label'] = 'Titre du site web';
$string['cms_title_help'] = 'Le nom de votre site web.';
$string['cms_website_from_address_label'] = 'De : adresse';
$string['cms_website_from_address_help'] = 'Cette adresse courriel est utilisée pour le courriel sortant, par exemple les alertes et les rappels de mot de passe.';
$string['cms_website_replyto_address_label'] = 'Répondre à : adresse';
$string['cms_website_replyto_address_help'] = 'Cette adresse courriel est ajoutée au courriel sortant et peut être utilisée pour spécifier une boîte aux lettres dans laquelle les réponses seront réellement lues (par vous) et non éliminées (par le logiciel du serveur Web).';
$string['cms_dir_label'] = 'Répertoire du site web';
$string['cms_dir_help'] = 'C\'est le chemin vers le répertoire qui contient les fichiers index.php et config.php, par ex. <strong>/home/httpd/htdocs</strong> ou <strong>C:\Program Files\Apache Group\Apache\htdocs</strong>.';
$string['cms_www_label'] = 'URL du site web';
$string['cms_www_help'] = 'C\'est l\'URL principale qui conduit à votre site, c\'est-à-dire l\'endroit où index.php peut être visité. Exemples : <strong>http://www.exemple.org</strong> ou <strong>https://example.org:443/site-ecole</strong>.';
$string['cms_progdir_label'] = 'Répertoire du programme';
$string['cms_progdir_help'] = 'C\'est le chemin vers le répertoire qui contient les fichiers programme de Website@School (habituellement le sous-répertoire <strong>program</strong> du répertoire du site Web). Exemples : <strong>/home/httpd/htdocs/program</strong> ou <strong>C:\Program Files\Apache Group\Apache\htdocs\program</strong>.';
$string['cms_progwww_label'] = 'URL du programme';
$string['cms_progwww_help'] = 'C\'est l\'URL qui mène au répertoire du programme (habituellement l\'URL du site suivie de <strong>/program</strong>). Exemples : <strong>http://www.exemple.org/program</strong> ou <strong>https://exemple.org:443/site-ecole/program</strong>.';
$string['cms_datadir_label'] = 'Répertoire de données';
$string['cms_datadir_help'] = 'C\'est un répertoire qui contient les fichiers téléversés et les autres fichiers de données. Il est très important que ce répertoire soit situé en dehors de la racine, c\'est-à-dire qu\'il ne soit pas directement accessible via un navigateur. Notez que le serveur Web doit avoir des droits suffisants pour y lire, créer et écrire des fichiers. Exemples : <strong>/home/httpd/wasdata</strong> ou <strong>C:\Program Files\Apache Group\Apache\wasdata</strong>.';
$string['cms_demodata_label'] = 'Peupler la base de données';
$string['cms_demodata_help'] = 'Cocher cette case si vous voulez démarrer votre nouveau site Web en utilisant des données de démonstration.';
$string['cms_demodata_password_label'] = 'Mot de passe de démonstration';
$string['cms_demodata_password_help'] = 'Le même mot de passe de démonstration sera affecté à <em>tous</em> les comptes utiisateurs de démonstration. Choisissez un bon mot de passe : au moins 8 caractères avec des majuscles, des minuscules et des chiffres. Vous pouvez laisser ce champ vide si vous n\'avez pas coché la case \'Peupler la base de données\'';
$string['dialog_user'] = 'Compte Utilisateur';
$string['dialog_user_title'] = 'Créer le premier utilisateur';
$string['dialog_user_explanation'] = 'Saisir les informations relatives au premier utilisateur de ce nouveau site Web. Notez que ce compte aura les privilèges complets d\'administrateur et toutes les permissions possibles et qu\'ainsi quiconque qui accèderait à ce compte pourrait effectuer toute action qu\'il désirerait.';
$string['user_full_name_label'] = 'Nom complet';
$string['user_full_name_help'] = 'Saisir votre propre nom ou, si vous le préférez, un autre nom fonctionnel, par ex. <strong>Célestin Freinet</strong> ou <strong>Webmestre</strong>.';
$string['user_username_label'] = 'Nom d\'utilisateur';
$string['user_username_help'] = 'Saisir le nom d\'utiisateur que vous voulez utiliser pour ce compte. Vous devrez saisir ce nom à chaque fois que vous voudrez vous connecter. Exemples: <strong>cfrein</strong> ou <strong>webmestre</strong>.';
$string['user_password_label'] = 'Mot de passe';
$string['user_password_help'] = 'Choisir un bon mot de passe : au moins 8 caractères avec des lettres majuscules, des lettres minuscules, des chiffres et ds caractères spéciaux tels que % (pourcentage), = (égal),  / (barre oblique) et . (point). Ne donnez jamais votre mot de passe à quiconque, créez plutôt des comptes supplémentaires pour chacun de vos collègues.';
$string['user_email_label'] = 'Adresse courriel';
$string['user_email_help'] = 'Saisir votre adresse courriel. Cette adresse est nécessaire pour demander un nouveau mot de passe. Assurez-vous que vous êtes seul(e) à accéder à cette boîte courriel (n\'utilisez pas une boîte partagée ou une adresse de liste). Exemples : <strong>celestin.freinet@exemple.org</strong> ou <strong>webmestre@exemple.org</strong>.';
$string['dialog_compatibility'] = 'Compatibilité';
$string['dialog_compatibility_title'] = 'Vérifier la compatibilité';
$string['dialog_compatibility_explanation'] = 'Ci-dessous est présentée une vue d\'ensemble des réglages requis et souhaitables. Assurez-vous que ces prérequis sont satisfaits avant de continuer.';
$string['compatibility_label'] = 'Test';
$string['compatibility_value'] = 'Valeur';
$string['compatibility_result'] = 'Résultat';
$string['compatibility_ok'] = 'OK';
$string['compatibility_warning'] = 'ATTENTION';
$string['compatibility_websiteatschool_version_label'] = 'Website@School';
$string['compatibility_websiteatschool_version_check'] = '(vérification)';
$string['compatibility_websiteatschool_version_value'] = 'version {RELEASE} ({VERSION}) {RELEASE_DATE}';
$string['compatibility_websiteatschool_version_check_title'] = 'Vérifier pour des versions postérieures de Website@School';
$string['compatibility_phpversion_label'] = 'Version PHP';
$string['compatibility_phpversion_obsolete'] = 'La version de PHP est obsolète';
$string['compatibility_phpversion_too_old'] = 'La version de PHP est trop ancienne : le minimum est {MIN_VERSION}';
$string['compatibility_php_safemode_label'] = 'Mode sûr de PHP';
$string['compatibility_php_safemode_warning'] = 'Le mode sûr de PHP est sur \'On\'. Merci de le basculer sur \'Off\' dans le fichier php.ini';
$string['compatibility_webserver_label'] = 'Serveur Web';
$string['compatibility_autostart_session_label'] = 'Démarrage automatique de session';
$string['compatibility_autostart_session_fail'] = 'Le démarrage automatique de session est sur \'On\'. Merci de le basculer sur \'Off\' dans le fichier php.ini';
$string['compatibility_file_uploads_label'] = 'Téléversement de fichiers';
$string['compatibility_file_uploads_fail'] = 'Le téléversement de fichiers est sur \'Off\'. Merci de le basculer sur \'On\' dans le fichier php.ini';
$string['compatibility_database_label'] = 'Serveur de base de données';
$string['compatibility_clamscan_label'] = 'Antivirus Clamscan ';
$string['compatibility_clamscan_not_available'] = '(non disponible)';
$string['compatibility_gd_support_label'] = 'Support GD';
$string['compatibility_gd_support_none'] = 'GD n\'est pas supporté';
$string['compatibility_gd_support_gif_readonly'] = 'Lecture seule';
$string['compatibility_gd_support_details'] = '{VERSION} (GIF: {GIF}, JPG: {JPG}, PNG: {PNG})';
$string['dialog_confirm'] = 'Confirmation';
$string['dialog_confirm_title'] = 'Confirmer les réglages';
$string['dialog_confirm_explanation'] = 'Vous êtes sur le point d\'installer votre nouveau site Web. Vérifiez avec attention les réglages de configuration ci-dessous puis presser [Suivant] pour lancer le processus réel d\'installation. Cela prendra quelque temps…';
$string['dialog_confirm_printme'] = 'Astuce : imprimez cette page et conservez-la pour pouvoir vous y référer ensuite si nécessaire.';
$string['dialog_cancelled'] = 'Annulé';
$string['dialog_cancelled_title'] = '';
$string['dialog_cancelled_explanation'] = 'L\'installation de Website@School a été annulée. Pressez le bouton ci-dessous pour réessayer ou cliquez sur le bouton d\'aide pour lire le manuel.';
$string['dialog_finish'] = 'Terminer';
$string['dialog_finish_title'] = 'Terminer la procédure d\'installation';
$string['dialog_finish_explanation_0'] = 'L\'installation de Website@School {VERSION} est maintenant presque terminée.<p>Il y a encore deux choses à faire :<ol><li>Vous devez maintenant {AHREF}télécharger{A} le fichier config.php, et<li>Vous devez place ce fichier config.php dans <tt><strong>{CMS_DIR}</strong></tt>.</ol>Une fois que le fichier config.php sera en place, vous pourrez fermer l\'installeur en pressant sur le bouton [OK] ci-dessous.';
$string['dialog_finish_explanation_1'] = 'L\'installation de Website@School {VERSION} est maintenant terminée.<p>Vous pouvez fermer l\'installeur en pressant sur le bouton [OK] ci-dessous.';
$string['dialog_finish_check_for_updates'] = 'Si vous le souhaitez, vous pouvez suivre le llien ci-dessous pour vérifier les éventuelles mises à jour (le lien s\'ouvre dans une nouvelle fenêtre).';
$string['dialog_finish_check_for_updates_anchor'] = 'Vérifier les mises à jour de Website@School.';
$string['dialog_finish_check_for_updates_title'] = 'Vérifier le statut de votre version de Website@School';
$string['jump_label'] = 'Aller à ';
$string['jump_help'] = 'Choisir où vous voulez aller après avoir pressé le bouton [OK] ci-dessous.';
$string['dialog_download'] = 'Télécharger le fichier config.php';
$string['dialog_download_title'] = 'Télécharger le fichier config.php sur votre ordinateur';
$string['dialog_unknown'] = 'Inconnu';
$string['error_already_installed'] = 'Erreur : Website@School est déjà installé';
$string['error_wrong_version'] = 'Erreur : mauvais numéro de version. Avez-vous téléchargé une nouvelle version durant l\'installation ?';
$string['error_fatal'] = 'Erreur fatale {ERROR} : SVP contactez &lt;{EMAIL}&gt; pour de l\'assistance';
$string['error_php_obsolete'] = 'Erreur : la version de PHP est trop ancienne';
$string['error_php_too_old'] = 'Erreur : la version de PHP ({VERSION}) est trop ancienne : utilisez au moins la version {MIN_VERSION}';
$string['error_not_dir'] = 'Erreur : {FIELD} : le répertoire n\'existe pas  : {DIRECTORY}';
$string['warning_switch_to_custom'] = 'Attention : bascule vers l\'installation personnalisée afin que les erreurs puissent être corrigées';
$string['error_not_create_dir'] = 'Erreur : {FIELD} : le répertoire ne peut pas être créé : {DIRECTORY}';
$string['error_db_unsupported'] = 'Erreur : la base de données {DATABASE} n\'est pas actuellement supportée';
$string['error_db_cannot_connect'] = 'Erreur : ne peut pas établir de connexion avec le serveur de base de données';
$string['error_db_cannot_select_db'] = 'Erreur : ne peut pas ouvrir la base de données';
$string['error_invalid_db_prefix'] = 'Erreur :  {FIELD} : doit commencer par une lettre, et ne doit contenir que des lettres, des chiffres ou des soulignés';
$string['error_db_prefix_in_use'] = 'Erreur : {FIELD} : déjà utilisé : {PREFIX}';
$string['error_time_out'] = 'Erreur fatale : time-out';
$string['error_db_parameter_empty'] = 'Erreur :  les paramètres de base de données vide ne sont pas acceptables';
$string['error_db_forbidden_name'] = 'Erreur :  {FIELD} : ce nom n\'est pas acceptable : {NAME}';
$string['error_too_short'] = 'Erreur :  {FIELD} : la chaîne est trop courte (mimimum = {MIN})';
$string['error_too_long'] = 'Erreur :  {FIELD} : la chaîne est trop longue (maximum = {MAX})';
$string['error_invalid'] = 'Erreur : {FIELD} : valeur invalide';
$string['error_bad_password'] = 'Erreur :  {FIELD} : valeur non acceptable ; le minimum requis est : chiffres : {MIN_DIGIT}, minuscules : {MIN_LOWER}, majuscules : {MIN_UPPER}';
$string['error_bad_data'] = '{MENU_ITEM} : des erreurs ont été détectées, merci de les corriger d\'abord (via le menu)';
$string['error_file_not_found'] = 'Erreur : fichier introuvable : {FILENAME}';
$string['error_create_table'] = 'Erreur : impossible de créer la table : {TABLENAME} ({ERRNO}/{ERROR})';
$string['error_insert_into_table'] = 'Erreur :  impossible d\'insérer des données dans la table : {TABLENAME} ({ERRNO}/{ERROR})';
$string['error_update_config'] = 'Erreur : impossible de mettre à jour la configuration : {CONFIG} ({ERRNO}/{ERROR})';
$string['warning_no_manifest'] = 'Attention : manifeste vide ou pas de manifeste pour {ITEM}';
$string['error_install_demodata'] = 'Erreur : impossible d\'installer les données de démonstration';
$string['error_directory_exists'] = 'Erreur :  {FIELD} : ce répertoire existe déjà : {DIRECTORY}';
$string['error_nameclash'] = 'Erreur : {FIELD} : modifiez le nom {USERNAME} ; il est déjà utilisé par un compte utilisateur de démonstration';
$string['warning_mysql_obsolete'] = 'Avertissement : la version \'{VERSION}\' de MySQL est obsolète et ne permet pas l\'utilisation de UTF-8. Merci de mettre à jour MySQL';
?>