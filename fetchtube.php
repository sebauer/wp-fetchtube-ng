<?php
/*
Plugin Name: fetchTube NG
Plugin URI: http://wordpress.org/extend/plugins/fetchtube-ng/
Description: Allows you to include Youtube videos of your or any other channel with preview pcitures into the sidebar
Version: 0.1.0
License: GPL
Author: Sebastian Bauer, Christian Leo
Author URI: http://passiondriving.de
*/

include('lib/Youtube.php');
use Madcoda\Youtube;

if(!class_exists(fetchTube)) {
  class fetchTube {
    function version() {
      $this->version = '0.1';
    }

    function getSettings() {
      if(!get_option('fetchtube_settings')) {
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
        $settings = get_option('fetchtube_settings');
      }
      return $settings;
    }

    function setupWidget() {
      if (!function_exists('wp_register_sidebar_widget')) return;
      
      function widget_fetchtube($args) {
        extract($args);
        $options = get_option('fetchtube_widget');
        $title = $options['title'];
        echo $before_widget . $before_title . $title . $after_title;
        get_fetchTube();
        echo $after_widget;
      }
      
      function widget_fetchtube_control() {
        $options = get_option('fetchtube_widget');
        
        if ( $_POST['fetchtube-submit'] ) {
          $options['title'] = strip_tags(stripslashes($_POST['fetchtube-title']));
          update_option('fetchtube_widget', $options);
        }
        
        $title = htmlspecialchars($options['title'], ENT_QUOTES);
        $settingspage = trailingslashit(get_option('siteurl')).'wp-admin/options-general.php?page='.basename(__FILE__);
        
        echo '<p><label for="fetchtube-title">Title:<input name="fetchtube-title" type="text" value="'.$title.'" /></label></p>'.
        '<p>To control the other settings, please visit the <a href="'.$settingspage.'">fetchTube Settings page</a>.</p>'.
        '<input type="hidden" id="fetchtube-submit" name="fetchtube-submit" value="1" />';
      }
      wp_register_sidebar_widget('fetchtube', 'fetchTube', 'widget_fetchtube');
      wp_register_widget_control('fetchtube', 'fetchTube', 'widget_fetchtube_control');
    }

    function setupSettingsPage() {
      if (function_exists('add_options_page')) {
        add_options_page('fetchTube Settings', 'fetchTube', 8, basename(__FILE__), array(&$this, 'printSettingsPage'));
      }
    }

    function printSettingsPage() {
      if (isset($_POST['save_fetchtube_settings'])) {
        $temp = array('title','apiKey','userId','typeOf','format','numberOfClips','orderBy','thumbWidth','thumbHeight','errorMsg');
        foreach ($temp as $name) {
          $settings[$name] = $_POST['fetchtube_'.$name];
        }
        update_option('fetchtube_settings', $settings);
        echo '<div class="updated"><p>fetchTube settings saved!</p></div>';
        
      } elseif (isset($_POST['reset_fetchtube_settings'])) {
        delete_option('fetchtube_settings');
        $settings = $this->getSettings();
        add_option('fetchtube_settings',$settings);
        echo '<div class="updated"><p>fetchTube settings restored to default!</p></div>';
      } else {
        $settings = get_option('fetchtube_settings');
      }

      include ("fetchtube-options.php");
    }

    function getClips() {
        
      $output = wp_cache_get('fetchTubeResults');
      if($output != false) return $output;

      $settings = $this->getSettings();
      $youtube = new Youtube(array('key' => $settings['apiKey']));
      $channel = $youtube->searchChannelVideos('', $settings['userId'], $settings['numberOfClips'], $settings['orderBy']);

      $output = ' <style>
        div.fetchTube div {list-style-type: none;padding: 0px; margin: 0px; margin-bottom: 20px; }
        div.fetchTubeLi { position: relative; list-style-type: none;padding: 0px; margin: 0px; padding-bottom: 15px; }
        div.fetchTubeLi img { border: 1px solid grey; }
        span.fetchTubeTitle { position: absolute; max-width: 280px; top: 30px; }
        .fetchTubeTitle a { background-color: white; padding: 2px; }
        </style>';
      $output.= ' <div class="fetchTube">';
      
      if(count($channel) > 0) {
        foreach($channel as $result) {
          $output.= '     <div class="fetchTubeLi">
          <span class="fetchTubeTitle"><a href="https://youtube.com/watch?v='.$result->id->videoId.'">'.$result->snippet->title.'</span>
                <img src="/timthumb.php?q=65&w='.$settings['thumbWidth'].'&h='.(($settings['thumbWidth']/16)*9-5).'&src='.urlencode($result->snippet->thumbnails->high->url).'" width="'.$settings['thumbWidth'].'" height="'.(($settings['thumbWidth']/16)*9-5).'" /></a>
            </div>';
        }
        wp_cache_set('fetchTubeResults', $output, null, 1800);

      } else {
        $output.= '<li>'.$settings['errorMsg'].'</div>';
      }
      $output.= '</div>';

      return $output;
    }
  }
}

$fetchTube = new fetchTube();
add_action( 'admin_menu', array(&$fetchTube, 'setupSettingsPage') );
add_action( 'plugins_loaded', array(&$fetchTube, 'setupWidget') );

function get_fetchTube() {
  global $fetchTube;
  echo $fetchTube->getClips();
}
?>
