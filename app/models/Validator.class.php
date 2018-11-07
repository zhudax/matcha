<?php

class Validator {

    public function checkLogin($login) {
        $pattern = "#^[a-z]+[0-9]*$#i";
        if (strlen($login) == 0)
            return "Le login ne doit pas être vide!";
        else if (strlen($login) < 3)
            return "Le login doit avoir au minimum 3 caractères!";
        else if (strlen($login) > 15)
            return "Le login ne doit pas dépasser de 15 caractères!";
        else if (!preg_match($pattern, $login))
            return "Le login doit commencer par une lettre et ne peut contenir que des lettres et des chiffres!";
        else
            return 0;
    }

    public function checkName($name) {
        $pattern = "#^[a-z]+$#i";
        if (strlen($name) == 0)
            return "Le champ ne doit pas être vide!";
        else if (!preg_match($pattern, $name))
            return "Le champ ne peut contenir que des lettres!";
        else
            return 0;
    }

    public function checkPassword($pass) {
        if (strlen($pass) < 6)
            return "Le mot de passe doit avoir au minimum 6 caractères!";
        else if (strlen($pass) > 16)
    		return "Le mot de passe ne doit pas dépasser 16 caractères!";
        else if ($this->passwordLevel($pass) != 3)
            return "Le mot de passe est trop simple! Il faut au moins une majuscule, une minuscule et un chiffre!";
        else
            return 0;
    }

    private function passwordLevel($pass) {
        $level = 0;

        if (preg_match("#[0-9]+#", $pass)) {
            $level++;
        }
        if (preg_match("#[a-z]+#", $pass)) {
            $level++;
        }
        if (preg_match("#[A-Z]+#", $pass)) {
            $level++;
        }
        return $level;
    }

    public function checkEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            return "Le format d'email n'est pas le bon!";
        else
            return 0;
    }

    public function checkBrithday($birthday) {
        if (empty($birthday))
            return "La date de naissance ne doit pas être vide!";
        else {
            list($uyear, $umonth, $uday) = explode('-', $birthday);
            if (!checkdate($umonth, $uday, $uyear))
                return "La date de naissance n'est pas valide!";
            $age = $this->calcAge($birthday);
            if ($age < 18)
                return "Votre êtrs trop jeune :)";
            else
                return 0;
        }
    }

    public function calcAge($birthday) {
        list($uyear, $umonth, $uday) = explode('-', $birthday);
		list($year, $month, $day) = explode('/', date('Y/m/d'));
        if (($umonth < $month) || (($umonth == $month) && ($uday <= $day)))
            $age = $year - $uyear;
        else
            $age = $year - $uyear - 1;
		return $age;
    }

    public function checkTag($tag) {
        $pattern = "#^[a-z0-9]+$#i";
        if (strlen($tag) == 0)
            return "Le tag ne doit pas être vide!";
        else if (!preg_match($pattern, $tag))
            return "Le tag ne peut contenir que des lettres et des chiffres!";
        else
            return 0;
    }
}
