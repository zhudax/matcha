<?php

class PostProfil {
    private $error = 0;
    private $db;
    private $picture;
    private $dir;
    private $validator;

    public function check_post($info) {
        if (!isset($info['action']) || empty($info['action']))
            $this->error = 1;
        else if ($info['action'] != "change_info" && $info['action'] != "cadresse" &&
                $info['action'] != "photo" && $info['action'] != "hidden") {
            if (!isset($info['value']) && (!isset($info['pw1']) || !isset($info['pw2'])) &&
                (!isset($info['path']) || empty($info['path'])))
                $this->error = 1;
        }
        else if ($info['action'] == "cadresse") {
            if (!$this->check_adresse_post($info))
                return 2;
        }
        return ($this->error) ? 1 : 0;
    }

    private function check_adresse_post($post) {
        if (!isset($post['num']) || !isset($post['rue']) ||
            !isset($post['ville']) || !isset($post['region']) ||
            !isset($post['cp']) || empty($post['cp']) ||
            !isset($post['pays']) || empty($post['pays']) ||
            !isset($post['latitude']) || empty($post['latitude']) ||
            !isset($post['longitude']) || empty($post['longitude']))
            return (0);
        return (1);
    }

    public function init_val($db, $picture, $dir, $validator) {
        $this->db = $db;
        $this->picture = $picture;
        $this->dir = $dir;
        $this->validator = $validator;
    }

    public function check_action($info, $uid) {
        if ($info['action'] == "hidden")
            return $this->action_hidden($info, $uid);
        else if ($info['action'] == "change_info")
            return $this->action_change_info($info, $uid);
        else if ($info['action'] == "cadresse")
            return $this->action_change_adresse($info, $uid);
        else if ($info['action'] == "cnom")
            return $this->action_change_nom($info, $uid);
        else if ($info['action'] == "cprenom")
            return $this->action_change_prenom($info, $uid);
        else if ($info['action'] == "cemail")
            return $this->action_change_email($info, $uid);
        else if ($info['action'] == "cpw")
            return $this->action_change_pw($info, $uid);
        else if ($info['action'] == "add_tag")
            return $this->action_add_tag($info, $uid);
        else if ($info['action'] == "del_tag")
            return $this->action_del_tag($info, $uid);
        else if ($info['action'] == "del_black_list")
            return $this->action_del_black_list($info, $uid);
        else if ($info['action'] == "profil")
            return $this->action_del_profil($info, $uid);
        else if ($info['action'] == "img")
            return $this->action_del_img($info, $uid);
        else if ($info['action'] == 'photo')
            return $this->action_get_photo($uid);
        else
            return "Error";
    }

    private function action_hidden($info, $uid) {
        $sqlHiddenYes = "UPDATE `adresse` SET `hidden`='Y' WHERE `uid`=?";
        $sqlHiddenNo = "UPDATE `adresse` SET `hidden`='N' WHERE `uid`=?";
        $sqlGetAdresse = "SELECT `hidden` FROM `adresse` WHERE `uid`=?";

        $this->db->execSql($sqlGetAdresse, array($uid));
        $ret = $this->db->getFirstResult();
        if (!$ret) return "NO";
        if ($info['value'] == "N") {
            if ($ret['hidden'] != "N") {
                $this->db->execSql($sqlHiddenNo, array($uid));
                return "Votre adresse est maintenant publique!";
            }
        }
        else if ($info['value'] == "Y") {
            if ($ret['hidden'] != "Y") {
                $this->db->execSql($sqlHiddenYes, array($uid));
                return "Votre adresse est maintenant privé!";
            }
        }
        return "OK";
    }

    private function action_change_info($info, $uid) {
        $sqlUpdateProfil = "UPDATE `profil` SET `genre`=?, `orient_sex`=?, `bio`=? WHERE `uid`=?";
        $sqlGetUserProfile = "SELECT `*` FROM `profil` WHERE `uid`=?";
        if (!empty($info['files'])) {
            $dir = $this->dir;
            $this->picture->createDir($dir, $uid);
            $i = (isset($info['files']['photo_profil'])) ? 0 : 1;
            foreach ($info['files'] as $file) {
                $this->picture->getExtension($file['name']);
                $this->error = ($i == 0) ? $this->picture->addProfil($file, $dir, $uid, $this->db) :
                            $this->picture->addPhoto($file, $dir, $uid, $this->db);
                if ($this->error)
                    break;
                $i++;
            }
        }
        if (!$this->error) {
            $this->db->execSql($sqlGetUserProfile, array($uid));
            $old_val = $this->db->getFirstResult();
            $genre = (isset($info['genre']) && !empty($info['genre'])) ? $info['genre'] : $old_val['genre'];
            $osex = (isset($info['orient_sex']) && !empty($info['orient_sex'])) ? $info['orient_sex'] : $old_val['orient_sex'];
            $bio = (isset($info['bio']) && !empty($info['bio'])) ? $info['bio'] : $old_val['bio'];
            $values = array($genre, $osex, $bio, $uid);
            $this->db->execSql($sqlUpdateProfil, $values);
            $_SESSION['sug'] = array();
        }
        return ($this->error) ? "Error" : "OK";
    }

