<?php
/**
 * Template Name: Sitemap
 *
 */
get_header(); 
?>

<div class="container">
	<h1 class="page-title">HTML Sitemap</h1>
	<h2 id="pages">Pages</h2>
	<ul>
	<?php
	// Add pages you'd like to exclude in the exclude here
	wp_list_pages(
	  array(
	    'exclude' => '',
	    'title_li' => '',
	  )
	);
	?>
	</ul>

	<h2 id="posts">Posts</h2>
	<ul>
	<?php
	// Add categories you'd like to exclude in the exclude here
	$cats = get_categories('exclude=');
	foreach ($cats as $cat) {
	  echo "<li><h3>".$cat->cat_name."</h3>";
	  echo "<ul>";
	  $args = array(
	  	"cat" => $cat->cat_ID,
	  	'post_status' => 'publish',   
	  );
	  $sitemap_query = new WP_Query($args);

	  while($sitemap_query->have_posts()) {
	    $sitemap_query->the_post();
	    $category = get_the_category();
	    // Only display a post link once, even if it's in multiple categories
	    if ($category[0]->cat_ID == $cat->cat_ID) {
	      echo '<li><a href="'.get_permalink().'">'.get_the_title().'</a></li>';
	    }
	  }
	  echo "</ul>";
	  echo "</li>";
	}
	?>
	</ul>


</div>

<?php get_footer(); ?>