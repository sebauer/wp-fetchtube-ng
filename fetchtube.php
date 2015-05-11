<?php
/*
Plugin Name: FetchTube NG
Plugin URI: http://wordpress.org/extend/plugins/FetchTubeNG-ng/
Description: Allows you to include Youtube videos of your or any other channel with preview pcitures into the sidebar
Version: 0.1.0
License: GPL
Author: Sebastian Bauer, Christian Leo
Author URI: http://passiondriving.de
*/

include('lib/Youtube.php');
use Madcoda\Youtube;

if(!class_exists(FetchTubeNG)) {
  class FetchTubeNG {
    function version() {
      $this->version = '0.1';
    }

    function getSettings() {
      if(!get_option('FetchTubeNG_settings')) {
        $settings = array(
          'title' => 'My YouTube Channel',
          'apiKey' => '',
          'userId' => 'deftones',
          'typeOf' => 'uploads',
          'format' => 'json',
          'numberOfClips' => '5',
          'orderBy' => 'rating',
          'thumbWidth' => '160',
          'thumbHeight' => '120',
          'errorMsg' => 'Sorry, no videos were found.'
        );
      } else {
        $settings = get_option('FetchTubeNG_settings');
      }
      return $settings;
    }

    function setupWidget() {
      if (!function_exists('wp_register_sidebar_widget')) return;
      
      function widget_FetchTubeNG($args) {
        extract($args);
        $options = get_option('FetchTubeNG_widget');
        $title = $options['title'];
        echo $before_widget . $before_title . $title . $after_title;
        get_FetchTubeNG();
        echo $after_widget;
      }
      
      function widget_FetchTubeNG_control() {
        $options = get_option('FetchTubeNG_widget');
        
        if ( $_POST['FetchTubeNG-submit'] ) {
          $options['title'] = strip_tags(stripslashes($_POST['FetchTubeNG-title']));
          update_option('FetchTubeNG_widget', $options);
        }
        
        $title = htmlspecialchars($options['title'], ENT_QUOTES);
        $settingspage = trailingslashit(get_option('siteurl')).'wp-admin/options-general.php?page='.basename(__FILE__);
        
        echo '<p><label for="FetchTubeNG-title">Title:<input name="FetchTubeNG-title" type="text" value="'.$title.'" /></label></p>'.
        '<p>To control the other settings, please visit the <a href="'.$settingspage.'">FetchTubeNG Settings page</a>.</p>'.
        '<input type="hidden" id="FetchTubeNG-submit" name="FetchTubeNG-submit" value="1" />';
      }
      wp_register_sidebar_widget('FetchTubeNG', 'FetchTubeNG', 'widget_FetchTubeNG');
      wp_register_widget_control('FetchTubeNG', 'FetchTubeNG', 'widget_FetchTubeNG_control');
    }

    function setupSettingsPage() {
      if (function_exists('add_options_page')) {
        add_options_page('FetchTubeNG Settings', 'FetchTubeNG', 8, basename(__FILE__), array(&$this, 'printSettingsPage'));
      }
    }

    function printSettingsPage() {
      if (isset($_POST['save_FetchTubeNG_settings'])) {
        $temp = array('title','apiKey','userId','typeOf','format','numberOfClips','orderBy','thumbWidth','thumbHeight','errorMsg');
        foreach ($temp as $name) {
          $settings[$name] = $_POST['FetchTubeNG_'.$name];
        }
        update_option('FetchTubeNG_settings', $settings);
        echo '<div class="updated"><p>FetchTubeNG settings saved!</p></div>';
        
      } elseif (isset($_POST['reset_FetchTubeNG_settings'])) {
        delete_option('FetchTubeNG_settings');
        $settings = $this->getSettings();
        add_option('FetchTubeNG_settings',$settings);
        echo '<div class="updated"><p>FetchTubeNG settings restored to default!</p></div>';
      } else {
        $settings = get_option('FetchTubeNG_settings');
      }

      include ("FetchTubeNG-options.php");
    }

    function getClips() {
        
      $output = wp_cache_get('FetchTubeNGResults');
      if($output != false) return $output;

      $settings = $this->getSettings();
      $youtube = new Youtube(array('key' => $settings['apiKey']));
      $channel = $youtube->searchChannelVideos('', $settings['userId'], $settings['numberOfClips'], $settings['orderBy']);

      $output = ' <style>
        div.FetchTubeNG div {list-style-type: none;padding: 0px; margin: 0px; margin-bottom: 20px; }
        div.FetchTubeNGLi { position: relative; list-style-type: none;padding: 0px; margin: 0px; padding-bottom: 15px; }
        div.FetchTubeNGLi img { border: 1px solid grey; }
        span.FetchTubeNGTitle { position: absolute; max-width: 280px; top: 30px; }
        .FetchTubeNGTitle a { background-color: white; padding: 2px; }
        </style>';
      $output.= ' <div class="FetchTubeNG">';
      
      if(count($channel) > 0) {
        foreach($channel as $result) {
          $output.= '     <div class="FetchTubeNGLi">
          <span class="FetchTubeNGTitle"><a href="https://youtube.com/watch?v='.$result->id->videoId.'">'.$result->snippet->title.'</span>
                <img src="/timthumb.php?q=65&w='.$settings['thumbWidth'].'&h='.(($settings['thumbWidth']/16)*9-5).'&src='.urlencode($result->snippet->thumbnails->high->url).'" width="'.$settings['thumbWidth'].'" height="'.(($settings['thumbWidth']/16)*9-5).'" /></a>
            </div>';
        }

      } else {
        $output.= '<li>'.$settings['errorMsg'].'</div>';
      }
      $output.= '</div>';
      
      wp_cache_set('FetchTubeNGResults', $output, null, 1800);
      
      return $output;
    }
  }
}

$FetchTubeNG = new FetchTubeNG();
add_action( 'admin_menu', array(&$FetchTubeNG, 'setupSettingsPage') );
add_action( 'plugins_loaded', array(&$FetchTubeNG, 'setupWidget') );

function get_FetchTubeNG() {
  global $FetchTubeNG;
  echo $FetchTubeNG->getClips();
}
?>
