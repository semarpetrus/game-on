<?php
function go_stats_overlay(){ 
	echo '<div id="go_stats_page_black_bg" style="display:none !important;"></div><div id="go_stats_white_overlay" style="display:none;"></div>';
}
function go_admin_bar_stats(){ 
 	global $wpdb;
 	$current_user = wp_get_current_user();
    $user_login =  $current_user->user_login ;
    $user_email = $current_user->user_email;
    $gamer_tag = $current_user->display_name ;
    $user_id = $current_user->ID ;
	$user_website = $current_user->user_url;
	global $current_points;
	global $current_currency;
 	$current_user = wp_get_current_user();
 	$current_user_id = $current_user->ID;
	$current_points = go_return_points($current_user_id);
	$current_currency = go_return_currency($current_user_id);
	$current_minutes = go_return_minutes($current_user_id);
	////////////////////////////////////////////////////////////
	go_get_rank($current_user_id);
	global $current_rank;
	global $next_rank_points;
	global $current_rank_points;
	global $next_rank;
	$dom = ($next_rank_points-$current_rank_points);
	if($dom <= 0){ $dom = 1;}
	$percentage = ($current_points-$current_rank_points)/$dom*100;
	if($percentage <= 0){ $percentage = 0;} else if($percentage >= 100){$percentage = 100;}
	$table_name_go = $wpdb->prefix . "go";
	///////////////////////////////////////////////////////////
	$numb_encountered = (int)$wpdb->get_var("select count(*) from ".$table_name_go." where uid = $user_id and status = 1 ");
	$numb_accepted = (int)$wpdb->get_var("select count(*) from ".$table_name_go." where uid = $user_id and status = 2 ");
	$numb_completed = (int)$wpdb->get_var("select count(*) from ".$table_name_go." where uid = $user_id and status = 3 ");
	$numb_mastered = (int)$wpdb->get_var("select count(*) from ".$table_name_go." where uid = $user_id and status = 4 ");
	$total_tasks_done = $numb_encountered+$numb_accepted+$numb_encountered+$numb_mastered;
	if($total_tasks_done == 0){ $total_tasks_done = 1;}
	$percentage_encountered = $numb_encountered/$total_tasks_done*100;
	$percentage_accepted = $numb_accepted/$total_tasks_done*100;
	$percentage_completed = $numb_completed/$total_tasks_done*100;
	$percentage_mastered = $numb_mastered/$total_tasks_done*100;
?>
<div id="go_stats_lay">

<button title="Close" onclick="go_stats_close();" id="go_stats_close_all"></button>
 <script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script language="javascript">
jQuery('#go_stats_accordion').accordion({
   collapsible: true,
   heightStyle: "content"
} );
jQuery( "#go_stats_progress_bar" ).progressbar({
      value: <?= $percentage ?>
    });
	var Pie = createPie("students","200px","white",4,[<?= $percentage_encountered ?>,<?= $percentage_accepted ?>,<?= $percentage_completed ?>, <?= $percentage_mastered ?>],["rgba(0,0,0,.25)","rgba(0,0,0,.5)","rgba(0,0,0,.75)","rgba(0,0,0,1)"]);


	 document.getElementById("go_stats_chart_div").appendChild(Pie);
      
</script>
<div id="go_stats_wrap">
<div id="go_stats_accordion">
  <h3 class="go_stats_header">General Information</h3>
  <div id="go_stats_general_information" class="go_stats_box go_border_radius">
  <div id="go_stats_gravatar" class="go_border_radius">
   <?php echo get_avatar($user_id, 96);?></div>
   <div id="go_stats_info" class="go_border_radius">
  <div class="go_info_boxes" class="go_border_radius"><a href="<?= $user_website ?>" style="color: black !important;
font-size: 25px !important;">Website</a></div>
  <div class="go_info_boxes" class="go_border_radius"><?= get_option('go_points_name').'<br />'.$current_points?> </div>
  <div class="go_info_boxes" class="go_border_radius"><?= get_option('go_currency_name').'<br />'.$current_currency?></div>
  <div class="go_info_boxes" class="go_border_radius">Minutes <?= '<br />'.$current_minutes?></div>
   </div>
  
   <div id="go_stats_progress_bar" style="margin-top: 115px; height:2em; position:relative; width:530px;;"><div id="go_stats_progress_bar_label" style="position: absolute;
    left: 50%;
    top: 4px;
    font-weight: bold;
    text-shadow: 1px 1px 0 #fff; color: black;
font-size: 19px;"><?= $current_points.'/'.$next_rank_points ?></div></div>
<div id="go_stats_current_rank"><?= $current_rank ?></div>
<div id="go_stats_next_rank"><?= $next_rank ?></div>
    <div id="go_stats_chart_div" style="margin-left: 55px;
margin-top: 40px; width:200px; display:inline;"></div
><div id="go_stats_chart_key"><div><div id="go_key_box" style="background-color:rgba(0,0,0,.25);"></div> Encountered (<?= $numb_encountered ?>)</div>
<div><div id="go_key_box" style="background-color:rgba(0,0,0,.5);"></div> Accepted (<?= $numb_accepted ?>)</div>
<div><div id="go_key_box" style="background-color:rgba(0,0,0,.75);"></div> Completed (<?= $numb_completed ?>)</div>
<div><div id="go_key_box" style="background-color:rgba(0,0,0,1);"></div> Mastered (<?= $numb_mastered ?>)</div></div>
  </div>
  
  
  
  
 
  
  <h3 class="go_stats_header" onclick="go_stats_task_list();"><?= get_option('go_tasks_name'); ?></h3>
  <div class="go_stats_box">
   <div id="go_stats_task_columns"><h6 class="go_stats_box_title">Encountered</h6>
<ul id="go_stats_encountered_list" class="go_stats_task_lists" ></ul></div>
   <div id="go_stats_task_columns" ><h6 class="go_stats_box_title">Accepted</h6>
<ul id="go_stats_accepted_list" class="go_stats_task_lists"></ul></div>
   <div id="go_stats_task_columns"><h6 class="go_stats_box_title">Completed</h6>
<ul id="go_stats_completed_list" class="go_stats_task_lists"></ul></div>
   <div id="go_stats_task_columns"><h6 class="go_stats_box_title">Mastered</h6>
<ul id="go_stats_mastered_list" class="go_stats_task_lists"></ul></div>

  </div>
  <h3 class="go_stats_header" onclick="go_stats_third_tab();"><?= get_option('go_points_name').' - '. get_option('go_currency_name').' - '. 'Minutes' ?></h3>
  <div class="go_stats_box">
  <div id="go_stats_third_tab_points"><h6 class="go_stats_box_title">Points</h6><ul id="go_stats_points" class="go_stats_task_lists" ></ul></div>
  <div id="go_stats_third_tab_currency"><h6 class="go_stats_box_title">Currency</h6><ul id="go_stats_currency" class="go_stats_task_lists" ></ul></div>
  <div id="go_stats_third_tab_minutes"><h6 class="go_stats_box_title">Minutes</h6><ul id="go_stats_minutes" class="go_stats_task_lists" ></ul></div>
  </div>



</div>
</div>
</div>
<?php die();}
function go_stats_task_list(){
	$stage = $_POST['stage'];
	global $wpdb;
 	$current_user = wp_get_current_user();
    $user_id = $current_user->ID ;
	$table_name_go = $wpdb->prefix . "go";
	$list = $wpdb->get_results("select post_id, points from ".$table_name_go." where uid = $user_id and status = $stage order by id desc");
	$x = 0;
	$sym = get_option('go_points_sym');
	foreach($list as $lists){
		
			$x++;
		?> <li class="go_<?= isEven($x)?>" ><a href=" <?= get_permalink( $lists->post_id) ?>" style="color:rgba(0,0,0,.4); font-size:12px;"> <?= get_the_title($lists->post_id) ?> (<?= $lists->points ?> <?=  $sym ?>)</a></li> <?php
		}
		die();
	}
	
