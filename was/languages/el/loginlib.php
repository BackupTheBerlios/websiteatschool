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

/** /program/languages/el/loginlib.php
 *
 * Language: el (Ελληνικά)
 * Release:  0.90.4 / 2013061400 (2013-06-14)
 *
 * @author Iakovos Christoforidis <translators@websiteatschool.eu>
 * @copyright Copyright (C) 2008-2013 Vereniging Website At School, Amsterdam
 * @license http://websiteatschool.eu/license.html GNU AGPLv3+Additional Terms
 * @package waslang_el
 * @version $Id: loginlib.php,v 1.1 2013/06/14 19:59:52 pfokker Exp $
 */
if (!defined('WASENTRY')) { die('no entry'); }
$string['translatetool_title'] = 'Σύνδεση';
$string['translatetool_description'] = 'Αυτό το αρχείο περιέχει μεταφράσεις σχετικά με τη σύνδεση/αποσύνδεση';
$string['access_denied'] = 'Δεν επιτρέπεται η πρόσβαση';
$string['change_password'] = 'Αλλάξτε κωδικό πρόσβασης';
$string['change_password_confirmation_message'] = 'Ο κωδικός πρόσβασής σας άλλαξε.

Η αίτηση αλλαγής κωδικού ελήφθη από {REMOTE_ADDR} στις {DATETIME}.

Με φιλικούς χαιρετισμούς,

Αυτοματοποιημένος διαχειριστής ιστοτόπου.';
$string['change_password_confirmation_subject'] = 'Ο κωδικός πρόσβασης άλλαξε επιτυχώς';
$string['contact_webmaster_for_new_password'] = 'Παρακαλώ επικοινωνήστε με τον διαχειριστή ιστότοπου για να αλλάξετε τον κωδικό σας.';
$string['do_you_want_to_try_forgot_password_procedure'] = 'Μη έγκυρα στοιχεία. Θέλετε να δοκιμάσετε τη διαδικασία «Ξεχάσατε τον κωδικό σας»;';
$string['email_address'] = 'Ηλεκτρονική διεύθυνση';
$string['failure_sending_laissez_passer_mail'] = 'Αποτυχία αποστολής ηλεκτρονικού μηνύματος με τον κωδικό μιας χρήσης. Παρακαλώ επικοινωνήστε με τον διαχειριστή αν το πρόβλημα παραμένει.';
$string['failure_sending_temporary_password'] = 'Αποτυχία αποστολής ηλεκτρονικού μηνύματος με τον προσωρινό κωδικό πρόσβασης. Παρακαλώ ξαναπροσπαθήστε ή επικοινωνήστε με τον διαχειριστή ιστότοπου αν το πρόβλημα παραμένει.';
$string['forgot_password'] = 'Ξεχάσατε τον κωδικό σας;';
$string['forgotten_password_mailmessage1'] = 'Σε αυτόν τον σύνδεσμο θα βρείτε έναν κωδικό μιας χρήσης που θα σας επιτρέψει να ζητήσετε νέο, προσωρινό κωδικό πρόσβασης. Αντιγράψτε τον παρακάτω σύνδεσμο στη γραμμή διευθύνσεων του προγράμματος περιήγησης που χρησιμοποιείτε και μετά πατήστε [Enter]:

    {AUTO_URL}

Εναλλακτικά, μπορείτε να πάτε σε αυτήν την τοποθεσία:

    {MANUAL_URL}

και να πληκτρολογήσετε το όνομα χρήστη (username) και τον κωδικό μιας χρήσης:

    {LAISSEZ_PASSER}

Σημειώστε ότι ο κωδικός αυτός ισχύει μόνο για {INTERVAL} λεπτά.

Η αίτηση για αυτόν τον κωδικό μιας χρήσης ελήφθη από την παρακάτω διεύθυνση
    {REMOTE_ADDR}

Καλή τύχη!

Αυτοματοποιημένος διαχειριστής ιστότοπου';
$string['forgotten_password_mailmessage2'] = 'Αυτός είναι ο προσωρινός κωδικός πρόσβασης:

    {PASSWORD}

Προσοχή! Ο κωδικός ισχύει μόνο για {INTERVAL} λεπτά.

Η αίτηση για τον προσωρινό κωδικό πρόσβασης ελήφθη από την παρακάτω διεύθυνση:

    {REMOTE_ADDR}

Καλή τύχη!

