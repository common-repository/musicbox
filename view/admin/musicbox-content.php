<?php
$id = $musicbox['musicbox']->id;
$url_edit = \wp_nonce_url(\admin_url("admin.php?page=" . $this->page_slug . "-edit&action=edit&id=" . $id), 'musicbox_edit');
$url_delete = \wp_nonce_url(\admin_url("admin.php?page=" . $this->page_slug . "&action=delete&id=" . $id), 'musicbox_delete');
?>
<div class="musicbox" id="musicbox_<?php echo $id; ?>">
    <div class="titlebar">
                    <span class="id"><label><?php echo __("musicbox id ", $plugin_slug); ?>: </label> <a href="<?php echo $url_edit; ?>"><?php echo $musicbox['musicbox']->id; ?></a></span>
                     <span class="date"><label><?php echo __("added ", $plugin_slug); ?>:</label> <?php echo date("n/j/y", strtotime($musicbox['musicbox']->added)); ?></span>
                     
                    <div class="name"><a href="<?php echo $url_edit; ?>"><?php echo $musicbox['musicbox']->name; ?></a></div>
                    <div class="controls">
                        <a href="<?php echo $url_delete;  ?>" onclick="return confirm('<?php _e('Are you sure you want to delete this musicbox?', $plugin_slug);?>');"><span class="dashicons dashicons-trash"><?php _e('delete', $plugin_slug); ?></span></a>
                        <a href="<?php echo $url_edit; ?>"><span class="dashicons dashicons-edit"><?php _e("edit", $plugin_slug); ?></span></a>
                    </div>
                </div>
                <?php if(!empty($musicbox['tracks'])): ?>
                <div class="tracks" id="tracks<?php echo $id; ?>">
                    <span class="tracknum"><?php echo count($musicbox['tracks']) . " " . __("tracks", $this->page_slug); ?></span>
                    <ul>
                    <?php 
                    
                    foreach($musicbox['tracks'] as $track): 
                        $url_delete_track = \wp_nonce_url(\admin_url("admin.php?page=" . $this->page_slug . "&action=delete-track&id=" . $track->id), 'musicbox_delete-track');
                        $url_itunes_affiliate = $track->trackViewUrl . "&at=" . $musicbox['itunes_affiliate_id'];
                        ?>
                        <li id="track_<?php echo $track->id; ?>"><div class="track-thumb" style="background-image:url('<?php  echo $track->artworkUrl60; ?>');"><a href="<?php echo $url_itunes_affiliate; ?>" target="_blank"></a></div><div class="track-info"><span class="name"><a href="<?php echo $url_itunes_affiliate; ?>" target="_blank"><?php echo $track->trackName; ?></a></span> &mdash; <span class="artist"><?php echo $track->artistName; ?></span>: <span class="collection"><?php echo $track->collectionName; ?></span> <?php ?> <a href="<?php echo $url_delete_track;  ?>" onclick="return confirm('<?php _e('Are you sure you want to remove this track?', $plugin_slug);?>');"><span class="dashicons dashicons-trash"><?php _e('delete', $plugin_slug); ?></span></a></div></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
    
                <script>
                 
                jQuery(document).ready(function(){
                    // sort tracks
                    jQuery("#tracks<?php echo $id; ?> ul").sortable({
                    update : function () {
                        var order = jQuery("#tracks<?php echo $id; ?> ul").sortable('serialize');
                        // console.log(order);
                        var data = {
                                    action: "musicbox_tracks_sort", 
                                    musicbox_id: <?php echo (int) $id; ?>,
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
                    
                    // collapsable
                    jQuery("#tracks<?php echo $id; ?>").accordion({
                        collapsible: true
                      });

                });
               </script>
                <?php else: ?>
                <div class="notracks tracks">[ <?php echo __("No tracks in this musicbox", $plugin_slug); ?> ]</div>
                <?php endif; ?>
</div>