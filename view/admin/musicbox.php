<div class="wrap" id="musicbox-admin">
    <h1><?php echo __('Manage Musicbox', $plugin_slug); ?></h1>
    <div id="message-container">
    <?php
    if( ! empty($message)):
        ?><div id="message" class="updated notice notice-success is-dismissible below-h1"><p><?php echo $message; ?></p></div>
    <?php endif; ?>
    </div>
    <div id="musicbox-wrapper" class="wrap">
        <div id="musicbox-top">   
            <?php if(!empty($data['musicboxes']) ): ?>
            <div id="select-musicbox">
                    <div class="trackprompt"></div>
                    <select name="musicbox" id="musicbox-select">
                        <option value=""><?php echo __("Select a musicbox", $plugin_slug); ?></option>
                        <?php foreach($data['musicboxes'] as $musicbox){ ?>
                        <option value="<?php echo $musicbox['musicbox']->id; ?>"><?php echo $musicbox['musicbox']->name; ?></option>
                        <?php } ?>
                    </select>
                    <button type="button" name="musicbox_addtrack_submit" id="musicbox-addtrack-submit"><?php _e("Submit", $plugin_slug); ?></button>
                    <button type="button" name="musicbox_addtrack_cancel" id="musicbox-addtrack-cancel"><?php _e("Cancel", $plugin_slug); ?></button>
            </div>
            <div id="message-add-container"></div>
            
            <div class="itunes-lookup">
                <div class="lookup-prompt"><?php echo __("Look up a song on iTunes to add to a music box below", $plugin_slug); ?>:</div>
                <form name="form-new-music-box">
                    <input type="text" name="term" id="text-term" placeholder="<?php echo __('Song Name', $plugin_slug); ?>" /> <button type="button" name="itunes-search" id="itunes-search"><?php echo __('Lookup', $plugin_slug); ?></button>
                </form>
                <div id="itunes-loading"></div>
                <div id="itunes-results"></div>
            </div>
            <?php endif; ?>
        </div>
        
       <?php if(!empty($data['musicboxes']) ): 
           $total_mb_count = count($data['musicboxes_list']);
           $mb_count = count($data['musicboxes']);
           ?>
        <h3 class="musicboxes-title">
            <?php 
            
            if( $mb_count > 1 ){
                $musicboxes_title = __("Musicboxes", $plugin_slug);
            }else{
                $musicboxes_title = __("Musicbox", $plugin_slug);
            }
            $display_title = $mb_count . " " . $musicboxes_title;
            if( ( $total_mb_count > 1 ) && ($mb_count < $total_mb_count) ) {
                $display_title = __("Showing", $plugin_slug) . " " . $mb_count . "/" . $total_mb_count . " " .  __("Musicboxes", $plugin_slug);
            }
            echo $display_title;
            ?></h3>
        <?php endif; ?>
        <div class="add-new">
                <a class="btn btn-primary" href="<?php echo \admin_url("admin.php?page=webdesignby-musicbox-edit"); ?>"><?php _e("Add New Musicbox"); ?></a>  
            </div>
         <?php if(!empty($data['musicboxes']) ): ?>
            <?php if( count($data['musicboxes_list']) > 1): ?>
            <div id="musicbox-filter-container">
                <?php _e("Filter musicboxes", $plugin_slug); ?>: 
                <select name="musicbox_filter">
                    <option value=""><?php _e('Show All'); ?></option>
                    <?php foreach($data['musicboxes_list'] as $musicbox){ ?>
                    <option value="<?php echo $musicbox['musicbox']->id; ?>" <?php if( $data['musicbox_filter'] == $musicbox['musicbox']->id ){?> selected="selected"<?php } ?>><?php echo $musicbox['musicbox']->name; ?></option>
                    <?php } ?>
                </select>
            </div>
            <?php endif; ?>
        <div id="musicbox-list"> 
            <?php
            // var_dump($data['music_boxes']);
            foreach($data['musicboxes'] as $musicbox){
                
                ?>
            
               <?php include("musicbox-content.php"); ?>
            
                <?php
            }
            ?>
        </div>
        <?php else: ?>
        <div class="start"><p><?php _e("Please start by adding a music box.", $plugin_slug); ?></p></div>
        <?php endif;?>
    </div>
