<?php /* Template Name: Page Home (Landing) */ ?>
    
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

	<meta property="og:title" content="IT Stars - Yorqin karyera!" />
	<meta property="og:url" content="https://itstars.uz/" />
	<meta property="og:image" content="https://itstars.uz/wp-content/uploads/2021/12/itstars.jpg" />

	<link rel="icon" href="<?php the_field('favicon'); ?>">
    <title>IT Stars - Yorqin karyera!</title>
    <link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_directory_uri() . '/assets/css/style.css'; ?>">

<!-- Meta Pixel Code -->
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '5354724654540276');
  fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
  src="https://www.facebook.com/tr?id=5354724654540276&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->

</head>
<body>

<!-- Header -->
<header class="header">
    <div class="container">
        <div class="header__inner">
            <a href="<?php bloginfo('url'); ?>" class="logo">
		        <img src="<?php the_field('logo'); ?>" alt="IT Stars">
            </a>
            <div class="header__right">
                <div class="menu">
                    <?php
                        wp_nav_menu([
                            'menu'            => 'landing_menu',
                            'theme_location'  => 'landing_menu',
                            'container'       => false,
                            'menu_id'         => false,
                            'menu_class'      => '',
                            'depth'           => 1,
                        ]);
                    ?>
                </div>
                <div class="buttons">
	            	<?php if ( !is_user_logged_in() ) : ?>
	                    <a href="<?php echo get_site_url() . '/user-account'; ?>" class="btn btn-purple btn-sm">Kirish</a>
	                <?php else: ?>
	                	<div class="dropdown">
	                        <button class="btn btn-purple btn-sm px-4 dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	                            <span class="icon icon-user"></span>
	                        </button>
                        	<div class="shadowy-triangle"></div>
	                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
	                            <a class="dropdown-item" href="<?php echo get_site_url() . '/user-account'; ?>">Mening Profilim</a>
	                            <a class="dropdown-item text-danger" href="<?php echo wp_logout_url(home_url()); ?>">Chiqish</a>
	                        </div>
	                    </div>
		            <?php endif; ?>
                    <a href="javascript:;" class="btn btn-purple btn-sm mobile-menu__toggle">
                        <i class="icon icon-bars"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Main -->
