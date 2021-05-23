<?php 

  get_header();
  pageBanner(array(
    'title' => 'Search Results',
    'subtitle' => 'You searched for &ldquo;' . esc_html(get_search_query(false)) . '&rdquo;'   // get_search_query() gets what you searched for, add false between the brackets will allow users to add script and etc.. better to not use false, but if used with esc_html in front the code is converted to html
  ));
?>  


<div class="container container--narrow page-section">
<?php 
    // if search is found display, if not display no results found and give search bar
    if(have_posts()){
        while(have_posts()){
            the_post(); 
            // Look inside of template-parts folder for content then second arg (professor, post, etc..)
            get_template_part('template-parts/content', get_post_type());
            }
            echo paginate_links();    // Create pagination
    } else{
        echo '<h2 class="headline headline--small-plus">No Results match that search.</h2>';
    }

    // Grab the search form from searchform.php
    get_search_form();
  
?>

</div>

<?php
  get_footer();
?>


