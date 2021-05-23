<!-- This file needs to be named single.php to be used with posts  -->
<?php
    get_header();

    while(have_posts()){
        the_post(); 
        pageBanner();
        ?>

        <div class="container container--narrow page-section">
                <div class="metabox metabox--position-up metabox--with-home-link">
                    <p><a class="metabox__blog-home-link" href="<?php echo site_url('/blog') // Set a specific link in php ?>"><i class="fa fa-home" aria-hidden="true"></i> Blog Home </a> <span class="metabox__main">Posted by <?php the_author_posts_link(); ?> on <?php the_time('F, d, Y'); ?> in <?php echo get_the_category_list(', ') ?></span></p>  
                </div>   

            <div class="generic-content">
                <?php the_content(); ?>
            </div>
        </div>

    <?php }

    get_footer();
?>
