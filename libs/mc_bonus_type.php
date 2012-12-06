<?php
/**
 * BTYPE type enum & Constant
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
    /**
     * callback for metadata function
     */
    const CB_UPDATE                 = 'update_plan_meta';

    const CB_INSERT                 = 'plan_add_meta';

    const CB_DELETE                 = false;

    const TRIGGER_PREFIX            = 'trigger_plan_';

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

}