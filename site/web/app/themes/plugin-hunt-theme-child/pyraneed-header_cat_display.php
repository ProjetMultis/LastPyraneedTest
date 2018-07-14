<?php 
$queried_object = get_queried_object();
$page_cat_id = $queried_object->term_id;
$queried_term = get_term( $page_cat_id, 'category' );

//j'aurais pu le faire avec $get_ancestors, à voir si utile par la suite
$ancestors = get_ancestors($queried_term->term_id, 'category');

if ($ancestors) {
	$first_ancestor_term = get_term($ancestors[0]);
}


$level = count($ancestors);

if ($level === 1) {
	$category_title = $queried_term->name;
	echo '<h1 class="categoryTitle">' . $category_title . '</h1><br>';
	$sub_titles = get_terms('category', array('parent' => $queried_term->term_id));
	foreach ($sub_titles as $sub_title) {
		echo '<a class="category-title" href="'. get_term_link($sub_title->term_id) .'">' . $sub_title->name . '</a>';
		# code...
	}
}

if ($level === 2) {
	$category_title = $first_ancestor_term->name;
	echo '<h1>' . $category_title . '</h1>';
	$sub_titles = get_terms('category', array('parent' => $first_ancestor_term->term_id));
	foreach ($sub_titles as $sub_title) {
		?>
		<a class="category-title" style="<?php echo $sub_title->term_id === $queried_term->term_id ? 'background: #061e3f; color: white !important;' : ''; ?>" href="<?php echo get_term_link($sub_title->term_id); ?>"><?php echo $sub_title->name;?></a>
	<?php }
}

 ?>