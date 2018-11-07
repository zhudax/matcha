<?php

class GetPages extends Controller {
	public function getHome($request, $response) {
		$this->render($response, 'pages/home.html', array('session' => $_SESSION));
	}

	public function getContact($request, $response) {
		return $this->render($response, 'pages/contact.html', array('session' => $_SESSION));
	}

	public function getSignin($request, $response) {
        if (!$this->checkSession()) {
            return $this->render($response, 'pages/signin.html', array('session' => $_SESSION));
        }
        else {
            $this->flash('error', "Vous êtes déjà connectés!");
            return $this->redirect($response, "root");
        }
	}

	public function getAbout($request, $response) {
		return $this->render($response, 'pages/about.html', array('session' => $_SESSION));
	}

	public function getSignup($request, $response) {
        if (!$this->checkSession()) {
            return $this->render($response, 'pages/signup.html', array('session' => $_SESSION));
        }
        else {
            $this->flash('error', "Vous êtes déjà connectés!");
            return $this->redirect($response, "root");
        }
	}

	public function getProfil($request, $response) {
        if ($this->checkSession($request, $response)) {
            $info = $this->getProfilInfo($_SESSION['uid']);
    		return $this->render($response, 'pages/profil.html', array('session' => $_SESSION,
    							'info' => $info));
        }
        else {
            $this->flash('error', "Vous devez vous connecter!");
            return $this->redirect($response, "root");
        }
	}

    private function getProfilInfo($uid) {
        $info = array();
        $sqlGetAdresse = "SELECT * FROM `adresse` WHERE `uid`=?";
		$sqlGetUserTag = "SELECT `tag` FROM `tags` WHERE `uid`=? ORDER BY `id` DESC";
		$sqlGetBlakList = "SELECT `dst_id` FROM `blacklist` WHERE `uid`=?";
		$sqlGetUserInfo = "SELECT `id`, `nom`, `prenom` FROM `users` WHERE `id`=?";
        $sqlGetUserProfile = "SELECT `*` FROM `profil` WHERE `uid`=?";
        $sqlGetProfilPhoto = "SELECT `img_path` FROM `img` WHERE `uid`=? AND `is_profil`='Y'";
		$sqlGetPhoto = "SELECT `img_path` FROM `img` WHERE `uid`=? AND `is_profil`='N'";
		$sqlGetAllTag = "SELECT `tag` FROM `tags` GROUP BY `tag`";

		$this->db->execSql($sqlGetAdresse, array($_SESSION['uid']));
        $ret = $this->db->getFirstResult();
		$info['adresse'] = ($ret) ? $ret : array();
		$this->db->execSql($sqlGetUserTag, array($_SESSION['uid']));
        $ret = $this->db->getAllResult();
		$info['tags'] = ($ret) ? $ret : array();
		$this->db->execSql($sqlGetAllTag);
		$info['all_tags'] = $this->db->getAllResult();
		$this->db->execSql($sqlGetBlakList, array($_SESSION['uid']));
        $ret = $this->db->getAllResult();
		$info['users'] = ($ret) ? $ret : array();
        $this->db->execSql($sqlGetUserProfile, array($_SESSION['uid']));
        $ret = $this->db->getFirstResult();
        $info['profil'] = ($ret) ? $ret : array();
		$this->db->execSql($sqlGetProfilPhoto, array($_SESSION['uid']));
        $ret = $this->db->getFirstResult();
		$info['profilPhoto'] = ($ret) ? $ret : array();
		$this->db->execSql($sqlGetPhoto, array($_SESSION['uid']));
        $ret = $this->db->getAllResult();
		$info['photo'] = ($ret) ? $ret : array();
        $info['blacklist'] = array();
		foreach ($info['users'] as $user) {
			$this->db->execSql($sqlGetUserInfo, array($user['dst_id']));
			$info['blacklist'][] = $this->db->getFirstResult();
		}
        if (!$info['blacklist'])
            $info['blacklist'] = array();
        return $info;
    }

