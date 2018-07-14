<?php

function example_enqueue_styles() {
	
	// enqueue parent styles
	// https://codex.wordpress.org/Function_Reference/wp_enqueue_style
	// wp_enqueue_style( $handle, $src, $deps, $ver, $media )
	wp_enqueue_style('plugin-hunt-theme', get_template_directory_uri() .'/style.css');
	
	// enqueue child styles
	wp_enqueue_style('plugin-hunt-theme-child', get_stylesheet_directory_uri() .'/style.css', array('plugin-hunt-theme'));
	
}
add_action('wp_enqueue_scripts', 'example_enqueue_styles');


function insert_post_color($cat_id = "0") {
    global $post;
    if ( ! empty( $cat_id ) ) {
        $post_color = get_term_meta($cat_id, 'cc_color', true);
    } else {
        $post_color = '#00659F'; //Return a default or fallback color if no colors exist
    }
    return $post_color;
}

function isSiteAdmin(){
    return in_array('administrator',  wp_get_current_user()->roles);
}

//NEW LENGTH BECAUSE OF CITATIONS
function custom_ph_product_excerpt(){
  ob_start();
  ?>

        <div class="post-submission--form-row post-submission--form-row-tagline post-category">
          <label class="form--label" for="tagline">
          <span class="form--label-icon">
            <svg width="16" height="6" viewBox="0 0 16 6" xmlns="http://www.w3.org/2000/svg"><title>Slice 1</title><path d="M0 0h11v2H0V0zm0 4h16v2H0V4z" fill="#BBB" fill-rule="evenodd"></path>
            </svg>
          </span>
          <span><?php _e('Tagline','pluginhunt');?></span></label><div class="form--field">
          <input class="form--input" maxlength="300" name="tagline" placeholder="<?php _e("Describe the product briefly","pluginhunt");?>" type="text" id="tagline" value="">
        </div>
      </div>

  <?php
    $content = ob_get_contents();
    ob_end_clean();
    echo $content;
}
//custom taxonomy fields
if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array (
  'key' => 'group_5a8823c6985ca',
  'title' => 'Taxonomies',
  'fields' => array (
    array (
      'default_value' => '',
      'min' => '',
      'max' => '',
      'step' => '',
      'placeholder' => '',
      'prepend' => '',
      'append' => '',
      'key' => 'field_5a8823d978189',
      'label' => 'Ordre d\'apparition',
      'name' => 'custom_display_order',
      'type' => 'number',
      'instructions' => '',
      'required' => 0,
      'conditional_logic' => 0,
      'wrapper' => array (
        'width' => '',
        'class' => '',
        'id' => '',
      ),
    ),
  ),
  'location' => array (
    array (
      array (
        'param' => 'taxonomy',
        'operator' => '==',
        'value' => 'category',
      ),
    ),
  ),
  'menu_order' => 0,
  'position' => 'normal',
  'style' => 'default',
  'label_placement' => 'top',
  'instruction_placement' => 'label',
  'hide_on_screen' => '',
  'active' => 1,
  'description' => '',
));

endif;

//redefine update function

function ph_newmedia(){
    $pid      = (int)$_POST['pid'];
    $vid      = sanitize_text_field($_POST['vid'] );
    $src      = sanitize_text_field( $_POST['src'] );
    $img      = esc_url($_POST['img']);

    $media = get_post_meta($pid,'phmedia',true);
    $media_array = json_decode($media); 
    if($media_array == ''){
      $media_array = array();
    }
    $item['url']    = esc_url($img);
    $item['source'] = $src;
    $item['id']     = $vid;
    $media_array[] = $item;
    $media = json_encode($media_array);
    $uid      = get_current_user_id();
    $post_tmp = get_post($pid);
    $author_id = $post_tmp->post_author;

//modifier cette fonction pour que le post puisse être modifié par un mec qui n'est pas l'admin

    if($uid == $author_id || isSiteAdmin()){
      update_post_meta($pid,'phmedia',$media);
      $response['msg']     = 'all OK for the title';
      $response['item'] = $item;
      $response['newmedia'] = $media_array;
      $response['src'] = $src;
    }else{
      $response['msg'] = 'nope';
      wp_mail('mike@epicplugins.com','someone tried to add media to the wrong post');
    }

    echo json_encode($response);
    die();
}

