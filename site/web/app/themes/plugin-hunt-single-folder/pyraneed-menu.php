<?php 
/**
 * This template is used for pyraneeds menu
 *
 */
?>
		<div id="MainMenuWrapper" class="menu-hide">
		<div>
			<h2 class="website-name">Besoins</h2>
		</div>
	      <div id="MainMenu">
	        <div class="list-group panel">

	        <?php

	        $page_cat_array = get_the_category();

	        $currently_disp_cat = get_queried_object_id();

	        if (!is_front_page()) {

	        	$currently_disp_cat_term = get_term($currently_disp_cat);
	        	$currently_disp_cat_term = $currently_disp_cat_term->parent;
	        } else {
	        $currently_disp_cat_term = "";        	
	        }
	        $page_cat_id = $page_cat_array[0]->category_parent;

	        if (get_ancestors($page_cat_id, 'category')) {
	        	$page_cat_id_array = get_ancestors($page_cat_id, 'category');
	        	$page_cat_id = $page_cat_id_array[0];

	        }


	        	$top_categories = get_terms('category', array('parent' => 0));
	        	// Set an empty array
	        	$top_categories_ordered = array();
	        	// Loop through terms and build our array
	        	foreach ( $top_categories as $term ) {
	        	 // Advanced Custom Fields Date field
	        	$top_categories_custom_order = "999";
	        	if (get_field( 'custom_display_order', $term )) {
	        		$top_categories_custom_order = get_field( 'custom_display_order', $term );
	        		# code...
	        	}
	        	 $top_categories_ordered[$top_categories_custom_order] = $term;
	        	}
	        	// Sort the terms by date
	        	krsort( $top_categories_ordered, SORT_NUMERIC );

	        	$top_categories_ordered = array_reverse($top_categories_ordered);
	        	// This will build the terms as cards 
	        	// I also has a custom field being used for category image


	        	//////*********TOP categories loop**********//////
	        	$top_category_counter = 1;
	        	foreach ($top_categories_ordered as $top_category) {
	        		$top_category_id = $top_category->term_id;
	        		$top_category_name = $top_category->name;
	        		$top_category_link = get_term_link($top_category_id);
	        		$top_category_count = "top-item-".$top_category_counter;
	        		$top_color = insert_post_color($top_category_id);
	        		
	        		?>

	        		<!-- top level item -->
	          		<a href="#<?php echo $top_category_count; ?>" class="list-group-item list-group-item" data-toggle="collapse" data-parent="#MainMenu" style="border-left: 7px solid <?php echo $top_color ? $top_color : 'black';?>;"><?php echo $top_category_name;?></a>

	          		<?php $first_sub_categories = get_terms('category', array('parent' => $top_category_id));
					if (!empty($first_sub_categories)) { 

					?>

					<div class="collapse <?php echo $top_category_id === $page_cat_id && !is_front_page() ? 'in': '';?>" id="<?php echo $top_category_count; ?>">

						<?php $first_sub_category_counter = 1; ?>
						<?php foreach ($first_sub_categories as $first_sub_category) {
								$first_sub_category_id = $first_sub_category->term_id;
								$first_sub_category_name = $first_sub_category->name;
								$first_sub_category_link = get_term_link($first_sub_category_id);
								$first_sub_category_count = "first-sub-item-".$first_sub_category_counter;
								$is_good_cat = false;

							if ($first_sub_category_id == $currently_disp_cat || $first_sub_category_id == $currently_disp_cat_term){
								$is_good_cat = true;
							}?>

							<div class="list-group-item"  style="border-left: 5px solid <?php echo $top_color;?>; <?php echo $is_good_cat == true ? 'background: #EEE !important;' : '';?>;">


								<a href="<?php echo $first_sub_category_link; ?>" class="list-group-item-link"><?php echo $first_sub_category_name; ?></a>
								
								
								
							</div>
          				
          				<?php }?>
          			</div><!-- end collapse top level link - so sub level category-->
          		<?php }

       			$top_category_counter++;
          		
          	}

          ?>

	        </div><!-- end list group panel -->
	      </div>
	      </div>

	      <style type="text/css">
	      	#MainMenuWrapper.stick {
	      	    margin-top: 110px !important;
	      	    position: fixed;
	      	    top: 0;
	      	    z-index: 10000;
	      	    width: 215px;
	      	}
	      </style>
	     <!--  <script type="text/javascript">
	      	jQuery( document ).ready(function($) {
	      		function sticky_relocate() {
	      		    var window_top = $(window).scrollTop();
	      		    var div_top = $('.post-wrapper').offset().top;
	      		    if (window_top > div_top) {
	      		        $('#MainMenuWrapper').addClass('stick');
	      		       
	      		    } else {
	      		        $('#MainMenuWrapper').removeClass('stick');
	      		        $('.post-wrapper').height(0);
	      		    }
	      		}
	      		$(function() {
	      		    $(window).scroll(sticky_relocate);
	      		    sticky_relocate();
	      		});
	      	 
	      	});
	      </script> -->