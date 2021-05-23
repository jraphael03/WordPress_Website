<!-- This file needs to be named page.php to be used with pages  -->
<?php 
    get_header();

    while(have_posts()){        // Grabs all posts
        the_post(); 
        pageBanner();
        ?>          <!-- Separate each post -->
        

        <div class="container container--narrow page-section">

            <?php // if page is a child page show metabox, if page is parent show nothing

                $theParent = wp_get_post_parent_id(get_the_ID());        // Get the id of the page we are viewing, then wordpress use that number to look up the id of the parent page

                if($theParent) {  ?>
                <div class="metabox metabox--position-up metabox--with-home-link">
                    <p><a class="metabox__blog-home-link" href="<?php echo get_permalink($theParent); // get_permalink() allows us to pass in an id to set a page ?>"><i class="fa fa-home" aria-hidden="true"></i><?php echo get_the_title($theParent); //get_the_title(id) will get the title of a different page, the_title(); gets the name of the current page ?></a> <span class="metabox__main"><?php the_title(); ?></span></p>  
                </div>               
            <?php    }   
            ?>


            <?php 
            $testArray = get_pages(array(       // return pages
                'child_of' => get_the_ID()      // If the page has children return the children pages, if it doesn't return Null
            ));  

            if($theParent or $testArray ) { ?>   <!-- Only display if we are on a parent page or child page -->
            <div class="page-links">
            <h2 class="page-links__title"><a href="<?php echo get_permalink($theParent); ?>"><?php echo get_the_title($theParent); ?></a></h2>
            <ul class="min-list">
                <?php // if $theParent variable does not equal zero run the case in the brackets  (variable set above), Making dynamic
                    if($theParent){
                        $findChildrenof = $theParent;
                    }else{
                        $findChildrenof = get_the_ID();
                    }

                    wp_list_pages(array(
                        'title_li' => Null,     // Don't echo out the word pages
                        'child_of' => $findChildrenof,       // child_of (gets child page) of, page you are on id
                        'sort_column' => 'menu_order',        // Change the order of pages, set this then in pages go into document and change the order
                    ));   // wp_list_pages() creates a link to every page, to pass children pages use associative array
                ?>
            </ul>
            </div>

            <?php } ?>

            <div class="generic-content">
            <?php the_content(); ?> <!-- Pulls out the content of our page -->
            </div>

        </div>
        
    <?php }

    get_footer();
?>
