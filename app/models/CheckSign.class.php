<?php

class CheckSign {
    public function checkSignUpValidator($info, $validator) {
        $errors = [];
		$got_err = 0;

		$errors['login'] = $validator->checkLogin($info['login']);
		$errors['pw'] = $validator->checkPassword($info['pw']);
		$errors['cpw'] = $validator->checkPassword($info['cpw']);
		$errors['email'] = $validator->checkEmail($info['email']);
		$errors['nom'] = $validator->checkName($info['nom']);
		$errors['prenom'] = $validator->checkName($info['prenom']);
        $errors['birthday'] = $validator->checkBrithday($info['birthday']);
		foreach ($errors as $err) {
			if ($err !== 0) {
				$got_err = 1;
				break;
			}
		}
		if ($got_err)
			return json_encode($errors);
		else
			return 0;
    }

    public function checkSignUpDatabase($info, $db) {
        $errors = [];
        $got_err = 0;
        $sqlCheckLogin = "SELECT `login` FROM `users` WHERE `login`=?";
        $sqlCheckEmail = "SELECT `email` FROM `users` WHERE `email`=?";

        $db->execSql($sqlCheckLogin, array($info['login']));
        $errors['login'] = ($db->getFirstResult() !== 0) ? "Le login existe déjà!" : 0;
        $errors['pw'] = $this->compareTwoPw($info['pw'], $info['cpw']);
        $errors['cpw'] = $errors['pw'];
        $db->execSql($sqlCheckEmail, array($info['email']));
        $errors['email'] = ($db->getFirstResult() !== 0) ? "L'email existe déjà!" : 0;
        foreach ($errors as $err) {
			if ($err !== 0) {
				$got_err = 1;
				break;
			}
		}
		if ($got_err)
			return json_encode($errors);
		else
			return 0;
    }

    public function compareTwoPw($pw, $cpw) {
        if ($pw !== $cpw)
            return "Les deux mot de passes sont différents";
        else
            return (0);
    }

    public function checkSignInValidator($info) {
        $errors = [];
		$got_err = 0;

		$errors['login'] = strlen($info['login']) == 0 ? "Le login ne doit pas être vide!" : 0;
		$errors['pw'] = strlen($info['pw']) == 0 ? "Le mot de passe ne doit pas être vide!" : 0;
		foreach ($errors as $err) {
			if ($err !== 0) {
				$got_err = 1;
				break;
			}
		}
		if ($got_err)
			return json_encode($errors);
		else
			return 0;
    }

    public function checkSignInDatabase($info, $db) {
        $errors = [];
        $got_err = 0;
        $sqlGetPassword = "SELECT password FROM `users` WHERE `login`=?";

        $db->execSql($sqlGetPassword, array($info['login']));
        $pw = $db->getFirstResult()['password'];
        $errors['pw'] = $pw === hash("whirlpool", $info['pw']) ? 0 : "Le login ou le mot de passe est incorrect!";
        $errors['login'] = $errors['pw'];
        foreach ($errors as $err) {
			if ($err !== 0) {
				$got_err = 1;
				break;
			}
		}
		if ($got_err)
			return json_encode($errors);
		else
			return 0;
    }
}
