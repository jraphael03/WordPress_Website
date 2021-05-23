<form class="search-form" method="get" action="<?php echo esc_url(site_url('/')); ?>">   <!-- When echoing out a url wrap in esc_url for security -->
    <label class="headline headline--medium" for="s">Perform a New Search:</label>
    <div class="search-form-row">
        <input placeholder="What are you looking for?" class="s" id="s" type="search" name="s" >     <!-- Name to be used in url is s because wp search uses s -->
        <input class="search-submit" type="submit" value="Search">
    </div>
</form>