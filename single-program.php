<!-- This file needs to be named single.php to be used with posts  -->
<?php
    get_header();

    while(have_posts()){
        the_post(); 
        pageBanner();
        ?>


        <div class="container container--narrow page-section">
                <div class="metabox metabox--position-up metabox--with-home-link">
                    <p><a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('program'); ?>"><i class="fa fa-home" aria-hidden="true"></i> All Programs </a> <span class="metabox__main"><?php the_title(); ?></span></p>  
                </div>   

            <div class="generic-content">
                <?php the_field('main_body_content'); ?>

        <?php

            // Professors
            $relatedProfessors = new WP_Query(array(   // Name new post and create custom query
              'posts_per_page' => -1,    // -1 for all items
              'post_type' => 'professor',   // The post we want to load
              'orderby' => 'title',  // Order by professor name
              'order' => 'ASC',
              'meta_query' => array( // Creating meta_query to take out the events whose days have already passed
                // if the array of related_programs contains or LIKE the ID number of current id post that is what we want
                array(
                    'key' => 'related_programs',
                    'compare' => 'LIKE',
                    'value' => '"' . get_the_ID() . '"',    // Need to search for string number     double quotes period to concatenate
                )
              ),
            )); 
            

            // if relatedProfessors has posts run if not do not run
            if ($relatedProfessors -> have_posts()){
                echo '<hr class="section-break">';
                echo '<h2 class="headline headline--medium">' . get_the_title() . ' Professors</h2>';

                echo '<ul class="professor-cards">';
                while($relatedProfessors -> have_posts()){
                    $relatedProfessors -> the_post(); ?> <!-- begin by looking into object -->

                    <li class="professor-card__list-item">
                        <a class="professor-card" href="<?php the_permalink(); ?>">
                            <img class="professor-card__image" src="<?php the_post_thumbnail_url('professorLandscape'); ?>">
                            <span class="professor-card__name"><?php the_title(); ?></span>
                        </a>
                    </li>

                <?php }
                echo '</ul>';
            }



            // Call this for upcoming events to show after professors
            // Resets global post object, objects like get_title, get_id, run this between multiple custom queries
            wp_reset_postdata();



            // Upcoming Events
            $today = date('Ymd');

            $homepageEvents = new WP_Query(array(   // Name new post and create custom query
              'posts_per_page' => 2,    // -1 for all items
              'post_type' => 'event',   // The post we want to load
              'meta_key' => 'event_date', // spell out name of the custom field we want
              'orderby' => 'meta_value_num',  // Using the query orderby you can choose how you want objects sorted, sort by custom query which will be by event date
              'order' => 'ASC',
              'meta_query' => array( // Creating meta_query to take out the events whose days have already passed
                array(    // Return event if it is greater or equal to today's date
                  'key' => 'event_date',
                  'compare' => '>=',
                  'value' => $today,
                  'type' => 'numeric',
                ), 
                // if the array of related_programs contains or LIKE the ID number of current id post that is what we want
                array(
                    'key' => 'related_programs',
                    'compare' => 'LIKE',
                    'value' => '"' . get_the_ID() . '"',    // Need to search for string number     double quotes period to concatenate
                )
              ),
            )); 

            // if homepageEvents has posts run if not do not run
            if ($homepageEvents -> have_posts()){
                echo '<hr class="section-break">';
                echo '<h2 class="headline headline--medium"> Upcoming ' . get_the_title() . ' Events</h2>';

                while($homepageEvents -> have_posts()){
                    $homepageEvents -> the_post(); //begin by looking into object
                    
                    get_template_part('template-parts/content-event');

                }
            }

            wp_reset_postdata();    // Clear our loops
            $relatedCampuses = get_field('related_campus');

            // only display if relatedCampuses is not empty
            if ($relatedCampuses) {
                echo '<hr class="section-break>';
                echo '<h2 class="headline headline--medium">' . get_the_title() . ' is Available At These Campuses</h2>';

                echo '<ul class="min-list link-list">';
                    forEach($relatedCampuses as $campus){
                        ?> <li><a href="<?php get_the_permalink($campus); ?>"><?php echo get_the_title($campus); ?></a></li><?php
                    }
                echo '</ul>';

            }

          ?>

            </div>

            

        </div>

    <?php }

    get_footer();
?>
