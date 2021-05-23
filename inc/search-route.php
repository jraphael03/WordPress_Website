<?php

add_action( 'rest_api_init', 'universityRegisterSearch');

function universityRegisterSearch(){
    // http://localhost:10003/wp-json/university/v1/search
    register_rest_route('university/v1', 'search', array(       // three args  name space we want to use (add v number so any changes will include version control), route, array
        'methods' => WP_REST_SERVER::READABLE,      // GET
        'callback' => 'universitySearchResults'
    ));       
}

    // http://localhost:10003/wp-json/university/v1/search
function universitySearchResults($data){        // add a param so we can utilize it
    $mainQuery =new WP_QUERY(array(
        'post_type' => array('post', 'page', 'professor', 'program', 'campus', 'event'),
        's' => sanitize_text_field($data['term'])     // s = search,   sanitize_text_field() is a secure way of using search
    ));

    // results is an array of multiple arrays for specific searches
    $results = array(
        'generalInfo' => array(),
        'professors' => array(),
        'programs' => array(),
        'events' => array(),
        'campuses' => array()
    );

    // Loop through results and depending on the post type it will be pushed into the appropriate array
    while($mainQuery -> have_posts()){
        $mainQuery -> the_post();

        // post_type above and arrays above
        if(get_post_type() == 'post' OR get_post_type() == 'page'){
            array_push($results['generalInfo'], array(       // takes 2 args, array you want to add onto, what you want to add onto the array
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'postType' => get_post_type(),
                'authorName' => get_the_author()
        ));
        }

        if(get_post_type() == 'professor'){
            array_push($results['professors'], array(       // takes 2 args, array you want to add onto, what you want to add onto the array
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'image' => get_the_post_thumbnail_url(0, 'professorLandscape')     // two args, which post you want to grab, size of photo you want
        ));
        }

        if(get_post_type() == 'program'){

               // Pull results for campuses
            // $relatedCampuses = get_field('related_campus');

            // if($relatedCampuses){
            //     foreach($relatedCampuses as $campus){
            //         array_push($results['campuses'], array(
            //             'title' => get_the_title($campus),
            //             'permalink' => get_the_permalink($campus)
            //         ))    
            //     }
            // }

            array_push($results['programs'], array(       // takes 2 args, array you want to add onto, what you want to add onto the array
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'id' => get_the_id()
        ));
        }

        // Did not sign up for google maps so campuses not working
        // if(get_post_type() == 'campus'){
        //     array_push($results['campuses'], array(       // takes 2 args, array you want to add onto, what you want to add onto the array
        //         'title' => get_the_title(),
        //         'permalink' => get_the_permalink()
        // ));

        if(get_post_type() == 'event'){
            $eventDate = new DateTime(get_field('event_date'));
            $description = null;

            if (has_excerpt()){  // if we have an excerpt display excerpt if not display limited content
                $description =  get_the_excerpt();
            }else {
                $description = wp_trim_words(get_the_content(), 18); // 2args, content you want to limit, how many words you want
            }

            array_push($results['events'], array(       // takes 2 args, array you want to add onto, what you want to add onto the array
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'month' => $eventDate -> format('M'),
                'day' => $eventDate -> format('d'),
                'description' => $description
        ));
        }

    };

    if($results['programs']){
        $programsMetaQuery = array('relation' => 'OR');

        // For each loop will grab the id for whatever program we search for
        foreach($results['programs'] as $item){
            array_push($programsMetaQuery, array(      // three args, key is the name of the advanced custom field we want to look within, compare, value we are looking for
                    'key' => 'related_programs',
                    'compare' => 'LIKE',
                    'value' => '"' . $item['id'] . '"'  
                ));       // 2 args, array you want to push onto, what you want to add on
        }


        $programRelationshipQuery = new WP_Query(array(
            'post_type' => array('professor', 'event'),
            'meta_query' => $programsMetaQuery
        ));


        while($programRelationshipQuery -> have_posts()) {
            $programRelationshipQuery -> the_post();

            if(get_post_type() == 'event'){
            $eventDate = new DateTime(get_field('event_date'));
            $description = null;

            if (has_excerpt()){  // if we have an excerpt display excerpt if not display limited content
                $description =  get_the_excerpt();
            }else {
                $description = wp_trim_words(get_the_content(), 18); // 2args, content you want to limit, how many words you want
            }

            array_push($results['events'], array(       // takes 2 args, array you want to add onto, what you want to add onto the array
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'month' => $eventDate -> format('M'),
                'day' => $eventDate -> format('d'),
                'description' => $description
            ));
            }

            if(get_post_type() == 'professor'){
                array_push($results['professors'], array(       // takes 2 args, array you want to add onto, what you want to add onto the array
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'image' => get_the_post_thumbnail_url(0, 'professorLandscape')     // two args, which post you want to grab, size of photo you want
            ));
            }

        }

        // Remove duplicates from the array
        $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));    // 2 args, array you want to work with, tell the function to look inside of the array to look for duplicates
        $results['events'] = array_values(array_unique($results['events'], SORT_REGULAR));
    }

    
    
    return $results;

    // Grabs the data of professor var above
    //return $professors -> posts;

}

?>