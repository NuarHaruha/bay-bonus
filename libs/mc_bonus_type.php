<?php
/**
 * BTYPE type enum & Constant
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
final class BTYPE
{
    const DB_PRIMARY                = 'mc_bonus';
    const DB_BONUS_META             = 'mc_bonus_meta';
    const DB_BONUS_PLAN             = 'mc_bonus_plan';
    const DB_BONUS_PLAN_META        = 'mc_bonus_plan_meta';
    const DB_BONUS_RATES            = 'mc_bonus_rates';

    const STATUS_ACTIVE             = 'active';
    const STATUS_INACTIVE           = 'inactive';

    const DURATION_TYPE             = 'duration_type';

    const DURATION_TYPE_LIMITED     = 'limited';
    const DURATION_TYPE_UNLIMITED   = 'unlimited';

    /**
     *  Meta Keys
     */
    const MK_LDURATION              = 'limited_duration';
    const MK_LDURATION_TYPE         = 'limited_duration_type';

    const MK_PAYMENT_PERIODS        = 'payment_periods';

    const MK_BONUS_RATE_OPTIONS     = 'bonus_rate_options';

    const MK_CREATED_BY             = 'created_by';

    const MK_MODIFIED_BY            = 'modified_by';

    /**
     * action key
     */
    const ACT_UPDATE                = 'action-updateplan';

    const ACT_DELETE                = 'deleteplan';

    const ACT_ADDNEW                = 'action-addnew';

    const MSG_REGISTER_BONUS        = 'Direct Downline Registration Bonus for %s';

    const MSG_STOCKIST_REGISTER_BONUS   = 'Stockist - Registration Bonus for %s';

    const MSG_PRODUCTS_PV_BONUS     = 'Product Purchase Bonus for item %s, ref:%s';

    const MEMBER_REGISTER_BONUS     = 20;

    const BONUS_TYPE_RM             = 'RM';

    const BONUS_TYPE_PV             = 'PV';

    const BONUS_TYPE_PERCENT        = 'PERCENT';
    /**
     * callback for metadata function
     */
    const CB_UPDATE                 = 'update_plan_meta';

    const CB_INSERT                 = 'plan_add_meta';

    const CB_DELETE                 = false;

    const TRIGGER_PREFIX            = 'trigger_plan_';

    const MK_SPONSOR_ID             = 'id_penaja';
    /**
     * use for redirect uri to
     * manage bonus plan page
     *
     * @var string
     */
    const URI_MANAGE_PLAN           = 'admin.php?page=mc-bonus&spage=bonusplan';

    static function DTIME() { return $_SERVER['REQUEST_TIME']; }

    static function DUMP($title, $args, $exit=false){
        t('H1',$title);
        var_dump($args);
        if ($exit) exit();
    }

    /**
     * @uses $wpdb wp database object
     * @author Nuarharuha <nhnoah+bay-isra@gmail.com>
     * @since 0.1
     *
     * @param string $name const of BTYPE::DB_{$}
     * @return string db table name with base prefix
     */
    public static function DB($name)
    {   global $wpdb;
        return $wpdb->base_prefix.$name;
    }

}