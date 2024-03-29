<?php

//Creates table for indivual logs.

function go_table_individual() {
   global $wpdb;

   $table_name = $wpdb->prefix . "go";
      
   $sql = "CREATE TABLE $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  uid INT,
  status INT,
  post_id INT,
  points INT,
  currency INT,
  minutes VARCHAR (200),
  reason VARCHAR (200),
  UNIQUE KEY  id (id)
    );";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $sql );
 
}



//Creates a table for totals.

function go_table_totals() {
   global $wpdb;

   $table_name = $wpdb->prefix . "go_totals";
      
   $sql = "CREATE TABLE $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  uid  INT,
  currency  INT,
  points  INT,
  minutes  VARCHAR (200),
  UNIQUE KEY  id (id)
    );";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $sql );
 
}



//Updates the totals upon activation of plugin.
function go_ranks_registration(){
	global $wpdb;
	$ranks = get_option('go_ranks',false);
		if(!$ranks){
			$ranks = array('Level 1'=>0, 'Level 2'=> 100);
			update_option('go_ranks',$ranks);
			}
	}

function go_install_data(){
	
	global $wpdb;
	 $table_name_user_meta = $wpdb->prefix . "usermeta";
	 $table_name_go_totals = $wpdb->prefix . "go_totals";
global $default_role;
	$role = get_option('go_role',$default_role);
	$uid = $wpdb->get_results("SELECT user_id
FROM ".$table_name_user_meta."
WHERE meta_key =  'wp_capabilities'
AND (meta_value LIKE  '%".$role."%' or meta_value like '%administrator%')");
 foreach($uid as $id){
 foreach($id as $uids){
	 
	 
	 
 




			$check = (int)$wpdb->get_var("select uid from ".$table_name_go_totals." where uid = $uids ");
				if($check == 0){
					$wpdb->insert( $table_name_go_totals,array( 'uid' => $uids ), array(  '%d' ) );
						} else {
		 					$wpdb->update( $table_name_go_totals, array( 'uid' => $uids), array( 'uid' => $uids ), array('%d'), array( '%d') ) ;
								}
								
								
$rank_check =	get_user_meta($uids, 'go_rank');
 if(empty($rank_check)){ 
 $ranks = get_option('go_ranks', false);
 $current_points = go_return_points($uids);
 while($current_points >= current($ranks)){
	 next($ranks);
	 }
 $next_rank_points = current($ranks);
 $next_rank = array_search($next_rank_points, $ranks);
 $rank_points = prev($ranks);
 $new_rank = array_search($rank_points, $ranks);
 $new_rank_array= array(array($new_rank, $rank_points),array($next_rank, $next_rank_points));
  update_user_meta($uids,'go_rank', $new_rank_array );
}								

								
				}
		}
}
	
//Adds user id to the totals table upon user creation.
function go_user_registration($user_id) {
 global $wpdb;
 global $role_default;
 $table_name_go_totals = $wpdb->prefix . "go_totals";
 $table_name_user_meta = $wpdb->prefix . "usermeta";
 $role = get_option('go_role',$role_default);
 $user_role = get_user_meta($user_id,'wp_capabilities', true);
 if(array_search(1, $user_role) == $role || $user_role == 'administrator'){
 	$ranks = get_option('go_ranks');
		$current_rank_points = current($ranks);
		$current_rank = array_search($current_rank, $ranks);
		$next_rank_points = next($ranks);
		$next_rank = array_search($next_rank_points, $ranks);
		$new_rank = array(array($current_rank, $current_rank_points),array($next_rank, $next_rank_points));
 $wpdb->insert( $table_name_go_totals,array( 'uid' => $user_id, 'totalpoints' => 0 ),  array(  '%s' ) );
 update_user_meta($user_id,'go_rank', $new_rank);
 }
}	


//Deltes all rows related to a user in the individual and total tables upon deleting said user.
function go_user_delete($user_id){
 	global $wpdb;
	$table_name_go_totals = $wpdb->prefix . "go_totals";
	$table_name_go = $wpdb->prefix . "go";

	$wpdb->delete( $table_name_go_totals, array('uid'=> $user_id));
	$wpdb->delete( $table_name_go, array('uid'=> $user_id) );
	

}


?>