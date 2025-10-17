<meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

        <title>NL Clinic Isle of Wight | <?php perch_pages_title(); ?></title>
<?php
    $domain        = 'https://'.$_SERVER["HTTP_HOST"];
    $url           = $domain.$_SERVER["REQUEST_URI"];
    $sitename      = "The name of my website";
    $twittername   = "@mytwittername";
    $sharing_image = '/images/default_fb_image.jpg';

    PerchSystem::set_var('domain',$domain);
    PerchSystem::set_var('url',$url);
    PerchSystem::set_var('sharing_image',$sharing_image);
    PerchSystem::set_var('twittername',$twittername);

    perch_page_attributes(array(        
      'template' => 'default.html'    
    ));
    ?>