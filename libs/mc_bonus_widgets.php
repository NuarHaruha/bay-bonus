<?php
/**
 *  Custom widgets & metabox 
 *  on admin panels
 * 
 * @package isralife
 * @category bonus
 * 
 * @copyright Copyright (c) 2012. Nuarharuha, MDAG Consultancy
 * @license http://mdag.mit-license.org MIT License
 * @author  Nuarharuha
 * @version 0.1
 */		

 
function manage_bonus_plans(){} 
function select_bonus_trigger(){

?>
<br />
<table class="form-table widefat">
    <tbody>
        <tr>
            <td>Trigger this plan on</td>
        </tr>
        <tr>
            <td>
                <select id="trigger_name" name="trigger_name">
                    <option value="members-registration">Members registration</option>
                    <option value="products-purchase">Product purchase</option>
                </select>
            </td>
        </tr>
    </tbody>
</table>
<?php    
    //var_dump($_REQUEST);
}


function addnew_bonus_plans_widget(){
    $pid = false;
if (isset($_GET['plan_id'])){
    $pid = $_GET['plan_id'];    
    $plan = get_bonus_plan($pid);
}

?>
            <!-- begin.table-form -->   <br />         
            <table class="form-table widefat">
                <thead>
                    <tr>
                        <th colspan="2"><strong>Global</strong></th>
                    </tr>
                </thead>
                <tbody>
                <tr valign="top">
                    <th scope="row"><label for="plan_title">Bonus Name</label></th>
                    <td><input id="plan_title" type="text" name="plan_title" class="regular-text code"/></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="status">Bonus Status</label></th>
                    <td> 
                        <fieldset>
                            <legend class="screen-reader-text"><span>Status</span></legend>                                
                                <select id="status" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                
                        </fieldset>
                    </td>
                </tr>  
                </tbody>
            </table><br />
            <table class="form-table widefat">
                <thead>
                    <tr>
                        <th colspan="2"><strong>Bonus Duration</strong></th>
                    </tr>
                </thead>    
                <tbody> 
                <tr valign="top">
                    <th scope="row"><label for="duration_type">Duration type</label></th>
                    <td> 
                        <fieldset>
                            <legend class="screen-reader-text"><span>Duration Type</span></legend>
                                <select id="duration_type" name="duration_type">
                                    <option value="limited">Limited Time</option>
                                    <option value="unlimited">No Limit</option>
                                </select>
                        </fieldset>
                        <script>
                            jQuery(document).ready(function($){
                               $('#duration_type').change(function(){
                                    $('#limited-time-duration').toggleClass('dn', $(this).val() == 'unlimited');
                                }); 
                            });
                        </script>
                    </td>
                </tr>  
                <tr valign="top" id="limited-time-duration" class="">
                    <th scope="row">Duration</th>
                    <td> 
                        <fieldset>
                            <legend class="screen-reader-text"><span>Duration type</span></legend>
                            <input type="text" id="limited_duration" name="limited_duration" value="" size="5">
                            <select id="limited_duration_type" name="limited_duration_type">
            	               <option value="hours">Hours</option>
                               <option value="days">Days</option>
                               <option value="weeks">Weeks</option>
                               <option value="months">Months</option>
                               <option value="years">Years</option>                               
                            </select>            
                        </fieldset>
                    </td>
                </tr> 
                </tbody>
            </table> 
            <br />
            <table class="form-table widefat">
                <thead>
                    <tr>
                        <th colspan="2"><strong>Payment options</strong></th>
                    </tr>
                </thead>                                                                        
                <tr valign="top">
                    <th scope="row"><label for="payment_periods">Payment period</label></th>
                    <td>
                        <select name="payment_periods" id="payment_periods">
            	           <option value="hourly">Hourly</option>
                           <option value="daily">Daily</option>
                           <option value="weekly">Weekly</option>
                           <option value="monthly" selected="selected">Monthly</option>
                           <option value="yearly">Yearly</option>
                           <option value="plan_end">After plan completion </option>            
                        </select>
                    </td>
                </tr>                             
                </tbody>
            </table><br />
            <table class="form-table widefat">
                <thead>
                    <tr>
                        <th colspan="2"><strong>Bonus Rates</strong></th>
                        
                    </tr>
                    
                </thead>                                                                        
                <tr valign="top">
                    <td colspan="2" style="padding:0pt">
                        <!-- begin.rates -->
                        <table>
                            <thead>
                                <tr>
                                    <th>Rate Name</th>
                                    <th>Min LV</th>
                                    <th>Max LV</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="rate-lists">                            
                                <!-- populate -->
                                <?php if ($pid && (isset($plan->rates[0]) && count($plan->rates) >= 0)):?>
                                <?php foreach($plan->rates as $index => $item):?>                                
                                <tr>
                                    <td>
                                        <input type="text" name="rate_name[<?php echo $item['id']?>]" value="<?php echo $item['rate_name']?>" size="40">
                                        <input type="hidden" name="rate_id[<?php echo $item['id']?>]" value="<?php echo $item['id']?>">
                                        <input type="hidden" name="update_id[<?php echo $item['id']?>]" value="<?php echo $item['id']?>">
                                    </td>
                                    <td><input type="text" name="rate_min_lv[<?php echo $item['id']?>]" value="<?php echo $item['min_lv']?>" size="5"/></td>
                                    <td><input type="text" name="rate_max_lv[<?php echo $item['id']?>]" value="<?php echo $item['max_lv']?>" size="5"/></td>
                                    <td><input type="text" name="rate_amount[<?php echo $item['id']?>]" value="<?php echo $item['rate_value']?>" /></td>
                                    <td>
                                        <select id="rt_<?php echo $item['id']?>" name="rate_type[<?php echo $item['id']?>]"><option value="rm">RM</option><option value="pv">PV</option><option value="percent">Percent</option></select>
                                        <script>
                                            jQuery(document).ready(function($){ 
                                                $("#rt_<?php echo $item['id']?>").val('<?php echo $item['rate_type']?>');
                                            });
                                        </script>
                                    </td>
                                    <td>
                                        <a class="button-secondary" href="javascript:void(0);" onclick="jQuery(jQuery(this).closest('tr')).remove();"><i class="icon-remove-sign"></i> Delete</a>
                                    </td>
                                </tr>                                
                                <?php endforeach; ?>
                                <?php endif;?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6" align="right"><button id="addrate" class="button-secondary"><i class="icon-plus-sign"></i> Add new rate</button></th>
                                </tr>
                            </tfoot>
                        </table>
                        <!-- end.rates -->                                           
                    </td>
                </tr>                             
                </tbody>
            </table><br />            
            <!-- end.table-form -->
                        <p>
                            <?php if ($pid): ?>
                            <a class="button-secondary" href="javascript:history.go(-1);">Back</a>
                            <?php endif; ?>
                            <button class="button-primary" type="submit">Update changes</button>
                        </p>

<script>
var plan = {};
jQuery(document).ready(function($){
    <?php if ($pid && (isset($plan->rates[0])) && count($plan->rates[0]) >= 0) : ?>
    var ratevar = <?php echo (count($plan->rates[0])+1);?>;
    <?php else: ?>
    var ratevar = 1;
    <?php endif; ?>
    function addnewrate(){
    	raterow = '<tr>';
    	raterow += '<td><input type="text" name="rate_name['+ratevar+']" value="" size="40" /><input type="hidden" name="rate_id['+ratevar+']" value="'+ratevar+'"></td>';
    	raterow += '<td><input type="text" name="rate_min_lv['+ratevar+']" value="" size="5"/></td>';
    	raterow += '<td><input type="text" name="rate_max_lv['+ratevar+']" value="" size="5"/></td>';
        raterow += '<td><input type="text" name="rate_amount['+ratevar+']" value="" /></td>';
    	raterow += '<td><select name="rate_type['+ratevar+']"><option value="rm">RM</option><option value="pv">PV</option><option value="percent">Percent</option></select></td>';
    	raterow += '<td align="center"><a class="button-secondary" href="javascript:void(0);" onclick="jQuery(jQuery(this).closest(\'tr\')).remove();"><i class="icon-remove-sign"></i> delete</a></td>';
    	raterow += '</tr>';
    	$(raterow).appendTo('#rate-lists');
    	ratevar++;
    }      
    
    <?php if ($pid && (isset($plan->rates[0])) && count($plan->rates[0]) >= 0): ?>
    plan = <?php echo json_encode($plan); ?>;
    $('#plan_title').val(plan.title);
    $('#status').val(plan.status);
    $('#duration_type').val(plan.duration_type);
    $('#payment_periods').val(plan.payment_periods);
    $('#trigger_name').val(plan.trigger_action);
    if (plan.duration_type == 'unlimited'){
        $('#limited-time-duration').toggleClass('dn');
    } else {
        $('#limited_duration').val(plan.limited_duration);
        $('#limited_duration_type').val(plan.limited_duration_type);
    }
    <?php else: ?>
    addnewrate();
    <?php endif; ?>    
    $('#addrate').click(function(e){e.preventDefault(); addnewrate();});    
}); 
</script> 
<?php    
    //var_dump($plan->rates);
    
}

