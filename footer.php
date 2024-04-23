		<?php $home_page_id = 8313; ?>

		<!-- Footer -->
		<?php
			$footer = get_field('footer', $home_page_id);
		?>


<a href="#" style="display: none"
   class="btn btn-default stm-lms-modal-oferta-button"
   data-target=".stm-lms-modal-oferta"
   data-lms-modal="oferta"> teeee

</a>


		<footer class="tech__footer">
		    <div class="container">
		        <div class="tech__footer__top">
		            <div class="tech__footer__menu">
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
		            <div class="tech__footer__logo">
		                <a href="<?php bloginfo('url'); ?>">
		                    <img src="<?php echo $footer['logo'] ?>" alt="">
		                </a>
		            </div>
		        </div>
		        <div class="tech__footer__bottom">
		            <div class="tech__footer__socials">
		                <ul>
		                    <li>
		                        <a href="<?php echo $footer['socials']['facebook']; ?>" target="_blank">
		                            <i class="tech__icon tech__icon-facebook"></i>
		                        </a>
		                    </li>
		                    <li>
		                        <a href="<?php echo $footer['socials']['tiktok']; ?>" target="_blank">
		                            <i class="tech__icon tech__icon-tiktok"></i>
		                        </a>
		                    </li>
		                    <li>
		                        <a href="<?php echo $footer['socials']['instagram']; ?>" target="_blank">
		                            <i class="tech__icon tech__icon-instagram"></i>
		                        </a>
		                    </li>
		                    <li>
		                        <a href="<?php echo $footer['socials']['youtube']; ?>" target="_blank">
		                            <i class="tech__icon tech__icon-youtube"></i>
		                        </a>
		                    </li>
		                    <li>
		                        <a href="<?php echo $footer['socials']['telegram']; ?>" target="_blank">
		                            <i class="tech__icon tech__icon-telegram"></i>
		                        </a>
		                    </li>
		                </ul>
		            </div>
		            <div class="tech__footer__copyright">
		                <p><?php echo $footer['copyright']; ?></p>
		            </div>
		        </div>
		    </div>
		</footer>

		<style>
			.tech__footer{padding:100px 0 80px 0;background-color:#241E28;}
			@media (max-width:991.98px){.tech__footer{padding:50px 0;}}
			.tech__footer__top{display:flex;justify-content:space-between;margin-bottom:50px;}
			@media (max-width:991.98px){.tech__footer__top{flex-direction:column-reverse;align-items:center;margin-bottom:20px;}}
			.tech__footer__bottom{display:flex;align-items:flex-end;justify-content:space-between;}
			@media (max-width:991.98px){.tech__footer__bottom{flex-direction:column;align-items:center;}}
			.tech__footer__menu{display:flex;}
			.tech__footer__menu ul{margin:0;padding:0;list-style:none;column-count:3;column-gap:40px;}
			@media (max-width:1199.98px){.tech__footer__menu ul{column-gap:20px;}}
			@media (max-width:767.98px){.tech__footer__menu ul{column-count:1;column-gap:0;text-align:center;}}
			.tech__footer__menu ul li{min-width:220px;margin-bottom:25px;}
			@media (max-width:1199.98px){.tech__footer__menu ul li{min-width:180px;}}
			.tech__footer__menu ul li a{color:#FFF;transition:all 0.2s ease-in-out;font-size: 1.25rem;line-height: 1.5;}
			@media (prefers-reduced-motion:reduce){.tech__footer__menu ul li a{transition:none;}}
			.tech__footer__menu ul li a:hover{color:#2EC097;text-decoration:none;}
			@media (max-width:991.98px){.tech__footer__logo{display:none;}}
			.tech__footer__logo img{width:190px;}
			@media (max-width:991.98px){.tech__footer__socials{margin-bottom:40px;}}
			.tech__footer__socials ul{display:flex;margin:0;padding:0;list-style:none;}
			.tech__footer__socials ul li:not(:last-child){margin-right:40px;}
			.tech__footer__socials ul li{margin-bottom:0;}
			@media (max-width:575.98px){.tech__footer__socials ul li:not(:last-child){margin-right:30px;}}
			.tech__footer__socials ul li a{display:block;font-size:30px;color:#FFF;transition:all 0.2s ease-in-out;}
			@media (prefers-reduced-motion:reduce){.tech__footer__socials ul li a{transition:none;}}
			.tech__footer__socials ul li a:hover{color:#2EC097;text-decoration:none;}
			@media (max-width:991.98px){.tech__footer__copyright{text-align:center;}}
			.tech__footer__copyright p{margin-bottom:0;color:rgba(255, 255, 255, 0.5);font-size: 1.25rem;line-height: 1.5;}

			/* Icons */
			@font-face {
			  font-family: 'icomoon';
			  src: url("../fonts/Icons/icomoon.eot?f8kjpq");
			  src: url("../fonts/Icons/icomoon.eot?f8kjpq#iefix") format("embedded-opentype"), url("../fonts/Icons/icomoon.ttf?f8kjpq") format("truetype"), url("../fonts/Icons/icomoon.woff?f8kjpq") format("woff"), url("../fonts/Icons/icomoon.svg?f8kjpq#icomoon") format("svg");
			  font-weight: normal;
			  font-style: normal;
			  font-display: block;
			}
			[class^="tech__icon-"], [class*=" tech__icon-"] {
			  /* use !important to prevent issues with browser extensions that change fonts */
			  font-family: 'icomoon' !important;
			  speak: never;
			  font-style: normal;
			  font-weight: normal;
			  font-variant: normal;
			  text-transform: none;
			  line-height: 1;
			  /* Better Font Rendering =========== */
			  -webkit-font-smoothing: antialiased;
			  -moz-osx-font-smoothing: grayscale;
			}
			.tech__icon-facebook:before {content: "\e900";}
			.tech__icon-tiktok:before {content: "\e901";}
			.tech__icon-instagram:before {content: "\e902";}
			.tech__icon-youtube:before {content: "\e903";}
			.tech__icon-telegram:before {content: "\e904";}
		</style>

	<?php wp_footer(); ?>

	</body>

</html>