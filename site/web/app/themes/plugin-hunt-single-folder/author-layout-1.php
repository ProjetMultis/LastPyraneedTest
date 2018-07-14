<?php
/**
 * The author template
*
*/

get_header();
	
	$author = get_user_by( 'slug', get_query_var( 'author_name' ) );
	$email = $author->user_email; 
	$site = $author->user_url;
	$size = 100;

global $wp_query,$post,$wpdb, $current_user,$query_string;
      
    wp_get_current_user();
	  $wpdb->myo_ip   = $wpdb->prefix . 'epicred';
    $ph_follows = $wpdb->prefix . "ph_follows";
// grab the queries we want for the new tabbed display, being:- 
    $fid = $current_user->ID;
    $aid = $author->ID;

    $q1 = "SELECT epicred_id FROM $wpdb->myo_ip JOIN $wpdb->posts ON $wpdb->myo_ip.epicred_id = $wpdb->posts.ID WHERE epicred_ip = $aid AND $wpdb->posts.post_status = 'publish'";
    $upvoted = $wpdb->get_results($q1, OBJECT);
    $cu =  count($upvoted);
    // #2 submitted 
    $q2 = "SELECT ID FROM $wpdb->posts WHERE $wpdb->posts.post_author = $aid AND $wpdb->posts.post_type = 'post' AND $wpdb->posts.post_status = 'publish'";
    $submitted = $wpdb->get_results($q2, OBJECT);
    $cs =  count($submitted);
    // #3 'made' (UX to be added - suggest a maker - how to handle since not just twitter login...)
    // #4 collections (UX to be added)
    $q4 = "SELECT ID FROM $wpdb->posts WHERE $wpdb->posts.post_author = $aid AND $wpdb->posts.post_type = 'collections' AND $wpdb->posts.post_status = 'publish'";
    $collected = $wpdb->get_results($q4, OBJECT);
    $cl =  count($collected);
    #} discussions
    if(of_get_option('ph_enable_discussions') == 1){
    $q7 = "SELECT ID FROM $wpdb->posts WHERE $wpdb->posts.post_author = $aid AND $wpdb->posts.post_type = 'discussions' AND $wpdb->posts.post_status = 'publish'";
    $discussed = $wpdb->get_results($q7, OBJECT);
    $cd =  count($discussed);
    }

    // #5 followers (UX to be added)
    $q5 = "SELECT follower FROM $ph_follows WHERE followed = $aid";
    $followers = $wpdb->get_results($q5, OBJECT);
    $cfr =  count($followers);
   // #6 following (UX to be added)
    $q6 = "SELECT followed FROM $ph_follows WHERE follower = $aid";
    $followed = $wpdb->get_results($q6, OBJECT);
    $cfd =  count($followed);

    global $sub_menu_ph62;
    if($sub_menu_ph62){
        $pheaderclass = 'extra-margin-top';
    }else{
        $pheaderclass = 'no-extra-margin';
    }

    
?>
<style>
.ph-list-thumbnail {
    float: left;
    width: 200px;
    margin-right: 10px;
}
</style>

<header class="page-header <?php echo $pheaderclass;?>">
      <div class="container">
        <div class="page-header--avatar">
            <!-- <a class="user-image--badge v-maker v-big" href="#">M</a>-->
            <?php echo get_avatar( $aid ); ?>
        </div>


        <div class="page-header--info" data-component="Emoji">
          <h1 class="page-header--title"><?php echo $author->nickname;?><span class="page-header--id">#<?php echo $aid; ?></span></h1>
          <h2 class="page-header--subtitle"><?php echo $author->description; ?></h2>
          <div class="page-header--links">
            <?php
            $tw = get_user_meta( $aid, 'twitter', true );
            if($tw){ 
            ?>
            <a class="page-header--links--link" target="_blank" href="http://twitter.com/<?php echo $tw; ?>">@<?php echo $tw; ?></a>
            <?php }?>
              <a class="page-header--links--link" target="_blank" href="<?php echo $site;?>"><?php echo $site;?></a>
          </div>
        </div>

        <div class="page-header--buttons">
          <?php 
          if($fid != $aid){
          $inDB = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $ph_follows WHERE follower = %d AND followed = %d", array($fid, $aid)) );
          if($inDB){ ?>
            <a class="button v-red ph-follow" data-crud = "0" data-follower="<?php echo $fid;?>" data-follow="<?php echo $aid;?>"  href="#"><?php _e("Unfollow","ph_theme");?></a>
          <?php }else{ ?>
            <a class="button v-green ph-follow" data-crud = "1"  data-follower="<?php echo $fid;?>" data-follow="<?php echo $aid;?>"  href="#"><?php _e("Follow","ph_theme");?></a>
          <?php } }?>
        </div>
        <?php ?>

        <nav class="page-header--navigation">
  <ul>
    <li class="page-header--navigation--tab m-active" id = 'ah-upvoted'>
      <a href="#"><strong><?php echo $cu; ?></strong> <?php _e('Upvoted','pluginhunt');?></a>
    </li>
      <li class="page-header--navigation--tab" id = 'ah-submitted'>
        <a href="#"><strong><?php echo $cs; ?></strong><?php _e('Submitted','pluginhunt');?></a>
      </li>
<!-- MAKER to come in the future
    <li class="page-header--navigation--tab" id = 'ah-made'>
      <a href="#"><strong>3</strong> Made</a>
    </li>
