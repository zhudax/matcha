<?php

class Order {
    private $valid_user_id;
    private $uid;
    private $db;

    public function check_order($ask) {
        $valid_user_id = array();
        if ($ask['order'] == 'age')
            $valid_user_id = $this->order_by_age_popu(1);
        else if ($ask['order'] == 'view')
            $valid_user_id = $this->order_by_age_popu(2);
        else if ($ask['order'] == 'like')
            $valid_user_id = $this->order_by_age_popu(3);
        else if ($ask['order'] == 'localisation')
            $valid_user_id = $this->order_by_loca();
        else if ($ask['order'] == 'tags')
            $valid_user_id = $this->order_by_tags();
        else
            return 1;
        return $valid_user_id;
    }

    public function set_valid_user_id($valid_user_id) {
        $this->valid_user_id = $valid_user_id;
    }

    public function set_uid($uid) {
        $this->uid = $uid;
    }

    public function set_db($db) {
        $this->db = $db;
    }

    public function get_order($order) {
        $valid_user_id = array();
        foreach ($order as $key => $value) {
            $valid_user_id[] = $key;
        }
        return $valid_user_id;
    }

    public function order_by_tags() {
        $order = array();
        $sqlGetUserTag = "SELECT `tag` FROM `tags` WHERE `uid`=?";
        $this->db->execSql($sqlGetUserTag, array($this->uid));
        $rets = $this->db->getAllResult();
        if (!$rets)
            $rets = array();
        $tags = array();
        foreach ($rets as $ret) {
            $tags[] = $ret['tag'];
        }
        $common = 0;
        foreach ($this->valid_user_id as $uid) {
            $this->db->execSql($sqlGetUserTag, array($uid));
            $rets = $this->db->getAllResult();
            if (!$rets)
                $rets = array();
            foreach ($rets as $ret) {
                if (in_array($ret['tag'], $tags))
                    $common++;
            }
            $order[$uid] = $common;
            $common = 0;
        }
        arsort($order);
        return $this->get_order($order);
    }

    public function order_by_loca() {
        $order = array();
        $sqlGetUserLatLong = "SELECT `latitude`, `longitude` FROM `adresse` WHERE `uid`=?";
        $this->db->execSql($sqlGetUserLatLong, array($this->uid));
        $start = $this->db->getFirstResult();
        $startCoords = array("latitude" => $start['latitude'], "longitude" => $start['longitude']);
        foreach ($this->valid_user_id as $uid) {
            $this->db->execSql($sqlGetUserLatLong, array($uid));
            $adresse = $this->db->getFirstResult();
            if (!$adresse)
                $distance = 6371;
            else {
                $destCoords = array("latitude" => $adresse['latitude'], "longitude" => $adresse['longitude']);
                $distance = $this->calculDistance($startCoords, $destCoords);
            }
            $order[$uid] = $distance;
        }
        asort($order);
        return $this->get_order($order);
    }

    public function order_by_age_popu($val) {
        $order = array();
        $sqlGetUserProfile = "SELECT `age`, `nb_like`, `nb_view` FROM `profil` WHERE `uid`=?";

        foreach ($this->valid_user_id as $uid) {
            $this->db->execSql($sqlGetUserProfile, array($uid));
            if ($val == 1)
                $ret = $this->db->getFirstResult()['age'];
            else if ($val == 2)
                $ret = $this->db->getFirstResult()['nb_view'];
            else if ($val == 3)
                $ret = $this->db->getFirstResult()['nb_like'];
            $order[$uid] = $ret;
        }
        ($val == 1) ? asort($order) : arsort($order);
        return $this->get_order($order);
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
