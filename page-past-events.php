<?php 

  get_header();
  pageBanner(array(
    'title' => 'Past Events',
    'subtitle' => 'A recap of our past events',
  ));

?>  

<div class="container container--narrow page-section">
<?php 

        $today = date('Ymd');

        $pastEvents = new WP_Query(array(   // Name new post and create custom query
            'paged' => get_query_var('paged', 1),    // Tells custom query which page number it should be on
            'post_type' => 'event',   // The post we want to load
            'meta_key' => 'event_date', // spell out name of the custom field we want
            'orderby' => 'meta_value_num',  // Using the query orderby you can choose how you want objects sorted, sort by custom query which will be by event date
            'order' => 'ASC',
            'meta_query' => array( // Creating meta_query to take out the events whose days have already passed
            array(    // Return event if it is greater or equal to today's date
                'key' => 'event_date',
                'compare' => '<',       // less than today's date aka. past dates
                'value' => $today,
                'type' => 'numeric',
            ), 
            ),
        )); 

  while($pastEvents -> have_posts()){
    $pastEvents -> the_post(); 

    get_template_part('template-parts/content-event');
 
    };

    echo paginate_links(array(      // Create custom pagination
        'total' => $pastEvents -> max_num_pages,    // look into variable and create max number of pages needed
    ));    // Create pagination

?>
</div>

<?php
  get_footer();
?>