-->
      <li class="page-header--navigation--tab" id = 'ah-collections'>
        <a href="#"><strong><?php echo $cl; ?></strong><?php  _e('Collections','pluginhunt');?></a>
      </li>

      <?php if(of_get_option('ph_enable_discussions') == 1){ ?>
      <li class="page-header--navigation--tab" id = 'ah-discussions'>
        <a href="#"><strong><?php echo $cd; ?></strong><?php _e('Discussions','pluginhunt');?></a>
      </li>
      <?php } ?>

    <li class="page-header--navigation--tab" id = 'ah-followers'>
      <a href="#"><strong><?php echo $cfr; ?></strong><?php _e('Followers','pluginhunt');?></a>
    </li>
    <li class="page-header--navigation--tab" id = 'ah-following'>
      <a href="#"><strong><?php echo $cfd; ?></strong><?php _e('Following','pluginhunt');?></a>
    </li>
  </ul>
</nav>

      </div>
    </header>

<div class='container ph-tab-container ph-layout-1'>

  <div class='ph-tabbed active' id ='ah-upvoted-tab'>
<?php global $post; ?>
 <?php foreach ($upvoted as $ID): ?>
 <?php 

  $post = get_post($ID->epicred_id); 

        $postvote = get_post_meta($post->ID, 'epicredvote' ,true);
        wpeddit_post_ranking($post->ID);

        if($postvote == NULL){
          $postvote = 0;
        }
      
        $fid = $current_user->ID;
        pluginhunt_GetRankings($post->ID, $fid);

  ?>

  <div class = 'row hunt-row <?php echo $blob;?>' style = 'margin-bottom:20px'>
  <?php pluginhunt_outputVoting($post); ?>
  <?php pluginhunt_FeaturedImage($post); ?>
  <div class='post-meta-hunt'>
    <?php pluginhunt_ExternalLink($post); ?>
    <?php pluginhunt_CollectionOutput($post->ID); ?>
    <?php pluginhunt_AuthorMeta($post); ?>
    <?php pluginhunt_commentList($post); ?>
  </div>
    <?php pluginhunt_PostContent($post); ?>
</div>
<div style="clear:both"></div>

        </div>  
        <?php
                $plugina = get_post_meta($post->ID,'pluginauthor', true);
                if($plugina ==''){
                  $pname = get_the_author_meta('user_nicename');
                  $auth = 'yes';
                }else{
                  $pname = $plugina;
                  $auth = 'no';
                }
                          $profileUrl = '#'; if (isset($post->ID)) $profileUrl = get_author_posts_url($post->post_author); 
                $url = home_url();

                if(wp_is_mobile()){
                  $mob = '-mob';
                }

              $out =  get_post_meta($post->ID, 'outbound', true);
              $n = parse_url($out);
      ?>
      
        <div style="clear:both"></div>
      

      
 <?php endforeach; ?>

  </div>
  <div class='ph-tabbed' id ='ah-submitted-tab'>
<?php global $post, $blob; ?>
 <?php foreach ($submitted as $ID): ?>
 <?php 

      $post = get_post($ID->ID);
?>
<div class = 'row hunt-row <?php echo $blob;?>' style = 'margin-bottom:20px'>
  <?php pluginhunt_outputVoting($post); ?>
  <?php pluginhunt_FeaturedImage($post); ?>
  <div class='post-meta-hunt'>
    <?php pluginhunt_ExternalLink($post); ?>
    <?php pluginhunt_CollectionOutput($post->ID); ?>
    <?php pluginhunt_AuthorMeta($post); ?>
    <?php pluginhunt_commentList($post); ?>
  </div>
    <?php pluginhunt_PostContent($post); ?>
</div>
<div style="clear:both"></div>
    
    </div>
      
 <?php endforeach; ?>
  </div>

  <div class='ph-tabbed' id='ah-collections-tab'>
    <?php
      foreach($collected as $c){
        $post = get_post($c->ID);
         get_template_part( 'content', 'collections' );
      }
    ?>

  </div>

<?php if(of_get_option('ph_enable_discussions') == 1){ ?>
    <div class='ph-tabbed' id='ah-discussions-tab'>
    <?php
      foreach($discussed as $d){
        $post = get_post($d->ID);
         get_template_part( 'content', 'discussions' );
      }
    ?>
  </div>
  <?php } ?>

  <div class='ph-tabbed' id ='ah-followers-tab'>
    <?php 
       $fw = 0;
       foreach($followers as $f){ 

         if($fw==0){
          echo "<div class='row author-row'>";
         }

        echo "<div class='col-md-4 author-list'>";
          echo "<span class='person'>";
          echo  '<a href="';
          echo get_author_posts_url( $f->follower );
          echo '">';
          echo get_avatar( $f->follower, 40 ); 
            echo "<div class='title'>";
              echo get_author_name( $f->follower );
            echo "</div></a></span>";
           $fw++;
          echo "</div>";

          if($fw == 3){
            $fw=0; 
            echo "</div>";
          }
          

        }
     
    ?>
  </div>
  </div>
  <div class='ph-tabbed' id ='ah-following-tab'>
    <?php 
       $fr = 0;
       foreach($followed as $g){ 

         if($fr==0){
          echo "<div class='row author-row'>";
         }

        echo "<div class='col-md-4 author-list'>";
          echo "<span class='person'>";
          echo  '<a href="';
          echo get_author_posts_url( $g->followed );
          echo '">';
          echo get_avatar( $g->followed, 40 ); 
            echo "<div class='title'>";
              echo get_author_name( $g->followed );
            echo "</div></span>";
           $fr++;
          echo "</div>";

          if($fr == 3){
            $fr=0; 
            echo "</div>";
          }

        }
        echo "</div>";
    ?>

  </div>
</div>


			
			<?php wp_reset_query(); ?>


<?php get_footer(); ?>
