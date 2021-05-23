<!-- This file needs to be named single.php to be used with posts  -->
<?php
    get_header();

    while(have_posts()){
        the_post(); 
        pageBanner();
        ?>


        <div class="container container--narrow page-section">


            <div class="generic-content">
                <div class="row group">
                    <div class="one-third">
                        <?php the_post_thumbnail('professorPortrait'); ?>
                    </div>
                    <div class="two-thirds">
                        <?php 
                            // Get like count 
                            $likeCount = new WP_Query(array(
                                'post_type' => 'like',
                                'meta_query' => array(
                                    array(
                                        'key' => 'liked_professor_id',      // Name of the field we are using
                                        'compare' => '=',       // Looking for key = value
                                        'value' => get_the_ID()
                                    )
                                )
                            ));

                            $existStatus = 'no';

                            // If statement makes it so if no one is logged in the like heart will not be filled
                            if(is_user_logged_in()){
                                // Only contains results if the current user has liked the current professor
                                $existQuery = new WP_Query(array(
                                'author' => get_current_user_id(),
                                'post_type' => 'like',
                                'meta_query' => array(
                                    array(
                                        'key' => 'liked_professor_id',      // Name of the field we are using
                                        'compare' => '=',       // Looking for key = value
                                        'value' => get_the_ID()
                                        )
                                    )
                                ));

                                // If found_posts equals true make $existQuery yes, and change CSS to fill in the heart
                                if($existQuery -> found_posts){
                                    $existStatus = 'yes';
                                }
                            }

                            

                        ?>
                        <span class="like-box" data-like="<?php echo $existQuery -> posts[0] ->ID; ?>" data-professor="<?php the_ID(); ?>" data-exists="<?php echo $existStatus ?>">
                            <i class="fa fa-heart-o" aria-hidden="true"></i>
                            <i class="fa fa-heart" aria-hidden="true"></i>
                            <span class="like-count"><?php echo $likeCount -> found_posts;  // Look inside our variable for found_posts(Gives number of posts) which will give the number of likes ?></span>
                        </span>
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>

            <?php 
                $relatedPrograms = get_field('related_programs');
                //print_r($relatedPrograms)

                // if it has relatedPrograms display if not do not display  (Needed for programs without any related programs)
                if($relatedPrograms){
                    echo '<hr class="section-break" />';
                    echo '<h2 class="headline headline--medium">Subject(s) Taught</h2>';
                    echo '<ul class="link-list min-list">';
                    //print_r($relatedPrograms);

                        foreach($relatedPrograms as $program){ 
                            //echo get_the_title($program); ?>
                            <li><a href="<?php echo get_the_permalink($program); ?>"> <?php echo get_the_title($program); ?></a></li>

                        <?php }
                    echo '</ul>';
                }
            ?>

        </div>

    <?php }

    get_footer();
?>
