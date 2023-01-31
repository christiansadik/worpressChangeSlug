<?php
/*
* Plugin Name: Cs_Dev - Change Slug
* Description: Plugin per il cambio slug di tutti gli articoli salvati
* Version: 1.0.0
* Company: Forbidden Design
* Comapany URI: http://www.forbidden.design
* Author: Christian Sadik Melik
*/

/**
 * Filter to disable gutenberg block
 */
add_filter('use_block_editor_for_post', '__return_false');


/**
 * Function to convert array to csv.file and donwload it
 */
function arrayToCsvDownload($array, $filename = "url.csv", $delimiter=",") {

    $csv = fopen('php://memory', 'w');

    foreach ($array as $line) {
        fputcsv($csv, $line/*Deve essere un array*/, $delimiter);
    }

    rewind($csv);

    header('Content-Type: application/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="'.$filename.'";');
    fpassthru($csv);

}

/**
 * Function to get all articles and from the old url create a new one
 */
function changeWithNewUrl(){
    $args = array(
        'post_type'=> 'post',
        'orderby'    => 'date',
        'post_status' => 'publish',
        'order'    => 'DESC',
        'posts_per_page' => -1
        );
        $results = get_posts($args);
        $array_url = array();
        // $redirectMatch = fopen("redirectMatch.txt", "w") or die("Impossibile aprire il file!");

        foreach ($results as $key => $result) {
            $site_url = get_site_url();
            $result_id = $result->ID;

            $old_url = substr(get_permalink($result_id), strlen($site_url));

            $result_information = get_post($result_id);
            $result_day = get_the_date('d',$result_id);
            $result_month = get_the_date('m',$result_id);
            $result_year = get_the_date('Y',$result_id);
            $result_slug = $result_information->post_name;
            $new_url = $site_url.'/'.$result_year.'/'.$result_month.'/'.$result_day.'/'.$result_slug;
            $url = $old_url.','.$new_url;
            // $txt = 'RedirectMatch 301 '.$old_url.' '.$new_url;
            $array_url[$key]['old_url'] = $old_url;
            $array_url[$key]['new_url'] = $new_url;

            // fwrite($redirectMatch, $txt);
        }
        arrayToCsvDownload($array_url);
        // fclose($redirectMatch);
}
// add_action( 'admin_post_nopriv_redirect_new_url', 'changeWithNewUrl' );
// add_action( 'admin_post_redirect_new_url', 'changeWithNewUrl' );


/**
 * Script that starts the function changeWithNewUrl()
 */
if ( isset($_GET["redirect_new_url"]) && $_GET['redirect_new_url'] == 1) {
	add_action( 'wp', 'changeWithNewUrl');
}

?>