function go_stats_points(){
		global $wpdb;
 	$current_user = wp_get_current_user();
    $user_id = $current_user->ID ;
	$table_name_go = $wpdb->prefix . "go";
	$list = $wpdb->get_results("select post_id, points, reason from ".$table_name_go." where uid = $user_id and points != 0 order by id desc");
	$x = 0;
	$sym = get_option('go_points_sym');
	foreach($list as $lists){
		if ($lists->post_id != 0){
			$x++;
		?> <li class="go_<?= isEven($x)?>" ><a href=" <?= get_permalink( $lists->post_id) ?>" style="color:rgba(0,0,0,.4); font-size:12px;"> <?= get_the_title($lists->post_id) ?> (<?= $lists->points ?> <?=  $sym ?>)</a></li> <?php
		} else{
			$x++;
		?> <li class="go_<?= isEven($x)?>" ><?= $lists->reason ?> (<?= $lists->points ?> <?=  $sym ?>)</li> <?php
			}}
		die(); 
	}
function go_stats_currency(){
		global $wpdb;
 	$current_user = wp_get_current_user();
    $user_id = $current_user->ID ;
	$table_name_go = $wpdb->prefix . "go";
	$list = $wpdb->get_results("select post_id, currency, reason from ".$table_name_go." where uid = $user_id and currency != 0 order by id desc");
	$x = 0;
	$sym = get_option('go_currency_sym');
	foreach($list as $lists){
		if ($lists->post_id != 0){
			$x++;
		?> <li class="go_<?= isEven($x)?>" ><a href=" <?= get_permalink( $lists->post_id) ?>" style="color:rgba(0,0,0,.4); font-size:12px;"> <?= get_the_title($lists->post_id) ?> (<?= $lists->currency ?> <?=  $sym ?>)</a></li> <?php
		} else{
			$x++;
		?> <li class="go_<?= isEven($x)?>" ><?= $lists->reason ?> (<?= $lists->currency ?> <?=  $sym ?>)</li> <?php
			}}
		die(); 
	}
function go_stats_minutes(){
		global $wpdb;
 	$current_user = wp_get_current_user();
    $user_id = $current_user->ID ;
	$table_name_go = $wpdb->prefix . "go";
	$list = $wpdb->get_results("select minutes, reason from ".$table_name_go." where uid = $user_id and minutes != 0 order by id desc");
	$x = 0;
	foreach($list as $lists){
		$reason_array = unserialize($lists->reason);
			$x++;
		?> <li class="go_<?= isEven($x)?>"><?= $lists->minutes ?> Minutes @ <?= $reason_array['time'] ?> For <?= $reason_array['reason'] ?></li> <?php
		}
		die(); 
	}
 ?>