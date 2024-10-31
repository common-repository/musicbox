<?php
// var_dump($musicbox);
$id = $musicbox['musicbox']->id;
$current_page = 1;
?>
<div class="musicbox" id="musicbox_<?php echo $id; ?>">
    <div class="titlebar"><div class="name"><h3 class="widget-title"><?php echo $musicbox['musicbox']->name; ?></h3></div></div>
    <?php if( $description ): ?>
    <div class="description"><?php echo $musicbox['musicbox']->description; ?></div>
    <?php endif; ?>
        <?php if(!empty($musicbox['tracks'])): ?>
                <div class="tracks" id="musicbox_<?php echo $id; ?>_tracks<?php echo $id; ?>">
                    <ul id="musicbox_<?php echo $id; ?>_tracks_page<?php echo $current_page; ?>">
                    <?php
                    $total_tracks = count($musicbox['tracks']);
                    $pages = ceil( $total_tracks / $tracks_perpage);  
                    $i = 1;
                    foreach($musicbox['tracks'] as $track):
                        if( $autoplay && ($i == 1) ){
                            ?>
                        <script>
                        if(!musicbox_autoplay_tracks){
                            var musicbox_autoplay_tracks = [];
                        }
                        musicbox_autoplay_tracks.push("track_<?php echo $track->id; ?>_sound");
                        </script>
                            <?php
                        }
                    ?>
                    <?php 
                    
                     
                        $url_itunes_affiliate = $track->trackViewUrl . "&at=" . $musicbox['itunes_affiliate_id'];
                        $url_itunes_collection = $track->collectionViewUrl . "&at=" . $musicbox['itunes_affiliate_id'];
                        $url_itunes_artist = $track->artistViewUrl . "&at=" . $musicbox['itunes_affiliate_id'];
                        ?>
                        <li id="track_<?php echo $track->id; ?>">
                            <div class="track-thumb" style="background-image:url('<?php  echo $track->artworkUrl60; ?>');"><div class="ui360 player">
                                <a id="track_<?php echo $track->id; ?>_sound" href="<?php echo $track->previewUrl; ?>"></a>
                                </div></div><div class="track-info"><span class="name"><a href="<?php echo $url_itunes_affiliate; ?>" target="_blank"><?php echo $track->trackName; ?></a></span><span class="artist"><a href="<?php echo $url_itunes_artist; ?>" target="_blank"><?php echo $track->artistName; ?></a></span>: <span class="collection"><a href="<?php echo $url_itunes_collection; ?>" target="_blank"><?php echo $track->collectionName; ?></a></span> <?php ?></div>
                             
                        </li>
                        
                        <script>
                            var musicbox_dir_url = "<?php echo $plugin_dir_url; ?>";
                           <?php if( $autoplay ): ?>
                            var musicbox_autoplay = true;
                            <?php endif; ?>
                        </script>
                    <?php 
                    
                    if( $i >= ($current_page * $tracks_perpage)  ):
                        $current_page ++;
                        ?></ul><ul id="musicbox_<?php echo $id; ?>_tracks_page<?php echo $current_page; ?>"><?php endif;
                    $i++;
                    endforeach; ?>
                    </ul>   
                </div>
                <?php if( $pages > 1 ): ?>
                <div class="musicbox_pagination">
                    <?php _e("page", $this->text_domain); ?>:
                    <ul>
                    <?php
                    for($i=1; $i<=$pages; $i++){
                    ?>
                        <li class="page_<?php echo $i; ?>"><a href="javascript:;" onclick="musicboxPage('<?php echo $i; ?>', '<?php echo $pages ?>', '<?php echo $id; ?>');"><?php echo $i; ?></a></li>
                    <?php } ?>
                    </ul>
                </div>
                <script>
                jQuery(document).ready(function(){
                    // hide pages
                    for(i=2; i<=<?php echo $pages; ?>; i++){
                        jQuery("#musicbox_<?php echo $id; ?>_tracks_page" + i).hide();
                    }
                    jQuery("#musicbox_<?php echo $id; ?> .musicbox_pagination ul li.page_1").addClass('current');
                });
                </script>
                <?php endif; ?>

        <?php else: ?>
                  <div class="notracks tracks">[ <?php echo __("No tracks in this musicbox", $plugin_slug); ?> ]</div>
        <?php endif; ?>
                  
</div>