<?php
/**
 *  registration bonus
 *
 * @package     isralife
 * @category    bonus
 *
 * @author      Nuarharuha
 * @copyright   Copyright (C) 2012, Nuarharuha, MDAG Consultancy
 * @license     http://mdag.mit-license.org/ MIT License
 * @filesource  http://code.mdag.my/baydura_isralife/src
 * @version     0.1
 * @access      public
 */

function member_register_downline_bonus($user_id){
    global $wpdb;

    $uid = false;

    $sponsor_code = uinfo($user_id,'sponsor_code');
    $sponsor_id   = mc_get_code_userid($sponsor_code);

    if (!empty($sponsor_id)){
        $uid = $sponsor_id;
    }

    $code   = uinfo($user_id,'code');

    $db     = BTYPE::DB(BTYPE::DB_PRIMARY);

    $meta = array(
        'bonus_uid'     => $uid,
        'bonus_title'   => sprintf(BTYPE::MSG_REGISTER_BONUS, $code),
        'bonus_type'    => BTYPE::BONUS_TYPE_RM,
        'bonus_value'   => BTYPE::MEMBER_REGISTER_BONUS,
        'timestamp'     => BTYPE::DTIME()
    );

    if (!$uid) return false;

    $result = $wpdb->insert($db, $meta, array('%d','%s','%s','%d','%s'));
    deposit_points_rm($uid, BTYPE::MEMBER_REGISTER_BONUS);
    if ($result){
        do_action('member_register_downline_bonus',$wpdb->insert_id, $meta);
    }
}

function stockist_register_member_bonus($request, $user_id){
    global $wpdb;

    unset($request);

    if (!current_user_is_stockist()) return false;


    $uid    = _current_user_id();

    $type   = get_stockist_type($uid);

    $amount = 0;

    // stockist has different bonus rates
    $rates = get_option(SKTYPE::MK_REGISTER_BONUS);

    switch($type){
        case 'mobile':
            $amount = $rates[SKTYPE::ST_MOBILE];
            break;
        case 'daerah':
            $amount = $rates[SKTYPE::ST_DISTRICT];
            break;
        case 'negeri':
            $amount = $rates[SKTYPE::ST_STATE];
            break;
    }


    $code   = uinfo($user_id,'code');

    $db     = BTYPE::DB(BTYPE::DB_PRIMARY);

    $meta = array(
        'bonus_uid'     => $uid,
        'bonus_title'   => sprintf(BTYPE::MSG_STOCKIST_REGISTER_BONUS, $code),
        'bonus_type'    => BTYPE::BONUS_TYPE_RM,
        'bonus_value'   => $amount,
        'timestamp'     => BTYPE::DTIME()
    );

    $result = $wpdb->insert($db, $meta, array('%d','%s','%s','%d','%s'));
    deposit_points_rm($uid, $amount);

    if ($result){
        do_action('member_register_downline_bonus',$wpdb->insert_id, $meta);
    }
}

function get_affiliate_bonus($uid){
    global $wpdb;

    $db = BTYPE::DB(BTYPE::DB_PRIMARY);

    $sql = "SELECT * FROM $db WHERE bonus_uid=%d";

    return $wpdb->get_results($wpdb->prepare($sql, $uid));
}
