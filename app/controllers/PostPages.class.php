<?php

class PostPages extends Controller {

    public function postSignup($request, $response) {
        $info = $request->getParams();
        if ($info['submit'] !== "signup" || $this->checkSession()) {
            $this->flash('error', "Une erreur s'est passée!");
            echo "Error";
            die();
        }
        $errors = $this->checksign->checkSignUpValidator($info, $this->validator);
        if ($errors) {
            echo $errors;
        } else {
            $errors = $this->checksign->checkSignUpDatabase($info, $this->db);
            if ($errors) {
                echo $errors;
            } else {
                $sqlGetId = "SELECT `id` FROM `users` WHERE `login`=?";
                $sqlAddNewUserToDB = "INSERT INTO `users` (`nom`, `prenom`, `login`, `password`, `email`, `cle`)
                                    VALUES (?, ?, ?, ?, ?, ?)";
                $sqlAddNewUserToProfil = "INSERT INTO `profil` (`uid`, `age`, `birthday`, `bio`)
                                        VALUES (?, ?, ?, ?)";
                $cle = hash("whirlpool", microtime(TRUE) * 100000);
                $pw = hash("whirlpool", $info['pw']);
                $addToDB = array($info['nom'], $info['prenom'], $info['login'], $pw, $info['email'], $cle);
                $this->db->execSql($sqlAddNewUserToDB, $addToDB);
                $this->db->execSql($sqlGetId, array($info['login']));
                $uid = $this->db->getFirstResult()['id'];
                $age = $this->validator->calcAge($info['birthday']);
                $addToProfil = array($uid, $age, $info['birthday'], "Cette personne n'a rien écrit!");
                $this->db->execSql($sqlAddNewUserToProfil, $addToProfil);
                $this->email->active_account($info['email'], $info['nom'], $info['prenom'], $uid, $cle);
                echo "OK";
            }
        }
    }

    public function postSignin($request, $response) {
        $info = $request->getParams();
        if (!isset($info['action']) || empty($info['action']))
            $this->isSignin($request, $response, $info);
        else {
            $sqlGetUserInfo = "SELECT `*` FROM `users` WHERE `email`=?";
            $this->db->execSql($sqlGetUserInfo, array($info['email']));
            $user = $this->db->getFirstResult();
            if (!$user || !isset($info['email']) || empty($info['email']))
                echo "Erreur! Vérifiez bien votre email!";
            else if ($user['checked'] == 'N')
                echo "Veuillez activer votre compte!";
            else {
                $this->email->forgot_pass($user['email'], $user['nom'], $user['prenom'],
                                        $user['id'], $user['cle']);
                echo "OK";
            }
        }
    }

    private function isSignin($request, $response, $info) {
        if (!$this->checkSession()) {
            $errors = $this->checksign->checkSignInValidator($info);
            if ($errors) {
                echo $errors;
            } else {
                $errors = $this->checksign->checkSignInDatabase($info, $this->db);
                if ($errors) {
                    echo $errors;
                } else {
                    $sqlGetUserInfo = "SELECT `*` FROM `users` WHERE `login`=?";
                    $sqlSetOnline = "UPDATE `users` SET `online`='Y' WHERE `login`=?";
                    $sqlGetNotifNumber = "SELECT COUNT(*) AS `numNotif` FROM `notif`
                                            WHERE `is_read`='N' AND `uid`=?";
                    $this->db->execSql($sqlGetUserInfo, array($info['login']));
                    $ret = $this->db->getFirstResult();
                    if ($ret['checked'] == 'Y') {
                        $this->db->execSql($sqlSetOnline, array($info['login']));
                        $this->db->execSql($sqlGetNotifNumber, array($ret['id']));
                        $_SESSION['uid'] = $ret['id'];
                        $_SESSION['login'] = $info['login'];
                        $_SESSION['nom'] = ucfirst($ret['nom']);
                        $_SESSION['prenom'] = ucfirst($ret['prenom']);
                        $_SESSION['email'] = $ret['email'];
                        $_SESSION['numNotif'] = $this->db->getFirstResult()['numNotif'];
                        $this->flash('success', "Bienvenue " . $_SESSION['prenom'] . "!");
                        echo "OK";
                    } else {
                        echo "NOACTIVE";
                    }
                }
            }
        } else {
            $this->flash('error', "Une erreur s'est passée!");
            echo "Error";
        }
    }

    public function postProfil($request, $response) {
        $info = $request->getParams();
        $result = $this->profil->check_post($info);
        if (!$this->checkSession() || $result == 1) {
            $this->flash('error', "Une erreur s'est passée!");
            echo "Error";
        }
        else if ($result == 2)
            echo "NOVALIDE";
        else {
            $info['files'] = $_FILES;
            $this->profil->init_val($this->db, $this->picture,
                                    $this->user_picture_dir, $this->validator);
            echo $this->profil->check_action($info, $_SESSION['uid']);
        }
    }

