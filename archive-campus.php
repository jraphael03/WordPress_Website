<?php 

  get_header();
  pageBanner(array(
    'title' => 'Our Campuses',
    'subtitle' => 'We have several conveniently located campuses.',
  ));

?>  

<div class="container container--narrow page-section">

<h1>Map is not working since we didn't sign up for google maps</h1>
<div class="acf-map">
    <?php 
    while(have_posts()){
        the_post(); 
        $mapLocation = get_field('map_location');
        ?>

        <div class="marker" data-lat="<?php echo $mapLocation['lat']; ?>" data-lng="<?php echo $mapLocation['lng']; ?>">
          <h3><a href="<?php the_permalink(); ?>"> <?php the_title(); // display title with link when pin is clicked ?> </a></h3>
          <?php echo $mapLocation['address'] // display address when pin is clicked ?>
        </div>
        
        <?php  
        /* This will grab the google map info, but need to sign up for the service

        $mapLocation = get_field('map_location');
        print_r(map_location); 
        echo $mapLocation['lng'] */ ?> 
    
    <?php  } ?>
</div>

</div>

<?php
  get_footer();
?>