// new php comment walker custom

class ph_comment_walker_custom extends Walker_Comment {
    var $tree_type = 'comment';
    var $db_fields = array( 'parent' => 'comment_parent', 'id' => 'comment_ID' );
 
    // constructor – wrapper for the comments list
    function __construct() { ?>

      <section class="comments-list">

    <?php }

    // start_lvl – wrapper for child comments list
    function start_lvl( &$output, $depth = 0, $args = array() ) {
      $GLOBALS['comment_depth'] = $depth + 2; ?>
      
      <section class="child-comments comments-list">

    <?php }
  
    // end_lvl – closing wrapper for child comments list
    function end_lvl( &$output, $depth = 0, $args = array() ) {
      $GLOBALS['comment_depth'] = $depth + 2; ?>

      </section>

    <?php }

    // start_el – HTML for comment template
    function start_el( &$output, $comment, $depth = 0, $args = array(), $id = 0 ) {
      $depth++;
      $GLOBALS['comment_depth'] = $depth;
      $GLOBALS['comment'] = $comment;
      $parent_class = ( empty( $args['has_children'] ) ? '' : 'parent' ); 
  
      if ( 'article' == $args['style'] ) {
        $tag = 'article';
        $add_below = 'comment';
      } else {
        $tag = 'article';
        $add_below = 'comment';
      } ?>

      <article <?php comment_class(empty( $args['has_children'] ) ? '' :'parent') ?> id="comment-<?php comment_ID() ?>" itemprop="comment" itemscope itemtype="http://schema.org/Comment">
        <div class='ph-comment-row'>
        <div class="content-post-meta">
            <figure class="gravatar"><?php echo get_avatar( $comment, 30 ); ?></figure>

            <div class="content-name-day">
                <div class="comment-meta post-meta" role="complementary">
                  <?php
                  $em =  get_comment_author_email();
                  $the_user = get_user_by('email', $em);
                  $the_user->ID;
                  $username = $the_user->user_nicename;
                  $text = wp_trim_words($comment->comment_content,'5');
                  $replyuri = home_url('/author/');
                  $content = preg_replace('/\B\@([a-zA-Z0-9_]{1,20})/', '<a class="at" href="'.$replyuri.'$1">$0</a>', $comment->comment_content);
                  $title = get_the_title($comment->comment_post_ID);
                  $perma = get_comment_link($comment);
              //    $perma = get_permalink($comment->comment_post_ID);
                  $share = urlencode($username . "'s thoughts on ") . $title . " " . $text;


                  ?>
                  <span class="comment-author"><?php comment_author(); ?></span><span class='ph-comment-info'> - <?php echo get_the_author_meta('description',$the_user->ID); ?></span>

                  <?php // edit_comment_link('<p class="comment-meta-item">Edit this comment</p>','',''); ?>
                  <?php if ($comment->comment_approved == '0') : ?>
                  <p class="comment-meta-item"><?php _e('Your comment is awaiting moderation.','pluginhunt'); ?></p>
                  <?php endif; ?>
                </div>

                <div class='pull-right'>
                    <time class="ph-m comment-meta-item" datetime="<?php comment_date('Y-m-d') ?>T<?php comment_time('H:iP') ?>" itemprop="datePublished"><?php printf( _x( '%s ago', '%s = human-readable time difference', 'pluginhunt' ), human_time_diff( get_comment_time( 'U' ), current_time( 'timestamp' ) ) ); ?></time>
                </div>
            </div>
            <div class='pull-left post-sociaux'>
                <!--
              <span class='ph-m upvote-comment' data-cid='<?php comment_ID();?>'><span class='ph-up-adj'><i class='fa fa-sort-up'></i></span> upvote </span>
-->              <span class='ph-m reply-comment' data-cid='<?php comment_ID(); ?>' data-un='<?php echo $username ; ?>'><i class='fa fa-mail-reply' data-cid='<?php comment_ID(); ?>'></i><?php _e(' Reply','pluginhunt'); ?></span>
                <span class='ph-m share-comment'><i class='fa  fa-twitter'></i><a class='comment-tweet-link' href='https://twitter.com/intent/tweet?text=<?php echo $share; ?>&amp;url=<?php echo urlencode( $perma ); ?>' title=share><?php _e('tweet','pluginhunt');?></a></span>
            </div>
        </div>
            <div class="comment-content" itemprop="text">
          <?php echo $content ?>
          <div class='ph-comment-meta'>

            <div style='clear:both'></div>
          </div>
        </div>

      </div>

    <?php }

