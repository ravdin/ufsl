<?php
/* Template Name: Search Results */

add_action( 'wp_footer', function() {
  ?>
    <script>
      document.addEventListener('facetwp-loaded', function() {
        if (FWP.loaded) {
			count = FWP.settings.pager.total_rows;
			$("#query-count").text("You're viewing " + count + " resource" + (count == 1 ? "" : "s") + ".");
		}
      });
    </script>
  <?php
}, 100 );


// retrieve our search query if applicable
function get_field_value($param) {
  return isset($_GET[$param]) ? sanitize_text_field($_GET[$param]) : '';
}

function add_to_query($key, $value, $compare, &$args) {
  if (!empty($value)) {
    $args['meta_query'][] = compact('key', 'value', 'compare');
  }
}

$orderby_fields = array(
  'post_title' => array(
    'orderby' => 'post_title'
  )
);
$order_fields = array('ASC', 'DESC');

if (!in_array($order, $order_fields)) {
  $order = 'ASC';
}

$args = array(
  "post_type" => "resource",
  "post_status" => "publish",
  "posts_per_page" => 20,
  "order" => $order,
  "custom_query" => true,
  "meta_query" => array(),
  "facetwp" => true
);
if (!isset($orderby_fields[$orderby])) {
  $orderby = 'post_title';
}

$args = array_merge($args, $orderby_fields[$orderby]);

if (!empty($resource_type)) {
  if (!is_array($resource_type)) {
    $resource_type = explode(',', $resource_type);
  }
  $resource_type = array_map('sanitize_text_field', $resource_type);
  add_to_query('$resource_type', $resource_type, 'IN', $args);
}

$resources = pods('resource');
$the_query = new WP_Query($args);
$total_count = $the_query->found_posts;

get_header(); ?>
	<section id="primary" class="et_pb_section et_pb_fullwidth_section search_results">
		<main id="main" class="site-main" role="main">

  			<header class="page-header">
  				<h1 class="page-title">
  					Browse the library
  				</h1>
          <h3 id="query-count">
            You're viewing <?= $total_count ?> resource<?= $total_count == 1 ? '' : 's' ?>.
          </h3>
  			</header><!-- .page-header -->
		<div class="keyword-tags">
		  <div class="label">Topics</div>
		  <?php	echo facetwp_display('facet', 'topics'); ?>
		  <div class="label">Features</div>
		  <?php	echo facetwp_display('facet', 'features'); ?>
		</div>
        <div class="et_pb_row et_pb_row_3-4_1-4">
          <div class="et_pb_column et_pb_column_1_4 et_pb_column_0 et_pb_column_single">
            <h3>Refine results</h3>
            <h5>Resource type</h5>
            <?php echo facetwp_display('facet', 'resource_type'); ?>
			<h5>DCI</h5>
            <?php echo facetwp_display('facet', 'dci'); ?>
			<h5>Phemonena</h5>
            <?php echo facetwp_display('facet', 'phenomena'); ?>
			<h5>Grades</h5>
            <?php echo facetwp_display('facet', 'grades'); ?>
          </div>
          <div class="et_pb_column et_pb_column_3_4 et_pb_column_1 et_pb_column_single facetwp-template">
            <?php while ( $the_query->have_posts() ) :
              $the_query->the_post();
              $resources->fetch(get_the_ID()); ?>
                  <div class="search-result-item">
                    <div class="resource-details">
                      <div class="resource-title">
                        <a href="<?= $resources->display('permalink') ?>"><?= $resources->display('title') ?></a>
                      </div>
                      <div>By&nbsp;<?= $resources->display('authors_names') ?></div>
					  <div><?= $resources->display('resource_type') ?></div>
					  <div class="resource-summary">
						  <?= $resources->display('summary') ?>
					  </div>
                    </div>
                  </div>
              <?php endwhile; ?>
              <?php echo facetwp_display( 'pager' ); ?>
            </div>
          </div>
		</main><!-- .site-main -->
	</section><!-- .content-area -->

<?php
// clean up after the query and pagination
wp_reset_postdata();
?>
<?php get_footer(); ?>
