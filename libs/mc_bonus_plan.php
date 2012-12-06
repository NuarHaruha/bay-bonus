<?php	


function save_bonus_planx(){
    global $wpdb;
    
    $table = $wpdb->base_prefix.mc_bonus::DB_BONUS_PLAN;    
    
    $plan = array(
        'plan_title'=> $_REQUEST['plan_title'],
        'status'=> $_REQUEST['bonus_status'],
        'duration_type' => $_REQUEST['duration_type'],
        'timestamp' => $_SERVER['REQUEST_TIME']
    );
    
    if (!isset($_REQUEST['plan_id'])){
        $results = $wpdb->insert($table, $plan, array('%s','%s','%s','%d'));
    } 
    
    if ($results){
        do_action('after_save_bonus_plan', $wpdb->insert_id, $_REQUEST);
    } else {
        do_action('after_save_bonus_plan_error', $wpdb->last_error);
    }
    

    wp_redirect(BTYPE::URI_MANAGE_PLAN);
    exit();
}

function update_bonus_plan(){
   global $wpdb;
    
    $req    = $_REQUEST;
    
    $table  = $wpdb->base_prefix.mc_bonus::DB_BONUS_PLAN;    
    
    $pid    = (int) $req['plan_id'];
    
    $plan   = array(
        'plan_title'    => $req['plan_title'],
        'status'        => $req['bonus_status'],
        'duration_type' => $req['duration_type']
    );
    
    $results = $wpdb->update( $table, $plan, 
        	array('plan_id' => $pid), array('%s','%s','%s'), array('%d'));   
    
    /** Begin metadata update */
    if (BTYPE::DURATION_TYPE_LIMITED === $req[BTYPE::DURATION_TYPE]) {        
        /** if duration is limited, update 
         *  limited duration & limited duration periods */
        update_plan_meta($pid, BTYPE::MK_LDURATION, $req[BTYPE::MK_LDURATION]); 
        update_plan_meta($pid, BTYPE::MK_LDURATION_TYPE, $req[BTYPE::MK_LDURATION_TYPE]);
     }  
             
    
    do_action('after_update_bonus_plan', $_REQUEST['plan_id'], $_REQUEST);        
    

    //wp_redirect(BTYPE::URI_MANAGE_PLAN);
    //exit();    
}


function update_plan_meta($pid, $meta_key, $meta_value){   
global $wpdb;
    
    $table = $wpdb->base_prefix.mc_bonus::DB_BONUS_PLAN_META;
    
    $sql    = "SELECT id FROM $table WHERE `plan_id`=%d AND `meta_key`=%d"; 
    $sql    = $wpdb->prepare($sql, $pid, $metakey);
    
    $id     = $wpdb->get_var($sql);
    
    if ($id){
        $data  = array($meta_key => maybe_serialize($meta_value));        
        $where = array('id'=> $id, 'plan_id' => $pid);    
        $results = $wpdb->update( $table, $data, $where,null, '%d');
        return $results;
        
    } else {
        if (defined('MC_DEBUG')){
            $dump = array(
                'args'=> array($pid, $meta_key, $meta_value),
                'search' => array('sql'=>$sql, 'return_id'=>$id));
            
            BTYPE::DUMP('update_plan_meta failed', $dump,1);
        }
    }
}

function delete_bonus_plan(){
    global $wpdb;
    
    $table  = $wpdb->base_prefix.mc_bonus::DB_BONUS_PLAN;
    
    $wpdb->query($wpdb->prepare("DELETE FROM $table WHERE `plan_id`= %d", $_REQUEST['plan_id'])); 

    wp_redirect(BTYPE::URI_MANAGE_PLAN);
    exit();    
}

function bonus_plan_meta_limited_duration($plan_id, $req){
    
    if ($req['duration_type'] == BTYPE::DURATION_TYPE_UNLIMITED ) return false;
    
        $cb = (isset($req[BTYPE::ACT_UPDATE])) ? BTYPE::CB_UPDATE : BTYPE::CB_INSERT;    
        
        call_user_func($cb, $plan_id, BTYPE::MK_LDURATION, $req[BTYPE::MK_LDURATION]); 
        call_user_func($cb, $plan_id, BTYPE::MK_LDURATION_TYPE, $req[BTYPE::MK_LDURATION_TYPE]);                
}

function bonus_plan_meta_payment_options($plan_id, $req){

        $cb = (isset($req[BTYPE::ACT_UPDATE])) ? BTYPE::CB_UPDATE : BTYPE::CB_INSERT;    
        
        call_user_func($cb, $plan_id, BTYPE::MK_PAYMENT_PERIODS, $req[BTYPE::MK_PAYMENT_PERIODS]);                  
}

