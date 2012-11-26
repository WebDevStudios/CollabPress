<?php
$url="http://plugins.webdevstudios.com/?plugin-lookup=collabpress";
$response = wp_remote_post( $url, array(
	'method' => 'POST',
	'timeout' => 45
    )
);
if( !is_wp_error( $response ) ) {
   $response_body = wp_remote_retrieve_body( $response );
   $plugins=json_decode( $response_body );
   //print_r( $plugins ); 
}
if($plugins){?>
<form method="post">
  <table class="form-table">
      <tr>
          <td colspan="2"><h3><?php _e( 'Premium Addons', 'collabpress' ); ?></h3><hr /></td>
      </tr>
      <?php foreach ($plugins as $plugin) { ?>
						
       <tr>
          <th scope="row" valign="top">
          <?php if(count($plugins)>0){?>
          <input type="checkbox" name="purchase_addons" value="<?php echo $plugin->plugin_slug;?>" />
          <?php }
		  echo $plugin->plugin_name;?>
          </th>
          <td valign="top">
          	User Key:<input type="text" style="width:300px;" />
            *Purchase this addon to receive a user key via email.
          </td>
      </tr>
      <?php } ?>
      <tr>
          <td colspan="2">
          	<input type="button" value="Purchase<?php if(count($plugins)>0){?> Checked<?php } ?>" class="button-primary"/>
            <input type="button" value="Validate & Install<?php if(count($plugins)>0){?> Checked<?php } ?>" class="button-primary"/>
            <hr />
          </td>
      </tr>
  </table>
</form>
<?php } ?>