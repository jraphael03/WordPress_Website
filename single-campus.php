<!-- This file needs to be named single.php to be used with posts  -->
<?php
    get_header();

    while(have_posts()){
        the_post(); 
        pageBanner();
        ?>


        <div class="container container--narrow page-section">
                <div class="metabox metabox--position-up metabox--with-home-link">
                    <p><a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('campus'); ?>"><i class="fa fa-home" aria-hidden="true"></i> All Campuses </a> <span class="metabox__main"><?php the_title(); ?></span></p>  
                </div>   

            <div class="generic-content">
                <?php the_content(); ?>
            </div>

        <h1>Map is not working since we didn't sign up for google maps</h1>
            <?php $mapLocation = get_field('map_location'); ?>
            <div class="acf-map">

                    <div class="marker" data-lat="<?php echo $mapLocation['lat']; ?>" data-lng="<?php echo $mapLocation['lng']; ?>">
                    <h3> <?php the_title(); // display title when pin is clicked ?> </h3>
                    <?php echo $mapLocation['address'] // display address when pin is clicked ?>
                    </div>
                
            </div>



        <?php

            // Programs
            $relatedPrograms = new WP_Query(array(   // Name new post and create custom query
              'posts_per_page' => -1,    // -1 for all items
              'post_type' => 'program',   // The post we want to load
              'orderby' => 'title',  // Order by professor name
              'order' => 'ASC',
              'meta_query' => array( // Creating meta_query to take out the events whose days have already passed
                // if the array of related_programs contains or LIKE the ID number of current id post that is what we want
                array(
                    'key' => 'related_campus',
                    'compare' => 'LIKE',
                    'value' => '"' . get_the_ID() . '"',    // Need to search for string number     double quotes period to concatenate
                )
              ),
            )); 
            

            // if relatedProfessors has posts run if not do not run
            if ($relatedPrograms -> have_posts()){
                echo '<hr class="section-break">';
                echo '<h2 class="headline headline--medium">Programs Available at this Campus</h2>';

                echo '<ul class="min-list link-list">';
                while($relatedPrograms -> have_posts()){
                    $relatedPrograms -> the_post(); ?> <!-- begin by looking into object -->

                    <li>
                        <a href="<?php the_permalink(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </li>

                <?php }
                echo '</ul>';
            }

            // Call this for upcoming events to show after professors
            // Resets global post object, objects like get_title, get_id, run this between multiple custom queries
            wp_reset_postdata();


          ?>

        </div>

    <?php }

    get_footer();
?>
