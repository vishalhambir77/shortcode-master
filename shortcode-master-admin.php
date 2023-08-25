<?php
/*
Plugin Name: Shortcode Master
Description: Shortcode Master is a powerful tool that effortlessly create shortcode & injects custom shortcodes into all WordPress posts and pages. With seamless integration, users can enhance content presentation and functionality across the entire website.
Version: 1.0
Author: Vishal Hambir
*/


// Enqueue the JavaScript file
add_action('admin_enqueue_scripts', 'shortcode_master_enqueue_admin_scripts');
function shortcode_master_enqueue_admin_scripts(){
  wp_enqueue_script('my-plugin-admin-script', plugin_dir_url(__FILE__) . 'shortcode-master-admin.js', array('jquery'), '1.0', true);
}


function get_php_files_in_directory($directory){
  $php_files = array();

  if (is_dir($directory)) {
    $files = scandir($directory);

    foreach ($files as $file) {
      if (pathinfo($file, PATHINFO_EXTENSION) === 'php' && $file != 'index.php' && $file != 'function.php' && $file != 'functions.php') {
        $php_files[] = $file;
      }
    }
  }

  return $php_files;
}

// Callback function to render the settings page
function shortcode_master_options_page(){
  if (!current_user_can('manage_options')) {
    return;
  }

  if (isset($_POST['save_shortcode'])) {
    $selected_page_id = $_POST['template_page'];
    $shortcode = 'custom_template_page_shortcode_' . $selected_page_id;
    add_option('custom_template_shortcode_' . $selected_page_id, $shortcode);

    if (isset($_POST['add_page_shortcode'])) {
      $page_short_code_from = get_option('shortcode_page_option_name');
      if (strpos($page_short_code_from, $shortcode) == false) {
        if ($page_short_code_from == '' ||   $page_short_code_from == null) {
          $append_shortcode = '[' . $shortcode . ']';
        } else {
          $append_shortcode = ',[' . $shortcode . ']';
        }
        update_option('shortcode_page_option_name', $page_short_code_from . $append_shortcode);
      }
    }

    if (isset($_POST['add_post_shortcode'])) {
      $page_short_code_from = get_option('shortcode_post_option_name');
      if (strpos($page_short_code_from, $shortcode) == false) {
        if ($page_short_code_from == '' ||   $page_short_code_from == null) {
          $append_shortcode = '[' . $shortcode . ']';
        } else {
          $append_shortcode = ',[' . $shortcode . ']';
        }
        update_option('shortcode_post_option_name', $page_short_code_from . $append_shortcode);
      }
    }
  }

  if (isset($_POST['delete_shortcode'])) {
    $selected_page_id = $_POST['short_id'];

    // remove page shortcode
    $page_short_code_ = get_option('shortcode_page_option_name');
    if ($page_short_code_ != '' ||   $page_short_code_ != null) {
      $array = explode(",", $page_short_code_);
      $newArray = array();
      $scode='[custom_template_page_shortcode_'.$selected_page_id.']';
      foreach ($array as $item) {
          if ($item !== $scode) {
              $newArray[] = $item;
          }
      }
        $modifiedString = implode(",", $newArray);
        update_option('shortcode_page_option_name',$modifiedString);
    }

    // remove post shortcode
    $post_short_code_ = get_option('shortcode_post_option_name');
    if ($post_short_code_ != '' ||   $post_short_code_ != null) {
      $array_post = explode(",", $post_short_code_);
      $newArray_post = array();
      $scode='[custom_template_page_shortcode_'.$selected_page_id.']';
      foreach ($array_post as $item) {
          if ($item !== $scode) {
              $newArray_post[] = $item;
          }
      }
        $modifiedString = implode(",", $newArray_post);
        update_option('shortcode_post_option_name',$modifiedString);
    }


       delete_option('custom_template_shortcode_' . $selected_page_id);
  }

  $theme_path = get_template_directory();
  $php_files = get_php_files_in_directory($theme_path);
  $selected_php_file = get_option('selected_php_file');
?>

  <div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <div class="nav-tab-wrapper" style="margin-top: 20px">
      <a href="#tab1" class="nav-tab nav-tab-active">Create Shortcode</a>
      <a href="#tab2" class="nav-tab">Add Shortcode</a>
    </div>
    <div class="tab-content" id="tab1">
      <div class="wrap">
        <h3 style="margin-top:30px;">Create Shortcode</h3>
        <form method="post" action="" style="margin-top: 20px;margin-bottom:30px;">
          <label for="template_page"><b>Select a File : </b></label>
          <select name="template_page" style="width: 500px;padding:5px;margin-top:10px;margin-left:120px;">
            <option value="0">Select a File...</option>
            <?php foreach ($php_files as $index => $file) :
              $filenameWithoutExtension = pathinfo($file, PATHINFO_FILENAME);
            ?>
              <option value="<?php echo $filenameWithoutExtension; ?>" <?php selected($selected_php_file, $file); ?>>
                <?php echo $file; ?>
              </option>
            <?php endforeach; ?>
          </select><br><br>

          <label for="template_page"><b>Add Shortcode To: </b></label>
          <input type="checkbox" style="margin-left:87px;" name="add_page_shortcode"> <span>Pages</span><br>

          <div style="margin-top:10px;">
            <label for="template_page" style="visibility: hidden;"><b>Add Shortcode : </b></label>
            <input type="checkbox" style="margin-left:100px;" name="add_post_shortcode"> <span>Posts</span>
          </div>
          <br><br>

          <label for="template_page" style="visibility: hidden;">Select a File : </label>
          <input type="submit" style="margin-left:120px;" class="button button-primary" name="save_shortcode" value="Create Shortcode">

        </form>

        <hr>
        <h2 style="margin-top: 50px;">Saved Shortcodes</h2>
        <table class="widefat">
          <thead>
            <tr>
              <th>File Name</th>
              <th>Shortcode</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>


            <?php foreach ($php_files as $index => $page) :
              $filenameWithoutExtension = pathinfo($page, PATHINFO_FILENAME);
            ?>
              <?php $shortcode = get_option('custom_template_shortcode_' . $filenameWithoutExtension); ?>
              <?php if ($shortcode) : ?>
                <tr>
                  <td><?php echo $page; ?></td>
                  <td><?php echo esc_html($shortcode); ?></td>
                  <td>
                    <form method="post" action="">
                      <input type="hidden" value="<?= $filenameWithoutExtension ?>" name="short_id">
                      <input type="submit" class="button button-secondary" name="delete_shortcode" value="Delete Shortcode">
                    </form>
                  </td>
                </tr>
              <?php endif; ?>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
    <div class="tab-content" id="tab2">
      <div class="main-cls" hidden>

        <form method="post" action="options.php">
          <?php settings_fields('shortcode_master_options_group'); ?>

          <style>
            .colmd {
              margin-top: 30px;
            }

            .main-cls {
              margin-bottom: 25px;
              margin-left: -20px;
              /* background: white; */
              padding: 10px;
              margin-right: -20px;
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

    </div>
  </div>
<?php
}

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
      $shortcode_page .= trim($scr);
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

function shortcode_master_register_settings(){
  register_setting('shortcode_master_options_group', 'shortcode_post_option_name', 'myplugin_callback');
  register_setting('shortcode_master_options_group', 'shortcode_page_option_name', 'myplugin_callback');
}
add_action('admin_init', 'shortcode_master_register_settings');

function shortcode_master_register_options_page(){
  add_options_page('Shortcode Master Setting', 'Shortcode Master', 'manage_options', 'shortcode-master', 'shortcode_master_options_page');
}
add_action('admin_menu', 'shortcode_master_register_options_page');

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'add_action_links');
function add_action_links($links){
  $mylinks = array(
    '<a href="' . admin_url('options-general.php?page=shortcode-master') . '">Settings</a>',
  );
  return array_merge($links, $mylinks);
}

$theme_path = get_template_directory();
$php_files = get_php_files_in_directory($theme_path);
$selected_php_file = get_option('selected_php_file');

foreach ($php_files as $index => $file) :
  $filenameWithoutExtension = pathinfo($file, PATHINFO_FILENAME);
  $shortcode = get_option('custom_template_shortcode_' . $filenameWithoutExtension);
  if ($shortcode) :
    add_shortcode($shortcode, function () use ($file) {
      ob_start();
      $filenameWithoutExtension = pathinfo($file, PATHINFO_FILENAME);
      get_template_part($filenameWithoutExtension);
      return ob_get_clean();
    });
  endif;
endforeach;