function bonus_plan_meta_bonus_rates($plan_id, $req){
        
        if (! isset($req['rate_id']) ) return false;
        
        $options = array();
        
        foreach($req['rate_id'] as $index => $id){
            $options[$index]['id']      = (int) $id;
            $options[$index]['title']   = $req['rate_name'][$index];
            $options[$index]['min_lv']  = $req['rate_min_lv'][$index];
            $options[$index]['max_lv']  = $req['rate_max_lv'][$index];
            $options[$index]['amount']  = (int) $req['rate_amount'][$index];
            $options[$index]['type']    = $req['rate_type'][$index];             
        }
        
        $cb = (isset($req[BTYPE::ACT_UPDATE])) ? BTYPE::CB_UPDATE : BTYPE::CB_INSERT;    
        
        call_user_func($cb, $plan_id, BTYPE::MK_BONUS_RATE_OPTIONS, $options );         
}

function bonus_plan_meta_created_by($plan_id, $req){        
        plan_add_meta($plan_id, BTYPE::MK_CREATED_BY, _current_user_id());   
}

function bonus_plan_meta_modified_by($plan_id, $req){  
        
        $cb = (isset($req[BTYPE::ACT_UPDATE])) ? BTYPE::CB_UPDATE : BTYPE::CB_INSERT;        
        $options = array('id'=> $req['cid'],'date'=> BTYPE::DTIME());
        call_user_func($cb, $plan_id, BTYPE::MK_MODIFIED_BY, $options );
}

function plan_add_meta($plan_id, $meta_key, $meta_value)
{   global $wpdb;
                            
    return $wpdb->insert( $wpdb->base_prefix.mc_bonus::DB_BONUS_PLAN_META, 
   	    array( 
      		'plan_id' => $plan_id, 
            'meta_key' => $meta_key, 
            'meta_value' => maybe_serialize($meta_value)
           	)
        );         
}


function get_planmeta($plan_id, $metakey, $return_single_data = true){
    global $wpdb;
    
    $plan_id = (int) $plan_id;
    
    $table = $wpdb->base_prefix.mc_bonus::DB_BONUS_PLAN_META;
    
    $sql = "SELECT * FROM $table WHERE `plan_id`=%d AND `meta_key`=%s";
    
    $sql = $wpdb->prepare($sql, $plan_id, $metakey);
    
    $results = $wpdb->get_results($sql);
    
    if ($results){
        if (count($results) >= 0){
            foreach($results as $index => $v){
                if (isset($results[$index]->meta_value)){
                $results[$index]->meta_value = maybe_unserialize($results[0]->meta_value);
                }
            }
        }
    }   
    
    if ($return_single_data){
        return $results[0]->meta_value;
    } else {
        
        $metadata = array();
        
        foreach($results as $index=>$v) {
            $metadata[$index] = $results[0]->meta_value;
        }        
        
        return $metadata;
    }
}

function get_bonus_plan($plan_id){
    global $wpdb;
    $table = $wpdb->base_prefix.BTYPE::DB_BONUS_PLAN;   
    
    $sql = "SELECT plan_title title, status, duration_type, limited_duration, limited_duration_type, payment_periods, created_by, trigger_name, trigger_action FROM $table WHERE `plan_id`=%d";
    $results = $wpdb->get_results($wpdb->prepare($sql,$plan_id));
    
    if ($results){
        $results = $results[0];
        
        $table = $wpdb->base_prefix.BTYPE::DB_BONUS_RATES;
        
        $sql = "SELECT * FROM $table WHERE `plan_id`=%d";
        $rates =  $wpdb->get_results($wpdb->prepare($sql,$plan_id), ARRAY_A);
        
        $results->rates = $rates;
    }
    
    return $results;
}


    
function delete_all_bonusplan_meta($plant_id, $request){ // except created_by  
    global $wpdb;
    
    $table  = $wpdb->base_prefix.mc_bonus::DB_BONUS_PLAN_META;
    
    $sql = "SELECT id, meta_key FROM $table WHERE `plan_id`=%d";
    
    $results = $wpdb->get_results($wpdb->prepare($sql, $plant_id));
    
    if ($results){
        foreach($results as $index => $result){
            $mid = $result->id;             
            if ($result->meta_key != BTYPE::MK_CREATED_BY){
                $wpdb->query($wpdb->prepare("DELETE FROM $table WHERE `id`=%d", $mid));
            }
        }
    }    
}