</div>
<script>
function loadingStart(){
    jQuery("#itunes-loading").show();
}
function loadingDone(){
    jQuery("#itunes-loading").hide();
}
jQuery(document).ready(function(){
    
    // itunes lookup interactivity
    jQuery("#itunes-search").click(function(){
         if(jQuery("#text-term").val().length > 0){
            var artist_name = jQuery("#text-term").val();
            var data = {action: "itunes_search", params: artist_name };
            loadingStart();
            jQuery.get(ajaxurl, data, function(response){
                 musicbox_display_itunes_results(response);
             });
         }else{
             alert("<?php _e('Please enter something in the lookup field.', $plugin_slug); ?>");
         }
     });
     // add track click button interactivity
     jQuery("#musicbox-addtrack-submit").click(function(){ musicboxAddtrackSubmitClick(); });
     // sort musicboxes
     jQuery("#musicbox-list").sortable({
                update : function () {
                    var order = jQuery('#musicbox-list').sortable('serialize');
                    // console.log(order);
                    var data = {
                                action: "musicbox_sort", 
                                order: order
                            }
                    jQuery.post(ajaxurl, data, function(response){
                        // console.log(response);
                        if( response.error){
                            alert(response.message);
                        }else{
                            // sorting successful don't do anything
                            // document.location.reload(true);
                        }
                    });
                }
            });
            
            jQuery("#musicbox-filter-container select").change(function(){
                var selected = jQuery(this).val();
                jQuery("#musicbox-select").val(selected);
                jQuery("#select-musicbox").val(selected);
                window.location = "<?php echo \admin_url("admin.php?page=webdesignby-musicbox&webdesignby-musicbox-filter=");?>" + selected;
            });
            
           <?php if( ! empty( $data['musicbox_filter'] ) ) : ?>
            jQuery("#musicbox-select").val(<?php echo $data['musicbox_filter']; ?>) ;
            jQuery("#select-musicbox").val(<?php echo $data['musicbox_filter']; ?>);
           <?php endif;  ?>
               
        // enter key behavior
        jQuery("#text-term").keypress(function(event) {
            if (event.which == 13) {
                event.preventDefault();
                jQuery("#itunes-search").trigger("click");
            }
        });
     
});

function musicboxAddtrackSubmitClick(){  
    var selected_box = jQuery("#musicbox-select").val();
    var message_select_a_box = "<?php _e("Please select a musicbox", $plugin_slug);?>";
    var message_no_track_selected = "<?php _e("Please select a track", $plugin_slug); ?>";
    // console.log('selected_box = ' + selected_box);
    if( selected_box == "" ){
        alert(message_select_a_box);
        return false;
    }
    if( jQuery.isEmptyObject(musicbox_selected_track_info) ){
        alert(message_no_track_selected);
        return false;
    }
    var data = {
            action: "musicbox_addtrack", 
            musicbox_id: selected_box,
            track_info: musicbox_selected_track_info
        }
   
    jQuery.post(ajaxurl, data, function(response){
        // console.log(response);
        if( response.error){
            alert(response.message);
        }else{
            clearItunesResults();
            data = { 
                        action: "refresh_musicbox",
                        musicbox_id: selected_box,
                    }
            jQuery.post(ajaxurl, data, function(response){
                // console.log(response);
                if( response.error){
                    alert(response.message);
                }else{
                    jQuery("#musicbox_" + selected_box).html(response);
                    wp_flashMessage("<?php _e("Added to musicbox!", $plugin_slug); ?>");
                    // alert("<?php _e("Added to musicbox!", $plugin_slug); ?>");
                }
            });
            // document.location.reload(true);
        }
    });
}

function wp_flashMessage(message){
    var msg_container = jQuery("#message-add-container");
    jQuery(msg_container).empty();
    var msg_div = jQuery("<div />").attr("id", "message");
    // var msg_btn = jQuery("<button />").attr("type", "button").addClass("notice-dismiss");
    var msg_p = jQuery("<p />").html(message);
    jQuery(msg_div).addClass("notice notice-success is-dismissible below-h1 below-h2");
    jQuery(msg_div).append(msg_p);
    // jQuery(msg_div).append(msg_btn);
    jQuery(msg_container).append(msg_div);
    jQuery(msg_div).fadeIn();
    setTimeout(function(){wp_flashMessageFadeout(msg_div); }, 800);
}
function wp_flashMessageFadeout(msg_div){
    jQuery(msg_div).fadeOut();
}
function clearItunesResults(){
    var result_container = jQuery("#itunes-results");
    jQuery(result_container).empty();
}

var musicbox_selected_track_info = {};

function addToMusicbox(target){
    
    // console.log(target);
    var track_id = jQuery(target).data('track-id');
    var collection_name = jQuery(target).data('collection-name');
    var track_name = jQuery(target).data('track-name');
    var artist_id = jQuery(target).data('artist-id');
    var artist_name = jQuery(target).data('artist-name');
    var collection_id = jQuery(target).data('collection-id');
    var collection_censored_name = jQuery(target).data('collection-censored-name');
    var track_censored_name = jQuery(target).data('track-censored-name');
    var artist_view_url = jQuery(target).data('artist-view-url');
    var track_view_url = jQuery(target).data('track-view-url');
    var collection_view_url = jQuery(target).data('collection-view-url');
    var preview_url = jQuery(target).data('preview-url');
    var artworkUrl60 = jQuery(target).data('artwork-url-60');
    var artworkUrl100 = jQuery(target).data('artwork-url-100');
    
    musicbox_selected_track_info = { 
                    track_id: track_id,
                    track_name: track_name,
                    artist_id: artist_id,
                    artist_name: artist_name, 
                    collection_name: collection_name,
                    collection_id: collection_id,
                    collection_censored_name: collection_censored_name,
                    track_censored_name: track_censored_name,
                    artist_view_url: artist_view_url,
                    track_view_url: track_view_url,
                    collection_view_url: collection_view_url,
                    preview_url: preview_url,
                    artworkUrl60: artworkUrl60,
                    artworkUrl100: artworkUrl100
                };
                
    jQuery("#select-musicbox .trackprompt").html("<?php _e("Add", $plugin_slug); ?> <span class=\"trackname\">" + track_name + "</span> <?php _e("to", $plugin_slug); ?>: ");
    if( jQuery("#select-musicbox").val() == ""){
        jQuery("#select-musicbox").show();
        jQuery('html, body').animate({
            scrollTop: jQuery("#select-musicbox").offset().top - 50
        }, 1000);
    }else{
        musicboxAddtrackSubmitClick();
    }
}

