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

/** /program/languages/fr/loginlib.php
 *
 * Language: fr (Français)
 * Release:  0.90.3 / 2012041700 (2012-04-17)
 *
 * @author Jean Peyratout <translators@websiteatschool.eu> <jean.peyratout@abul.org>
 * @copyright Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_fr
 * @version $Id: loginlib.php,v 1.4 2013/06/11 11:25:12 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = 'Connexion';
$string['translatetool_description'] = 'Ce fichier contient des traductions à propos de la connexion / déconnexion';
$string['access_denied'] = 'Accès refusé';
$string['change_password'] = 'Modifier le mot de passe';
$string['change_password_confirmation_message'] = 'Votre mot de passe a été modifié. 

La demande de modification de mot de passe a été envoyée 
par l\'adresse {REMOTE_ADDR} le {DATETIME}.

Cordialement, 

Votre webmestre automatique';
$string['change_password_confirmation_subject'] = 'Votre mot de passe a bien été modifié.';
$string['contact_webmaster_for_new_password'] = 'Contactez le webmestre pour modifier votre mot de passe.';
$string['do_you_want_to_try_forgot_password_procedure'] = 'Identifiant ou mot de passe invalide(s). Voulez-vous utiliser la procédure "Mot de passe oublié" ?';
$string['email_address'] = 'Adresse courriel';
$string['failure_sending_laissez_passer_mail'] = 'Échec de l\'envoi par courriel d\'un code à usage unique.  Réessayez, ou contactez le webmestre si le problème persiste.';
$string['failure_sending_temporary_password'] = 'Échec de l\'envoi par courriel d\'un mot de passe temporaire.  Réessayez, ou contactez le webmestre si le problème persiste.';
$string['forgot_password'] = 'Vous avez oublié votre mot de passe ?';
$string['forgotten_password_mailmessage1'] = 'Voici un lien avec un code à usage unique qui vous permettra de solliciter un nouveau mot de passe temporaire. Copiez le lien ci-dessous dans la barre d\'adresse de votre navigateur et appuyez sur la touche [Entrée] :

    {AUTO_URL}

Vous pouvez aussi aller à cette adresse :

    {MANUAL_URL}

et y saisir votre identifiant et ce code à usage unique :

    {LAISSEZ_PASSER}

Notez que ce code n\'est valide que durant {INTERVAL} minutes.

La demande pour ce code à usage unique a été reçue depuis cette adresse courriel :

    {REMOTE_ADDR}

Bonne chance ! 

Votre webmestre automatique';
$string['forgotten_password_mailmessage2'] = 'Voici votre mot de passe temporaire : 

    {PASSWORD}

Notez que ce mot de passe n\'est valable que durant {INTERVAL} minutes.

La demande pour ce mot de passe temporaire a été reçue depuis cette adresse courriel :

    {REMOTE_ADDR}

Bonne chance ! 

Votre webmestre automatique';
$string['home_page'] = '(home)';
$string['invalid_credentials_please_retry'] = 'Identifiant ou mot de passe invalide(s), réessayez.';
$string['invalid_laissez_passer_please_retry'] = 'Code à usage unique invalide, réessayez.';
$string['invalid_new_passwords'] = 'Votre nouveau mot de passe n\'est pas valable. Raisons possibles :
le second mot de passe saisi ne coïncide pas exactement avec le premier ;
le nouveau mot de passe n\'est pas assez long  (minimum {MIN_LENGTH}),
il ne comprend pas suffisamment de lettres minuscules (minimum {MIN_LOWER}), de lettres majuscules (minumum {MIN_UPPER}) ou de chiffres (minimum {MIN_DIGIT}) ; 
ou le nouveau mot de passe est identique à l\'ancien.

Imaginez un nouveau mot de passe convenable et réessayez.';
$string['invalid_username_email_please_retry'] = 'Identifiant et adresse courriel invalides, réessayez.';
$string['laissez_passer'] = 'Code à usage unique';
$string['login'] = 'Identifiant';
$string['logout_forced'] = 'Déconnexion forcée';
$string['logout_successful'] = 'Connexion réussie';
$string['message_box'] = 'Boîte pour message';
$string['must_change_password'] = 'Maintenant vous devez changer votre mot de passe.';
$string['new_password1'] = 'Nouveau mot de passe';
$string['new_password2'] = 'Confirmez le nouveau mot de passe';
$string['OK'] = 'OK';
$string['password'] = 'Mot de passe';
$string['password_changed'] = 'Votre mot de passe a été modifié.';
$string['please_enter_new_password_twice'] = 'Entrez votre identifiant et mot de passe, puis votre nouveau mot de passe deux fois, et pressez le bouton';
$string['please_enter_username_email'] = 'Entrez votre identifiant et votre adresse courriel puis pressez le bouton.';
$string['please_enter_username_laissez_passer'] = 'Entrez votre identifiant et votre code à usage unique puis pressez le bouton.';
$string['please_enter_username_password'] = 'Entrez votre identifiant et votre mot de passe puis pressez le bouton.';
$string['request_bypass'] = 'Demande de mot de passe temporaire';
$string['request_laissez_passer'] = 'Demande de code à usage unique';
$string['see_mail_for_further_instructions'] = 'Consultez votre courriel pour la suite des instructions.';
$string['see_mail_for_new_temporary_password'] = 'Consultez votre courriel pour obtenir votre mot de passe temporaire.';
$string['too_many_change_password_attempts'] = 'Trop d\'essais successifs de changement de mot de passe.';
$string['too_many_login_attempts'] = 'Trop d\'essais successifs de connexion.';
$string['username'] = 'Identifiant';
$string['your_forgotten_password_subject1'] = 'Re: Demande de code de connexion à usage unique';
$string['your_forgotten_password_subject2'] = 'Re: Demande de mot de passe temporaire';
?>