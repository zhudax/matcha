<?php

class SugList {
    private $db;
    private $uid;
    private $ask;
    private $match;
    private $profile;
    private $error = 0;
    private $sug_list = array();

    public function suggestion_list($db, $uid, $match, $ask, $order, $filtre, $list) {
        $this->db = $db;
        $this->uid = $uid;
        $this->ask = $ask;
        $this->match = $match;
        $this->match->init_val($db, $uid, $ask, $order, $filtre);
        if (!$list) {
            $this->profile = $this->get_orient_sex();
            $sug = array();
            $sug['orient'] = $this->check_orient_sex();
            $this->match->order->set_valid_user_id($sug['orient']);
            $sug['loca'] = $this->match->order->order_by_loca();
            $sug['tags'] = $this->match->order->order_by_tags();
            $sug['view'] = $this->match->order->order_by_age_popu(2);
            $sug['like'] = $this->match->order->order_by_age_popu(3);
            $sug_list = $this->get_position($sug);
            $this->sug_list = $sug_list;
        }
        else {
            $sug_list = $this->check_ask($list);
            if (!$this->error) {
                $this->match->order->set_valid_user_id($sug_list);
                if (!empty($this->ask['order'])) {
                    $sug_list = $this->match->order->check_order($this->ask);
                    if ($sug_list === 1)
                        $this->error = 1;
                }
                $this->sug_list = $sug_list;
            }
        }
    }

    public function get_result() {
        return ($this->error) ? "ErrorPost" : $this->match->getInfo($this->sug_list, $this->ask['offset']);
    }

    public function get_sug_list() {
        return ($this->sug_list) ? $this->sug_list : array();
    }

    private function check_ask($list) {
        $valid_user_id = $list;
        if (!isset($this->ask['orient_sex']) || !isset($this->ask['age_min']) ||
            !isset($this->ask['age_max']) || !isset($this->ask['view']) ||
            !isset($this->ask['like']) || !isset($this->ask['localisation']) ||
            !isset($this->ask['tags']) || !isset($this->ask['order']) ||
            !isset($this->ask['offset']))
            $this->error = 1;
        if (!$this->error) {
            if (!empty($this->ask['age_min']) && !empty($this->ask['age_max']))
                $valid_user_id = $this->match->filtre->get_ask_user(1, array("min" => $this->ask['age_min'], "max" => $this->ask['age_max']), $valid_user_id);
            if (!empty($this->ask['view']))
                $valid_user_id = $this->match->filtre->get_ask_user(2, $this->ask['view'], $valid_user_id);
            if (!empty($this->ask['like']))
                $valid_user_id = $this->match->filtre->get_ask_user(3, $this->ask['like'], $valid_user_id);
            if (!empty($this->ask['tags']))
                $valid_user_id = $this->match->filtre->get_ask_tags($this->ask['tags'], $valid_user_id);
            if (!empty($this->ask['localisation']))
                $valid_user_id = $this->match->filtre->get_ask_loca($this->ask['localisation'], $valid_user_id);
            return $valid_user_id;
        }
    }

    private function get_position($sug) {
        $valid_user_id = array();
        $sug_list = array();
        $max = count($sug['orient']);
        foreach ($sug['orient'] as $key => $value) {
            $sug_list[$value] = $key;
            $sug_list[$value] += $this->get_value($value, $sug['loca'], $max);
            $sug_list[$value] += $this->get_value($value, $sug['tags'], $max);
            $sug_list[$value] += $this->get_value($value, $sug['view'], $max);
            $sug_list[$value] += $this->get_value($value, $sug['like'], $max);
        }
        asort($sug_list);
        $valid_user_id = $this->match->order->get_order($sug_list);
        return $valid_user_id;
    }

    private function get_value($uid, $sug, $max) {
        $value = array_search($uid, $sug);
        if ($value || $value === 0)
            return $value;
        else
            return $max;
    }

    private function get_orient_sex() {
        $sqlGetUserProfile = "SELECT `*` FROM `profil` WHERE `uid`=?";
        $this->db->execSql($sqlGetUserProfile, array($this->uid));
        return $this->db->getFirstResult();
    }

    private function check_orient_sex() {
        $valid_user_id = array();
        $sqlGetUserProfile = "SELECT `uid`, `genre` FROM `profil`";
        $sqlGetUserGenre = "SELECT `uid`, `genre` FROM `profil` WHERE `genre`=?";
        if ($this->profile['orient_sex'] == 'M')
            $this->db->execSql($sqlGetUserGenre, array("M"));
        else if ($this->profile['orient_sex'] == 'F')
            $this->db->execSql($sqlGetUserGenre, array("F"));
        else
            $this->db->execSql($sqlGetUserProfile);
        $rets = $this->db->getAllResult();
        foreach ($rets as $ret) {
            $valid_user_id[] = $ret['uid'];
        }
        return $valid_user_id;
    }
}
