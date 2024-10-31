<?php
 if( ! empty($_POST) ){
    check_admin_referer( 'process' );
    update_option($plugin_key, $_POST[$plugin_key]);
    $message = "<div id=\"setting-error-settings_updated\" class=\"updated settings-error\"> 
                <p><strong>" . __('Settings saved', 'webdesignby-musicbox') . "</strong></p></div>";
}
$opt = get_option($plugin_key);

if( ! empty($message))
    echo $message;
?>
<h1><?php echo __($page_title, $option_key); ?></h1>
<div id="musicbox-wrapper" class="wrap">
   <form name="form" action="" method="post">
<?php echo wp_nonce_field('process'); ?>
<table class="form-table">
    <tbody>
        <tr>
            <th><label for="<?php echo $plugin_key; ?>[itunes_affiliate_id]"><?php echo __('iTunes Affiliate ID', 'webdesignby-musicbox'); ?>:</label></th>
            <td><input name="<?php echo $plugin_key; ?>[itunes_affiliate_id]" id="field1_name" type="text" class="regular-text code" value="<?php echo trim($opt['itunes_affiliate_id']); ?>" /></td>
        </tr>
    </tbody>
</table>
<p class="submit">
    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Save Changes', 'webdesignby-musicbox'); ?>">
</p>
</form>
</div>