	public function getUprofil($request, $response) {
        $get = $request->getParams();
        if ($this->checkSession()) {
            if (isset($get['uid']) && (count($get) != 1 || empty($get['uid']) ||
                !ctype_digit($get['uid'])) || (count($get) == 1 && !isset($get['uid']))) {
                $this->flash('error', "Une erreur s'est passée!");
                return $this->redirect($response, "root");
            }
    		$info = array();
            $info['error'] = 0;
            $uid = (isset($get['uid'])) ? $uid = $get['uid'] : $uid = $_SESSION['uid'];
            $info = $this->getUprofilInfo($uid, $info);
            if ($info['error']) {
                $this->flash('error', "Une erreur s'est passée!");
                return $this->redirect($response, "root");
            }
    		return $this->render($response, 'pages/uprofil.html', array('session' => $_SESSION, 'info' => $info));
        } else {
            $this->flash('error', "Vous devez vous connecter!");
            return $this->redirect($response, "root");
        }
	}

    private function getUprofilInfo($uid, $info) {
        $sqlGetUserInfo = "SELECT `*` FROM `users` WHERE `id`=?";
		$sqlGetUserProfile = "SELECT `*` FROM `profil` WHERE `uid`=?";
		$sqlGetProfilPhoto = "SELECT `img_path` FROM `img` WHERE `uid`=? AND `is_profil`='Y'";
		$sqlGetPhoto = "SELECT `img_path` FROM `img` WHERE `uid`=? AND `is_profil`='N'";
		$sqlGetAdresse = "SELECT * FROM `adresse` WHERE `uid`=?";
		$sqlGetUserTag = "SELECT `tag` FROM `tags` WHERE `uid`=? ORDER BY `id` DESC";
		$sqlCheckLikes = "SELECT `id` FROM `likes` WHERE `src_id`=? AND `dst_id`=?";
        $sqlDeleteView = "DELETE FROM `view` WHERE `src_id`=? AND `dst_id`=?";
        $sqlAddView = "INSERT INTO `view` (`src_id`, `dst_id`) VALUES (?, ?)";
        $sqlCheckView = "SELECT `id` FROM `view` WHERE `src_id`=? AND `dst_id`=?";
        $sqlNewView = "UPDATE `profil` SET `nb_view`=? WHERE `uid`=?";
        $sqlGetBlakList = "SELECT * FROM `blacklist` WHERE `uid`=? AND `dst_id`=?";
        $sqlAddViewNotif = "INSERT INTO `notif` (`uid`, `src_id`, `view`) VALUES (?, ?, 'Y')";

        $this->db->execSql($sqlGetUserProfile, array($uid));
        $info['profil_info'] = $this->db->getFirstResult();
        if (!$info['profil_info']) {
            $info['error'] = 1;
            return ;
        }
        $this->db->execSql($sqlGetUserInfo, array($uid));
        $info['user'] = $this->db->getFirstResult();
        $this->db->execSql($sqlGetProfilPhoto, array($uid));
        $info['profil_photo'] = $this->db->getFirstResult()['img_path'];
        $this->db->execSql($sqlGetPhoto, array($uid));
        $info['photos'] = $this->db->getAllResult();
        $this->db->execSql($sqlGetAdresse, array($uid));
        $info['adresse'] = $this->db->getFirstResult();
        $this->db->execSql($sqlGetUserTag, array($uid));
        $info['tags'] = $this->db->getAllResult();
        if ($uid != $_SESSION['uid']) {
            $this->db->execSql($sqlGetBlakList, array($uid, $_SESSION['uid']));
            if (!$this->db->getFirstResult()) {
                $this->db->execSql($sqlAddViewNotif, array($uid, $_SESSION['uid']));
            }
            $this->db->execSql($sqlCheckView, array($_SESSION['uid'], $uid));
            $ret = $this->db->getFirstResult();
            $this->db->execSql($sqlDeleteView, array($_SESSION['uid'], $uid));
            if (!$ret) {
                $info['profil_info']['nb_view']++;
                $this->db->execSql($sqlNewView, array($info['profil_info']['nb_view'], $uid));
            }
            $this->db->execSql($sqlAddView, array($_SESSION['uid'], $uid));
            $this->db->execSql($sqlCheckLikes, array($_SESSION['uid'], $uid));
            $like = $this->db->getFirstResult();
            $this->db->execSql($sqlCheckLikes, array($uid, $_SESSION['uid']));
            $like_retour = $this->db->getFirstResult();
            $info['like'] = ($like) ? 1 : 0;
            $info['like_retour'] = ($like_retour) ? 1 : 0;
            $info['match'] = ($like && $like_retour) ? 1 : 0;
        }
        return ($info);
    }