<main class="main">

    <!-- Section - Intro -->
    <?php $section_intro = get_field('intro'); ?>
    <?php $advice_modal = get_field('advice_modal'); ?>
    <section class="section section-intro">
        <div class="container section__container">
            <div class="row align-items-end">
                <div class="col section__col-1">
                    <div class="title">
                        <h1><?php echo $section_intro['title']; ?></h1>
                        <?php
                            $intro_subtitles = array();
                            for ($i=1; $i <= 5 ; $i++) {
                                $subtitle_number = 'subtitle_' . $i;
                                $subtitle = $section_intro['subtitles'][$subtitle_number];

                                if ($subtitle != '') {
                                    $subtitle = array_push($intro_subtitles, $subtitle);
                                }
                            }
                        ?>
                        <?php if ( !empty($intro_subtitles) ): ?>
                            <div class="type-wrap">
                                <span id="typed" class="typed"></span>
                            </div>
                        <?php endif; ?>
                        <p><?php echo $section_intro['description']; ?></p>
                    </div>
                    <div class="d-flex justify-content-center justify-content-xl-start flex-column flex-md-row align-items-center align-items-md-start">
                        <?php if ( is_user_logged_in() ) : ?>
                            <a href="/courses" class="btn btn-purple shimmer-animation animated px-md-5 mb-4 mb-md-0 mr-md-4">Boshlash</a>
                        <?php else: ?>
                            <?php $intro_button = $section_intro['button'];
                                if ( $intro_button ) : 
                                    $intro_button_url = $intro_button['url'];
                                    $intro_button_title = $intro_button['title'];
                            ?>
                                <a href="<?php echo $intro_button_url; ?>" class="btn btn-purple shimmer-animation animated px-md-5 mb-4 mb-md-0 mr-md-4">
                                    <?php echo $intro_button_title; ?>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php $advice_modal_button = $advice_modal['advice_button'];
                            if ( $advice_modal_button ) : 
                                $advice_modal_button_url = $advice_modal_button['url'];
                                $advice_modal_button_title = $advice_modal_button['title'];
                        ?>
                            <a href="javascript:;" class="btn btn-primary px-md-5" data-toggle="modal" data-target="<?php echo $advice_modal_button_url; ?>">
                                <?php echo $advice_modal_button_title; ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col section__col-2">
                    <img src="<?php echo $section_intro['image']; ?>" class="section__image" alt="">
                </div>
            </div>
        </div>
        <div class="shape-1"></div>
        <div class="shape-2"></div>
    </section>

    <!-- Section - What -->
    <div class="anchor" id="what"></div>
    <?php $section_what = get_field('what'); ?>
    <section class="section section-what">
        <div class="container section__container">
            <div class="title title--center">
                <h2><?php echo $section_what['title']; ?></h2>
                <p><?php echo $section_what['description']; ?></p>
            </div>
            <div class="row align-items-end">
                <div class="col-xl-6 section__col-1">
                    <div class="section__col-inner">
                        <?php echo $section_what['content']; ?>
                        <?php if ( is_user_logged_in() ) : ?>
                            <a href="/courses" class="btn btn-purple shimmer-animation">Boshlash</a>
                        <?php else: ?>
    		                <?php $button = $section_what['button'];
    		                	if ( $button ) : 
    		                		$button_url = $button['url'];
    		                		$button_title = $button['title'];
    		                ?>
    	                        <a href="<?php echo $button_url; ?>" class="btn btn-purple shimmer-animation">
    	                        	<?php echo $button_title; ?>
    	                        </a>
    		                <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-xl-6 section__col-2">
                    <img src="<?php echo $section_what['image']; ?>" class="section__image" alt="">
                    <div class="shape-3"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section - Mission -->
    <div class="anchor" id="mission"></div>
    <?php $section_mission = get_field('mission'); ?>
    <div class="section section-mission">
        <div class="container section__container">
            <div class="title title--center">
                <h2><?php echo $section_mission['title']; ?></h2>
                <p><?php echo $section_mission['description']; ?></p>
            </div>
            <div class="row mission__row">
                <div class="col-md-6 col-lg-4 mission__col-1">
                    <div class="mission__item">
                    	<?php echo $section_mission['mission_1']['icon']; ?>
                        <strong><?php echo $section_mission['mission_1']['title']; ?></strong>
                        <p><?php echo $section_mission['mission_1']['description']; ?></p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mission__col-2">
                    <div class="mission__item">
                    	<?php echo $section_mission['mission_2']['icon']; ?>
                        <strong><?php echo $section_mission['mission_2']['title']; ?></strong>
                        <p><?php echo $section_mission['mission_2']['description']; ?></p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mission__col-3">
                    <div class="mission__item">
                    	<?php echo $section_mission['mission_3']['icon']; ?>
                        <strong><?php echo $section_mission['mission_3']['title']; ?></strong>
                        <p><?php echo $section_mission['mission_3']['description']; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="mission__line">
            <div class="container">
                <div class="row mission__row">
                    <div class="col-lg-4 mission__col"><span></span></div>
                    <div class="col-lg-4 mission__col"><span></span></div>
                    <div class="col-lg-4 mission__col"><span></span></div>
                </div>
            </div>
        </div>
        <div class="shape-4"></div>
        <div class="shape-5"></div>
    </div>

    <!-- Section - Who -->
    <?php $section_who = get_field('who'); ?>
    <section class="section section-who">
        <div class="container section__container">
            <div class="title title--center">
                <h2><?php echo $section_who['title']; ?></h2>
                <p><?php echo $section_who['description']; ?></p>
            </div>
            <div class="checklist">
                <ul>
                	<li><?php echo $section_who['items']['item_1']; ?></li>
                	<li><?php echo $section_who['items']['item_2']; ?></li>
                	<li><?php echo $section_who['items']['item_3']; ?></li>
                	<li><?php echo $section_who['items']['item_4']; ?></li>
                	<li><?php echo $section_who['items']['item_5']; ?></li>
                	<li><?php echo $section_who['items']['item_6']; ?></li>
                	<li><?php echo $section_who['items']['item_7']; ?></li>
                	<li><?php echo $section_who['items']['item_8']; ?></li>
                </ul>
            </div>
            <?php if ( is_user_logged_in() ) : ?>
                <div class="text-center">
                    <a href="/courses" class="btn btn-purple shimmer-animation">Boshlash</a>
                </div>
            <?php else: ?>
                <?php $button = $section_who['button'];
                	if ( $button ) : 
                		$button_url = $button['url'];
                		$button_title = $button['title'];
                ?>
    	            <div class="text-center">
    	                <a href="<?php echo $button_url; ?>" class="btn btn-purple shimmer-animation"><?php echo $button_title; ?></a>
    	            </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="shape-6"></div>
        <div class="shape-7"></div>
    </section>

    <!-- Section - Services -->
    <div class="anchor" id="services"></div>
    <?php $section_services = get_field('services'); ?>
    <section class="section section-services">
        <div class="container">
            <div class="title title--center">
                <h2><?php echo $section_services['title']; ?></h2>
                <p><?php echo $section_services['description']; ?></p>
            </div>
            <div class="row services__row">
                <div class="col-md-6 col-lg-4 services__col-1">
                    <div class="services__item">
                        <img src="<?php echo $section_services['service_1']['image']; ?>" alt="">
                        <strong><?php echo $section_services['service_1']['title']; ?></strong>
                        <p><?php echo $section_services['service_1']['description']; ?></p>
			            <?php $button = $section_services['service_1']['button'];
			            	if ( $button ) : 
			            		$button_url = $button['url'];
			            		$button_title = $button['title'];
			            ?>
			                <a href="<?php echo $button_url; ?>" class="btn btn-purple btn-sm shimmer-animation"><?php echo $button_title; ?></a>
			            <?php else: ?>
	                        <a href="#" class="btn btn-purple btn-sm disabled">Tez kunda</a>
			            <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 services__col-2">
                    <div class="services__item">
                        <img src="<?php echo $section_services['service_2']['image']; ?>" alt="">
                        <strong><?php echo $section_services['service_2']['title']; ?></strong>
                        <p><?php echo $section_services['service_2']['description']; ?></p>
			            <?php $button = $section_services['service_2']['button'];
			            	if ( $button ) : 
			            		$button_url = $button['url'];
			            		$button_title = $button['title'];
			            ?>
			                <a href="<?php echo $button_url; ?>" class="btn btn-purple btn-sm shimmer-animation"><?php echo $button_title; ?></a>
			            <?php else: ?>
	                        <a href="#" class="btn btn-purple btn-sm disabled">Tez kunda</a>
			            <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 services__col-3">
                    <div class="services__item">
                        <img src="<?php echo $section_services['service_3']['image']; ?>" alt="">
                        <strong><?php echo $section_services['service_3']['title']; ?></strong>
                        <p><?php echo $section_services['service_3']['description']; ?></p>
			            <?php $button = $section_services['service_3']['button'];
			            	if ( $button ) : 
			            		$button_url = $button['url'];
			            		$button_title = $button['title'];
			            ?>
			                <a href="<?php echo $button_url; ?>" class="btn btn-purple btn-sm shimmer-animation"><?php echo $button_title; ?></a>
			            <?php else: ?>
	                        <a href="#" class="btn btn-purple btn-sm disabled">Tez kunda</a>
			            <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section - About -->
    <?php $section_about = get_field('about'); ?>
    <section class="section section-about">
        <div class="container section__container">
            <div class="title title--center">
                <h2><?php echo $section_about['title']; ?></h2>
                <p><?php echo $section_about['description']; ?></p>
            </div>
            <div class="row align-items-end">
                <div class="col-xl-5 section__col">
                    <div class="about">
                        <div class="about__item">
                            <h3><?php echo $section_about['rows']['about_row_1']['title']; ?></h3>
                            <p><?php echo $section_about['rows']['about_row_1']['description']; ?></p>
                        </div>
                        <div class="about__item">
                            <div class="about__item-title">
                                <img src="<?php echo $section_about['rows']['about_row_2']['image']; ?>" alt="">
                                <h5><?php echo $section_about['rows']['about_row_2']['title']; ?></h5>
                            </div>
                            <p><?php echo $section_about['rows']['about_row_2']['description']; ?></p>
                        </div>
                        <div class="about__item">
                            <div class="about__item-title">
                                <img src="<?php echo $section_about['rows']['about_row_3']['image']; ?>" alt="">
                                <h5><?php echo $section_about['rows']['about_row_3']['title']; ?></h5>
                            </div>
                            <p><?php echo $section_about['rows']['about_row_3']['description']; ?></p>
                        </div>
                    </div>
                    <?php if ( is_user_logged_in() ) : ?>
                        <div class="text-center text-lg-left">
                            <a href="/courses" class="btn btn-purple shimmer-animation">Boshlash</a>
                        </div>
                    <?php else: ?>
    		            <?php $button = $section_about['rows']['button'];
    		            	if ( $button ) : 
    		            		$button_url = $button['url'];
    		            		$button_title = $button['title'];
    		            ?>
    		                <a href="<?php echo $button_url; ?>" class="btn btn-purple shimmer-animation"><?php echo $button_title; ?></a>
    		            <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="col-xl-7 section__col">
                    <div class="d-flex justify-content-end">
                        <img src="<?php echo $section_about['image']; ?>" class="section__image" alt="">
                    </div>
                    <div class="follow-pages">
                        <ul>
                            <li>
                            	<a href="<?php echo $section_about['pages']['upwork']; ?>" target="_blank">
                            		<img src="<?php echo $section_about['pages']['upwork_logo']; ?>" alt="Upwork">
                            	</a>
                            </li>
                            <li>
                            	<a href="<?php echo $section_about['pages']['toptal']; ?>" target="_blank">
                            		<img src="<?php echo $section_about['pages']['toptal_logo']; ?>" alt="Toptal">
                            	</a>
                            </li>
                            <li>
                            	<a href="<?php echo $section_about['pages']['techcells']; ?>" target="_blank">
                            		<img src="<?php echo $section_about['pages']['techcells_logo']; ?>" alt="Techcells">
                            	</a>
                            </li>
                            <li>
                            	<a href="<?php echo $section_about['pages']['youtube']; ?>" target="_blank">
                            		<img src="<?php echo $section_about['pages']['youtube_logo']; ?>" alt="Youtube">
                            	</a>
                            </li>
                            <li>
                            	<a href="<?php echo $section_about['pages']['facebook']; ?>" target="_blank">
                            		<img src="<?php echo $section_about['pages']['facebook_logo']; ?>" alt="Facebook">
                            	</a>
                            </li>
                            <li>
                            	<a href="<?php echo $section_about['pages']['telegram']; ?>" target="_blank">
                            		<img src="<?php echo $section_about['pages']['telegram_logo']; ?>" alt="Telegram">
                            	</a>
                            </li>
                            <li>
                            	<a href="<?php echo $section_about['pages']['instagram']; ?>" target="_blank">
                            		<img src="<?php echo $section_about['pages']['instagram_logo']; ?>" alt="Instagram">
                            	</a>
                            </li>
                            <li>
                            	<a href="<?php echo $section_about['pages']['tiktok']; ?>" target="_blank">
                            		<img src="<?php echo $section_about['pages']['tiktok_logo']; ?>" alt="Tiktok">
                            	</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="shape-8"></div>
    </section>

    <!-- Section - courses -->
    <?php $section_courses = get_field('courses');


    ?>
    <div class="anchor" id="courses"></div>
    <section class="section section-courses">
        <div class="container">
            <div class="title title--center">
                <h2><?php echo $section_courses['title']; ?></h2>
                <p><?php echo $section_courses['description']; ?></p>
            </div>
            <div class="row courses__row">
                <div class="col-md-6 col-lg-4 col-xl-3 courses__col-1">
                    <div class="courses__item">
                        <img src="<?php echo $section_courses['course_1']['image']; ?>" alt="">
                        <strong><?php echo $section_courses['course_1']['title']; ?></strong>
                        <p><?php echo $section_courses['course_1']['description']; ?></p>
			            <?php


                        $button = $section_courses['course_1']['button'];
			            	if ( $button || $section_courses['course_1']['title'] =='Upwork' ) :
			            		$button_url = ($button['url'])? $button['url']: 'https://www.itstars.uz/courses/upwork/';
			            		$button_title = ($button['title'])?$button['title']:'Kirish';
			            ?>
			                <a href="<?php echo $button_url; ?>" class="btn btn-purple btn-sm shimmer-animation"><?php echo $button_title; ?></a>
			            <?php else: ?>
	                        <a href="#" class="btn btn-purple btn-sm disabled">Tez kunda</a>
			            <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 col-xl-3 courses__col-2">
                    <div class="courses__item">
                        <img src="<?php echo $section_courses['course_2']['image']; ?>" alt="">
                        <strong><?php echo $section_courses['course_2']['title']; ?></strong>
                        <p><?php echo $section_courses['course_2']['description']; ?></p>
			            <?php $button = $section_courses['course_2']['button'];
			            	if ( $button ) : 
			            		$button_url = $button['url'];
			            		$button_title = $button['title'];
			            ?>
			                <a href="<?php echo $button_url; ?>" class="btn btn-purple btn-sm shimmer-animation"><?php echo $button_title; ?></a>
			            <?php else: ?>
	                        <a href="#" class="btn btn-purple btn-sm disabled">Tez kunda</a>
			            <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 col-xl-3 courses__col-3">
                    <div class="courses__item">
                        <img src="<?php echo $section_courses['course_3']['image']; ?>" alt="">
                        <strong><?php echo $section_courses['course_3']['title']; ?></strong>
                        <p><?php echo $section_courses['course_3']['description']; ?></p>
			            <?php $button = $section_courses['course_3']['button'];
			            	if ( $button ) : 
			            		$button_url = $button['url'];
			            		$button_title = $button['title'];
			            ?>
			                <a href="<?php echo $button_url; ?>" class="btn btn-purple btn-sm shimmer-animation"><?php echo $button_title; ?></a>
			            <?php else: ?>
	                        <a href="#" class="btn btn-purple btn-sm disabled">Tez kunda</a>
			            <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 col-xl-3 courses__col-4">
                    <div class="courses__item">
                        <img src="<?php echo $section_courses['course_4']['image']; ?>" alt="">
                        <strong><?php echo $section_courses['course_4']['title']; ?></strong>
                        <p><?php echo $section_courses['course_4']['description']; ?></p>
			            <?php $button = $section_courses['course_4']['button'];
			            	if ( $button ) : 
			            		$button_url = $button['url'];
			            		$button_title = $button['title'];
			            ?>
			                <a href="<?php echo $button_url; ?>" class="btn btn-purple btn-sm shimmer-animation"><?php echo $button_title; ?></a>
			            <?php else: ?>
	                        <a href="#" class="btn btn-purple btn-sm disabled">Tez kunda</a>
			            <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section - Testimonials -->
    <?php
    	$section_testimonials = get_field('testimonials');
    	$testimonials = new WP_Query(
    		array(
		        'post_type'		 => 'tech_testimonials',
		        'post_status'	 => 'publish',
		        'posts_per_page' => -1,
			)
	    );
    ?>
    <section class="section section-testimonials">
        <div class="container section__container">
            <div class="title title--center">
                <h2><?php echo $section_testimonials['title']; ?></h2>
                <p><?php echo $section_testimonials['description']; ?></p>
            </div>
            <div class="testimonials">
                <div id="testimonials_slider" class="owl-carousel">
                    <?php if ( $testimonials->have_posts() ) : ?>
					    <?php while ( $testimonials->have_posts() ) : $testimonials->the_post(); ?>
					    	<?php 
						    	$testimonial_id = get_the_ID();
					    		$customer = get_field('customer', $testimonial_id);
					    	?>
		                    <div class="item">
		                        <div class="testimonial">
		                            <?php the_content(); ?>
		                            <div class="testimonial__footer">
		                                <div class="testimonial__author">
		                                    <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>">
		                                    <div>
		                                        <strong><?php the_title(); ?></strong>
		                                        <span><?php echo $customer['profession']; ?></span>
		                                    </div>
		                                </div>
		                                <div class="testimonial__logo">
		                                    <a href="<?php echo $customer['url']; ?>" target="_blank"><img src="<?php echo $customer['logo']; ?>" alt=""></a>
		                                </div>
		                            </div>
		                        </div>
		                    </div>
					    <?php endwhile; ?>
					    <?php wp_reset_postdata(); ?>
					<?php else : ?>
					    <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
					<?php endif; ?>
                </div>
                <div id="testimonials_slider_nav" class="owl-nav"></div>
            </div>
        </div>
        <div class="shape-9"></div>
    </section>

    <!-- Section - Videos -->
    <?php
    	$section_videos = get_field('videos');
    	$videos = new WP_Query(
    		array(
		        'post_type'		 => 'tech_videos',
		        'post_status'	 => 'publish',
		        'posts_per_page' => -1,
			)
	    );
	?>
    <section class="section section-videos">
        <div class="container section__container">
            <div class="title title--center">
                <h2><?php echo $section_videos['title']; ?></h2>
                <p><?php echo $section_videos['description']; ?></p>
            </div>
            <div class="videos">
                <div id="videos_slider" class="owl-carousel">
                    <?php if ( $videos->have_posts() ) : ?>
					    <?php $i = 0; while ( $videos->have_posts() ) : $videos->the_post(); ?>
		                    <div class="item">
		                    	<a href="javascript:;" class="video" data-toggle="modal" data-target="#youtubeVideo-<?php echo $i; ?>">
		                            <div class="video__icon">
		                                <div><i class="icon icon-play-o"></i></div>
		                            </div>
		                            <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>">
		                            <div class="video__overlay"></div>
		                        </a>
		                    </div>
					    <?php $i++; endwhile; ?>
					    <?php wp_reset_postdata(); ?>
					<?php else : ?>
					    <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
					<?php endif; ?>
                </div>
                <div id="videos_slider_nav" class="owl-nav"></div>
            </div>
        </div>
        <div class="shape-10"></div>
    </section>

