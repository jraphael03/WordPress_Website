<?php get_header(); ?>

    <div class="page-banner">
      <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('/images/library-hero.jpg') ?>);"></div>
      <div class="page-banner__content container t-center c-white">
        <h1 class="headline headline--large">Welcome!</h1>
        <h2 class="headline headline--medium">We think you&rsquo;ll like it here.</h2>
        <h3 class="headline headline--small">Why don&rsquo;t you check out the <strong>major</strong> you&rsquo;re interested in?</h3>
        <a href="<?php echo get_post_type_archive_link('program') ?>" class="btn btn--large btn--blue">Find Your Major</a>
      </div>
    </div>

    <div class="full-width-split group">
      <div class="full-width-split__one">
        <div class="full-width-split__inner">
          <h2 class="headline headline--small-plus t-center">Upcoming Events</h2>

          <?php
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
              ),
            )); 

            while($homepageEvents -> have_posts()){
              $homepageEvents -> the_post();    
              get_template_part('template-parts/content', 'event');  // This function takes two args, folder name file name,  secondary arg  | file name is content-event 

           }

          ?>

          <p class="t-center no-margin"><a href="<?php echo get_post_type_archive_link('event'); ?>" class="btn btn--blue">View All Events</a></p>
        </div>
      </div>
      <div class="full-width-split__two">
        <div class="full-width-split__inner">
          <h2 class="headline headline--small-plus t-center">From Our Blogs</h2>
          <?php

            $homepagePosts = new WP_Query(array(
                'posts_per_page' => 2,       // Instead of the default 10 posts return 2 posts_per_page
            ));    // this will create a new query for wordpress

            // Look inside $homepagePosts for a method named have_posts()
            while($homepagePosts->have_posts()) {
                $homepagePosts->the_post(); // for every post in have_posts() ?>
            <div class="event-summary">
                <a class="event-summary__date event-summary__date--beige t-center" href="<?php the_permalink(); ?>">
                    <span class="event-summary__month"><?php the_time('M') ?></span>
                    <span class="event-summary__day"><?php the_time('d') ?></span>
                </a>
                <div class="event-summary__content">
                    <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>

                    <p><?php if (has_excerpt()){  // if we have an excpert display excerpt if not display limited content
                      echo get_the_excerpt();
                    }else {
                      echo wp_trim_words(get_the_content(), 18); // 2args, content you want to limit, how many words you want
                    } ?><a href="<?php the_permalink(); ?>" class="nu gray">Read more</a></p>

                </div>
            </div>
            <?php } wp_reset_postdata();    // reset different wp global data after it made it's query, use whenever finishing custom queries
          ?>

          <p class="t-center no-margin"><a href="<?php echo site_url('/blog'); ?>" class="btn btn--yellow">View All Blog Posts</a></p>
        </div>
      </div>
    </div>

    <div class="hero-slider">
      <div data-glide-el="track" class="glide__track">
        <div class="glide__slides">
          <div class="hero-slider__slide" style="background-image: url(<?php echo get_theme_file_uri('/images/bus.jpg') ?>);">
            <div class="hero-slider__interior container">
              <div class="hero-slider__overlay">
                <h2 class="headline headline--medium t-center">Free Transportation</h2>
                <p class="t-center">All students have free unlimited bus fare.</p>
                <p class="t-center no-margin"><a href="#" class="btn btn--blue">Learn more</a></p>
              </div>
            </div>
          </div>
          <div class="hero-slider__slide" style="background-image: url(<?php echo get_theme_file_uri('/images/apples.jpg') ?>);">
            <div class="hero-slider__interior container">
              <div class="hero-slider__overlay">
                <h2 class="headline headline--medium t-center">An Apple a Day</h2>
                <p class="t-center">Our dentistry program recommends eating apples.</p>
                <p class="t-center no-margin"><a href="#" class="btn btn--blue">Learn more</a></p>
              </div>
            </div>
          </div>
          <div class="hero-slider__slide" style="background-image: url(<?php echo get_theme_file_uri('/images/bread.jpg') ?>);">
            <div class="hero-slider__interior container">
              <div class="hero-slider__overlay">
                <h2 class="headline headline--medium t-center">Free Food</h2>
                <p class="t-center">Fictional University offers lunch plans for those in need.</p>
                <p class="t-center no-margin"><a href="#" class="btn btn--blue">Learn more</a></p>
              </div>
            </div>
          </div>
        </div>
        <div class="slider__bullets glide__bullets" data-glide-el="controls[nav]"></div>
      </div>
    </div>
    
<?php    get_footer();
?>