    // end_el – closing HTML for comment template
    function end_el(&$output, $comment, $depth = 0, $args = array() ) { ?>

      </article>

    <?php }

    // destructor – closing wrapper for the comments list
    function __destruct() { ?>

      </section>
    
    <?php }
}


/**
 * Get an attachment ID given a URL.
 * 
 * @param string $url
 *
 * @return int Attachment ID on success, 0 on failure
 */
function get_attachment_id( $url ) {

  $attachment_id = 0;

  $dir = wp_upload_dir();

  if ( false !== strpos( $url, $dir['baseurl'] . '/' ) ) { // Is URL in uploads directory?

    $file = basename( $url );

    $query_args = array(
      'post_type'   => 'attachment',
      'post_status' => 'inherit',
      'fields'      => 'ids',
      'meta_query'  => array(
        array(
          'value'   => $file,
          'compare' => 'LIKE',
          'key'     => '_wp_attachment_metadata',
        ),
      )
    );

    $query = new WP_Query( $query_args );

    if ( $query->have_posts() ) {

      foreach ( $query->posts as $post_id ) {

        $meta = wp_get_attachment_metadata( $post_id );

        $original_file       = basename( $meta['file'] );
        $cropped_image_files = wp_list_pluck( $meta['sizes'], 'file' );

        if ( $original_file === $file || in_array( $file, $cropped_image_files ) ) {
          $attachment_id = $post_id;
          break;
        }

      }

    }

  }

  return $attachment_id;
}

// replacement function for newpost because of attachements

