<?php  $cpage = strtolower($_REQUEST['spage']); ?>
<div class="wrap body_mc_bonus_bonusplan">
    <div id="icon-bonus" class="icon32"></div>
    <h2 class="">Manage Bonus Plans <a href="admin.php?page=mc-bonus&spage=addnew" class="add-new-h2">Add New</a></h2>
    <?php settings_errors(); ?> 
    <?php //var_dump($_REQUEST);?>
    <?php do_action('mc_notification',$_REQUEST); ?>
    <form name="form-<?php echo $cpage; ?>" method="post">
    <input type="hidden" name="action" value="action-<?php echo $cpage; ?>">
    <input type="hidden" name="timestamp" value="<?php echo $_SERVER['REQUEST_TIME'] ; ?>">    
    <?php wp_nonce_field(DTYPE::NONCE_BONUS);
    /* Used to save closed meta boxes and their order */
    wp_nonce_field( 'meta-box-bonus', 'meta-box-order-nonce', false );
    wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>    
				<div id="poststuff">		
					 <div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">
						  <div id="post-body-content">
							<?php do_action('content-'.$cpage, $_REQUEST); ?>                                
						  </div>
						  <div id="postbox-container-1" class="postbox-container">
						        <?php do_meta_boxes('','side',null); ?>
						  </div> 
						  <div id="postbox-container-2" class="postbox-container">
						        <?php do_meta_boxes('','normal',null);  ?>
						        <?php do_meta_boxes('','advanced',null); ?>
						  </div>	 
					 </div> <!-- #post-body -->
				 </div> <!-- #poststuff -->
    </form> 
</div>