    public function postUprofil($request, $response) {
        $info = $request->getParams();
        if (!$this->checkSession() || $this->uprofil->check_post($info)) {
            $this->flash('error', "Une erreur s'est passée!");
            echo "Error";
        }
        else {
            $this->uprofil->set_db($this->db, $this->email);
            echo $this->uprofil->check_action($info, $_SESSION['uid']);
        }
    }

    public function postMatch($request, $response) {
        $info = $request->getParams();
        if (!$this->checkSession() || !isset($info['action']) || empty($info['action'])) {
            $this->flash('error', "Une erreur s'est passée!");
            echo "Error";
        }
        else if (!$this->check_enough_info($_SESSION['uid']))
            echo "ENOUGH";
        else if ($info['action'] == "tout"){
            $this->match->init_val($this->db, $_SESSION['uid'], $info, $this->order, $this->filtre);
            echo $this->match->check_match();
        }
        else if ($info['action'] == "liste") {
            if (!isset($_SESSION['sug']) || empty($_SESSION['sug']))
                $_SESSION['sug'] = array();
            $this->sug->suggestion_list($this->db, $_SESSION['uid'], $this->match, $info, $this->order, $this->filtre, $_SESSION['sug']);
            if (!isset($_SESSION['sug']) || empty($_SESSION['sug']))
                $_SESSION['sug'] = $this->sug->get_sug_list();
            echo $this->sug->get_result();
        }
        else if ($info['action'] == "get_lat_long") {
            $sqlUpdateLatLong = "UPDATE `adresse` SET `latitude`=?, `longitude`=? WHERE `uid`=?";
            $sqlGetAdresse = "SELECT * FROM `adresse` WHERE `uid`=?";

            $this->db->execSql($sqlGetAdresse, array($_SESSION['uid']));
            if ($this->db->getFirstResult())
                $this->db->execSql($sqlUpdateLatLong, array($info['latitude'], $info['longitude'], $_SESSION['uid']));
        }
        else {
            $this->flash('error', "Une erreur s'est passée!");
            echo "Error";
        }
    }

    private function check_enough_info($uid) {
        $sqlGetAdresse = "SELECT * FROM `adresse` WHERE `uid`=?";
        $sqlGetUserTag = "SELECT `tag` FROM `tags` WHERE `uid`=?";

        $this->db->execSql($sqlGetAdresse, array($uid));
        if (!$this->db->getFirstResult()) return (0);
        $this->db->execSql($sqlGetUserTag, array($uid));
        if (!$this->db->getFirstResult()) return (0);
        return (1);
    }

    public function postForgot($request, $response) {
        $info = $request->getParams();
        if (!isset($info['uid']) || !isset($info['cle']) || !isset($info['pw']) ||
            !isset($info['cpw']) || !isset($info['submit']) || empty($info['uid']) ||
            empty($info['cle']) || empty($info['submit']) || !ctype_digit($info['uid']))
            echo "Error";
        else {
            $sqlGetCle = "SELECT `cle` FROM `users` WHERE `id`=?";
            $sqlNewPassword = "UPDATE `users` SET `password`=? WHERE `id`=?";
            $errors = [];

            if ($info['pw'] !== $info['cpw']) {
                $errors['pw'] = "Les deux mot de passe sont différents!";
                $errors['cpw'] = "Les deux mot de passe sont différents!";
            }
            else {
                $errors['pw'] = $this->validator->checkPassword($info['pw']);
                $errors['cpw'] = $this->validator->checkPassword($info['cpw']);
            }
            if (!$errors['pw'] && !$errors['cpw']) {
                $this->db->execSql($sqlGetCle, array($info['uid']));
                $cle = $this->db->getFirstResult();
                if ($cle && ($cle['cle'] !== $info['cle'])) {
                    $errors['cle'] = "Erreur! Veuillez ouvrir la page avec le lien qui se trouve dans votre mail ou demander un nouveau mail de reinitialisation!";
                    echo json_encode($errors);
                }
                else {
                    $this->db->setCle($info['uid']);
                    $this->db->execSql($sqlNewPassword, array(hash("whirlpool", $info['pw']), $info['uid']));
                    echo json_encode($errors);
                }
            }
            else {
                echo json_encode($errors);
            }
        }
    }

    public function postContact($request, $response) {
        $info = $request->getParams();
        if (!isset($info['name']) || !isset($info['email']) || !isset($info['content']) ||
            !isset($info['action']) || $info['action'] != 'advice')
            echo "Error";
        else {
            $errors = array();
            $get_error = 0;
            $errors['name'] = 0;
            $errors['content'] = 0;

            if (strlen($info['name']) == 0) $errors['name'] = "Le nom ne doit pas etre vide!";
            if (strlen($info['content']) == 0) $errors['content'] = "Le message ne doit pas etre vide!";
            $errors['email'] = $this->validator->checkEmail($info['email']);
            if ($errors['name'] || $errors['content'] || $errors['email']) $get_error = 1;

            if ($get_error) {
                echo json_encode($errors);
            }
            else {
                $this->email->send_advice($info['content'], $info['name'], $info['email']);
                echo "OK";
            }
        }
    }

}