</main>

<!-- Footer -->
<?php
	$footer = get_field('footer');
?>
<footer class="footer">
    <div class="container">
        <div class="footer__top">
            <div class="footer__menu">
                <?php
                    wp_nav_menu([
                        'menu'            => 'landing_footer',
                        'theme_location'  => 'landing_footer',
                        'container'       => false,
                        'menu_id'         => false,
                        'menu_class'      => '',
                        'depth'           => 1,
                    ]);
                ?>
            </div>
            <div class="footer__logo">
                <a href="<?php bloginfo('url'); ?>">
                    <img src="<?php echo $footer['logo'] ?>" alt="">
                </a>
            </div>
        </div>
        <div class="footer__bottom">
            <div class="footer__socials">
                <ul>
                    <li>
                        <a href="<?php echo $footer['socials']['tiktok']; ?>" target="_blank">
                            <i class="icon icon-tiktok"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $footer['socials']['instagram']; ?>" target="_blank">
                            <i class="icon icon-instagram"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $footer['socials']['youtube']; ?>" target="_blank">
                            <i class="icon icon-youtube"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo $footer['socials']['telegram']; ?>" target="_blank">
                            <i class="icon icon-telegram"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="footer__copyright">
                <p><?php echo $footer['copyright']; ?></p>
            </div>
        </div>
    </div>
