<?php

class CheckMatch {

    private $db;
    private $uid;
    private $ask;
    private $error = 0;
    private $valid_user_id;
    public $order;
    public $filtre;

    public function init_val($db, $uid, $ask, $order, $filtre) {
        $this->db = $db;
        $this->uid = $uid;
        $this->ask = $ask;
        $this->filtre = $filtre;
        $this->filtre->set_db($db);
        $this->filtre->set_uid($uid);
        $this->order = $order;
        $this->order->set_db($db);
        $this->order->set_uid($uid);
    }

    public function set_valid_user_id($valid_user_id) {
        $this->valid_user_id = $valid_user_id;
    }

    public function check_match() {
        $this->check_ask();
        if (!$this->error) {
            $this->order->set_valid_user_id($this->valid_user_id);
            $this->valid_user_id = $this->order->check_order($this->ask);
            if ($this->valid_user_id === 1)
                $this->error = 1;
        }
        return ($this->error) ? "ErrorPost" : $this->getInfo($this->valid_user_id, $this->ask['offset']);
    }

    public function getInfo($valid_user_id, $offset) {
        $offset = $offset * 6;
        $sqlGetUserInfo = "SELECT `id`, `nom`, `prenom` FROM `users` WHERE `id`=?";
        $sqlGetUserProfile = "SELECT `*` FROM `profil` WHERE `uid`=?";
        $sqlGetProfilPhoto = "SELECT `uid`, `img_path` FROM `img` WHERE `uid`=? AND `is_profil`='Y'";
        $sqlGetBlakList = "SELECT * FROM `blacklist` WHERE `uid`=? AND `dst_id`=?";
        $info = array();
        $info['user'] = array();
        if ($offset >= 0)
            $valid_user_id = array_slice($valid_user_id, $offset);
        $i = 0;
        foreach ($valid_user_id as $uid) {
            if ($i == 6 && $offset >= 0)
                break;
            $this->db->execSql($sqlGetUserInfo, array($uid));
            $src = $this->db->getFirstResult();
            $this->db->execSql($sqlGetBlakList, array($this->uid, $uid));
            $black = $this->db->getFirstResult();
            if ($src && !$black) {
                $info['user'][] = $src;
                $i++;
            }
        }
        foreach ($info['user'] as $user) {
            $this->db->execSql($sqlGetUserProfile, array($user['id']));
            $info['profil'][] = $this->db->getFirstResult();
            $this->db->execSql($sqlGetProfilPhoto, array($user['id']));
            $info['imgProfil'][] = $this->db->getFirstResult();
        }
        return json_encode($info);
    }

    private function check_ask() {
        $valid_user_id = array();
        if (!isset($this->ask['orient_sex']) || !isset($this->ask['age_min']) ||
            !isset($this->ask['age_max']) || !isset($this->ask['view']) ||
            !isset($this->ask['like']) || !isset($this->ask['localisation']) ||
            !isset($this->ask['tags']) || !isset($this->ask['order']) ||
            !isset($this->ask['offset']))
            $this->error = 1;
        if (!$this->error) {
            $valid_user_id = $this->get_ask_sex($this->ask['orient_sex']);
            if (!empty($this->ask['age_min']) && !empty($this->ask['age_max']))
                $valid_user_id = $this->filtre->get_ask_user(1, array("min" => $this->ask['age_min'], "max" => $this->ask['age_max']), $valid_user_id);
            if (!empty($this->ask['view']))
                $valid_user_id = $this->filtre->get_ask_user(2, $this->ask['view'], $valid_user_id);
            if (!empty($this->ask['like']))
                $valid_user_id = $this->filtre->get_ask_user(3, $this->ask['like'], $valid_user_id);
            if (!empty($this->ask['tags']))
                $valid_user_id = $this->filtre->get_ask_tags($this->ask['tags'], $valid_user_id);
            if (!empty($this->ask['localisation']))
                $valid_user_id = $this->filtre->get_ask_loca($this->ask['localisation'], $valid_user_id);
            $this->valid_user_id = $valid_user_id;
        }
    }

    private function get_ask_sex($value) {
        $sqlGetAllUser = "SELECT `uid` FROM `profil` WHERE `uid`!=?";
        $sqlGetAllUserSameSex = "SELECT `uid` FROM `profil` WHERE `genre`=? AND `uid`!=?";
        if ($value) {
            $sql = $sqlGetAllUserSameSex;
            $values = array($value, $this->uid);
        }
        else {
            $sql = $sqlGetAllUser;
            $values = array($this->uid);
        }
        $this->db->execSql($sql, $values);
        $uids = $this->db->getAllResult();
        $valid_user_id = array();
        foreach ($uids as $uid) {
            $valid_user_id[] = $uid['uid'];
        }
        return $valid_user_id;
    }
}