    private function action_change_adresse($info, $uid) {
        $sqlDeleteAdresse = "DELETE FROM `adresse` WHERE `uid`=?";
        $sqlAddAdresse = "INSERT INTO `adresse` (`uid`, `pays`, `region`, `ville`, `arrond`,
                                                `rue`, `num`, `latitude`, `longitude`)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $adresseValues = array($uid, $info['pays'], $info['region'], $info['ville'],
                                $info['cp'], $info['rue'], $info['num'], $info['latitude'], $info['longitude']);
        $this->db->execSql($sqlDeleteAdresse, array($uid));
        $this->db->execSql($sqlAddAdresse, $adresseValues);
        return "OK";
    }

    private function action_change_nom($info, $uid) {
        $sqlNewNom = "UPDATE `users` SET `nom`=? WHERE `id`=?";
        $error = $this->validator->checkName($info['value']);
        if ($error)
            return $error;
        else {
            $nom = ucfirst($info['value']);
            $this->db->execSql($sqlNewNom, array($nom, $uid));
            $_SESSION['nom'] = $nom;
            return "OK";
        }
    }

    private function action_change_prenom($info, $uid) {
        $sqlNewPrenom = "UPDATE `users` SET `prenom`=? WHERE `id`=?";
        $error = $this->validator->checkName($info['value']);
        if ($error)
            return $error;
        else {
            $prenom = ucfirst($info['value']);
            $this->db->execSql($sqlNewPrenom, array($prenom, $uid));
            $_SESSION['prenom'] = $prenom;
            return "OK";
        }
    }

    private function action_change_email($info, $uid) {
        $sqlNewEmail = "UPDATE `users` SET `email`=? WHERE `id`=?";
        $sqlCheckEmail = "SELECT `email` FROM `users` WHERE `email`=?";
        $error = $this->validator->checkEmail($info['value']);
        if ($error)
            return $error;
        else {
            $this->db->execSql($sqlCheckEmail, array($info['value']));
            $error = ($this->db->getFirstResult() !== 0) ? "L'email existe déjà!" : 0;
            if ($error)
                return $error;
            else {
                $this->db->execSql($sqlNewEmail, array($info['value'], $uid));
                $_SESSION['email'] = $info['value'];
                return "OK";
            }
        }
    }

    private function action_change_pw($info, $uid) {
        $sqlNewPassword = "UPDATE `users` SET `password`=? WHERE `id`=?";
        $error1 = $this->validator->checkPassword($_POST['pw1']);
        $error2 = $this->validator->checkPassword($_POST['pw2']);
        if ($error1)
            return $error1;
        else if ($error2)
            return $error2;
        else if ($info['pw1'] !== $info['pw2'])
            return "Les deux mot de passes sont différents";
        else {
            $this->db->execSql($sqlNewPassword, array(hash("whirlpool", $info['pw1']), $uid));
            return "OK";
        }
    }

    private function action_add_tag($info, $uid) {
        $sqlAddTag = "INSERT INTO `tags` (`uid`, `tag`) VALUES (?, ?)";
        $sqlCheckTag = "SELECT `tag` FROM `tags` WHERE `uid`=? AND `tag`=?";
        $error = $this->validator->checkTag($info['value']);
        if ($error)
            return $error;
        else {
            $tag = "#" . $info['value'];
            $this->db->execSql($sqlCheckTag, array($uid, $tag));
            if ($this->db->getFirstResult())
                return "Le tag existe déjà!";
            else {
                $this->db->execSql($sqlAddTag, array($uid, $tag));
                return "OK";
            }
        }
    }

    private function action_del_tag($info, $uid) {
        $sqlDeleteTag = "DELETE FROM `tags` WHERE `uid`=? AND `tag`=?";
        $this->db->execSql($sqlDeleteTag, array($uid, $info['value']));
        return "OK";
    }

    private function action_del_black_list($info, $uid) {
        $sqlDeleteBlackList = "DELETE FROM `blacklist` WHERE `uid`=? AND `dst_id`=?";
        $this->db->execSql($sqlDeleteBlackList, array($uid, $info['value']));
        return "OK";
    }

    private function action_del_profil($info, $uid) {
        $this->picture->delPicture($info['path'], $uid, $this->db, 1);
        return "OK";
    }

    private function action_del_img($info, $uid) {
        $this->picture->delPicture($info['path'], $uid, $this->db, 2);
        return "OK";
    }

    private function action_get_photo($uid) {
        $photos = array();
        $ret = $this->picture->getProfil($uid, $this->db);
        $photos['profil'] = ($ret) ? $ret : array();
        $photos['photos'] = array();
        $tmp = $this->picture->getPhotos($uid, $this->db);
        if (!$tmp)
            $tmp = array();
        $i = 0;
        foreach ($tmp as $photo) {
            $photos['photos'][$i] = $photo['img_path'];
            $i++;
        }
        return json_encode($photos);
    }
}
