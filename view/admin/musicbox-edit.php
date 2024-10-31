<?php
// $opt = get_option($plugin_key);

if( ! empty($message))
    echo $message;

$name = $description = $id = "";
if( isset($musicbox) ){
    $name = $musicbox['name'];
    $description = $musicbox['description'];
    $id = $musicbox['id'];
}

if( ! empty($id) ){
    $action = "update";
    $form_title = __("Update Music Box", $plugin_slug);
}else{
    $action = "insert";
    $form_title = __("Add Music Box", $plugin_slug);
}

?>
<h1><?php echo $form_title ?></h1>
<div id="musicbox-wrapper" class="wrap">
    <div id="musicbox-top">
        <div class="add-new">
            <div class="new-music-box">
                <form name="form-new-music-box" method="POST">
                    <input type="hidden" name="action" value="<?php echo $action; ?>" />
                    <input type="hidden" name="id" value="<?php echo $id; ?>" />
                    <?php wp_nonce_field( "musicbox_" .  $action );?>
                    <div class="form-group">
                       
                        <input type="text" class="big-title" name="name" id="input-musicbox-name" value="<?php echo $name; ?>" placeholder="<?php echo __('Muicbox Name', $plugin_slug); ?>" />
                    </div>
                    <div class="form-group">
                         <label for="name"><?php _e("Description", $plugin_slug); ?>:</label>
                         <textarea class="input-description" id="input-musicbox-description" name="description"><?php echo $description; ?></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="save" id="btn-save-musicbox"><?php echo __('Save', $plugin_slug); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>