<?php
/*
Plugin Name: Shortcode Master
Description: Shortcode Master is a powerful tool that effortlessly injects custom shortcodes into all WordPress posts and pages. With seamless integration, users can enhance content presentation and functionality across the entire website.
Version: 1.0
Author: Vishal Hambir
*/
function shortcode_to_for_post($content){
  //shortcode for post
  $post_short_code_from_database = get_option('shortcode_post_option_name');
  $shortcode_post = '';
  if ($post_short_code_from_database != null) {
    $postshortcodeArr = explode(',', $post_short_code_from_database);
    foreach ($postshortcodeArr as $scr) {
      if ($scr == null) {
        continue;
      }
      $shortcode_post .= trim($scr);
    }
  }

  //shortcode for page
  $page_short_code_from_database = get_option('shortcode_page_option_name');
  $shortcode_page = '';
  if ($page_short_code_from_database != null) {
    $pageshortcodeArr = explode(',', $page_short_code_from_database);
    foreach ($pageshortcodeArr as $scr) {
      if ($scr == null) {
        continue;
      }
      $shortcode_page .=trim($scr);
    }
  }

  global $post;
  if (!$post instanceof WP_Post) return $content;
  switch ($post->post_type) {
    case 'post':
      return $content . $shortcode_post;

    case 'page':
      return $content . $shortcode_page;

    default:
      return $content;
  }
}

add_filter('the_content', 'shortcode_to_for_post');

function myplugin_register_settings(){
  register_setting('shortcode_master_options_group', 'shortcode_post_option_name', 'myplugin_callback');
  register_setting('shortcode_master_options_group', 'shortcode_page_option_name', 'myplugin_callback');
}
add_action('admin_init', 'myplugin_register_settings');

function shortcode_master_register_options_page(){
  add_options_page('Page Title', 'Shortcode Master', 'manage_options', 'shortcode-master', 'shortcode_master_options_page');
}
add_action('admin_menu', 'shortcode_master_register_options_page');


function shortcode_master_options_page(){
?>
<div class="ecss">
    <h1 style="text-shadow: 1px 3px 10px rgba(0, 0, 0, 0.5);">Shortcode-Master</h1>
</div>
  <div class="main-cls">

    <form method="post" action="options.php">
      <?php settings_fields('shortcode_master_options_group'); ?>

      <style>
      
        .colmd {
          margin-top: 30px;
        }

        .main-cls {
          margin-bottom: 25px;
          margin-left: -20px;
          background: white;
          padding: 10px;
        }

        textarea {
          display: block;
          z-index: 2;
          border: 2px solid #dde4e9;
          border-radius: 0;
          color: #444;
          overflow: auto;
          resize: none;
          transition: transform 1s;
          width: 90%;
          background-color: white;
          height: 140px;
          border-left: 50px solid #dde4e9;
          padding: 10px;
          font: 17px/28px 'Open Sans', sans-serif;
          letter-spacing: 1px;
        
        }

        .ecss {
          margin-bottom: 25px;
        }
      </style>
      <div class="container" style="margin:0px auto;margin-left: 20px;">
      <div style="margin:0px auto;">
        <div class="colmd">
          <h3>Shortcode For Post</h3>
          <textarea id="shortcode_post_option_name" name="shortcode_post_option_name" placeholder="e.g. [externalJS],[externalCSS]"><?php echo get_option('shortcode_post_option_name'); ?></textarea>
        </div>
        <div class="colmd">
          <h3>Shortcode For Pages</h3>
          <textarea id="shortcode_page_option_name" name="shortcode_page_option_name" placeholder="e.g. [externalJS],[externalCSS]"><?php echo get_option('shortcode_page_option_name'); ?></textarea>
        </div>
        <p><span style="font-weight: bold;font-size:16px">Usage - </span> Add your multiple shortcodes as comma separated.</p>

        <?php submit_button(); ?>
      </div>
      </div>

    </form><br>

  </div>
<?php
}

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'add_action_links' );

function add_action_links ( $links ) {
 $mylinks = array(
 '<a href="' . admin_url( 'options-general.php?page=shortcode-master' ) . '">Settings</a>',
 );
return array_merge( $links, $mylinks );
}
