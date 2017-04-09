(function() {
	"use strict";
	
	var _editor = null;
    tinymce.PluginManager.add('Wd_shortcodes', function( editor, url ) {
		_editor = editor;
		var menu = new Array();
		
		var shop_shortcode = new Array();
		wd_mce_addMenu(shop_shortcode,'[WD]Custom product','[custom_product style="1" title="" id="" sku="" show_add_to_cart="1" show_sku="1" show_rating="1" show_label="1" show_categories="0"]');
		wd_mce_addMenu(shop_shortcode,'[WD]Custom products','[custom_products style="1" ids="" skus="" show_title="1" show_short_desc="1" show_price="1" show_add_to_cart="1" show_sku="0" show_rating="1" show_label="1" show_label_title="0" show_categories="0"]');
		wd_mce_addMenu(shop_shortcode,'[WD]Custom products category','[custom_products_category per_page="10" title="" product_cats="" show_thumbnail="1" show_title="1" show_short_desc="1" show_sku="0" show_price="1" show_label="1" show_label_title="0" show_rating="1" show_categories="0" show_add_to_cart="1"]' );
		wd_mce_addMenu(shop_shortcode,'[WD]Feature product','[featured_product columns="4" style="1" per_page="8" product_cats="" title="your title" desc="" show_image="1" show_title="1" show_short_desc="1" show_sku="0" show_price="1" show_label="1" show_label_title="0" show_rating="1" show_categories="0" show_add_to_cart="1" show_load_more="0"]' );
		wd_mce_addMenu(shop_shortcode,'[WD]Feature product slider','[featured_product_slider columns="4" style="1" per_page="8" product_cats="" title="your title" desc="" show_nav="1" show_image="1" show_title="1" show_short_desc="1" show_sku="0" show_price="1" show_label="1" show_label_title="0" show_rating="1" show_categories="0" show_add_to_cart="1"]' );
		wd_mce_addMenu(shop_shortcode,'[WD]Sale product','[sale_product columns="4" style="1" per_page="8" product_cats="" title="your title" desc="" show_image="1" show_title="1" show_short_desc="1" show_sku="0" show_price="1" show_label="1" show_label_title="0" show_rating="1" show_categories="0" show_add_to_cart="1" show_load_more="0"]' );
		wd_mce_addMenu(shop_shortcode,'[WD]Sale product slider','[sale_product_slider columns="4" style="1" per_page="8" product_cats="" title="your title" desc="" show_nav="1" show_image="1" show_title="1" show_short_desc="1" show_sku="0" show_price="1" show_label="1" show_label_title="0" show_rating="1" show_categories="0" show_add_to_cart="1"]' );
		wd_mce_addMenu(shop_shortcode,'[WD]Popular product','[popular_product columns="4" style="1" per_page="8" product_cats="" title="your title" desc="" product_tag="" show_image="1" show_title="1" show_short_desc="1" show_sku="0" show_price="1" show_label="1" show_label_title="0" show_rating="1" show_categories="0" show_add_to_cart="1" show_load_more="0"]' );
		wd_mce_addMenu(shop_shortcode,'[WD]Popular product slider','[popular_product_slider columns="4" style="1" per_page="8" product_cats="" title="your title" desc="" show_nav="1" show_image="1" show_title="1" show_short_desc="1" show_sku="0" show_price="1" show_label="1" show_label_title="0" show_rating="1" show_categories="0" show_add_to_cart="1"]' );
		wd_mce_addMenu(shop_shortcode,'[WD]Recent product','[recent_product columns="4" style="1" per_page="8" title="your title" desc="" product_cats="" product_tag="" show_image="1" show_title="1" show_short_desc="1" show_sku="0" show_price="1" show_label="1" show_label_title="0" show_rating="1" show_categories="0" show_add_to_cart="1" show_load_more="0"]' );
		wd_mce_addMenu(shop_shortcode,'[WD]Recent product slider','[recent_product_slider columns="4" style="1" per_page="8" title="your title" desc="" product_cats="" product_tag="" show_nav="1" show_image="1" show_title="1" show_short_desc="1" show_sku="0" show_price="1" show_label="1" show_label_title="0" show_rating="1" show_categories="0" show_add_to_cart="1"]' );
		wd_mce_addMenu(shop_shortcode,'[WD]Best selling product','[best_selling_product columns="4" style="1" per_page="8" product_cats="" title="your title" desc="" show_image="1" show_title="1" show_short_desc="1" show_sku="0" show_price="1" show_label="1" show_label_title="0" show_rating="1" show_categories="0" show_add_to_cart="1" show_load_more="0"]' );
		wd_mce_addMenu(shop_shortcode,'[WD]Best selling product slider','[best_selling_product_slider columns="4" style="1" per_page="8" product_cats="" title="your title" desc="" show_nav="1" show_image="1" show_title="1" show_short_desc="1" show_sku="0" show_price="1" show_label="1" show_label_title="0" show_rating="1" show_categories="0" show_add_to_cart="1"]' );
		wd_mce_addMenu(shop_shortcode,'[WD]Product Categories Slider','[product_categories_slider number="" parent="" ids="" hide_empty="0" columns="4" show_nav="1" show_item_title="1" title="your title" desc=""]' );
		wd_mce_addMenu(shop_shortcode,'[WD]Product Filter By Sub Category','[product_filter_by_sub_category columns="4" style="1" per_page="8" title="your title" desc="" product_cats="" show_nav="1" show_image="1" show_title="1" show_short_desc="1" show_sku="0" show_price="1" show_rating="1" show_label="1" show_label_title="0" show_categories="0" show_add_to_cart="1"]' );
		wd_mce_addSubMenu(menu,'Shop Shortcode',shop_shortcode);
		
		var column = new Array();
		wd_mce_addMenu(column, '1/2',"[one_half]your_content[/one_half]" );
		wd_mce_addMenu(column, '1/3',"[one_third]your_content[/one_third]" );
		wd_mce_addMenu(column, '1/4',"[one_fourth]your_content[/one_fourth]" );
		wd_mce_addMenu(column, '1/5',"[one_fifth]your_content[/one_fifth]" );
		wd_mce_addMenu(column, '1/6',"[one_sixth]your_content[/one_sixth]" );
		wd_mce_addMenu(column, '2/3',"[two_third]your_content[/two_third]" );
		wd_mce_addMenu(column, '3/4',"[three_fourth]your_content[/three_fourth]" );
		wd_mce_addMenu(column, '2/5',"[two_fifth]your_content[/two_fifth]" );
		wd_mce_addMenu(column, '3/5',"[three_fifth]your_content[/three_fifth]" );
		wd_mce_addMenu(column, '4/5',"[four_fifth]your_content[/four_fifth]" );
		wd_mce_addMenu(column, '5/6',"[five_sixth]your_content[/five_sixth]" );
		wd_mce_addMenu(column, '1/2 last',"[one_half_last]your_content[/one_half_last]" );
		wd_mce_addMenu(column, '1/3 last',"[one_third_last]your_content[/one_third_last]" );
		wd_mce_addMenu(column, '1/4 last',"[one_fourth_last]your_content[/one_fourth_last]" );
		wd_mce_addMenu(column, '1/5 last',"[one_fifth_last]your_content[/one_fifth_last]" );
		wd_mce_addMenu(column, '1/6 last',"[one_sixth_last]your_content[/one_sixth_last]" );
		wd_mce_addMenu(column, '2/3 last',"[two_third_last]your_content[/two_third_last]" );
		wd_mce_addMenu(column, '3/4 last',"[three_fourth_last]your_content[/three_fourth_last]" );
		wd_mce_addMenu(column, '2/5 last',"[two_fifth_last]your_content[/two_fifth_last]" );
		wd_mce_addMenu(column, '3/5 last',"[three_fifth_last]your_content[/three_fifth_last]" );
		wd_mce_addMenu(column, '4/5 last',"[four_fifth_last]your_content[/four_fifth_last]" );
		wd_mce_addMenu(column, '5/6 last',"[five_sixth_last]your_content[/five_sixth_last]" );
		wd_mce_addSubMenu(menu,'Column',column);
		
		
		//heading
		wd_mce_addMenu(menu, 'heading','[heading size=""]your_content[/heading]');
		//feature
		wd_mce_addMenu(menu, 'feature','[feature slug="" id="" style="style-1" class_icon_font="fa-thumbs-up" show_icon_font="yes" title="yes" excerpt="yes" content="no" ]');
		//child categories
		wd_mce_addMenu(menu, 'child categories','[child_categories category="" taxonomy="product_cat" desc="" bg_color="" bg_image="" text_color="#fff" limit="5" button_text="View All Categories" ]'); 
		//icon
		wd_mce_addMenu(menu, 'icon','[icon icon="" color=""]');
		//listing
		wd_mce_addMenu(menu, 'listing','[ew_listing custom_class="" style_class=""]your_content[/ew_listing]');
		//add_line
		wd_mce_addMenu(menu, 'add line','[add_line height_line="" color="" class=""]');
		//banner
		wd_mce_addMenu(menu, 'banner','[banner link_url="#" title="your title" bg_image="url" bg_color="#ffffff" bg_hover="#000000" bg_text="url" position_text_bottom="30px" show_label="no" label_text="save off" label_style="big onsale two_word" responsive="normal"]' );
		//banner description
		wd_mce_addMenu(menu, 'banner_description','[banner_description link_url="#" title="your title" description="you description" image="url"]' );
		//recent post
		wd_mce_addMenu(menu, 'recent post','[recent_blogs layout="vertical" category="" columns="4" number_posts="4" title="yes" thumbnail="yes" meta="yes" tag="yes" sharing="yes" excerpt="yes" excerpt_words="30"]');
		//recent post sticky
		wd_mce_addMenu(menu, 'recent post sticky','[recent_blogs_sticky layout_sticky="vertical" category="" columns_child="1" number_posts="5" title_sticky="1" thumbnail_sticky="1" meta_sticky="1" tag_sticky="0" sharing_sticky="0" excerpt_sticky="1" excerpt_words_sticky="40" title="1" thumbnail="1" meta="1" tag="0" sharing="0" excerpt="0" excerpt_words="10"]');
		//recent post video
		wd_mce_addMenu(menu, 'recent post video','[recent_blogs_video right_columns="2" number_posts="5"]');
		// Portfolio slider */
		wd_mce_addMenu(menu,'portfolio slider','[wd_portfolio_slider title="Our Recent Work" desc="" portfolio_cats="" per_page="5"]');
		//testimonial
		wd_mce_addMenu(menu, 'testimonial','[testimonial slug="" id="" slider="0" limit="4"]'); 
		//accordion khong bo dc lien quan tab widget tren phone
		wd_mce_addMenu(menu, 'accordion','[accordions][accordion_item title="title"]your_content[/accordion_item][/accordions]' );
		//table price */
		wd_mce_addMenu(menu,'pricing table','[wd_ptable title="Standard" active="no" price="19" currency="$" price_period="/mo" link="#" button_text="Sign up now!"][/wd_ptable]')
		//badges
		wd_mce_addMenu(menu, 'badges','[badge type="" ]your_content[/badge] ' );	
		//button
		wd_mce_addMenu(menu, 'button','[button size="default" link="#" background="#f34948" color="#ffffff" border_color="#f34948" background_hover="#ffffff" color_hover="#f34948" border_color_hover="#f34948"]BUTTON TEXT[/button]' );
		//checklist
		wd_mce_addMenu(menu, 'checklist','[checklist icon=""]your_content[/checklist]');
		//code
		wd_mce_addMenu(menu, 'code','[code]your_content[/code]');
		//dropcap
		wd_mce_addMenu(menu, 'dropcap','[dropcap color=""]your_text[/dropcap]');
		//label
		wd_mce_addMenu(menu, 'label','[label type=" "]your_text[/label]');
		//tabs khong bo duoc
		wd_mce_addMenu(menu, 'tabs','[tabs][tab_item title=""]your_content[/tab_item][tab_item title=""]your_content[/tab_item][/tabs]');
		//faq
		wd_mce_addMenu(menu, 'faq','[faq title=""]your_content[/faq]'); 			
		//google map
		wd_mce_addMenu(menu, 'google_map','[google_map address="" title="" height="360" zoom="16" map_type="TERRAIN" map_color="" water_color="" road_color=""]your_content[/google_map]');	
		//hidden phone
		wd_mce_addMenu(menu, 'hidden phone','[hidden_phone]your_content[/hidden_phone]');
		//quote
		wd_mce_addMenu(menu, 'quote','[quote class=""]your_content[/quote]');
		//tooltip
		wd_mce_addMenu(menu, 'tooltip','[tooltip style="" tooltip_content=""]your_content[/tooltip]'); 
		
		
        editor.addButton( 'wd_shortcodes_button', {
            title: 'WD Shortcode'
            ,text: ''
            ,type: 'menubutton'
            ,icon: false
			,classes:'wd_shortcode_button'
            ,menu: menu
        });
		
    });
	function wd_mce_addMenu(d,e,a){d.push({text:e,value:a,onclick:function(event){event.stopPropagation();_editor.insertContent(a)}})}
	function wd_mce_addSubMenu(d,t,m){d.push({text:t,menu:m});}
})();