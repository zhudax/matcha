<?php

class Picture {
    private $extension;

    public function createDir($dir, $uid) {
        if (!file_exists($dir))
            mkdir($dir);
        if (!file_exists($dir . "/user_" . $uid))
            mkdir($dir . "/user_" . $uid);
    }

    public function getExtension($filename) {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        if (!$extension || $extension == "jpeg")
            $extension = "jpg";
        $this->extension = $extension;
    }

    public function addProfil($file, $dir, $uid, $db) {
        $sqlAddProfilPhoto = "INSERT INTO `img` (`uid`, `img_path`, `is_profil`) VALUES (?, ?, 'Y')";
        $path = "app/public/user_picture";
        $savename = "/user_" . $uid . "/" . "profil." . $this->extension;
        $filename = $dir . $savename;
        if (!(move_uploaded_file($file["tmp_name"], $filename)))
            return (1);
        $this->delProfil($uid, $db);
        $db->execSql($sqlAddProfilPhoto, array($uid, $path . $savename));
        return (0);
    }

    public function delProfil($uid, $db) {
        $sqlDeleteProfilPhoto = "DELETE FROM `img` WHERE `uid`=? AND `is_profil`='Y'";
        $db->execSql($sqlDeleteProfilPhoto, array($uid));
    }

    public function addPhoto($file, $dir, $uid, $db) {
        $sqlAddPhoto = "INSERT INTO `img` (`uid`, `img_path`) VALUES (?, ?)";
        $path = "app/public/user_picture";
        $savename = "/user_" . $uid . "/" . "photo" . uniqid() . "." . $this->extension;
        $filename = $dir . $savename;
        if (!(move_uploaded_file($file["tmp_name"], $filename)))
            return (1);
        $db->execSql($sqlAddPhoto, array($uid, $path . $savename));
        return (0);
    }

    public function delPicture($img_path, $uid, $db, $action) {
        $sqlDeletePhoto = "DELETE FROM `img` WHERE `img_path`=? AND `uid`=? AND `is_profil`='N'";
        $sqlDeleteProfilPhoto = "DELETE FROM `img` WHERE `uid`=? AND `is_profil`='Y'";
        $img_path = "app/public/user_picture/user_" . $uid . "/" . basename($img_path);
        if ($action == 1) {
            $db->execSql($sqlDeleteProfilPhoto, array($uid));
        }
        else if ($action == 2) {
            $db->execSql($sqlDeletePhoto, array($img_path, $uid));
        }
        $filename = $img_path;
        if (file_exists($filename))
            unlink($filename);
    }

    public function getProfil($uid, $db) {
        $sqlGetProfilPhoto = "SELECT `img_path` FROM `img` WHERE `uid`=? AND `is_profil`='Y'";
        $db->execSql($sqlGetProfilPhoto, array($uid));
        $ret = $db->getFirstResult();
        return ($ret) ? $ret['img_path'] : 0;
    }

    public function getPhotos($uid, $db) {
        $sqlGetPhoto = "SELECT `img_path` FROM `img` WHERE `uid`=? AND `is_profil`='N'";
        $db->execSql($sqlGetPhoto, array($uid));
        $ret = $db->getAllResult();
        return ($ret) ? $ret : 0;
    }
}