function addnew_bonus_plans(){
    $pid = false;
if (isset($_GET['plan_id'])){
    $pid = $_GET['plan_id'];
    
    $plan = get_bonus_plan($pid);
}

?>
<table class="widefat">
    <tbody>
        <tr>
            <td>
            <!-- begin.table-form -->            
            <table class="form-table">
                <thead>
                    <tr>
                        <th colspan="2"><strong>Global</strong></th>
                    </tr>
                </thead>
                <tbody>
                <tr valign="top">
                    <th scope="row"><label for="plan_title">Bonus Name</label></th>
                    <td><input id="plan_title" type="text" name="plan_title" class="regular-text code"/></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="bonus_status">Bonus Status</label></th>
                    <td> 
                        <fieldset>
                            <legend class="screen-reader-text"><span>Status</span></legend>                                
                                <select id="bonus_status" name="bonus_status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                
                        </fieldset>
                    </td>
                </tr>  
                </tbody>
            </table><br />
            <table class="form-table">
                <thead>
                    <tr>
                        <th colspan="2"><strong>Bonus Duration</strong></th>
                    </tr>
                </thead>    
                <tbody> 
                <tr valign="top">
                    <th scope="row"><label for="duration_type">Duration type</label></th>
                    <td> 
                        <fieldset>
                            <legend class="screen-reader-text"><span>Duration Type</span></legend>
                                <select id="duration_type" name="duration_type">
                                    <option value="limited">Limited Time</option>
                                    <option value="unlimited">No Limit</option>
                                </select>
                        </fieldset>
                        <script>
                            jQuery(document).ready(function($){
                               $('#duration_type').change(function(){
                                    $('#limited-time-duration').toggleClass('dn', $(this).val() == 'unlimited');
                                }); 
                            });
                        </script>
                    </td>
                </tr>  
                <tr valign="top" id="limited-time-duration" class="">
                    <th scope="row">Duration</th>
                    <td> 
                        <fieldset>
                            <legend class="screen-reader-text"><span>Duration type</span></legend>
                            <input type="text" id="limited_duration" name="limited_duration" value="" size="5">
                            <select id="limited_duration_type" name="limited_duration_type">
            	               <option value="hours">Hours</option>
                               <option value="days">Days</option>
                               <option value="weeks">Weeks</option>
                               <option value="months">Months</option>
                               <option value="years">Years</option>                               
                            </select>            
                        </fieldset>
                    </td>
                </tr> 
                </tbody>
            </table> 
            <br />
            <table class="form-table">
                <thead>
                    <tr>
                        <th colspan="2"><strong>Payment options</strong></th>
                    </tr>
                </thead>                                                                        
                <tr valign="top">
                    <th scope="row"><label for="payment_periods">Payment period</label></th>
                    <td>
                        <select name="payment_periods" id="payment_periods">
            	           <option value="hourly">Hourly</option>
                           <option value="daily">Daily</option>
                           <option value="weekly">Weekly</option>
                           <option value="monthly" selected="selected">Monthly</option>
                           <option value="yearly">Yearly</option>
                           <option value="plan_end">After plan completion </option>            
                        </select>
                    </td>
                </tr>                             
                </tbody>
            </table><br />
            <table class="form-table">
                <thead>
                    <tr>
                        <th colspan="2"><strong>Bonus Rates</strong></th>
                    </tr>
                </thead>                                                                        
                <tr valign="top">
                    <td colspan="2" style="padding:0pt">
                        <!-- begin.rates -->
                        <table>
                            <thead>
                                <tr>
                                    <th>Rate Name</th>
                                    <th>Min LV</th>
                                    <th>Max LV</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="rate-lists">                            
                                <!-- populate -->
                                <?php if ($pid):?>
                                <?php foreach($plan->rates[0] as $index => $item):?>
                                <tr>
                                    <td><input type="text" name="rate_name['<?php echo $item['id']?>']" value="<?php echo $item['title']?>" size="30" /><input type="hidden" name="rate_id['<?php echo $item['id']?>']" value="<?php echo $item['id']?>"></td>
                                    <td><input type="text" name="rate_min_lv['<?php echo $item['id']?>']" value="<?php echo $item['min_lv']?>" /></td>
                                    <td><input type="text" name="rate_max_lv['<?php echo $item['id']?>']" value="<?php echo $item['max_lv']?>" /></td>
                                    <td><input type="text" name="rate_amount['<?php echo $item['id']?>']" value="<?php echo $item['amount']?>" /></td>
                                    <td>
                                        <select id="rt_<?php echo $item['id']?>" name="rate_type['<?php echo $item['id']?>']"><option value="rm">RM</option><option value="pv">PV</option><option value="percent">Percent</option></select>
                                        <script>
                                            jQuery(document).ready(function($){ 
                                                $("#rt_<?php echo $item['id']?>").val('<?php echo $item['type']?>');
                                            });
                                        </script>
                                    </td>
                                    <td>
                                        <a class="button-secondary" href="javascript:void(0);" onclick="jQuery(jQuery(this).closest('tr')).remove();"><i class="icon-remove-sign"></i> Delete</a>
                                    </td>
                                </tr>                                
                                <?php endforeach; ?>
                                <?php endif;?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6" align="right"><button id="addrate" class="button-secondary"><i class="icon-plus-sign"></i> Add new rate</button></th>
                                </tr>
                            </tfoot>
                        </table>
                        <!-- end.rates -->   
                                        
                    </td>
                </tr>                             
                </tbody>
            </table><br />            
            <!-- end.table-form -->
            </td>
        </tr>
    </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2">
                            <?php if ($pid): ?>
                            <a class="button-secondary" href="javascript:history.go(-1);">Back</a>
                            <?php endif; ?>
                            <button class="button-primary" type="submit">Save changes</button>
                        </th>
                    </tr>
                </tfoot>    
</table>
<script>
jQuery(document).ready(function($){
    <?php if ($pid): ?>
    var ratevar = <?php echo (count($plan->rates[0])+1);?>;
    <?php else: ?>
    var ratevar = 1;
    <?php endif; ?>
    function addnewrate(){
    	raterow = '<tr>';
    	raterow += '<td><input type="text" name="rate_name['+ratevar+']" value="" size="30" /><input type="hidden" name="rate_id['+ratevar+']" value="'+ratevar+'"></td>';
    	raterow += '<td><input type="text" name="rate_min_lv['+ratevar+']" value="" /></td>';
    	raterow += '<td><input type="text" name="rate_max_lv['+ratevar+']" value="" /></td>';
        raterow += '<td><input type="text" name="rate_amount['+ratevar+']" value="" /></td>';
    	raterow += '<td><select name="rate_type['+ratevar+']"><option value="rm">RM</option><option value="pv">PV</option><option value="percent">Percent</option></select></td>';
    	raterow += '<td align="center"><a class="button-secondary" href="javascript:void(0);" onclick="jQuery(jQuery(this).closest(\'tr\')).remove();"><i class="icon-remove-sign"></i> Delete</a></td>';
    	raterow += '</tr>';
    	$(raterow).appendTo('#rate-lists');
    	ratevar++;
    }
    
    <?php if ($plan): ?>
    var plan = <?php echo json_encode($plan); ?>;
    $('#plan_title').val(plan.title);
    $('#status').val(plan.status);
    $('#duration_type').val(plan.duration_type);
    $('#payment_periods').val(plan.payment_periods);
    if (plan.duration_type == 'unlimited'){
        $('#limited-time-duration').toggleClass('dn');
    } else {
        $('#limited_duration').val(plan.limited_duration);
        $('#limited_duration_type').val(plan.limited_duration_type);
    }
    <?php else: ?>
    addnewrate();
    <?php endif; ?>
    
    $('#addrate').click(function(e){
      e.preventDefault(); 
      addnewrate(); 
    });    
}); 
</script> 
<?php    
    //var_dump($plan->rates);
    
}