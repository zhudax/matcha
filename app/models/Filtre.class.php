<?php

class Filtre {
    private $db;
    private $uid;

    public function set_db($db) {
        $this->db = $db;
    }

    public function set_uid($uid) {
        $this->uid = $uid;
    }

    public function get_ask_user($ask, $value, $uids) {
        $sqlGetAllUserBtwAge = "SELECT `uid` FROM `profil` WHERE (`age` BETWEEN ? AND ?) AND `uid`=?";
        $sqlGetAllUserLikeUpper = "SELECT `uid` FROM `profil` WHERE `nb_like`>=? AND `uid`=?";
        $sqlGetAllUserViewUpper = "SELECT `uid` FROM `profil` WHERE `nb_view`>=? AND `uid`=?";
        $sql = "";
        $values = array();
        $valid_user_id = array();
        foreach ($uids as $uid) {
            if ($ask == 1) {
                $sql = $sqlGetAllUserBtwAge;
                $values = array($value['min'], $value['max'], $uid);
            }
            else if ($ask == 2) {
                $sql = $sqlGetAllUserViewUpper;
                $values = array($value, $uid);
            }
            else if ($ask == 3) {
                $sql = $sqlGetAllUserLikeUpper;
                $values = array($value, $uid);
            }
            $this->db->execSql($sql, $values);
            $result = $this->db->getFirstResult();
            if ($result)
                $valid_user_id[] = $result['uid'];
        }
        return ($valid_user_id);
    }

    public function get_ask_tags($value, $uids) {
        $sqlGetUserTag = "SELECT `tag` FROM `tags` WHERE `uid`=?";
        $valid_user_id = array();
        $valid = 1;
        $value = explode(",", $value);
        foreach ($uids as $uid) {
            $this->db->execSql($sqlGetUserTag, array($uid));
            $rets = $this->db->getAllResult();
            if (!$rets)
                $rets = array();
            $tags = array();
            foreach ($rets as $ret) {
                $tags[] = $ret['tag'];
            }
            foreach ($value as $tag) {
                if (!in_array($tag, $tags)) {
                    $valid = 0;
                    break;
                }
            }
            if ($valid)
                $valid_user_id[] = $uid;
            else
                $valid = 1;
        }
        return $valid_user_id;
    }

    public function get_ask_loca($value, $uids) {
        $sqlGetUserLatLong = "SELECT `latitude`, `longitude` FROM `adresse` WHERE `uid`=?";
        $this->db->execSql($sqlGetUserLatLong, array($this->uid));
        $start = $this->db->getFirstResult();
        if (!$start)
            return array();
        else {
            $valid_user_id = array();
            $startCoords = array("latitude" => $start['latitude'], "longitude" => $start['longitude']);
            foreach ($uids as $uid) {
                $this->db->execSql($sqlGetUserLatLong, array($uid));
                $adresse = $this->db->getFirstResult();
                if (!$adresse)
                    $distance = 6371;
                else {
                    $destCoords = array("latitude" => $adresse['latitude'], "longitude" => $adresse['longitude']);
                    $distance = $this->calculDistance($startCoords, $destCoords);
                }
                if ($distance < $value)
                    $valid_user_id[] = $uid;
            }
            return $valid_user_id;
        }
    }

    private function calculDistance($startCoords, $destCoords)
    {
        $startLatRads = $this->degreesEnRadians($startCoords['latitude']);
        //variable stock le degree en radians en fonction de la latitude de depart
        $startLongRads = $this->degreesEnRadians($startCoords['longitude']);
        //variable stock le degree en radians en fonction de la longitude de depart
        $destLatRads = $this->degreesEnRadians($destCoords['latitude']);
        //variable stock le degree en radians en fonction de la latitude d'arrivee
        $destLongRads = $this->degreesEnRadians($destCoords['longitude']);
        //variable stock le degree en radians en fonction de la longitude d'arrivee
        $radius = 6371; // rayon de la Terre en km
        $distance = acos(sin($startLatRads) * sin($destLatRads) +
        cos($startLatRads) * cos($destLatRads) *
        cos($startLongRads - $destLongRads)) * $radius;
        //calcule mathematique un peu compliquee a expliquer
        return $distance; // mais finalement on obtient une distancestance apres le calcule
    }

    private function degreesEnRadians($degrees)
    {
        $radians = ($degrees * M_PI) / 180;
        // radians = degrees * pi / 180
        // convertir degree en radians.
        return $radians;
    }
}
