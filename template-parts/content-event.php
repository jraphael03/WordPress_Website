<div class="event-summary">
    <a class="event-summary__date t-center" href="#">

        <span class="event-summary__month"><?php    
        $eventDate = new DateTime(get_field('event_date'));  // Created a variable, get_field, grab our created field
        echo $eventDate -> format('M');    // Take our variable and pull out the month
        ?></span>
        <span class="event-summary__day"><?php 
        echo $eventDate -> format('d')    // Utilizing the variable above pull out the day
        ?></span>

    </a>
    <div class="event-summary__content">
        <h5 class="event-summary__title headline headline--tiny"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h5>

        <p><?php if (has_excerpt()){  // if we have an excpert display excerpt if not display limited content
            echo get_the_excerpt();
        }else {
            echo wp_trim_words(get_the_content(), 18); // 2args, content you want to limit, how many words you want
        } ?><a href="<?php the_permalink(); ?>" class="nu gray">Learn more</a></p>

    </div>
</div>