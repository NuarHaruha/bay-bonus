<?php
/**
 *  products purchase bonus
 *
 * @package     isralife
 * @category    bonus
 *
 * @author      Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @copyright   Copyright (C) 2012, Nuarharuha <nhnoah+bay-isra@gmail.com>, MDAG Consultancy
 * @license     http://mdag.mit-license.org/ MIT License
 * @filesource  http://code.mdag.my/baydura_isralife/src
 * @version     0.1
 * @access      public
 */

function add_product_pv_bonus($uid, $sku, $invoice_id, $amount){
    global $wpdb;

    $db     = BTYPE::DB(BTYPE::DB_PRIMARY);

    $iid = explode('-', $invoice_id);
    $iid = $iid[1];

    $invoice = _t('a',$invoice_id, array('href'=>"/purchase/checkout-complete/?get_invoice=$iid&ordered_by=$uid&view_invoice=1"));

    $meta = array(
        'bonus_uid'     => $uid,
        'bonus_title'   => sprintf(BTYPE::MSG_PRODUCTS_PV_BONUS, $sku, $invoice),
        'bonus_type'    => BTYPE::BONUS_TYPE_PV,
        'bonus_value'   => $amount,
        'timestamp'     => BTYPE::DTIME()
    );

    $result = $wpdb->insert($db, $meta, array('%d','%s','%s','%d','%s'));
    deposit_points_pv($uid, $amount);

    if ($result){
        do_action('add_products_pv_bonus', $result, $meta);
    }
}