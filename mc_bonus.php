<?php
/*
Plugin Name: MDAG Bonus
Plugin URI: http://mdag.my
Description: Bonus Management
Version: 1.0.0
Author: Nuar, MDAG Consultancy
Author URI: http://mdag.my
License: MIT License
License URI: http://mdag.mit-license.org/
*/

define('MC_DEBUG',1);

/**
 * Bonus
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
class mc_bonus
{
    /**
     *  Version numbers
     * 
     * @access  public
     * @var     string
     */    
    public $version     = '0.1';
    
    /**
     *  Valid user ID 
     * 
     * @access  public
     * @var     int
     */
    public $uid;
    
    /**
     *  Points to store
     * 
     * @access  public
     * @var     float
     */
    public $bonus_points      = 0;    
    
    /**
     *  bonus type
     * 
     * @see     DTYPE
     * @access  public
     * @var     mixed   
     */
    public $bonus_type;  

    /**
     *  bonus limit
     * 
     * @see     DTYPE
     * @access  public
     * @var     mixed 
     */    
    public $bonus_limit;   

    /**
     *  DB Insert ID
     * 
     * @see     wpdb::$insert_id
     * @access  public
     * @var     int
     */      
    public $insert_id   = false ;
    
    /**
     * primary menu slug
     * 
     * @var string
     * @access public
     */
    public $slug = 'mc-bonus';
    
    /**
     * admin page menu id, use for 
     * menu page referrence
     * 
     * @var mixed array 
     */
    public $page = array('primary'=>false,'bonusplan','addnew'=>false,'settings'=>'false','updateplan'=>false);
    
    /**
     * default capability to manage plugin
     * @var string
     */
    public $cap = 'manage_options';
    
    /**
     * procedural flag, for better
     * checking & stopping accidental
     * loop.
     * 
     * @var int
     * @access private
     */
    private $_flag = 0;
    
    /**
     * all options prefix inside wp
     * will use this schema, for 
     * uninstall options 
     * 
     * @var string
     */
    const OP_PREFIX     = 'mc_bonus_';
    
    /**
     * primary table name without
     * prefix
     * 
     * @var string
     * @deprecated 
     */     
    const DB_PRIMARY ='mc_bonus';
    
    /**
     * secondary table stored all
     * bonus plan
     * 
     * @var string
     * @deprecated
     */     
    const DB_BONUS_PLAN ='mc_bonus_plan';    
    
    /**
     * secondary table stored all
     * bonus plan meta 
     * 
     * @var string
     * @deprecated
     */     
    const DB_BONUS_PLAN_META ='mc_bonus_plan_meta';     
     
     /**
      * meta table name without
      * prefix, this db will store
      * all metadata for our primary
      * table
      * 
      * @var string
      * @deprecated
      */
    const DB_META = 'mc_bonus_meta';
    
    /**
     * mc_bonus::__construct()
     * 
     * Constructor, the actual setting up of the class properties
     * 
     * @author  Nuarharuha
     * @since   0.1
     * @access  public 
     * 
     */      
    public function __construct()
    {        
        $this->_init();   
    }
    /** mc_bonus::__construct() */

	/**
	 * Destructor and will run when object is destroyed.
	 *
	 * @see    mc_bonus::__construct()
	 * @since  0.1
	 * @return bool        true
	 */    
    public function __destruct()
    { 
        return true; 
    }
    /** mc_bonus::__destruct() */
    
    
    /**
     * mc_bonus::_init()
     * 
     * register global hooks & filters
     * define base configuration settings
     * 
     * @author  Nuarharuha
     * @since   0.1
     * @access  private
     */    
    private function _init()
    {
        $this->_set_default_settings();
        
        if (is_admin()) {
            $this->_initAdmin();
        }
    }
    /** mc_bonus::_init() */
    
    /**
     * mc_bonus::_initAdmin()
     * 
     * register global hooks & filters
     * for admin page
     * 
     * @author  Nuarharuha
     * @since   0.1
     * @access  private
     */    
    private function _initAdmin()
    {
        add_action('admin_init', array(&$this, 'register_stylesheets'));
        add_action('admin_menu', array(&$this, 'register_admin_menus'));
        add_action('add_meta_boxes', array(&$this,'register_metabox')); 
        
    }
    /** mc_bonus::_initAdmin() */    

    /**
     * mc_ewallet::register_admin_menus()
     * 
     * register admin menus & subpage
     * 
     * @author  Nuarharuha
     * @since   1.0.0
     * @access  public
     */     
    public function register_admin_menus()
    {
        $title      = 'Bonus';
        $callback   = array(&$this,'load_panel');
        $icon       = $this->plugin_uri.'img/bonus-16.png';
        $pos        = 101;
        
        $this->page['primary'] = add_menu_page($title, $title, $this->cap, $this->slug, $callback, $icon, $pos); 
        
        $this->_page_setup($this->page['primary']);

        $title      = 'Manage Plans';

        $this->page['bonusplan'] = add_submenu_page($this->slug, $title, $title, $this->cap, $this->slug.'&spage=bonusplan', $callback);
        
        $this->_page_setup($this->page['bonusplan']);         
        
        $title      = 'Add Bonus Plan';
        
        $this->page['addnew'] = add_submenu_page($this->slug, $title, $title, $this->cap, $this->slug.'&spage=addnew', $callback);
        
        $this->_page_setup($this->page['addnew']);
                
        //unset($this->page['primary']);  
    }
    /** mc_bonus::register_admin_menus() */ 

    /**
     * mc_ewallet::register_metabox()
     * 
     * register admin menus & subpage
     * 
     * @author  Nuarharuha
     * @since   1.0.0
     * @access  public
     */      
    public function register_metabox()
    {
        /** panel addnew form */
        add_meta_box('mc-plan-form','General Options', 'addnew_bonus_plans_widget', 'addnew-normal','normal','high');
        add_meta_box('mc-plan-trigger','Event settings', 'select_bonus_trigger', 'addnewside','side','high');
        
                
        add_meta_box('mc-plan-form','General Options', 'addnew_bonus_plans_widget', 'update-normal','normal','high');
        add_meta_box('mc-plan-trigger','Event settings', 'select_bonus_trigger', 'update-side','side','high');
    }
    /** mc_bonus::register_metabox() */ 
    
    
    /**
     * mc_bonus::load_panel()
     * 
     * load admin panel page
     * 
     * @author  Nuarharuha
     * @since   1.0.0
     * @access  public
     */
    public function load_panel()
    {   global $pagenow;
        
        $current_page   = false;
    
        if (isset($_REQUEST['page'])) $current_page = $_REQUEST['page'];
        
        switch($current_page){
            case $this->slug;
                // check for subpages
                if (!isset($_REQUEST['spage'])){
                    require_once $this->plugin_path.'panels/mc-bonus.php';
                } else {
                    switch ($_REQUEST['spage']){
                        case 'updateplan':                            
                            require_once $this->plugin_path.'panels/mc-bonus-updateplan.php';
                            break;                        
                        case 'addnew':
                            require_once $this->plugin_path.'panels/mc-bonus-addnew.php';
                            break;
                        case 'bonusplan':
                            require_once $this->plugin_path.'panels/mc-bonus-bonusplan.php';
                            break;
                    }
                }
            break;
        }
        
    }
    /**  mc_bonus::load_panel() */    
        
    /**
     * mc_bonus::_page_setup()
     * 
     * set admin page stylesheet, scripts & metabox 
     * 
     * @author  Nuarharuha
     * @since   1.0.0
     * @access  private
     */      
    private function _page_setup($hook)
    {
        add_action('admin_print_styles-'.$hook, array(&$this,'print_stylesheets') );
        //add_action('admin_footer-'.$hook, array($this,'print_scripts')); 
        
        add_action('load-'.$hook, array($this,'page_actions'),9);
        add_action('load-'.$hook, array($this,'save_settings'),10);  
        
    }
    /**  mc_bonus::_page_setup() */    

    /**
     * mc_bonus::page_actions()
	 * Actions to be taken prior to page loading. This is after headers have been set.
     * call on load-$hook
	 * This calls the add_meta_boxes hooks, adds screen options and enqueues the postbox.js script.   
     * 
     * @author  Nuarharuha
     * @since   1.0.0
     * @access  public
     */       
	public function page_actions(){
	   
        $page = (isset($_REQUEST['spage']) ) ? $_REQUEST['spage'] : $_REQUEST['page'];
        $page   = 'bonus_page_'.$page;
       
		do_action('add_meta_boxes_'.$page, null);
		do_action('add_meta_boxes', $page, null);
       
		/* User can choose between 1 or 2 columns (default 2) */
		add_screen_option('layout_columns', array('max' => 2, 'default' => 2) );

		/* Enqueue WordPress' script for handling the metaboxes */
        wp_enqueue_script('jquery');
		wp_enqueue_script('postbox'); 
        
	}
    /**  mc_bonus::page_actions() */

     
    /**
     * mc_bonus::save_settings()
     * 
     * save all settings method
     * 
     * @author  Nuarharuha
     * @since   1.0.0
     * @access  public
     */
     public function save_settings()
     {
        if (isset($_REQUEST['action']))
        {                        
            switch($_REQUEST['page']){
                case 'mc-bonus':
                    switch ($_REQUEST['action']){
                        case BTYPE::ACT_DELETE: delete_bonus_plan();
                            break;
                        case BTYPE::ACT_UPDATE:
                            if (wp_verify_nonce($_REQUEST['_wpnonce'], DTYPE::NONCE_BONUS) )
                            {
                                if ($_SERVER['REQUEST_METHOD'] == 'POST'){
                                
                                    if (is_valid_request(array('plan_id'))) {
                                        $this->update_plan();
                                    }
                                                                    
                                }
                            }
                            break;
                        case BTYPE::ACT_ADDNEW:
                            if (wp_verify_nonce($_REQUEST['_wpnonce'], DTYPE::NONCE_BONUS) )
                            {                            
                                $request = array('bonus_status','plan_title','duration_type');
                                
                                if (is_valid_request($request)) {
                                    $this->save_plan();
                                }
                            }
                            break;
                    }
                break;   
            }               
        }
     }
     /**  mc_bonus::save_settings() */ 

    /**
     * mc_bonus::update_plan()
     * 
     * save all settings method
     * 
     * @author  Nuarharuha
     * @since   1.0.0
     * @access  public
     */
     public function update_plan()
     {  global $wpdb;
        
        $table = $wpdb->base_prefix.BTYPE::DB_BONUS_PLAN;   
                
        $req  = foreach_push(new stdClass(), $_POST);
        
        $cid  = _current_user_id();
        $pid  = $req->plan_id;
        
        $metadata = array(
            'plan_title'            => 0, 'status'                => 0,
            'duration_type'         => 0, 'limited_duration'      => 0,            
            'limited_duration_type' => 0, 'payment_periods'       => 0, 
            'trigger_name'          => 0, 'trigger_action'        => 0            
        );
        
        $filters = array('%s','%s','%s','%d','%s','%s','%s','%s');
        
        $clause  = array('plan_id'=> $pid);
        
        foreach($metadata as $key => $v){
            if (isset($req->$key) && $req->$key != ''){
                $metadata[$key] = $_POST[$key];
            }
        }        
        
        $metadata['trigger_action'] = $metadata['trigger_name'];
        $metadata['trigger_name']   = ucwords(str_replace('-',' ',$metadata['trigger_name']));
        
        $wpdb->update($table, $metadata, $clause, $filters, array('%d'));
        
        do_action('after_update_bonus_plan', $pid, $req);
        
        /**
         *  update data meta rates
         */
        // BTYPE::DUMP('POST',$req,1);
        
        $table = $wpdb->base_prefix.BTYPE::DB_BONUS_RATES;
        
        /** update previous bonus rates data
         *  
         *  first we filter
         *  the $req->update_id. this array 
         *  should contain the previous row id 
         *  
         *  if there is none, empty
         * 
         *  1. query select, 
         *  2. delete the previous row if exists
         *  
         *  if there is new data row, do insert 
         */
        
        
        // are we doing update?
        if (! isset($req->update_id))
        {
            // let's looks for previous if there is any matchs
            $sql = "SELECT id FROM $table WHERE `plan_id`=%d";
            $results = $wpdb->get_results($wpdb->prepare($sql, $pid));
            
            if ($results){
                // if there is a match, proceed with deleting the row
                foreach($results as $index => $row){
                    $wpdb->query($wpdb->prepare("DELETE FROM $table WHERE `id`= %d", $row->id));
                }
            }
            
        } else {
            // we have a previous id, lets check if it matched 
            $counts = 0;
            
            $sql = "SELECT id FROM $table WHERE `plan_id`=%d";
            $results = $wpdb->get_results($wpdb->prepare($sql, $pid));
                        
            if ($results && !empty($results)){ 
                        
                // now we do some basic maths
                if (count($results) > count($req->update_id)){
                    // if the row count is higher
                    // we find the missing Id and Delete it
                    
                    foreach($results as $index => $row){
                        
                        // $wpdb object mostly return string
                        // type cast it first.                        
                        $rid = (int) $row->id;
                        
                        // $req->update_id array_key is the actual id
                        // if it missing, this is the row we want to 
                        // delete
                        if (! isset($req->update_id[$rid])){
                            $wpdb->query($wpdb->prepare("DELETE FROM $table WHERE `id`= %d", $rid));
                        } else {

                        } 
                                                                       
                    } // end.foreach $results
                                                                          
                } else {
                    
                    
                } // count.$req->update_id
                    
                foreach($req->update_id as $index => $rid){
                            // proceed with updating the previous row
                            $rates = array(
                                'plan_id'       => $pid,
                                'rate_name'     => $req->rate_name[$rid],
                                'min_lv'        => (int) $req->rate_min_lv[$rid],
                                'max_lv'        => (int) $req->rate_max_lv[$rid],
                                'rate_value'    => (int) $req->rate_amount[$rid],
                                'rate_type'     => $req->rate_type[$rid]
                            );
                            
                            $filters = array('%d','%s','%d','%d','%d','%s');
                            $clause  = array('id'=> $rid);
                                            
                            $wpdb->update($table, $rates, $clause, $filters, array('%d')); 
                            
                            do_action('after_update_bonus_plan_rates', $rid, $rates);   
                }             
                
            } // end.$results
                        
        } // end.isset($req->update_id)
  
                               
        /**
         *  Insert new bonus rates
         */
         
         // let see if there's any left
         if ( isset($req->rate_id) && count($req->rate_id) >= 0){
            
            // do the hawaian looop on rate id
            foreach($req->rate_id as $index => $rid){
                
                if ( $req->update_id[$rid] != $rid){
                
                    $rates = array(
                        'plan_id'       => $pid,
                        'rate_name'     => $req->rate_name[$rid],
                        'min_lv'        => (int) $req->rate_min_lv[$rid],
                        'max_lv'        => (int) $req->rate_max_lv[$rid],
                        'rate_value'    => (int) $req->rate_amount[$rid],
                        'rate_type'     => $req->rate_type[$rid]
                    );
                    
                    $wpdb->insert($table, $rates, array('%d','%s','%d','%d','%d','%s'));
                    do_action('after_save_bonus_plan_rates', $wpdb->insert_id, $rates, $req); 
                }
                             
            }
            
         } // end.isset($req->rate_id)

        wp_redirect(BTYPE::URI_MANAGE_PLAN);
        exit(); 
                 
     }
     /**  mc_bonus::update_plan() */
     
     
    /**
     * mc_bonus::save_plan()
     * 
     * save bonus plan
     * @uses    $wpdb
     * 
     * @author  Nuarharuha
     * @since   1.0.0
     * @access  public
     */     
    public function save_plan()
    { global $wpdb;    
    
        //BTYPE::DUMP('$_POST', $_POST,1);
        
        $table = $wpdb->base_prefix.BTYPE::DB_BONUS_PLAN;   
                
        $req  = foreach_push(new stdClass(), $_POST);
        
        $cid  = _current_user_id();
        
        $metadata = array(
            'plan_title'            => 0, 'status'                => 0,
            'duration_type'         => 0, 'limited_duration'      => 0,            
            'limited_duration_type' => 0, 'timestamp'             => 0,
            'payment_periods'       => 0, 'trigger_name'          => 0,
            'trigger_action'        => 0, 'created_by'            => $cid,            
        );
        
        foreach($metadata as $key => $v){
            if (isset($req->$key) && $req->$key != ''){
                $metadata[$key] = $_POST[$key];
            }
        }
        
        $metadata['trigger_action'] = $metadata['trigger_name'];
        $metadata['trigger_name']   = ucwords(str_replace('-',' ',$metadata['trigger_name'])); 
        
        
        
        if (!isset($req->plan_id)){
            $plan_id = $wpdb->insert($table, $metadata, array('%s','%s','%s','%d'));
        } 
        
        if ($plan_id){
            /** if there is a valid insert id, save the rates options **/
            do_action('after_save_bonus_plan', $plan_id, $req);
            
            $pid = $wpdb->insert_id;
            $table = $wpdb->base_prefix.BTYPE::DB_BONUS_RATES; 
            
            /** bail out if no id */ 
            if (! isset($req->rate_id) ) return false;
            
            foreach($req->rate_id as $key => $id){
                $rates = array(
                    'plan_id'       => $pid,
                    'rate_name'     => $req->rate_name[$key],
                    'min_lv'        => (int) $req->rate_min_lv[$key],
                    'max_lv'        => (int) $req->rate_max_lv[$key],
                    'rate_value'    => (int) $req->rate_amount[$key],
                    'rate_type'     => $req->rate_type[$key]
                );
                
                //BTYPE::DUMP('$rates Plan', $rates, 1);
                $wpdb->insert($table, $rates, array('%d','%s','%d','%d','%d','%s'));
                
                do_action('after_save_bonus_plan_rates', $wpdb->insert_id, $rates, $req);                         
            }           
            
        } else {
            do_action('after_save_bonus_plan_error', $wpdb->last_error);
        }
        
            
        wp_redirect(BTYPE::URI_MANAGE_PLAN);
        exit();         
       
    }
    /**  mc_bonus::save_plan() */
            
    /**
     * mc_bonus::register_stylesheets()
     * 
     * register plugin page stylsheets
     * 
     * @author  Nuarharuha
     * @since   1.0.0
     * @access  public
     */     
    public function register_stylesheets()
    {
        wp_register_style( 'font-awesome', plugins_url('/css/font-awesome.css', __FILE__) );
        wp_register_style( self::OP_PREFIX.'styles', plugins_url('/css/styles.css', __FILE__), array('font-awesome') );
        
    }
    /**  mc_bonus::register_stylesheets() */

    /**
     * mc_bonus::print_stylesheets()
     * 
     * enqueue plugin stylsheets on admin_head
     * 
     * @author  Nuarharuha
     * @since   1.0.0
     * @access  public
     */         
    public function print_stylesheets()
    {
        wp_enqueue_style(self::OP_PREFIX.'styles');        
    }
    /**  mc_bonus::print_stylesheets() */
            
    /**
     * mc_bonus::_set_default_settings()
     * 
     * set plugin path & options
     * 
     * @author  Nuarharuha
     * @since   0.1
     * @access  private
     */    
    private function _set_default_settings()
    {
        $this->plugin_uri   = plugin_dir_url(__FILE__);
        $this->plugin_path  = plugin_dir_path(__FILE__);
        $this->plugin_libs  = $this->plugin_path.'libs/'; 
        
        $includes = array('install','type','widgets','actions','plan','affiliate');
        
        foreach($includes as $file){
            require_once $this->plugin_libs.self::OP_PREFIX.$file.'.php';
        }       
        
        require_once $this->plugin_path.self::OP_PREFIX.'plantable'.'.php';
        
    }
    /** mc_bonus::_set_default_settings() */  
    
    /**
     * mc_bonus::save_bonus()
     * 
     * save transaction
     * 
     * @author  Nuarharuha
     * @since   0.1
     * @access  private
     * 
     * @uses    $wpdb WordPress database object for queries.
     * @return  void
     */      
    private function save_bonus()
    {   global $wpdb;
        
        $table = $wpdb->base_prefix.mc_ewallet::DB_PRIMARY;
        
        $transaction = array(
            'bonus_uid',
            'bonus_title',
            'bonus_type',            
            'bonus_value',
            'timestamp' => $_SERVER['REQUEST_TIME']
        );
        
        $transaction = apply_filters('before_points_transaction', $transaction);
                
        $results = $wpdb->insert($table, $transaction, array('%d','%s','%s','%d','%d'));        
        
        if ($results){
            $this->insert_id = $wpdb->insert_id;
            do_action('after_save_bonus', $this->insert_id, $transaction);
        }
        
    }
    /** mc_bonus::save_bonus() */
    
    /**
     * mc_bonus::_valid_id()
     * 
     * check if user ID is valid
     * 
     * @author  Nuarharuha
     * @since   0.1
     * @access  private
     */     
    private function _valid_id()
    {
        $user = get_userdata($this->uid);
        
        return ($user->user_login !='' && !empty($user->user_login) ); 
    }
    /** mc_bonus::_valid_id() */
    
    
    /** mc_bonus::add_meta()
     * 
     *  Static method
     *  Add metadata for transaction log.
     * 
     * @uses    $wpdb WordPress database object for queries.
     * 
     * @author  Nuarharuha
     * @since   0.1
     * @access  public
     * 
     * @param   int       $transaction_id     ID of the object metadata is for
     * @param   string    $meta_key           Metadata key
     * @param   string    $meta_value         Metadata value     * 
     * @return  bool                          The meta ID on successful update, false on failure.
     * 
     */     
    public static function add_meta($transaction_id, $meta_key, $meta_value)
    {   global $wpdb;
                            
        return $wpdb->insert( $wpdb->base_prefix.mc_bonus::DB_META, 
                	array( 
                		'bonus_id' => (int) $transaction_id, 
                        'meta_key' => $meta_key, 
                        'meta_value' => maybe_serialize($meta_value)
                	)
                 );         
    }
    /** mc_bonus::add_meta() */
    
    
    /** mc_bonus::update_meta()
     * 
     *  Static method
     *  Update metadata for transaction log.
     * 
     * @uses    $wpdb WordPress database object for queries.
     * 
     * @author  Nuarharuha
     * @since   0.1
     * @access  public
     * 
     * @param   int       $id                 valid Id of the object metadata is for
     * @param   int       $transaction_id     transaction Id of the object metadata is for
     * @param   string    $meta_key           Metadata key
     * @param   string    $meta_value         Metadata value 
     * @return  bool                          True on successful update, false on failure.
     * 
     */      
    public static function update_meta($id, $transaction_id, $meta_key, $meta_value)
    {   global $wpdb;
    
        $table = $wpdb->base_prefix.mc_bonus::DB_META;        
        $data  = array($meta_key => maybe_serialize($meta_value));        
        $where = array('id'=> $id, 'bonus_id' => $transaction_id);
    
        return $wpdb->update( $table, $data, $where,null, '%d');
    }
    /** mc_bonus::update_meta() */
    
    
    /** mc_bonus::delete_meta()
     * 
     *  Static method
     *  Delete metadata for the specified transaction log.
     * 
     * @uses    $wpdb WordPress database object for queries.
     * 
     * @author  Nuarharuha
     * @since   0.1
     * @access  public
     * 
     * @param   int       $id                Valid Id of the object metadata is for
     * @return  bool                         True on successful delete, false on failure.
     * 
     */     
    public static function delete_meta($id)
    {   global $wpdb;
    
        $table  = $wpdb->base_prefix.mc_bonus::DB_META;
        return $wpdb->query($wpdb->prepare("DELETE FROM $table WHERE id = %d", $id)); 
    }
    /** mc_bonus::delete_meta() */    
}

$mc_bonus = new mc_bonus();

/** plugin setup installation, run once */
register_activation_hook( __FILE__ , 'mc_bonus_on_activate_install_db');
function mc_bonus_on_activate_install_db(){	mc_bonus_install_db(); }