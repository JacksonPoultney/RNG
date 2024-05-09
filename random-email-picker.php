<?php
/*
Plugin Name: Random Email Picker
Description: Picks a random email from a CSV file located in the uploads directory.
Version: 2.0
Author: Jackson
*/

function serve_email_list() {
    $data = array();
    $selectedRows = array();
    $uploads = wp_upload_dir();
    $upload_path = $uploads['basedir'];
    $file_path = plugin_dir_path(__FILE__) . 'demo-data.csv';

    if (file_exists($file_path)) {
        $file = fopen($file_path, 'r');

        while (($line = fgetcsv($file)) !== FALSE) {
            $data[] = array(
                'name' => $line[0],
                'email' => $line[1]
            );
        }
        fclose($file);

        // Select 50 unique random rows if there are enough rows
        $totalRows = count($data);
        $numToSelect = min(50, $totalRows);
        $randomIndexes = array();

        while (count($randomIndexes) < $numToSelect) {
            $randIndex = random_int(0, $totalRows - 1);
            if (!in_array($randIndex, $randomIndexes)) {
                $randomIndexes[] = $randIndex;
                $selectedRows[] = $data[$randIndex];
            }
        }

        // Output selected emails as JSON
        echo json_encode($selectedRows);
    } else {
        echo json_encode(array('error' => 'CSV file does not exist.'));
    }
    wp_die(); // Required to terminate immediately and return a proper response
}
add_action('wp_ajax_serve_email_list', 'serve_email_list');
add_action('wp_ajax_nopriv_serve_email_list', 'serve_email_list');

function load_picker_scripts() {
    wp_enqueue_script('random-email-picker-js', plugins_url('picker.js', __FILE__), array('jquery'), '1.0.0', true);
    wp_localize_script('random-email-picker-js', 'picker', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'load_picker_scripts');

function load_picker_styles() {
    wp_enqueue_style('random-email-picker-css', plugins_url('random-email-picker.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'load_picker_styles');

function display_random_email_picker() {
    ob_start();
    ?>
    <div>
        <div id="emailDisplay"><span>Winners Email</span>Please click "Pick Winner" to start...</div>
        <button id="startPickerButton">Pick Winner</button>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('random_email_picker', 'display_random_email_picker');
