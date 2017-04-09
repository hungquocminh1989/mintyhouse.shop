<?php
function show_wd_portfolio( $columns = 4, $portfolio_cats = '', $show_filter = "yes", $style="", $show_title = "yes" ,$show_desc = "yes", $show_paging = 'yes', $count = "-1" ){
	$wd_portfolio = new WD_Portfolio();
	$post_per_page = $count;
?>
						<div id="portfolio-container">
							<div id="portfolio-container-holder">	
								<div class="portfolio-galleries" id="portfolio-galleries">
									<?php $terms = get_terms('wd-portfolio-category',array('hide_empty'=>true)); ?>
									<input class="limited" type="hidden" value="<?php echo $count ;?>" />
									<?php if( $columns > 1 && strcmp('yes',$show_filter) == 0 ) : ?>
									<div class="container">
										<div class="portfolio-filter-wrapper">
											
												<div class="filter_menu">
													<span class="label-title"><?php _e('Sort Portfolio:','wpdance_pf');?></span>
													<ul class="portfolio-filter">
														<li id="all" class="active"><a href="javascript:void(0)" id="all_a" class="filter-portfoio active"><?php _e('ALL','wpdance_pf');?></a></li>
													<?php foreach( $terms as $term ) : ?>
														<li id="<?php echo esc_html($term->slug) ; ?>"><a href="javascript:void(0)" id="<?php echo esc_html($term->slug) ; ?>_a" class="filter-portfoio"><?php echo esc_html(get_term($term,'wd-portfolio-category')->name); ?></a></li>
													<?php endforeach;?>
													</ul>
												</div>
												<div class="current-select">
													<span><?php _e('All','wpdance_pf');?></span>
												</div>

										</div>
									</div>
									<?php endif; ?>
								
									<?php $terms=get_terms('wd-portfolio-category',array('hide_empty'=>true)); ?>
									<input class="limited" type="hidden" value="<?php echo get_option('posts_per_page' ) ;?>" />
									<div id="portfolio-galleries-holder">
									<?php	
										$title_icon = "";
										
										$args = array(
											'post_type' => 'portfolio'
											,'posts_per_page' => $count
											,'paged' => get_query_var('page')
										);
										if( $portfolio_cats!="" ){
											$args['tax_query'] = array(
																array(
																	'taxonomy' => 'wd-portfolio-category'
																	,'terms' => explode(',',esc_attr($portfolio_cats))
																	,'field' => 'slug'
																	,'operator' => 'IN'
																)
											);
										}
										query_posts($args);
										
										$count=0;
										if(have_posts()) : while(have_posts()) : the_post(); global $post;global $wp_query;
											$post_title = esc_html(get_the_title($post->ID));
											$post_url =  esc_url(get_permalink($post->ID));

											$term_list = implode( ' ', wp_get_post_terms($post->ID, 'wd-portfolio-category', array("fields" => "slugs")) );

											$thumb = get_post_thumbnail_id($post->ID);
											$thumburl = wp_get_attachment_image_src($thumb,'portfolio_image');
											$item_class = "thumb-image";
											
											$light_box_url = trim($wd_portfolio->wd_portfolio_get_meta('wd-portfolio'));
							
											
											if( strlen( $light_box_url ) <= 0 ){
												$light_box_url = $thumburl[0];
											}
											$light_box_class = $wd_portfolio->wd_portfolio_get_filetype( $light_box_url );
											
											
											$portfolio_slider = get_post_meta($post->ID,'_wd_slider',true);
											$portfolio_slider = unserialize($portfolio_slider);
											$slider_thumb = false;
											if( is_array($portfolio_slider) && count($portfolio_slider) > 0 ){
												$slider_thumb = true;
												$item_class = "thumb-slider";
											}
											$post_count = $wp_query->post_count;
											$div_pos = $count % 3;
										?>
										<?php $class_span = 'col-sm-'.(24 / $columns);?>
										<div class="item <?php echo $class_span;?> item-portfolio<?php //if($count % $columns == 0)  echo " first"; ?><?php  //if($count % $columns == ($columns - 1) || ($count + 1) == $wp_query->post_count ){ echo " last";} ?>" data-type="<?php echo $term_list;?>" data-id="<?php echo $post->ID;?>">
											<div class="p-content">	
												<div class="thumb-holder <?php echo $item_class;?>">
													<div class="thumbnail">	
														<div class="thumb-image post-item ">
															<?php if( $slider_thumb ){?>	
															
																	<a class="image image-holder" href="<?php echo $post_url; ?>">
																		<?php if(  $_thumb_uri = wp_get_attachment_image_src( $portfolio_slider[0]['thumb_id'], 'portfolio_image', false ) ): ?>
																			<img alt="<?php echo $post_title?>" title="<?php echo $post_title;?>" class="opacity_0" src="<?php echo  esc_url($_thumb_uri[0]);?>"/>																
																		<?php else:  ?>
																			<img alt="<?php echo $post_title?>" title="<?php echo $post_title;?>" class="opacity_0" src="<?php echo get_template_directory_uri(); ?>/images/no-image-portfolio.gif"/>
																		<?php endif;?>
																	</a>															
																	
																	<div class="wd-portfolio-slider">
																		<ul class="port-slides">
																			<?php foreach( $portfolio_slider as $slide ){ ?>	
																			<?php $_thumb_uri = wp_get_attachment_image_src( $slide['thumb_id'], 'portfolio_image', false );
																				$_thumb_uri = $_thumb_uri[0];
																				$_sub_thumb_uri = wp_get_attachment_image_src( $slide['thumb_id'], 'portfolio_image', false );
																				$_sub_thumb_uri = $_sub_thumb_uri[0]; 
																			?>
																				<li data-thumb="<?php  echo esc_url($_sub_thumb_uri);//echo print_thumbnail($slide['image_url'],true,$post_title,124,68,'',false,true); ?>"><a href="<?php echo esc_url($slide['url']);?>"><img alt="<?php echo esc_html($slide['alt']);?>" class="opacity_0" src="<?php echo  esc_url($_thumb_uri);//echo print_thumbnail($slide['image_url'],true,$post_title,620,340,'',false,true); ?>"/></a></li>
																				
																			<?php } ?>
																		</ul>	
																		
																	</div>	
																<?php }else{ ?>	
																	
																	<a class="image" href="<?php echo $post_url; ?>">
																		<?php if($thumburl[0]): ?>
																			<img alt="<?php echo $post_title?>" title="<?php echo $post_title;?>" class="opacity_0" src="<?php echo  esc_url($thumburl[0]);?>"/>																
																		<?php else:  ?>
																			<img alt="<?php echo $post_title?>" title="<?php echo $post_title;?>" class="opacity_0" src="<?php echo get_template_directory_uri(); ?>/images/no-image-blog.gif"/>
																		<?php endif;?>
																	</a>
																	
																<?php } ?>	
															<div class="hover-default thumb-image-hover">
																<div class="background opacity_6"></div>
																<div class="icons">
																	<a class="zoom-gallery wd_pretty_photo thumb-image <?php echo $light_box_class;?>" title="<?php _e("View Portfolio","wpdance_pf"); ?>" data-rel="wd_pretty_photo['<?php echo $light_box_class;?>']" href="<?php echo esc_url($light_box_url);?>"></a>
																	<a class="link-gallery " title="<?php _e("View Details","wpdance_pf");?>" href="<?php echo $post_url;?>"></a>
																</div>
															</div>
															<div class="thumb-hover-start"></div>													
														</div>
													</div>	
													<div class="thumb-tag">
														<?php if($show_title == 'yes'): ?>
														<h2 class="post-title heading-title list-title portfolio-grid-title">
															<a  href="<?php echo $post_url; ?>">
															<?php echo $post_title; ?>
															</a>
														</h2>
														<div class="date"> <i class="fa fa-calendar-o"></i><?php the_time(get_option('date_format')); ?></div>
														<?php endif; ?>
														<?php if($show_desc == 'yes'): ?>
														<div class="desc"><?php echo do_shortcode(substr(get_the_content(),0,90)); ?></div>
														<?php endif; ?>
													</div>   										
												</div>
											</div>
										</div>
										<?php		
											$count++;
										endwhile;
										else : _e('Sorry. There are no posts to display', 'wpdance_pf');
										endif;	
									?>	
									</div>
								</div>
							</div>
							<div class="clear"></div>
							<?php if( have_posts() && strcmp($show_paging,'yes') == 0 ) : ?>
							<?php global $wp_query; ?>
							<div class="end_content">
							   <div class="count_project"><span class="number_project"><?php echo $wp_query->post_count; ?></span> <?php _e('Project(s)','wpdance_pf');?></div>
							   <?php if( $wp_query->max_num_pages > 1): ?>
									<div class="page_navi"><div class="nav-content"><?php ew_pagination();?></div></div>
							   <?php endif; ?>
							</div>
							<?php endif; ?>
							<?php wp_reset_query(); ?>
						</div>
					<script type="text/javascript">
							function untrigger_event_hover(data){
								data.children('.thumb-image .post-item,.thumb-video .post-item').unbind('hover');
							}
							function trigger_event_hover(){
								var backgOverColor      = "#3f3f3f";
								var backgOutColor       = '#141211';
								var text1BaseColor      = '#fff';
								jQuery('.thumb-image .post-item,.thumb-video .post-item').hover(
									function(event){ 
										jQueryelement = jQuery(this);
										var icon_zoom = jQuery(".icons > .zoom-gallery", this);
										var icon_link = jQuery(".icons > .link-gallery", this);								
										var w = jQueryelement.width(), h = jQueryelement.height();
										x = ( ( w/2 ) - icon_zoom.width() - 4  );//
										y = ( ( h/2 ) - icon_zoom.width()/2 );			
											customHoverAnimation( "over", event, jQuery(this), jQuery(".thumb-image-hover", this) ); 
											var text = jQuery(".thumb-hover-start", this);
											TweenMax.to( text, 1, { css:{ color: backgOutColor },  ease:Quad.easeOut });
											TweenMax.to( jQuery(this), 1, { css:{ backgroundColor: backgOverColor },  ease:Quad.easeOut });
											TweenMax.to(icon_zoom, .5, { css:{
												boxShadow: "0px 0px 24px 6px white",
												borderRadius:"0%",
												rotation: 360,
												left:x,
												top: y
												},
												ease:Quad.easeOutBounce
											});
											TweenMax.to(icon_link, .5, { css:{
												boxShadow: "0px 0px 24px 6px white",
												borderRadius:"0%",
												rotation: 180,
												left:(x+48),
												top: y
												},
												ease:Quad.easeOutBounce
											});									
										},
										function(event){ 
											var icon_zoom = jQuery(".icons > .zoom-gallery", this);
											var icon_link = jQuery(".icons > .link-gallery", this);
											customHoverAnimation( "out", event, jQuery(this), jQuery(".thumb-image-hover", this) ); 
											var text = jQuery(".thumb-hover-start", this);
											TweenMax.to( text, 1, { css:{ color: text1BaseColor },  ease:Circ.easeOut });
											TweenMax.to( jQuery(this), 1, { css:{ backgroundColor: backgOutColor },  ease:Quad.easeOut });
											TweenMax.to(icon_zoom, 0.5, { css:{
												boxShadow: "0px 0px 24px 6px black",
												rotation: 0,
												left:"0",
												top: "0"										
												}
											});
											TweenMax.to(icon_link, 0.5, { css:{
												boxShadow: "0px 0px 24px 6px black",
												rotation: 0,
												left:"100%",
												top: "100%"										
												}
											});										
								});	
							
							}
							
							jQuery(function() {
								"use strict";
								
								jQuery("a[data-rel^='wd_pretty_photo']").prettyPhoto({
									hook : 'data-rel'
									,social_tools : false
									,theme: 'pp_woocommerce'
									,default_width: jQuery('body').innerWidth()/8*5
									,default_height: window.innerHeight - 30
								});
											
								if (jQuery.browser.msie && jQuery.browser.version == 8) {
									jQuery(".thumb-image-hover").each(function(index,value){
										jQuery(value).width( jQuery(value).parent().width() ).height( jQuery(value).parent().height() );
									});
								}
								//jQuery('.wd-portfolio-slider ul li').hide();
								if(jQuery('.wd-portfolio-slider ul').length > 0 ){
									window.setTimeout( function(){
										var li_width = jQuery('#portfolio-galleries-holder').width() / <?php echo $columns ?> - 20;
										
										jQuery('.wd-portfolio-slider').each(function(i,value){
											jQuery(value).siblings('a.image.image-holder').hide();
											jQuery(value).show();	
											//jQuery(value).find("ul li").show();											
											var control_prev =  jQuery('<div class="wd_portfolio_control_' + i +'"><a class="prev" id="wd_portfolio_prev_' + i + '" href="#">&lt;</a><a class="next" id="wd_portfolio_next_' + i + '" href="#" >&gt;</a> </div>');
											jQuery(value).append(control_prev);
											jQuery(value).children('ul.port-slides').carouFredSel({
												responsive: true
												,width	: li_width
												,scroll  : {
													items	: 1,
													auto	: true,
													pauseOnHover    : true
												}
												,swipe	: { onMouse: false, onTouch: true }
												,auto    : true
												,items   : { 
													width		: li_width
													,height		: 'auto'					
												}
												,prev    : '#wd_portfolio_prev_' + i
												,next 	 : '#wd_portfolio_next_' + i
											});
										});	
									},0);	
								}
								trigger_event_hover();
								var applications = jQuery('#portfolio-galleries-holder');
								applications.find('div.item-portfolio').each(function(i,value){
									if(i % <?php echo $columns ?> == 0 ) { jQuery(this).addClass('first') ; };
									if(i % <?php echo $columns ?> == <?php echo $columns - 1; ?> || i == <?php echo $post_count - 1; ?> ) { jQuery(this).addClass('last');} ;
								});
								<?php if( $columns > 1 ) : ?>
								var filterType = jQuery('.portfolio-filter > li');
								var data = applications.clone();
								var flag = 0;
								// attempt to call Quicksand on every form change
								
								filterType.click(function(e) {
									if(!jQuery(this).hasClass('active')){
										var list_id = [];
										jQuery('.portfolio-filter > li.active').removeClass('active');
										jQuery(this).addClass('active');
										/* Change current select */
										jQuery(this).parent('.portfolio-filter').parent('.filter_menu').siblings('.current-select').find('span').text(jQuery(this).find('a').text());
										
										if (jQuery(this).attr('id') == 'all') {
											var filteredData = data.find('div.item-portfolio');
										} else {	
											var filteredData = data.find('div.item-portfolio[data-type~=' + jQuery(this).attr('id') + ']');
										}
	
										
										for( var i = 0 ; i < filteredData.length ; i++ ){
											list_id.push(filteredData.eq(i).attr('data-id'));
											var li_width = jQuery('#portfolio-galleries-holder').width() / <?php echo $columns ?> - 20;
											if( filteredData.eq(i).find('.wd-portfolio-slider').length > 0 ){
												var new_slider = jQuery('<ul class="port-slides"></ul>');
												filteredData.eq(i).find('ul.port-slides > li').not('.clone').appendTo(new_slider)/*.filter(':not(:first)').hide()*/;
												new_slider.css('height',li_width + 'px').height(li_width).css('width',li_width + 'px').css('overflow','hidden');
												var control_prev =  jQuery('<div class="wd_portfolio_control' + i +'"><a class="prev" id="wd_portfolio_prev_" href="#">&lt;</a><a class="next" id="wd_portfolio_next_" href="#" >&gt;</a> </div>');
												
												filteredData.eq(i).find('.wd-portfolio-slider').html(new_slider);//.append(control_prev);
											}
										}

										if(flag != 0){
											//console.log('flag_0');
											endModuleGallery(false);
										}
										
										window.setTimeout( function(){
											
											applications.quicksand(filteredData, {
													duration			: 0
													,easing				: 'easeInOutQuad'
													,retainExisting		: false
												},function() {
													if(filteredData.length > 0){
														
														jQuery("a[data-rel^='wd_pretty_photo']").prettyPhoto({
															hook : 'data-rel'
															,social_tools : false
															,theme: 'pp_woocommerce'
															,default_width: jQuery('body').innerWidth()/8*5
															,default_height: window.innerHeight - 30
														});
														
														moduleGallery();
														
														jQuery('.not-found-wrapper').hide();
														if(jQuery('.wd-portfolio-slider ul').length > 0 ){
															var li_width = jQuery('#portfolio-galleries-holder').width() / <?php echo $columns ?> - 20;
															jQuery('.wd-portfolio-slider').each(function(i,value){
																jQuery(value).siblings('a.image.image-holder').hide();
																jQuery(value).show();	
																var control_prev =  jQuery('<div class="awd_portfolio_control_' + i +'"><a class="prev wd_portfolio_prev_" href="#">&lt;</a><a class="next wd_portfolio_next_" href="#" >&gt;</a> </div>');
																var check = 0;
																if(jQuery(value).children('div.awd_portfolio_control_'+i).length > 0) {
																	check = 1;
																	//console.log(jQuery(value).children('div.awd_portfolio_control_'+i).html());
																} 
																if(check == 0){
																	jQuery(value).append(control_prev);	
																}					

																jQuery(value).children('ul').carouFredSel({
																	responsive: true
																	,width	: li_width
																	,scroll  : {
																		items	: 1,
																		auto	: true,
																		pauseOnHover    : true
																	}
																	,swipe	: { onMouse: true, onTouch: true }
																	,auto :true
																	,debug	 : true
																	,items   : { 
																		width		: li_width
																		,height		: 'auto'					
																	}
																	
																	,prev    : '.awd_portfolio_control_' + i +' .wd_portfolio_prev_'
																	,next 	: '.awd_portfolio_control_' + i +' .wd_portfolio_next_'		
																});
															});
														}
														trigger_event_hover();
														jQuery('#portfolio-galleries-holder').height('auto');
													}else{
														jQuery('.not-found-wrapper').show();
													}
													
													
											});
											applications.find('div.item-portfolio').removeClass('first').removeClass('last');
											var count = 0;
											for( i = 0 ; i < list_id.length ; i++ ){
												var temp = jQuery('#portfolio-galleries-holder div.item-portfolio[data-id='+list_id[i]+']');
												if(i % <?php echo $columns ?> == 0 ) { 
													jQuery(temp).addClass('first') ; 
												}
												if(i % <?php echo $columns ?> == <?php echo $columns - 1; ?>  ) { 
													jQuery(temp).addClass('last');
												}
											}
											
											
										}, flag );
										
										flag = 1500;	
									}
								});
								<?php endif; ?>
							});
						</script>
<?php
}
?>