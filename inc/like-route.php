<?php 

// rest_api_init creates a new custom route or field
add_action('rest_api_init', 'universityLikeRoutes');

function universityLikeRoutes(){
    // POST(create) Like Method
    register_rest_route('university/v1', 'manageLike', array(
        'methods' => 'POST',
        'callback' => 'createLike'
    ));      // 3 args, beginning part of the url (namespace), name for this specific route or url, array 

    // DELETE Like Method
    register_rest_route('university/v1', 'manageLike', array(
        'methods' => 'DELETE',
        'callback' => 'deleteLike'
    )); 
}

function createLike($data){ // Grab data from Like.js

    // if user has any kind of account else cancel the request
    if(is_user_logged_in()){
        $professor = sanitize_text_field($data['professorId']);    // name of the obj being passed through data from Like.js

        $existQuery = new WP_Query(array(
        'author' => get_current_user_id(),
        'post_type' => 'like',
        'meta_query' => array(
            array(
                'key' => 'liked_professor_id',      // Name of the field we are using
                'compare' => '=',       // Looking for key = value
                'value' => $professor
                )
            )
        ));

        // if post wasn't liked like post, else post was liked can't like again, AND $professor needs to match a professor id
        if($existQuery -> found_posts == 0 AND get_post_type($professor) == 'professor'){
            // create new like post
            return wp_insert_post(array(   // let's us programatically create a new post within our php code
            'post_type' => 'like',
            'post_status' => 'publish',
            'post_title' => '2nd php test',
            'meta_input' => array(
                'liked_professor_id' => $professor      // Grab the professor id from the field we created
                )
            ));   
        }else{
            die('Invalid professor id');
        }

        
    } else{
        die('Only logged in users can like a professor');
    }

    
}

function deleteLike($data){
    $likeId = sanitize_text_field($data['like']);    // name of the obj being passed through data from Like.js

    // If id matches
    if(get_current_user_id() == get_post_field('post_author', $likeId) AND get_post_type($likeId) == 'like'){               // 2args, what info you want about the post ,id that liked the post, AND $likeID equals the id like
        wp_delete_post($likeId, true);        // 2 args, id of the post you want to delete, do you want to send to the trash or delete completely (true skips the trash step)
        return 'Like has been deleted';
    }else{
        die('You do not have permission to delete that.');
    }
}

