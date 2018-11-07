<?php

class Refresh {
    private $db;
    private $uid;
    private $post;

    public function init_val($db, $uid, $post) {
        $this->db = $db;
        $this->uid = $uid;
        $this->post = $post;
    }

    public function get_notif() {
        $sqlGetNotifNumber = "SELECT COUNT(*) AS `numNotif` FROM `notif`
                            WHERE `is_read`='N' AND `uid`=?";
        $this->db->execSql($sqlGetNotifNumber, array($this->uid));
        return $this->db->getFirstResult()['numNotif'];
    }

    public function read_notif() {
        $sqlGetNoReafNotif = "SELECT * FROM `notif` WHERE `is_read`='N' AND `uid`=? ORDER BY `id` DESC";
        $sqlNotifRead = "UPDATE `notif` SET `is_read`='Y' WHERE `uid`=?";
        $sqlGetUserInfo = "SELECT * FROM `users` WHERE `id`=?";
        $this->db->execSql($sqlGetNoReafNotif, array($this->uid));
        $notifs = $this->db->getAllResult();
        if (!$notifs)
            $notifs = array();
        $this->db->execSql($sqlNotifRead, array($this->uid));
        $result = array();
        foreach ($notifs as $notif) {
            $this->db->execSql($sqlGetUserInfo, array($notif['src_id']));
            $info = $this->db->getFirstResult();
            $src_user = $info['prenom'] . " " . $info['nom'];
            $result[] = $this->checkNotif($src_user, $notif);
        }
        return json_encode($result);
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

    public function get_friends() {
        $sqlGetMatchFriends = "SELECT * FROM `match` WHERE `user1`=? OR `user2`=?";
        $sqlGetUserInfo = "SELECT `login`, `nom`, `prenom` FROM `users` WHERE `id`=?";
        $this->db->execSql($sqlGetMatchFriends, array($this->uid, $this->uid));
        $friends = $this->db->getAllResult();
        if (!$friends)
            return 0;
        else {
            $len = count($friends);
            $ret = array();
            for ($i=0; $i < $len; $i++) {
                $u1 = $friends[$i]['user1'];
                $u2 = $friends[$i]['user2'];
                $friend = ($u1 == $this->uid) ? $u2 : $u1;
                $this->db->execSql($sqlGetUserInfo, array($friend));
                $ret[$i] = $this->db->getFirstResult();
            }
            return json_encode($ret);
        }
    }

    public function get_history() {
        if (!isset($this->post['src']) || empty($this->post['src']) ||
            empty($this->post['dst']) || !isset($this->post['dst']))
            return "Error";
        $sqlGetReadChat = "SELECT * FROM `chat` WHERE `is_read`='Y' AND ((`src_id`=? AND `dst_id`=?) OR (`src_id`=? AND `dst_id`=?))";
        $sqlGetNoReadChat = "SELECT * FROM `chat` WHERE `src_id`=? AND `dst_id`=? AND `is_read`='N'";
        $sqlGetId = "SELECT `id` FROM `users` WHERE `login`=?";
        $this->db->execSql($sqlGetId, array($this->post['src']));
        $ret = $this->db->getFirstResult();
        if (!$ret)
            return "Error";
        $src_id = $ret['id'];
        $this->db->execSql($sqlGetId, array($this->post['dst']));
        $ret = $this->db->getFirstResult();
        if (!$ret)
            return "Error";
        $dst_id = $ret['id'];
        $this->db->execSql($sqlGetReadChat, array($src_id, $dst_id, $dst_id, $src_id));
        $ret = $this->db->getAllResult();
        $chat = ($ret) ? $ret : array();
        $this->db->execSql($sqlGetNoReadChat, array($src_id, $dst_id));
        $ret = $this->db->getAllResult();
        $chat = array_merge($chat, ($ret) ? $ret : array());
        return ($chat) ? json_encode($chat) : json_encode(array());
    }

    public function save_message() {
        if (!isset($this->post['src']) || empty($this->post['src'])) {
            return "Error";
        }
        else if (!isset($this->post['dst']) || empty($this->post['dst'])) {
            return "Vous devez choisir un destinataire!";
        }
        else if (!isset($this->post['message']) || empty($this->post['message'])) {
            return "Vous devez saisir du message!";
        }
        else {
            $sqlGetId = "SELECT `id` FROM `users` WHERE `login`=?";
            $sqlAddChatMsg = "INSERT INTO `chat` (`src_id`, `dst_id`, `message`) VALUES (?, ?, ?)";
            $sqlAddMessageNotif = "INSERT INTO `notif` (`uid`, `src_id`, `message`) VALUES (?, ?, 'Y')";

            $this->db->execSql($sqlGetId, array($this->post['dst']));
            $ret = $this->db->getFirstResult();
            if (!$ret)
                return "Error";
            $dst_id = $ret['id'];
            $this->db->execSql($sqlAddChatMsg, array($this->post['src'], $dst_id, $this->post['message']));
            $this->db->execSql($sqlAddMessageNotif, array($dst_id, $this->post['src']));
            return "OK";
        }
    }

    public function read_message() {
        if (!isset($this->post['src']) || !isset($this->post['dst']) ||
            empty($this->post['dst']) || empty($this->post['src'])) {
            return "Error";
        }
        else {
            $sqlGetNoReadChat = "SELECT * FROM `chat` WHERE `src_id`=? AND `dst_id`=? AND `is_read`='N'";
            $sqlChatRead = "UPDATE `chat` SET `is_read`='Y' WHERE `src_id`=? AND `dst_id`=?";
            $sqlGetId = "SELECT `id` FROM `users` WHERE `login`=?";

            $this->db->execSql($sqlGetId, array($this->post['src']));
            $ret = $this->db->getFirstResult();
            if (!$ret)
                return "Error";
            $src_id = $ret['id'];
            $this->db->execSql($sqlGetNoReadChat, array($src_id, $this->post['dst']));
            $ret = $this->db->getAllResult();
            if (!$ret)
                $ret = array();
            $message = json_encode($ret);
            $this->db->execSql($sqlChatRead, array($src_id, $this->post['dst']));
            return $message;
        }
    }
}
