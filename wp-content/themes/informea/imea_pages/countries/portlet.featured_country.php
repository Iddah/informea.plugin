<?php
	$countries_data = new imea_countries_page(null);
    if ($use_random){
        $country_to_show = $countries_data->get_random_country();
    }
    else{
        $country_to_show = $countries_data->get_country_for_id(84);
    }
    
?>
<a href="<?php bloginfo('url'); ?>/countries/<?php echo $country_to_show->id?>">
    <img title="<?php echo $country_to_show->name?>" alt="" src="<?php echo bloginfo('url')."/".$country_to_show->icon_large?>">
    <span>
        <span class="short-name"><?php echo $country_to_show->name?></span>
        <span class="name"><?php echo $country_to_show->long_name?> profile</span>
    </span>
</a>
