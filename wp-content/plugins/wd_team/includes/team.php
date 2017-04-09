<?php
global $post;
wp_nonce_field( 'wd_team_box', 'wd_team_box_nonce' );
?>

<label>Role</label>
<input type="text" name="member_role" value="<?php echo get_post_meta($post->ID,'wd_member_role',true);?>"/><br/>

<label>Email</label>
<input type="text" name="member_email" value="<?php echo get_post_meta($post->ID,'wd_member_email',true);?>"/><br/>

<label>Phone</label>
<input type="text" name="member_phone" value="<?php echo get_post_meta($post->ID,'wd_member_phone',true);?>"/><br/>

<label>Profile Link</label>
<input type="text" name="member_link" value="<?php echo get_post_meta($post->ID,'wd_member_link',true);?>"/><br/>

<!--<label>Social Type:</label>-->
<?php 
$social = array('facebook','twitter','pinterest','google','linkedln','dribble'); 
$my_social = get_post_meta($post->ID,'wd_member_social',true);
$rs = '';
foreach($social as $scl){
	$select = '';
	if($my_social == $scl)
		$select = 'selected';
	$rs = $rs.	'<option '.$select.' value="'.$scl.'" >'.$scl.'</option>';
}
?>

<label>Facebook Link</label>
<input type="text" name="member_facebook_link" value="<?php echo get_post_meta($post->ID,'wd_member_facebook_link',true);?>"/><br/>
<label>Twitter Link</label>
<input type="text" name="member_twitter_link" value="<?php echo get_post_meta($post->ID,'wd_member_twitter_link',true);?>"/><br/>
<label>RSS Link</label>
<input type="text" name="member_rss_link" value="<?php echo get_post_meta($post->ID,'wd_member_rss_link',true);?>"/><br/>
<label>Google+ Link</label>
<input type="text" name="member_google_link" value="<?php echo get_post_meta($post->ID,'wd_member_google_link',true);?>"/><br/>
<label>Linkedln Link</label>
<input type="text" name="member_linkedlin_link" value="<?php echo get_post_meta($post->ID,'wd_member_linkedlin_link',true);?>"/><br/>
<label>Dribble Link</label>
<input type="text" name="member_dribble_link" value="<?php echo get_post_meta($post->ID,'wd_member_dribble_link',true);?>"/><br/>
<label>Vimeo Link</label>
<input type="text" name="member_vimeo_link" value="<?php echo get_post_meta($post->ID,'wd_member_vimeo_link',true);?>"/><br/>