	public function getMatch($request, $response) {
        if ($this->checkSession()) {
    		$sqlGetAllTag = "SELECT `tag` FROM `tags` GROUP BY `tag`";
    		$this->db->execSql($sqlGetAllTag);
    		$tags = $this->db->getAllResult();
    		return $this->render($response, 'pages/match.html', array('session' => $_SESSION, 'tags' => $tags));
        }
        else {
            $this->flash('error', "Vous devez vous connecter!");
            return $this->redirect($response, "root");
        }
	}

	public function getLogout($request, $response) {
		if (isset($_SESSION)) {
			$sqlSetOffline = "UPDATE `users` SET `online`='N' WHERE `login`=?";
			$sqlNewLastCo = "UPDATE `users` SET `last_log`=CURRENT_TIMESTAMP WHERE `login`=?";
			$this->db->execSql($sqlSetOffline, array($_SESSION['login']));
			$this->db->execSql($sqlNewLastCo, array($_SESSION['login']));
			$_SESSION = array();
			session_destroy();
		}
		return $this->redirect($response, "root");
	}

    public function getForgot($request, $response) {
        $get = $request->getParams();
        if (!isset($get['uid']) || !isset($get['cle']) || empty($get['uid']) ||
            empty($get['cle']) || count($get) != 2 || !ctype_digit($get['uid'])) {
            $this->flash('error', "Erreur! Veuillez ouvrir la page avec le lien qui se trouve dans votre mail!");
            return $this->redirect($response, "root");
        }
        return $this->render($response, 'pages/forgot_pass.html', array('info' => $get));
    }

    public function getActive($request, $response) {
        $get = $request->getParams();
        $info = array();
        $info['error'] = 0;
        $info['success'] = 0;
        if (!isset($get['uid']) || !isset($get['cle']) || empty($get['uid']) ||
            empty($get['cle']) || count($get) != 2 || !ctype_digit($get['uid'])) {
            $info['error'] = "Erreur! Veuillez ouvrir la page avec le lien qui se trouve dans votre mail!";
        } else {
            $sqlGetCle = "SELECT `cle` FROM `users` WHERE `id`=?";
            $sqlEmailChecked = "UPDATE `users` SET `checked`='Y' WHERE `id`=?";

            $this->db->execSql($sqlGetCle, array($get['uid']));
            $cle = $this->db->getFirstResult();
            if ($cle && ($cle['cle']) != $get['cle'])
                $info['error'] = "Erreur! Veuillez ouvrir la page avec le lien qui se trouve dans votre mail!";
            else {
                $this->db->setCle($get['uid']);
                $this->db->execSql($sqlEmailChecked, array($get['uid']));
                $info['success'] = "Votre compte est bien activé!";
            }
        }
        return $this->render($response, 'pages/active.html', array('info' => $info));
    }

    public function getNotifs($request, $response) {
        if ($this->checkSession()) {
    		$sqlGetAllNotif = "SELECT * FROM `notif` WHERE `uid`=? ORDER BY `id` DESC";
            $sqlGetUserInfo = "SELECT * FROM `users` WHERE `id`=?";
			$sqlGetProfilPhoto = "SELECT `img_path` FROM `img` WHERE `uid`=? AND `is_profil`='Y'";

			$users = array();
			$temp = array();
            $this->db->execSql($sqlGetAllNotif, array($_SESSION['uid']));
            $ret = $this->db->getAllResult();
            if ($ret) {
                foreach ($ret as $uid) {
					$this->db->execSql($sqlGetUserInfo, array($uid['src_id']));
                    $info = $this->db->getFirstResult();
                    $temp['nom'] = $info['prenom'] . " " . $info['nom'];
					$temp['uid'] = $info['id'];
					$temp['notif'] = $this->checkNotif($temp['nom'], $uid);
					$this->db->execSql($sqlGetProfilPhoto, array($uid['src_id']));
					$profil = $this->db->getFirstResult();
					$temp['profil'] = ($profil) ? $profil['img_path'] : 0;
					$users[] = $temp;
                }
            }
            return $this->render($response, 'pages/notifs.html', array('session' => $_SESSION, 'users' => $users));
        }
        else {
            $this->flash('error', "Vous devez vous connecter!");
            return $this->redirect($response, "root");
        }
    }

