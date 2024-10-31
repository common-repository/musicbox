<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// base functionality
require_once( \plugin_basename( '/lib/Base.php') );
require_once( \plugin_basename( '/lib/Registry.php') );
require_once( \plugin_basename( '/lib/View.php') );
require_once( \plugin_basename( '/lib/AdminPageBase.php') );
require_once( \plugin_basename( '/lib/AdminPage.php') );
require_once( \plugin_basename( '/lib/OptionsPage.php') );

// model used by plugins
require_once( \plugin_basename( '/lib/MusicboxModel.php') );
// extend the base with spefic plugin functionality {PluginName}Plugin.php
require_once( \plugin_basename( '/lib/MusicboxPlugin.php') );

// special tools & helpers
require_once( \plugin_basename( '/lib/iTunesInfo.php') );