</footer>

<!-- Video modal -->
<?php if ( $videos->have_posts() ) : ?>
    <?php $i = 0; while ( $videos->have_posts() ) : $videos->the_post(); ?>
    	<?php 
	    	$video_id = get_the_ID();
    		$video_url = get_field('video_url', $video_id);
    	?>
		<div class="modal fade modal-style-1" id="youtubeVideo-<?php echo $i; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		    <div class="modal-dialog modal-dialog-centered modal-lg">
		        <div class="modal-content">
		            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		                <span class="icon icon-remove"></span>
		            </button>
		            <div class="modal-body p-0 d-flex">
		                <iframe width="100%" height="450" src="<?php echo $video_url; ?>" frameborder="0" allowfullscreen></iframe>
		            </div>
		        </div>
		    </div>
		</div>
    <?php $i++; endwhile; ?>
    <?php wp_reset_postdata(); ?>
<?php endif; ?>

<!-- Advice modal -->
<div class="modal fade modal-style-1" id="advice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span class="icon icon-remove"></span>
            </button>
            <div class="modal-body px-6 py-6 px-sm-10 py-sm-10">
                <div class="text-center">
                    <h3><?php echo $advice_modal['title'] ?></h3>
                    <p><?php echo $advice_modal['description'] ?></p>
    				
                    <?php /* ?>
                    <?php $call_button = $advice_modal['call_button'];
                        if ( $call_button ) : 
                            $call_button_url = $call_button['url'];
                            $call_button_title = $call_button['title'];
                    ?>
                        <h3>
                            <a href="<?php echo $call_button_url; ?>" class="text-secondary">
                                <?php echo $call_button_title; ?>
                            </a>
                        </h3>
                    <?php endif; ?>
                    <p>Yoki</p>
                    <?php */ ?>
                    <?php $telegram_button = $advice_modal['telegram_button'];
                        if ( $telegram_button ) : 
                            $telegram_button_url = $telegram_button['url'];
                            $telegram_button_title = $telegram_button['title'];
                    ?>
                        <a href="<?php echo $telegram_button_url; ?>" target="_blank" class="btn btn-primary px-4 px-sm-14">
                            <i class="icon icon-telegram fs-30 mr-2 mr-sm-4" style="vertical-align: -6px;"></i><?php echo $telegram_button_title; ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
    
<script src="<?php echo get_stylesheet_directory_uri() . '/assets/js/jquery.min.js'; ?>"></script>
<script src="<?php echo get_stylesheet_directory_uri() . '/assets/js/bootstrap.bundle.min.js'; ?>"></script>
<script src="<?php echo get_stylesheet_directory_uri() . '/assets/js/owl.carousel.js'; ?>"></script>
<script src="<?php echo get_stylesheet_directory_uri() . '/assets/js/typed.min.js'; ?>"></script>
<script src="<?php echo get_stylesheet_directory_uri() . '/assets/js/main.js'; ?>"></script>

<?php if ( !empty($intro_subtitles) ): ?>
<script async>
    $("#typed").typed({
        strings: <?php echo json_encode($intro_subtitles); ?>,
        typeSpeed: 0,
        startDelay: 0,
        backSpeed: 0,
        backDelay: 3000,
        loop: true,
        cursorChar: "|",
        contentType: 'html'
    });
</script>
<?php endif; ?>
</body>
</html>