    private function checkNotif($src_user, $notif) {
        if ($notif['likes'] == 'Y') {
            return $src_user . " vous a liké!";
        }
        else if ($notif['retour_likes'] == 'Y') {
            return $src_user . " vous a liké de retour, vous etes maintenant amis!";
        }
        else if ($notif['match_unlikes'] == 'Y') {
            return $src_user . " ne vous like plus, vous n'etes plus amis! :(";
        }
        else if ($notif['view'] == 'Y') {
            return $src_user . " a regardé votre profil!";
        }
        else if ($notif['message'] == 'Y') {
            return $src_user . " vous a envoyé un message!";
        }
    }

	public function getLikes($request, $response) {
		if ($this->checkSession()) {
    		$get = $request->getParams();

			if (!isset($get['uid']) || empty($get['uid']) || count($get) != 1 || !ctype_digit($get['uid'])) {
				$this->flash('error', "Une erreur s'est passée!");
                return $this->redirect($response, "root");
			}
			$sqlGetWhoLikes = "SELECT `src_id` FROM `likes` WHERE `dst_id`=?";
			$sqlGetProfilPhoto = "SELECT `img_path` FROM `img` WHERE `uid`=? AND `is_profil`='Y'";
			$sqlGetUserInfo = "SELECT `*` FROM `users` WHERE `id`=?";
			$users = array();
			$temp = array();

			$this->db->execSql($sqlGetWhoLikes, array($get['uid']));
			$ret = $this->db->getAllResult();
            if ($ret) {
                foreach ($ret as $uid) {
                    $this->db->execSql($sqlGetUserInfo, array($uid['src_id']));
                    $info = $this->db->getFirstResult();
                    $temp['nom'] = $info['prenom'] . " " . $info['nom'];
					$temp['uid'] = $info['id'];
					$this->db->execSql($sqlGetProfilPhoto, array($uid['src_id']));
					$profil = $this->db->getFirstResult();
					$temp['profil'] = ($profil) ? $profil['img_path'] : 0;
					$users[] = $temp;
                }
            }
            return $this->render($response, 'pages/likes.html', array('session' => $_SESSION, 'users' => $users));
        }
        else {
            $this->flash('error', "Vous devez vous connecter!");
            return $this->redirect($response, "root");
        }
	}

	public function getView($request, $response) {
		if ($this->checkSession()) {
    		$get = $request->getParams();

			if (!isset($get['uid']) || empty($get['uid']) || count($get) != 1 || !ctype_digit($get['uid'])) {
				$this->flash('error', "Une erreur s'est passée!");
                return $this->redirect($response, "root");
			}
			$sqlGetWhoView = "SELECT `src_id` FROM `view` WHERE `dst_id`=?";
			$sqlGetProfilPhoto = "SELECT `img_path` FROM `img` WHERE `uid`=? AND `is_profil`='Y'";
			$sqlGetUserInfo = "SELECT `*` FROM `users` WHERE `id`=?";
			$users = array();
			$temp = array();

			$this->db->execSql($sqlGetWhoView, array($get['uid']));
			$ret = $this->db->getAllResult();
            if ($ret) {
                foreach ($ret as $uid) {
                    $this->db->execSql($sqlGetUserInfo, array($uid['src_id']));
                    $info = $this->db->getFirstResult();
                    $temp['nom'] = $info['prenom'] . " " . $info['nom'];
					$temp['uid'] = $info['id'];
					$this->db->execSql($sqlGetProfilPhoto, array($uid['src_id']));
					$profil = $this->db->getFirstResult();
					$temp['profil'] = ($profil) ? $profil['img_path'] : 0;
					$users[] = $temp;
                }
            }
            return $this->render($response, 'pages/view.html', array('session' => $_SESSION, 'users' => $users));
        }
        else {
            $this->flash('error', "Vous devez vous connecter!");
            return $this->redirect($response, "root");
        }
	}
}
