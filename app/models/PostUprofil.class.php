<?php

class PostUprofil {

    private $db;

    public function check_post($info) {
        if (!isset($info['action']) || !isset($info['dst_uid']) ||
        empty($info['action']) || empty($info['dst_uid']))
            return 1;
        return 0;
    }

    public function set_db($db, $email) {
        $this->db = $db;
        $this->email = $email;
    }

    public function check_action($info, $uid) {
        if ($info['action'] == "like")
            return $this->action_like($info['dst_uid'], $uid);
        else if ($info['action'] == "unlike")
            return $this->action_unlike($info['dst_uid'], $uid);
        else if ($info['action'] == "bloquer")
            return $this->action_bloquer($info['dst_uid'], $uid);
        else if ($info['action'] == "report")
            return $this->action_report($info['dst_uid'], $uid);
        else
            return "Error";
    }

    private function action_like($dst, $uid) {
        $sqlAddLikes = "INSERT INTO `likes` (`src_id`, `dst_id`) VALUES (?, ?)";
        $sqlGetBlakList = "SELECT * FROM `blacklist` WHERE `uid`=? AND `dst_id`=?";
        $sqlAddLikesNotif = "INSERT INTO `notif` (`uid`, `src_id`, `likes`) VALUES (?, ?, 'Y')";
        $sqlGetLikes = "SELECT `nb_like` FROM `profil` WHERE `uid`=?";
        $sqlNewLike = "UPDATE `profil` SET `nb_like`=? WHERE `uid`=?";
        $sqlCheckLikes = "SELECT `id` FROM `likes` WHERE `src_id`=? AND `dst_id`=?";
        $sqlAddRetourLikesNotif = "INSERT INTO `notif` (`uid`, `src_id`, `retour_likes`) VALUES (?, ?, 'Y')";
        $sqlAddMatch = "INSERT INTO `match` (`user1`, `user2`) VALUES (?, ?)";

        $this->db->execSql($sqlCheckLikes, array($uid, $dst));
        if (!$this->db->getFirstResult()) {
            $this->db->execSql($sqlAddLikes, array($uid, $dst));
            $this->db->execSql($sqlCheckLikes, array($dst, $uid));
            $check = $this->db->getFirstResult();
            $this->db->execSql($sqlGetBlakList, array($dst, $uid));
            if (!$this->db->getFirstResult()) {
                ($check) ? $this->db->execSql($sqlAddRetourLikesNotif, array($dst, $uid)) :
                    $this->db->execSql($sqlAddLikesNotif, array($dst, $uid));
            }
            if ($check)
                $this->db->execSql($sqlAddMatch, array($uid, $dst));
            $this->db->execSql($sqlGetLikes, array($dst));
            $nb_like = $this->db->getFirstResult()['nb_like'];
            $this->db->execSql($sqlNewLike, array($nb_like + 1, $dst));
            return $nb_like + 1;
        }
        return "-1";
    }

    private function action_unlike($dst, $uid) {
        $sqlDeleteLikes = "DELETE FROM `likes` WHERE `src_id`=? AND `dst_id`=?";
        $sqlGetBlakList = "SELECT * FROM `blacklist` WHERE `uid`=? AND `dst_id`=?";
        $sqlGetLikes = "SELECT `nb_like` FROM `profil` WHERE `uid`=?";
        $sqlAddMatchUnlikesNotif = "INSERT INTO `notif` (`uid`, `src_id`, `match_unlikes`) VALUES (?, ?, 'Y')";
        $sqlCheckFriend = "SELECT `id` FROM `match` WHERE (`user1`=? AND `user2`=?) OR (`user1`=? AND `user2`=?)";
        $sqlDeleteMatch = "DELETE FROM `match` WHERE `id`=?";
        $sqlNewLike = "UPDATE `profil` SET `nb_like`=? WHERE `uid`=?";
        $sqlCheckLikes = "SELECT `id` FROM `likes` WHERE `src_id`=? AND `dst_id`=?";

        $this->db->execSql($sqlCheckLikes, array($uid, $dst));
        if ($this->db->getFirstResult()) {
            $this->db->execSql($sqlDeleteLikes, array($uid, $dst));
            $this->db->execSql($sqlCheckFriend, array($uid, $dst, $dst, $uid));
            $match = $this->db->getFirstResult();
            if ($match) {
                $this->db->execSql($sqlDeleteMatch, array($match['id']));
                $this->db->execSql($sqlGetBlakList, array($dst, $uid));
                if (!$this->db->getFirstResult()) {
                    $this->db->execSql($sqlAddMatchUnlikesNotif, array($dst, $uid));
                }
            }
            $this->db->execSql($sqlGetLikes, array($dst));
            $nb_like = $this->db->getFirstResult()['nb_like'];
            $this->db->execSql($sqlNewLike, array($nb_like - 1, $dst));
            return $nb_like - 1;
        }
        return "-1";
    }

    private function action_bloquer($dst, $uid) {
        $sqlAddBlackList = "INSERT INTO `blacklist` (`uid`, `dst_id`) VALUES (?, ?)";
        $sqlGetBlakList = "SELECT * FROM `blacklist` WHERE `uid`=? AND `dst_id`=?";

        $this->db->execSql($sqlGetBlakList, array($uid, $dst));
        if (!$this->db->getFirstResult()) {
            $this->db->execSql($sqlAddBlackList, array($uid, $dst));
            $this->action_unlike($dst, $uid);
            return "OK";
        }
        return "FAIL";
    }

    private function action_report($dst, $uid) {
        $sqlAddReport = "INSERT INTO `report` (`uid`, `dst_id`) VALUES (?, ?)";

        $this->db->execSql($sqlAddReport, array($uid, $dst));
        $this->email->send_report_account($uid, $dst);
        return "OK";
    }
}
