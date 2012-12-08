<?php
/**
 * mc_bonus_install_db()
 * setup our database, this function should be
 * run on plugin active
 * 
 * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @since   1.4
 * @return  void
 */	
function mc_bonus_install_db(){
    global $wpdb;
    
    $db = $primary_db = $wpdb->base_prefix.BTYPE::DB_PRIMARY; 

	if($wpdb->get_var("SHOW TABLES LIKE '".$db."'") != $db 
    || (float) get_option(mc_bonus::OP_PREFIX.'db_version') < 1.4) 
    {
	   /**
	    * used KEY instead of INDEX for adding
        * INDEX.
	    */
	   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$sql = "CREATE TABLE " . $db . " (
			  bonus_id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,
			  bonus_uid BIGINT(20) unsigned NOT NULL,			  
              bonus_title VARCHAR(255) NOT NULL,
			  bonus_type VARCHAR(5) NOT NULL,              
			  bonus_value BIGINT(20) NOT NULL,
			  timestamp BIGINT(20) NOT NULL,
			  PRIMARY KEY bonus_id (bonus_id),
              KEY bonus_uid (bonus_uid),
              KEY bonus_type (bonus_type)
			) ENGINE=INNODB;";
            
          
        dbDelta($sql);
        
        /**
         *  auto delete all bonus 
         *  on user drop
         */
        $user_table = $wpdb->users;
        $sql = "ALTER TABLE $db 
                ADD FOREIGN KEY (bonus_uid) REFERENCES $user_table(ID)
                      ON DELETE CASCADE;";
                      
        $wpdb->query($sql);        
   
       $db = $wpdb->base_prefix.BTYPE::DB_BONUS_META;
       
       if($wpdb->get_var("SHOW TABLES LIKE '".$db."'") != $db){       
    		$sql = "CREATE TABLE " . $db . " (
    			  id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,  
                  bonus_id BIGINT(20) unsigned NOT NULL,  			  
                  meta_key VARCHAR(255) DEFAULT NULL,
                  meta_value LONGTEXT,
                  PRIMARY KEY (id),
                  KEY bonus_id (bonus_id),
                  KEY meta_key (meta_key)
    			) ENGINE=INNODB;";
                
            dbDelta($sql);            
        }
        
        $sql = "ALTER TABLE $db 
                ADD FOREIGN KEY (bonus_id) REFERENCES $primary_db(bonus_id)
                      ON DELETE CASCADE;";
                      
        $wpdb->query($sql);
        
        /**
         *  table bonus plan
         */
       $db = $plan_db = $wpdb->base_prefix.BTYPE::DB_BONUS_PLAN;
       
       if($wpdb->get_var("SHOW TABLES LIKE '".$db."'") != $db){       
    		$sql = "CREATE TABLE " . $db . " (
    			  plan_id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,    			  			  
                  plan_title VARCHAR(255) NOT NULL,
    			  status ENUM('active','inactive') NOT NULL,              
    			  duration_type ENUM('limited','unlimited') NOT NULL,
                  limited_duration BIGINT(20) DEFAULT 0,
                  limited_duration_type ENUM('hours','days','weeks','months','years','none') NOT NULL,
                  payment_periods ENUM('hourly','daily','weekly','monthly','yearly','plan_end') NOT NULL,
                  trigger_name VARCHAR(255) NOT NULL,
                  trigger_action VARCHAR(255) NOT NULL,
                  created_by BIGINT(20) unsigned NOT NULL,
    			  timestamp BIGINT(20) NOT NULL,
    			  PRIMARY KEY plan_id (plan_id),
                  KEY status (status),
                  KEY plan_title (plan_title)
    			) ENGINE=INNODB;";                
              
            dbDelta($sql);                                       
      }  
        /**
         *  bonus plan meta table
         */          
        
       $db = $wpdb->base_prefix.BTYPE::DB_BONUS_PLAN_META;
       
       if($wpdb->get_var("SHOW TABLES LIKE '".$db."'") != $db){       
    		$sql = "CREATE TABLE " . $db . " (
    			  id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,  
                  plan_id BIGINT(20) unsigned NOT NULL,  			  
                  meta_key VARCHAR(255) DEFAULT NULL,
                  meta_value LONGTEXT,
                  PRIMARY KEY id (id),
                  KEY plan_id (plan_id),
                  KEY meta_key (meta_key)
    			) ENGINE=INNODB;";
                
            dbDelta($sql);            
        }
        
        $sql = "ALTER TABLE $db 
                ADD FOREIGN KEY (plan_id) REFERENCES $plan_db(plan_id)
                      ON DELETE CASCADE;";
                      
        $wpdb->query($sql);  
        
        /**
         * Bonus rates table 
         */      
        /**
         *  bonus plan meta table
         */          
        
       $db = $wpdb->base_prefix.BTYPE::DB_BONUS_RATES;
       
       if($wpdb->get_var("SHOW TABLES LIKE '".$db."'") != $db){       
    		$sql = "CREATE TABLE " . $db . " (
    			  id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,  
                  plan_id BIGINT(20) unsigned NOT NULL,  			  
                  rate_name VARCHAR(255) DEFAULT NULL,
                  min_lv BIGINT(4) unsigned DEFAULT 0,
                  max_lv BIGINT(4) unsigned DEFAULT 0,
                  rate_value BIGINT(20) unsigned NOT NULL,
                  rate_type VARCHAR(255) NOT NULL,
                  PRIMARY KEY id (id),
                  KEY plan_id (plan_id),
                  KEY rate_name (rate_name),
                  KEY min_lv (min_lv),
                  KEY max_lv (max_lv)
    			) ENGINE=INNODB;";
                
            dbDelta($sql);            
        }
        
        $sql = "ALTER TABLE $db 
                ADD FOREIGN KEY (plan_id) REFERENCES $plan_db(plan_id)
                      ON DELETE CASCADE;";
                      
        $wpdb->query($sql);          
                
        add_option(mc_bonus::OP_PREFIX.'db_version', 1.4);
        add_option(mc_bonus::OP_PREFIX.'version', 0.1);       
	}    
}
/** mc_bonus_install_db() */