function ph_newpost(){

  //die if current user cannot edit posts
  if(!current_user_can( 'edit_posts' )){
      die();
  }

  //sanitise stuff
  $title          = $_POST['name'];
  $title          = sanitize_post_field( 'post_title', $title,'','db' );

  $slug           = sanitize_title($_POST['name']);
  $url            = esc_url($_POST['url']);

  $desc           = $_POST['tag'];


  $media          = json_encode($_POST['media']);
  $cat            = (int)$_POST['cat'];
  $type           = (int)$_POST['type'];
  $avail          = (int)$_POST['avail'];
  $prod           = $_POST['prod'];
  $discat         = (int)$_POST['discat'];

  $slug     = wp_unique_post_slug( $slug, '','publish','post','');

  $image = $_POST['media'][0]['url'];     //image has uploaded to this point...
  if($_POST['media'][0]['source'] == 'yt'){
    $image = $_POST['media'][0]['image'];
    $islug = $_POST['media'][0]['id'];
  }else{
    $islug = '';
  }

  $current_user   = wp_get_current_user();
  $uid            = $current_user->ID;

  if(current_user_can( 'publish_posts' )){
    $status = 'publish';
  }else{
    $status = 'pending';
  }

  if($prod == 'post'){
    $ptype = 'post';
    $post = array(
    'post_content'   => $desc, 
    'post_title'     => $title, 
    'post_status'    => $status,
    'post_type'      => $ptype,
    'post_author'    => $uid,
    'post_name'      => $slug,
    'post_category'  => array($cat)
     ); 
     $wid = wp_insert_post( $post, $wp_error );
     wp_set_object_terms( $wid, $type, 'type', true );
     wp_set_object_terms( $wid, $avail, 'post_availibility', true );
     update_post_meta($wid, 'outbound', $url);
  }

  if($prod == 'woo'){
    //product stuff
    $price   = $_POST['price'];
    $reserve = $_POST['resprice'];
    $condition = (int)$_POST['condition'];
    if($condition == '1'){
      $condition = 'new';
    }else{
      $condition == 'used';
    }

    $start   = date('Y-m-d 00:00');   //start time today @ midnight
    $end     = date('Y-m-d 00:00', strtotime('+1 Week')); //end time in a week..  (can flex if you want)

    $title          = $_POST['name'];
    $title          = sanitize_post_field( 'post_title', $title,'','db' );

    $slug     = wp_unique_post_slug( $slug, '','publish','product','');
    $ptype = 'product';
    $post = array(
    'post_content'   => $desc, 
    'post_title'     => $title, 
    'post_status'    => $status,
    'post_type'      => $ptype,
    'post_author'    => $uid,
    'post_name'      => $slug,
    'post_category'  => array($cat)
     ); 
     $wid = wp_insert_post( $post, $wp_error );



     
     if($type == '1'){
       //buy now
      update_post_meta($wid,'_regular_price', number_format($price));
      wp_set_object_terms( $wid, 'simple_product', 'product_type' );
      update_post_meta($wid, '_price', number_format($price));   // price
      update_post_meta($wid,'_stock_status', 'instock');
      update_post_meta($wid,'_stock', "1"); 
      update_post_meta($wid,'_visibility', 'visible');
     }else{
       //auction
      update_post_meta($wid,'_regular_price', number_format($price));   // price
      update_post_meta($wid, '_price', number_format($price));   // price
      update_post_meta($wid, '_auction_reserved_price', number_format($reserve));  // reserve price

      update_post_meta($wid,'_auction_item_condition', $condition);          // condition
      update_post_meta($wid,'_auction_type','normal');  //auction type
      update_post_meta($wid,'_auction_dates_from',$start);  //start date
      update_post_meta($wid,'_auction_dates_to',$end);  //end date
      update_post_meta($wid,'_stock', "1"); 
      update_post_meta($wid,'_manage_stock','yes');
      update_post_meta($wid,'_sold_individually','yes');

      update_post_meta($wid,'_stock_status', 'instock');
      update_post_meta($wid,'_visibility', 'visible');



      wp_set_object_terms( $wid, 'auction', 'product_type' );
     }



  }

  if($prod == 'discussion'){
    $ptype = 'discussions';
    $slug     = wp_unique_post_slug( $slug, '','discussion','post','');
     $post = array(
    'post_content'   => $desc, 
    'post_title'     => $title, 
    'post_status'    => $status,
    'post_type'      => $ptype,
    'post_author'    => $uid,
    'post_name'      => $slug,
     ); 
     $wid = wp_insert_post( $post, $wp_error );
     wp_set_object_terms( $wid, $discat, 'discussion_category', true );

  }


    
    update_post_meta($wid,'phmedia',$media);
    update_post_meta($wid, 'epicredvote', 0);
    update_post_meta($wid, 'epicredrank',0);

    //set featured image to be from the $featured variable if the image is already uploaded to the media library skip the upload part
    
      if($image){      
            

            //extra code to upload the image and set it as the featured image

            #} if image is an external image then upload and set..  
            if($_POST['media'][0]['source'] == 'ei'){
              $upload_dir = wp_upload_dir();
              $image_data = file_get_contents($image);
              $filename = basename($image);
              if($islug!=''){
                $ext = explode(".",$filename);  
                $filename = $slug . "." . $ext[1]; 
              }
              if(wp_mkdir_p($upload_dir['path']))
                  $file = $upload_dir['path'] . '/' . $filename;
              else
                $file = $upload_dir['basedir'] . '/' . $filename;
                if($file != $image){
                  file_put_contents($file, $image_data);
                }else{
                  $file == $image;
                }     
                $wp_filetype = wp_check_filetype($filename, null );
                $attachment = array(
                    'post_mime_type' => $wp_filetype['type'],
                    'post_title' => sanitize_file_name($filename),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );
                $attach_id = wp_insert_attachment( $attachment, $file, $wid);
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
                wp_update_attachment_metadata( $attach_id, $attach_data );           
                set_post_thumbnail($wid, $attach_id ); 
                update_post_meta($wid, 'epic_externalURL', $image );
              }elseif($_POST['media'][0]['source'] == 'med'){
                #} image is already in the media library
                // find image from post thumbnail url
                $file = $_POST['media'][0]['url'];

                $filename = basename($file);

                $attach_id_flo = get_attachment_id($file);

                // $wp_filetype = wp_check_filetype($file, null );
                // $attachment = array(
                //     'post_mime_type' => $wp_filetype['type'],
                //     'post_title' =>     sanitize_file_name($filename),
                //     'post_content' => '',
                //     'post_status' => 'inherit'
                // );
                // $attach_id = wp_insert_attachment( $attachment, $file, $wid);
                set_post_thumbnail($wid, $attach_id_flo);
              }
              
          } 
    
  update_post_meta($wid,'_thumbnail_id', $attach_id_flo);
  //store in post meta the $image
  update_post_meta($wid, 'phfeaturedimage',$image);

  //fire the notifications action
  $cid = get_current_user_id();
  do_action('notifyme_new_post', $wid, $cid);


  $response['success'] = 'post added';
  $response['slug'] = $slug;
  $response['perma'] = get_post_permalink($wid);
  $response['prod'] = $prod;
  // echo json_encode($response); 
  die();
}

