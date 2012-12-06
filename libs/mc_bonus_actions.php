<?php
/**
 * Manage all Action Hook
 * 
 * @package isralife
 * @category bonus
 * 
 * @copyright Copyright (c) 2012. Nuarharuha, MDAG Consultancy
 * @license http://mdag.mit-license.org MIT License
 * @author  Nuarharuha
 * @version 0.1
 */	

add_action('content-mc-bonus','manage_bonus_plans'); 
add_action('content-bonusplan','mc_render_bonus_plan_page');

// on user registration
add_action('user_register','member_register_downline_bonus');