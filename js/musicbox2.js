function musicboxPage(page, pages, id){
    for(i=1; i<=pages; i++){
        jQuery("#musicbox_" + id + "_tracks_page" + i).hide();
        jQuery("#musicbox_" + id + " .musicbox_pagination ul li").removeClass('current');
    }
    jQuery("#musicbox_" + id + " .musicbox_pagination ul li.page_" + page).addClass('current');
    jQuery("#musicbox_" + id + "_tracks_page" + page).show();
}
if( ! musicbox_autoplay ){
    var musicbox_autoplay = false;
}
if(!musicbox_autoplay_tracks){
    var musicbox_autoplay_tracks = [];
}
function musicboxAutoplay(){
     if(musicbox_autoplay_tracks){
        for(i=0; i<musicbox_autoplay_tracks.length; i++){
            // TODO: can we autplay the tracks??
        }
    }
}

jQuery(document).ready(function(){
        soundManager.setup({
          // path to directory containing SM2 SWF
          url: musicbox_dir_url + 'swf/',
           onready: function() {
                musicboxAutoplay();
            }
        });
        if(musicbox_autoplay){
            threeSixtyPlayer.config = {
                autoPlay: true, // start playing the first sound right away
            }
        }
        threeSixtyPlayer.config = {
            autoPlay: musicbox_autoplay,
            playNext: true,   // stop after one sound, or play through list until end
            allowMultiple: false,  // let many sounds play at once (false = only one sound playing at a time)
            loadRingColor: '#ccc', // how much has loaded
            playRingColor: '#000', // how much has played
            backgroundRingColor: '#eee', // color shown underneath load + play ("not yet loaded" color)

            // optional segment/annotation (metadata) stuff..
            segmentRingColor: 'rgba(255,255,255,0.33)', // metadata/annotation (segment) colors
            segmentRingColorAlt: 'rgba(0,0,0,0.1)',
            loadRingColorMetadata: '#ddd', // "annotations" load color
            playRingColorMetadata: 'rgba(128,192,256,0.9)', // how much has played when metadata is present

            circleDiameter: null, // set dynamically according to values from CSS
            circleRadius: null,
            animDuration: 500,
            animTransition: window.Animator.tx.bouncy, // http://www.berniecode.com/writing/animator.html
            showHMSTime: false, // hours:minutes:seconds vs. seconds-only
            scaleFont: true,  // also set the font size (if possible) while animating the circle

            // optional: spectrum or EQ graph in canvas (not supported in IE <9, too slow via ExCanvas)
            useWaveformData: false,
            waveformDataColor: '#0099ff',
            waveformDataDownsample: 3, // use only one in X (of a set of 256 values) - 1 means all 256
            waveformDataOutside: false,
            waveformDataConstrain: false, // if true, +ve values only - keep within inside circle
            waveformDataLineRatio: 0.64,

            // "spectrum frequency" option
            useEQData: false,
            eqDataColor: '#339933',
            eqDataDownsample: 4, // use only one in X (of 256 values)
            eqDataOutside: true,
            eqDataLineRatio: 0.54,

            // enable "amplifier" (canvas pulses like a speaker) effect
            usePeakData: true,
            peakDataColor: '#ff33ff',
            peakDataOutside: true,
            peakDataLineRatio: 0.5,

            useAmplifier: true, // "pulse" like a speaker

            fontSizeMax: null, // set according to CSS

            scaleArcWidth: 1,  // thickness factor of playback progress ring

            useFavIcon: false // Experimental (also requires usePeakData: true).. Try to draw a "VU Meter" in the favicon area, if browser supports it (Firefox + Opera as of 2009)

    }

});