Αυτοματοποιημένος διαχειριστής ιστοτόπου';
$string['home_page'] = '(Αρχική)';
$string['invalid_credentials_please_retry'] = 'Μη έγκυρα στοιχεία, παρακαλώ δοκιμάστε ξανά.';
$string['invalid_laissez_passer_please_retry'] = 'Μη έγκυρος κωδικός μιας χρήσης. Παρακαλώ δοκιμάστε ξανά.';
$string['invalid_new_passwords'] = 'Ο νέος κωδικός πρόσβασής σας δεν είναι αποδεκτός. Πιθανοί λόγοι: οι δυο κωδικοί δεν είναι ίδιοι, ο νέος κωδικός πρόσβασης δεν αρκετά μεγάλος (ελάχιστο μήκος {MIN_LENGTH}), δεν χρησιμοποιήσατε αρκετά πεζά γράμματα (ελάχιστα πεζά {MIN_LOWER}) ή αρκετά κεφαλαία γράμματα (ελάχιστα κεφαλαία {MIN_UPPER}) ή αρκετά ψηφία (ελάχιστα ψηφία {MIN_DIGIT}), ο νέος κωδικός σας είναι ίδιος με τον παλιό.
Παρακαλώ σκεφτείτε έναν κατάλληλο νέο κωδικό και δοκιμάστε ξανά.';
$string['invalid_username_email_please_retry'] = 'Μη έγκυρο όνομα χρήστη και διεύθυνση ηλεκτρονικού ταχυδρομείου, παρακαλώ δοκιμάστε ξανά.';
$string['laissez_passer'] = 'Κωδικός μιας χρήσης';
$string['login'] = 'Σύνδεση';
$string['logout_forced'] = 'Η σύνδεσή σας τερματίστηκε.';
$string['logout_successful'] = 'Αποσυνδεθήκατε επιτυχώς.';
$string['message_box'] = 'Πλαίσιο μηνύματος';
$string['must_change_password'] = 'Πρέπει να αλλάξετε τον κωδικό πρόσβασής σας.';
$string['new_password1'] = 'Νέος κωδικός πρόσβασης';
$string['new_password2'] = 'Επιβεβαιώστε τον νέο κωδικό';
$string['OK'] = 'OK';
$string['password'] = 'Κωδικός πρόσβασης';
$string['password_changed'] = 'Ο κωδικός πρόσβασής σας άλλαξε επιτυχώς.';
$string['please_enter_new_password_twice'] = 'Παρακαλώ πληκτρολογήστε το όνομα χρήστη (username) και τον κωδικό πρόσβασης. Επιπλέον, πληκτρολογήστε τον νέο κωδικό σας δύο φορές και μετά πατήστε το πλήκτρο.';
$string['please_enter_username_email'] = 'Παρακαλώ πληκτρολογήστε το όνομα χρήστη (username) και τη διεύθυνση ηλεκτρονικού ταχυδρομείου και μετά πατήστε το πλήκτρο.';
$string['please_enter_username_laissez_passer'] = 'Παρακαλώ πληκτρολογήστε το όνομα χρήστη (username) και τον κωδικό μιας χρήσης και μετά πατήστε το πλήκτρο.';
$string['please_enter_username_password'] = 'Παρακαλώ πληκτρολογήστε το όνομα χρήστη (username) και τον κωδικό πρόσβασης σας και μετά πατήστε το πλήκτρο.';
$string['request_bypass'] = 'Ζητήστε προσωρινό κωδικό πρόσβασης';
$string['request_laissez_passer'] = 'Ζητήστε κωδικό μιας χρήσης.';
$string['see_mail_for_further_instructions'] = 'Παρακαλώ ελέγξτε τα μηνύματα ηλεκτρονικού ταχυδρομείου σας για περαιτέρω οδηγίες.';
$string['see_mail_for_new_temporary_password'] = 'Παρακαλώ ελέγξτε τα μηνύματα ηλεκτρονικού ταχυδρομείου σας τον νέο προσωρινό κωδικό πρόσβασης.';
$string['too_many_change_password_attempts'] = 'Πραγματοποιήθηκαν πάρα πολλές προσπάθειες αλλαγής κωδικού πρόσβασης.';
$string['too_many_login_attempts'] = 'Πραγματοποιήθηκαν πάρα πολλές προσπάθειες σύνδεσης.';
$string['username'] = 'Όνομα χρήστη';
$string['your_forgotten_password_subject1'] = 'Re: Αίτηση κωδικού μιας χρήσης';
$string['your_forgotten_password_subject2'] = 'Re: Αίτηση προσωρινού κωδικού πρόσβασης';
?>