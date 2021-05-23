<!-- This file needs to be named page.php to be used with pages  -->
<?php 
    // if user is not logged in when trying to visit my-notes redirect to homepage
    if(!is_user_logged_in()){
        wp_redirect(esc_url(site_url('/')));
        exit;
    }

    get_header();

    while(have_posts()){        // Grabs all posts
        the_post(); 
        pageBanner();
        ?>          <!-- Separate each post -->
        

        <div class="container container--narrow page-section">

            <div class="create-note">
                <h2 class="headline headline--medium">Create New Note</h2>
                <input class="new-note-title" placeholder="title" >
                <textarea class="new-note-body" placeholder="Your Note Here..." ></textarea>
                <span class="submit-note">Create Note</span>
                <span class="note-limit-message">Note limit reached: Delete an existing note to make room for a new one.</span>
            </div>

            <ul class="min-list link-list" id="my-notes">
                <?php 
                    $userNotes = new WP_Query(array(
                        'post_type' => 'note',  // What type of post do we want to grab
                        'posts_per_page' => -1, // Grab all notes
                        'author' => get_current_user_id()   // Only grab the notes for the specific user that is logged in
                    ));

                    while($userNotes -> have_posts()){
                        $userNotes -> the_post();   ?>
                        <li data-id="<?php the_ID(); ?>">
                            <input readonly class="note-title-field" value="<?php echo str_replace('Private: ', '', esc_attr(get_the_title())); // within str_replace 3 args, string of text you want to search for within arg3, what you want to replace arg1 with ,text you want to manipulate ?>">

                            <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i>Edit</span>
                            <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i>Delete</span>

                            <textarea readonly class="note-body-field"><?php echo esc_textarea(wp_strip_all_tags(get_the_content())); ?></textarea>      <!-- wp_strip_all_tags() will stop any tags from displaying -->

                            <span class="update-note btn btn--blue btn--small"><i class="fa fa-arrow-right" aria-hidden="true"></i>Save</span>
                        </li>
                    <?php }

                ?>
            </ul>
        </div>
        
    <?php }

    get_footer();
?>