<?php

require get_theme_file_path('/inc/like-route.php');
require get_theme_file_path('/inc/search-route.php');

    // Custom REST API
    function university_custom_rest(){
        register_rest_field('post', 'authorName', array(        // takes three args, post type to customize, name for the new field, array that describes how we want to manage the field
            'get_callback' => function(){return get_the_author();}
        ));  
        register_rest_field('note', 'userNoteCount', array(        // takes three args, post type to customize, name for the new field, array that describes how we want to manage the field
            'get_callback' => function(){return count_user_posts(get_current_user_id(), 'note');}     // user we want to count for, which post_type to count
        ));      
    }

    add_action('rest_api_init', 'university_custom_rest');   // takes two args



    function pageBanner($args = NULL){     // Any name you want to use, we can now pass args in from our pages, NULL makes the field optional
        // Set up for default logic in case the page doesn't pass one in
        if (!$args['title']){    // if function was called and title wasn't passed in
            $args['title'] = get_the_title();
        }

        if (!$args['subtitle']){
            $args['subtitle'] = get_field('page_banner_subtitle');
        }

        // if someone doesn't have a custom photo, if the page doesn't have a custom field pageBanner photo, get the photo from our theme folder
        if (!$args['photo']) {
            if (get_field('page_banner_background_image') AND !is_archive() AND !is_home() ) {
            $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
            } else {
            $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
            }
        }

        ?>
            <div class="page-banner">
                <!-- <?php print_r($pageBannerImage); ?> -->
                <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo']; ?>);"></div>   
                <div class="page-banner__content container container--narrow">
                <h1 class="page-banner__title"><?php echo $args['title'] // echo out the title we passed in?></h1> <!-- the_title() calls the title of whatever page we are on -->
                <div class="page-banner__intro">
                    <p><?php echo $args['subtitle'] ?></p>
                </div>
                </div>  
            </div>
    <?php }


    function university_files(){        // Inside the body of this function we can load however many CSS or JS files we want
        wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');  // 'nickname' 'file'
        wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');  // take http: out of the link

        // Google Map | Load whether wp is ran local, or server 
        wp_enqueue_script('googleMap', '//maps.googleapis.com/maps/api/js?key=AIzaSyBQ_OwM4DsYsMQh2lt98zzk_6vfCGNsYB0', Null, '1.0', true);   // function name, then connect to google maps and paste maps api key

        // if string exists in string
        if(strstr($_SERVER['SERVER_NAME'], 'localhost:10003')){
            wp_enqueue_script('main-university-js', 'http://localhost:3000/bundled.js', Null, '1.0', true);  // 'nickname', where to get js file, 'does the file have dependencies', 'version number', 'do you want to load file right before closing body tag'
        }else{
            wp_enqueue_script('our-vendors-js', get_theme_file_uri('/bundled-assets/vendors~scripts.07cb7586e2052fd66bd1.js'), Null, '1.0', true);  // Above is js for our local use, these are for public use
            wp_enqueue_script('main-university-js', get_theme_file_uri('/bundled-assets/scripts.5f649246756ddbb2239f.js'), Null, '1.0', true);  // 
            wp_enqueue_style('our-main-styles', get_theme_file_uri('/bundled-assets/styles.5f649246756ddbb2239f.css'));  // 
        }

        wp_localize_script('main-university-js', 'universityData', array(       // Takes three args, name or handle of main JS file you want to make flexible, make up variable name, Create an array of data 
            'root_url' => get_site_url(),       // Wordpress function to grab the url
            'nonce' => wp_create_nonce('wp_rest'),
            
        ));     
    } 

    //Takes in two arguments, first let's wordpress know what instructions are being given, second gives wordpress the name of a function we want to run
    add_action('wp_enqueue_scripts', 'university_files');    // wp_enqueue_scripts (tells wordpress we want to run a script file like JS), name of function


    function university_features(){
        add_theme_support('title-tag');            // To enable a feature for your theme call add_theme_support( 'feature you want to use );
        add_theme_support('post-thumbnails');   // post-thumbnails are also featured images
        add_image_size('professorLandscape', 400, 260, true);   // can take 7 args name, pixel width, pixel height, if you want to crop the image
        add_image_size('professorPortrait', 480, 650, true);
        add_image_size('pageBanner', 1500, 350, true);
    }
    add_action( 'after_setup_theme', 'university_features' );     // Change title depending on what page we are on

    
    function university_adjust_queries($query){
        // If not in admin for archive-campus.php
        if(!is_admin() AND is_post_type_archive('campuses') AND is_main_query()){
            $query -> set('posts_per_page', -1);    // Show all pins on map
        }
        
        // If statement for archive-program.php
        if(!is_admin() AND is_post_type_archive('program') AND is_main_query()){
            $query -> set('orderby', 'title');
            $query -> set('order', 'ASC');
            $query -> set('posts_per_page', -1);
        }
        // Archieve.php, before wordpress sends the queries it will run this function to adjust the query
        if (!is_admin() AND is_post_type_archive('event') AND $query -> is_main_query) {
            $today = date('Ymd');

            $query -> set('meta_key', 'event_date');    // grab queries from front-page.php
            $query -> set('order_by', 'meta-value-num');
            $query -> set('order', 'ASC');
            $query -> set('meta_query', array(     // Creating meta_query to take out the events whose days have already passed
                array(                          // Return event if it is greater or equal to today's date
                  'key' => 'event_date',
                  'compare' => '>=',
                  'value' => $today,
                  'type' => 'numeric',
                ), 
            ));
        }
    }
    add_action('pre_get_posts', 'university_adjust_queries');