function pluginhunt_QueryPosts_pyra($paged, $ph_grouping){
  global $wpdb, $pageposts,$d,$m,$y,$ph_grouping, $key, $join;
  if($ph_grouping == 'ph-group-day'){
    if($paged == 0){
      $query = "SELECT post_date, YEAR(post_date) AS 'year', MONTH(post_date) AS 'month',
            DAYOFMONTH(post_date) AS 'dayofmonth' FROM $wpdb->posts WHERE post_status='publish' AND post_type = 'post' ORDER BY post_date DESC LIMIT 10";
      $first = $wpdb->get_results($query);
      $c =  count($first) - 1;
      $f =  $first[$c]->post_date;
      $tf = substr($f,0,10);
      $date = date('U');
      $d =  $first[$c]->dayofmonth;
      $m =  $first[$c]->month;
      $y =  $first[$c]->year;
      $key = '';
      #} extra check - does the day, month and year match for the first and last post from the 10. If so, then get all posts from that day, month and year
      
      if($d == $first[0]->dayofmonth && $m == $first[0]->month && $y == $first[0]->year){
      $querystr = "
          SELECT $wpdb->posts.*, YEAR(post_date) AS 'year',
            MONTH(post_date) AS 'month',
            DAYOFMONTH(post_date) AS 'dayofmonth'
          FROM $wpdb->posts, $wpdb->postmeta
          WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id 
          AND $wpdb->postmeta.meta_key = 'epicredvote' 
          AND $wpdb->posts.post_status = 'publish' 
          AND $wpdb->posts.post_type = 'post'
          AND DAYOFMONTH(post_date) ='$d'
          AND MONTH(post_date) = '$m'
          AND YEAR(post_date) = '$y'
          GROUP BY ID
          ORDER BY LEFT($wpdb->posts.post_date, 10) DESC, $wpdb->postmeta.meta_value+0 DESC
       ";
      }else{
      #} build our first query of posts minimum 20 + the full day in which the 20th post is taken
      $querystr = "
          SELECT $wpdb->posts.*, YEAR(post_date) AS 'year',
            MONTH(post_date) AS 'month',
            DAYOFMONTH(post_date) AS 'dayofmonth'
          FROM $wpdb->posts, $wpdb->postmeta
          WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id 
          AND $wpdb->postmeta.meta_key = 'epicredvote' 
          AND $wpdb->posts.post_status = 'publish' 
          AND $wpdb->posts.post_type = 'post'
          AND post_date >= '$tf'
          GROUP BY ID
          ORDER BY LEFT($wpdb->posts.post_date, 10) DESC, $wpdb->postmeta.meta_value+0 DESC
       ";
      }
       $pageposts = $wpdb->get_results($querystr, OBJECT);
       $pageposts = get_posts(array("suppress_filters" => false));

       var_dump($pageposts);
    }else if($paged == 1){
      
      $d = $_GET['day'];
      $m = $_GET['month'];
      $y = $_GET['year'];

          $query = "SELECT YEAR(post_date) AS 'year',
                    MONTH(post_date) AS 'month',
                    DAYOFMONTH(post_date) AS 'dayofmonth',
                    count(ID) as posts
                    FROM $wpdb->posts
                    WHERE post_type = 'post'
                    AND post_status = 'publish'
                    GROUP BY YEAR(post_date),
                    MONTH(post_date),
                    DAYOFMONTH(post_date)
                    ORDER BY post_date DESC";
   
          $arcresults = $wpdb->get_results($query);    //this gets the posts grouped by year, month, dayofmonth



      $key = pluginhunt_findPrevious($y, $m, $d, $arcresults);

      echo "<div id='epic-key' class='hideme'>" . $key . "</div>";

      $d = $arcresults[$key]->dayofmonth;
      $m = $arcresults[$key]->month;
      $y = $arcresults[$key]->year;


      $querystr = "
          SELECT $wpdb->posts.*, YEAR(post_date) AS 'year',
            MONTH(post_date) AS 'month',
            DAYOFMONTH(post_date) AS 'dayofmonth'
          FROM $wpdb->posts, $wpdb->postmeta
          WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id 
          AND $wpdb->postmeta.meta_key = 'epicredvote' 
          AND $wpdb->posts.post_status = 'publish' 
          AND $wpdb->posts.post_type = 'post'
          AND DAYOFMONTH(post_date) ='$d'
          AND MONTH(post_date) = '$m'
          AND YEAR(post_date) = '$y'
          GROUP BY (ID)
          ORDER BY LEFT($wpdb->posts.post_date, 10) DESC, $wpdb->postmeta.meta_value+0 DESC
       ";


       $pageposts = $wpdb->get_results($querystr, OBJECT);
    }else{
          $query = "SELECT YEAR(post_date) AS 'year',
                    MONTH(post_date) AS 'month',
                    DAYOFMONTH(post_date) AS 'dayofmonth',
                    count(ID) as posts
                    FROM $wpdb->posts ".$join."
                    WHERE post_type = 'post'
                    AND post_status = 'publish'
                    GROUP BY YEAR(post_date),
                    MONTH(post_date),
                    DAYOFMONTH(post_date)
                    ORDER BY post_date DESC";
   
          $arcresults = $wpdb->get_results($query);  //this gets the posts grouped by year, month, dayofmonth
      $key = $_GET['key'];

      $d = $arcresults[$key]->dayofmonth;
      $m = $arcresults[$key]->month;
      $y = $arcresults[$key]->year;

      $querystr = "
          SELECT $wpdb->posts.*, YEAR(post_date) AS 'year',
            MONTH(post_date) AS 'month',
            DAYOFMONTH(post_date) AS 'dayofmonth'
          FROM $wpdb->posts, $wpdb->postmeta
          WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id 
          AND $wpdb->postmeta.meta_key = 'epicredvote' 
          AND $wpdb->posts.post_status = 'publish' 
          AND $wpdb->posts.post_type = 'post'
          AND DAYOFMONTH(post_date) ='$d'
          AND MONTH(post_date) = '$m'
          AND YEAR(post_date) = '$y'
          GROUP BY (ID)
          ORDER BY LEFT($wpdb->posts.post_date, 10) DESC, $wpdb->postmeta.meta_value+0 DESC
       ";
       $pageposts = $wpdb->get_results($querystr, OBJECT);
    }
  }else{
    if($paged == 0){
      $query = "SELECT post_date, YEAR(post_date) AS 'year', MONTH(post_date) AS 'month' FROM $wpdb->posts WHERE post_status='publish' AND post_type = 'post' ORDER BY post_date DESC LIMIT 10";
      $first = $wpdb->get_results($query);
      $c =  count($first) - 1;  //position in array....

      $position = count($first);
      
      $f =  $first[$c]->post_date;
      $tf = substr($f,0,10);
      $date = date('U');
      $m =  $first[$c]->month;
      $y =  $first[$c]->year;
      $key = '';
      #} extra check - does the month and year match for the first and last post from the 10. If so, then get all posts from that month and year
      
      if($m == $first[0]->month && $y == $first[0]->year){
      $querystr = "
          SELECT $wpdb->posts.*, YEAR(post_date) AS 'year',
            MONTH(post_date) AS 'month'
          FROM $wpdb->posts, $wpdb->postmeta
          WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id 
          AND $wpdb->postmeta.meta_key = 'epicredvote' 
          AND $wpdb->posts.post_status = 'publish' 
          AND $wpdb->posts.post_type = 'post'
          AND MONTH(post_date) = '$m'
          AND YEAR(post_date) = '$y'
          GROUP BY ID
          ORDER BY LEFT($wpdb->posts.post_date, 10) DESC, $wpdb->postmeta.meta_value+0 DESC
       ";
      }else{
      #} build our first query of posts minimum 20 + the full day in which the 20th post is taken
      $querystr = "
          SELECT $wpdb->posts.*, YEAR(post_date) AS 'year',
            MONTH(post_date) AS 'month'
          FROM $wpdb->posts, $wpdb->postmeta
          WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id 
          AND $wpdb->postmeta.meta_key = 'epicredvote' 
          AND $wpdb->posts.post_status = 'publish' 
          AND $wpdb->posts.post_type = 'post'
          AND post_date >= '$tf'
          GROUP BY ID
          ORDER BY LEFT($wpdb->posts.post_date, 10) DESC, $wpdb->postmeta.meta_value+0 DESC
       ";
      }
      $pageposts = $wpdb->get_results($querystr, OBJECT);
    }else if($paged == 1){

      $m = (int)$_GET['month'];
      $y = (int)$_GET['dog'];

  
      $key = $paged;
      if(!isset($key)){
        $key = $paged;
      }

          $query = "SELECT YEAR(post_date) AS 'year',
                    MONTH(post_date) AS 'month',
                    count(ID) as posts
                    FROM $wpdb->posts
                    WHERE post_type = 'post'
                    AND post_status = 'publish'
                    GROUP BY YEAR(post_date),
                    MONTH(post_date)
                    ORDER BY post_date DESC";

          $d=1; //first day of month 
          $arcresults = $wpdb->get_results($query);    //this gets the posts grouped by year, month, dayofmonth

      echo "<div id='epic-key' class='hideme'>" . $key . "</div>";


      $m = $arcresults[1]->month;
      $y = $arcresults[1]->year;

  
      $querystr = "
          SELECT $wpdb->posts.*, YEAR(post_date) AS 'year',
            MONTH(post_date) AS 'month',
            DAYOFMONTH(post_date) AS 'dayofmonth'
          FROM $wpdb->posts, $wpdb->postmeta
          WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id 
          AND $wpdb->postmeta.meta_key = 'epicredvote' 
          AND $wpdb->posts.post_status = 'publish' 
          AND $wpdb->posts.post_type = 'post'
          AND MONTH(post_date) = '$m'
          AND YEAR(post_date) = '$y'
          GROUP BY (ID)
          ORDER BY LEFT($wpdb->posts.post_date, 10) DESC, $wpdb->postmeta.meta_value+0 DESC
       ";
       $pageposts = $wpdb->get_results($querystr, OBJECT);
    }else{
          $query = "SELECT YEAR(post_date) AS 'year',
                    MONTH(post_date) AS 'month',
                    count(ID) as posts
                    FROM $wpdb->posts
                    WHERE post_type = 'post'
                    AND post_status = 'publish'
                    GROUP BY YEAR(post_date),
                    MONTH(post_date)
                    ORDER BY post_date DESC";
   
          $arcresults = $wpdb->get_results($query);  //this gets the posts grouped by year, month, dayofmonth
      $key = $paged;

      $m = $arcresults[$key]->month;
      $y = $arcresults[$key]->year;

      $querystr = "
          SELECT $wpdb->posts.*, YEAR(post_date) AS 'year',
            MONTH(post_date) AS 'month'
          FROM $wpdb->posts, $wpdb->postmeta
          WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id 
          AND $wpdb->postmeta.meta_key = 'epicredvote' 
          AND $wpdb->posts.post_status = 'publish' 
          AND $wpdb->posts.post_type = 'post'
          AND MONTH(post_date) = '$m'
          AND YEAR(post_date) = '$y'
          GROUP BY (ID)
          ORDER BY LEFT($wpdb->posts.post_date, 10) DESC, $wpdb->postmeta.meta_value+0 DESC
       ";
       $pageposts = $wpdb->get_results($querystr, OBJECT);
    }      //grouping by month...
  }
}

?>



