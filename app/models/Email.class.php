<?php

class Email {
    function forgot_pass($to, $nom, $prenom, $uid, $cle) {
        $message = "Bonjour " . ucfirst($prenom) . " " . ucfirst($nom) . ",<br>

		Pour changer le mot de passe, veuillez cliquer sur le lien ci dessous
		ou copier/coller dans votre navigateur internet.<br>

		http://localhost:8080/matcha/forgot?uid=" . $uid . "&cle=" . $cle . "<br>

        Ceci est un mail automatique, merci de ne pas y répondre.";
		mail($to, "Changer le mot de passe", $message, "From: forgot_pass@matcha.42.fr");
    }

    function active_account($to, $nom, $prenom, $uid, $cle) {
        $message = "Bonjour " . ucfirst($prenom) . " " . ucfirst($nom) . ",<br>

		Pour activer votre compte, veuillez cliquer sur le lien ci dessous
		ou copier/coller dans votre navigateur internet.<br>

		http://localhost:8080/matcha/active?uid=" . $uid . "&cle=" . $cle . "<br>

        Ceci est un mail automatique, merci de ne pas y répondre.";
		mail($to, "Activer votre compte", $message, "From: active_account@matcha.42.fr");
    }

    function send_report_account($uid, $dst_uid) {
        $message = "L'utilisateur " . $uid . " a report l'utilisateur " . $dst_uid . " comme un faux compte!";

        mail("zxu@student.42.fr", "Un compte a été report", $message, "From: report_account@matcha.42.fr");
        mail("lezhang@student.42.fr", "Un compte a été report", $message, "From: report_account@matcha.42.fr");
    }

    function send_advice($message_user, $nom, $email) {
        $message = ucfirst($nom) . " a donné des conseils!

        <br><br>" . $message_user;

        mail("zxu@student.42.fr", "Conseils pour le site", $message, "From: " . $email);
        mail("lezhang@student.42.fr", "Conseils pour le site", $message, "From: " . $email);
    }
}