// Redirect subscriber accounts out of admin and onto homepage
add_action('admin_init', 'redirectSubsToFrontend');

function redirectSubsToFrontend(){      // Only if user has one role (subscriber) redirect them to the homepage
    $ourCurrentUser = wp_get_current_user();

    if(count($ourCurrentUser -> roles) == 1 AND $ourCurrentUser -> roles[0] == 'subscriber'){       
        wp_redirect(site_url('/'));
        exit;   // let's wp know it can stop after redirecting
    }
}

// Take away the admin bar for subscriber role users
add_action('wp_loaded', 'noSubsAdminBar');

function noSubsAdminBar(){      // Only if user has one role (subscriber) redirect them to the homepage
    $ourCurrentUser = wp_get_current_user();

    if(count($ourCurrentUser -> roles) == 1 AND $ourCurrentUser -> roles[0] == 'subscriber'){       
        show_admin_bar(false);
    }
}

// Customize Login Screen
// Change url link for logo
add_filter('login_headerurl', 'ourHeaderUrl');   // takes 2 args, value or object you want to change, function that returns whatever you want to use instead

function ourHeaderUrl(){
    return esc_url(site_url('/'));   // Homepage
}

// Change the logo
add_action('login_enqueue_scripts', 'ourLoginCSS');

function ourLoginCSS(){
    wp_enqueue_style('our-main-styles', get_theme_file_uri('/bundled-assets/styles.5f649246756ddbb2239f.css'));  
    wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');  // 'nickname' 'file'
}

// Change login screen title
add_filter('login_headertitle', 'ourLoginTitle');

function ourLoginTitle(){
    return get_bloginfo('name');
}

/* Google Maps API Key 
function universityMapKey($api){
    $api['key'] = 'AIzaSyBQ_OwM4DsYsMQh2lt98zzk_6vfCGNsYB0';
    return $api;
}

add_filter('acf/fields/google_map/api', 'universityMapKey');  <!- - First arg target advanced custom field plugin and let it know we have an API Key, Second arg is the name of a function we are going to create - ->

*/


// Force note posts to be private
add_filter('wp_insert_post_data', 'makeNotePrivate', 10, 2);   // 2 args, filter hook we want to use, function we want to use, 10 is the priority of the callback function you're calling ,2 means we want the function to work with 2 params

function makeNotePrivate($data, $postarr){   // data is passed in and being filtered by our filter hook, postarr is also passed from our filter hook and contains our id#

    // if post_type is for note sanitize field so user cannot write any kind of code
    if($data['post_type'] == 'note'){
        // if the current user has more than 4 and $postarr does not have an id posts stop the req
        if(count_user_posts(get_current_user_id(), 'note') > 4 AND !$postarr['ID']){
            die("You have reached your note limit.");      // Stops the function
        }

        $data['post_content'] = sanitize_textarea_field($data['post_content']);     // sanitize post_content data (can be other fields like input etc..)
        $data['post_title'] = sanitize_text_field($data['post_title']);     
    }

    // If the post that is going to be saved is a Note post and does not equal trash(so we can delete), if we do not do this it will run all posts
    if($data['post_type'] == 'note' AND $data['post_status'] != 'trash'){
        $data['post_status'] = "private";   // change the datas post_status to private
    }
    return $data;
}

?>