function musicbox_display_itunes_results(response){
    var result_container = jQuery("#itunes-results");
    jQuery(result_container).empty();
    var count = response.resultCount;
    var ul = jQuery("<ul />");
    jQuery(ul).addClass("results");
    var li_labels = jQuery("<li />");
    jQuery(li_labels).addClass('labels');
    var container_song = jQuery("<div />");
    
    var container_artist = jQuery("<div />");
    var container_collection = jQuery("<div />");
    var container_actions = jQuery("<div />");
    
    jQuery(container_song).addClass('song');
    jQuery(container_artist).addClass('artist');
    jQuery(container_collection).addClass('collection');
    jQuery(container_actions).addClass('action');
    jQuery(container_song).html("<?php echo __('Song', $plugin_slug); ?>");
    jQuery(container_artist).html("<?php echo __('Artist', $plugin_slug); ?>");
    jQuery(container_collection).html("<?php echo __('Collection', $plugin_slug); ?>");
    jQuery(container_actions).html("<?php echo __('Actions', $plugin_slug); ?>");
    jQuery(li_labels).append(container_song);
    jQuery(li_labels).append(container_artist);
    jQuery(li_labels).append(container_collection);
    jQuery(ul).append(li_labels);
    jQuery(result_container).append(ul);
    for(i=0; i<count; i++){
        var li = jQuery("<li />");
        var item = response.results[i];
        // console.log(item);
        jQuery(li).addClass(item.wrapperType);
        jQuery(li).html("<div class=\"debug\">" + item.collectionId + " - " + item.kind + " - " + item.collectionName + "</div>");
        var container_song= jQuery("<div />");
        var url_song = jQuery("<a />");
        jQuery(url_song).attr("href", item.trackViewUrl).attr("target", "_blank");
        var container_artist = jQuery("<div />");
        var container_collection = jQuery("<div />");
        var container_actions = jQuery("<div />");
        var add_click = jQuery("<a />");
        jQuery(add_click).attr("data-track-id", item.trackId);
        jQuery(add_click).attr("data-collection-name", item.collectionName);
        jQuery(add_click).attr("data-track-name", item.trackName);
        jQuery(add_click).attr("data-artist-name", item.artistName);
        jQuery(add_click).attr("data-artist-id", item.artistId);
        jQuery(add_click).attr("data-collection-id", item.collectionId);
        jQuery(add_click).attr("data-track-censored-name", item.trackCensoredName);
        jQuery(add_click).attr("data-collection-censored-name", item.collectionCensoredName);
        jQuery(add_click).attr("data-artist-view-url", item.artistViewUrl);
        jQuery(add_click).attr("data-collection-view-url", item.collectionViewUrl);
        jQuery(add_click).attr("data-track-view-url", item.trackViewUrl);
        jQuery(add_click).attr("data-preview-url", item.previewUrl);
        jQuery(add_click).attr("data-artwork-url-100", item.artworkUrl100);
        jQuery(add_click).attr("data-artwork-url-60", item.artworkUrl60);
        jQuery(add_click).attr("href", "javascript:;");
        jQuery(add_click).on('click', function(event){addToMusicbox(event.target);});
        jQuery(add_click).html("<?php _e("+ add to musicbox", $plugin_slug); ?>");
        jQuery(container_actions).append(add_click);
        jQuery(container_song).addClass('song');
        jQuery(container_song).css('background-image', 'url(' + item.artworkUrl60 + ')');
        var track_name = jQuery("<span />");
        jQuery(track_name).html(item.trackName);
        jQuery(url_song).append(track_name);
        jQuery(container_song).append(url_song);
        jQuery(container_artist).addClass('artist');
        jQuery(container_collection).addClass('collection');
        jQuery(container_actions).addClass('action');
        jQuery(container_artist).html(item.artistName);
        jQuery(container_collection).html(item.collectionName);
        jQuery(li).append(container_song);
        jQuery(li).append(container_artist);
        jQuery(li).append(container_collection);
        jQuery(li).append(container_actions);
        // jQuery(li).html(response.results[i]);
        jQuery(ul).append(li);
    }
    
    loadingDone();
}
</script>