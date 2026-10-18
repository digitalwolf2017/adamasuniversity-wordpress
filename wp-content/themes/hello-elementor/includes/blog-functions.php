<?php
/**
 * Dynamic Blog Functions & Components
 * Adamas University
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Calculate Estimated Reading Time based on 200 words per minute.
 */
function adamas_get_reading_time() {
	$content = get_post_field( 'post_content', get_the_ID() );
	$word_count = str_word_count( strip_tags( $content ) );
	$reading_time = ceil( $word_count / 200 );
	return max( 1, $reading_time ) . ' min read';
}

/**
 * Render Modern Social Share Buttons
 */
function adamas_render_social_share() {
	$url   = urlencode( get_permalink() );
	$title = urlencode( get_the_title() );
	?>
	<div class="adamas-social-share">
		<span class="share-label"><i class="fas fa-share-alt"></i> Share:</span>
		<div class="share-buttons-group">
			<a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $url; ?>" target="_blank" rel="noopener noreferrer" class="share-btn share-linkedin" title="Share on LinkedIn" aria-label="Share on LinkedIn">
				<i class="fab fa-linkedin-in"></i>
			</a>
			<a href="https://twitter.com/intent/tweet?url=<?php echo $url; ?>&text=<?php echo $title; ?>" target="_blank" rel="noopener noreferrer" class="share-btn share-twitter" title="Share on X" aria-label="Share on X">
				<i class="fab fa-twitter"></i>
			</a>
			<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>" target="_blank" rel="noopener noreferrer" class="share-btn share-facebook" title="Share on Facebook" aria-label="Share on Facebook">
				<i class="fab fa-facebook-f"></i>
			</a>
			<a href="https://api.whatsapp.com/send?text=<?php echo $title . '%20' . $url; ?>" target="_blank" rel="noopener noreferrer" class="share-btn share-whatsapp" title="Share on WhatsApp" aria-label="Share on WhatsApp">
				<i class="fab fa-whatsapp"></i>
			</a>
			<button type="button" class="share-btn share-copy" data-url="<?php echo esc_url( get_permalink() ); ?>" title="Copy Link" aria-label="Copy Link">
				<i class="fas fa-link"></i>
			</button>
		</div>
	</div>
	<?php
}

/**
 * Render Dynamic Author Card
 */
function adamas_render_author_card() {
	$author_id   = get_the_author_meta( 'ID' );
	$author_name = get_the_author_meta( 'display_name' );
	$author_bio  = get_the_author_meta( 'description' );
	if ( empty( $author_bio ) ) {
		$author_bio = 'Passionate contributor sharing insights, university perspectives, and educational updates at Adamas.';
	}
	?>
	<div class="adamas-author-card">
		<div class="author-avatar-wrapper">
			<?php echo get_avatar( $author_id, 90, '', $author_name, [ 'class' => 'author-avatar' ] ); ?>
		</div>
		<div class="author-info">
			<span class="author-byline">Written by</span>
			<h4 class="author-name"><?php echo esc_html( $author_name ); ?></h4>
			<p class="author-bio"><?php echo esc_html( $author_bio ); ?></p>
		</div>
	</div>
	<?php
}

/**
 * Render Dynamic Related Posts from same category
 */
function adamas_render_related_posts() {
	$current_id = get_the_ID();
	$categories = wp_get_post_categories( $current_id );
	$args = [
		'post_type'      => 'post',
		'posts_per_page' => 3,
		'post__not_in'   => [ $current_id ],
		'orderby'        => 'rand',
	];
	if ( ! empty( $categories ) ) {
		$args['category__in'] = $categories;
	}
	$query = new WP_Query( $args );
	if ( ! $query->have_posts() ) {
		unset( $args['category__in'] );
		$query = new WP_Query( $args );
	}
	if ( $query->have_posts() ) :
	?>
	<section class="adamas-related-posts">
		<div class="related-header">
			<h3 class="related-title">Related Articles</h3>
			<div class="related-line"></div>
		</div>
		<div class="related-grid">
			<?php while ( $query->have_posts() ) : $query->the_post(); ?>
				<article class="related-card">
					<a href="<?php the_permalink(); ?>" class="related-thumb-link">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'medium_large', [ 'class' => 'related-thumb' ] ); ?>
						<?php else : ?>
							<div class="related-thumb-placeholder"><i class="fas fa-newspaper"></i></div>
						<?php endif; ?>
					</a>
					<div class="related-content">
						<div class="related-meta">
							<span><i class="far fa-calendar-alt"></i> <?php echo get_the_date( 'M j, Y' ); ?></span>
							<span>•</span>
							<span><?php echo adamas_get_reading_time(); ?></span>
						</div>
						<h4 class="related-card-title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h4>
						<a href="<?php the_permalink(); ?>" class="related-read-more">Read Article <i class="fas fa-arrow-right"></i></a>
					</div>
				</article>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</section>
	<?php
	endif;
}

/**
 * Render Post Navigation (Prev / Next Story)
 */
function adamas_render_post_nav() {
	$prev_post = get_previous_post();
	$next_post = get_next_post();
	if ( ! $prev_post && ! $next_post ) {
		return;
	}
	?>
	<nav class="adamas-post-navigation">
		<div class="nav-prev">
			<?php if ( $prev_post ) : ?>
				<a href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>">
					<span class="nav-label"><i class="fas fa-arrow-left"></i> Previous Story</span>
					<span class="nav-title"><?php echo esc_html( get_the_title( $prev_post->ID ) ); ?></span>
				</a>
			<?php endif; ?>
		</div>
		<div class="nav-next">
			<?php if ( $next_post ) : ?>
				<a href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>">
					<span class="nav-label">Next Story <i class="fas fa-arrow-right"></i></span>
					<span class="nav-title"><?php echo esc_html( get_the_title( $next_post->ID ) ); ?></span>
				</a>
			<?php endif; ?>
		</div>
	</nav>
	<?php
}

/**
 * Dynamic Sidebar Content
 */
function adamas_render_sidebar() {
	?>
	<aside class="adamas-blog-sidebar">
		<!-- About Box -->
		<div class="sidebar-widget widget-about">
			<h4 class="widget-title">About The Perspective Shift</h4>
			<div class="widget-title-line"></div>
			<p>Insights, dialogues, and forward-looking discussions from faculty, thinkers, and visionary leaders at Adamas.</p>
		</div>

		<!-- Recent Articles -->
		<div class="sidebar-widget widget-recent">
			<h4 class="widget-title">Recent Articles</h4>
			<div class="widget-title-line"></div>
			<?php
			$recent = new WP_Query( [
				'post_type'      => 'post',
				'posts_per_page' => 4,
				'post__not_in'   => [ get_the_ID() ],
			] );
			if ( $recent->have_posts() ) :
			?>
			<ul class="recent-posts-list">
				<?php while ( $recent->have_posts() ) : $recent->the_post(); ?>
					<li class="recent-post-item">
						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php the_permalink(); ?>" class="recent-thumb-link">
								<?php the_post_thumbnail( 'thumbnail', [ 'class' => 'recent-thumb' ] ); ?>
							</a>
						<?php endif; ?>
						<div class="recent-info">
							<a href="<?php the_permalink(); ?>" class="recent-title"><?php the_title(); ?></a>
							<span class="recent-date"><?php echo get_the_date( 'M j, Y' ); ?></span>
						</div>
					</li>
				<?php endwhile; wp_reset_postdata(); ?>
			</ul>
			<?php endif; ?>
		</div>

		<!-- Categories -->
		<div class="sidebar-widget widget-categories">
			<h4 class="widget-title">Categories</h4>
			<div class="widget-title-line"></div>
			<ul class="category-list">
				<?php
				$cats = get_categories( [ 'hide_empty' => true ] );
				foreach ( $cats as $cat ) :
				?>
					<li>
						<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>">
							<span><?php echo esc_html( $cat->name ); ?></span>
							<span class="cat-count"><?php echo esc_html( $cat->count ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</aside>
	<?php
}

/**
 * Dynamic Blog Grid Shortcode: [adamas_blog_grid]
 * Usage: Insert [adamas_blog_grid] anywhere on the Blogs page to automatically list all posts.
 */
function adamas_blog_grid_shortcode( $atts ) {
	$atts = shortcode_atts( [
		'posts_per_page' => 6,
		'paged'          => max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) ),
	], $atts, 'adamas_blog_grid' );

	$query = new WP_Query( [
		'post_type'      => 'post',
		'posts_per_page' => intval( $atts['posts_per_page'] ),
		'paged'          => intval( $atts['paged'] ),
	] );

	ob_start();
	if ( $query->have_posts() ) :
	?>
	<div class="adamas-dynamic-blog-grid">
		<?php while ( $query->have_posts() ) : $query->the_post(); ?>
			<article class="adamas-blog-card">
				<a href="<?php the_permalink(); ?>" class="card-thumb-link">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'large', [ 'class' => 'card-thumb' ] ); ?>
					<?php else : ?>
						<div class="card-thumb-placeholder"><i class="fas fa-newspaper"></i></div>
					<?php endif; ?>
					<?php
					$cats = get_the_category();
					if ( ! empty( $cats ) ) :
					?>
						<span class="card-category-badge"><?php echo esc_html( $cats[0]->name ); ?></span>
					<?php endif; ?>
				</a>
				<div class="card-body">
					<div class="card-meta">
						<span><i class="far fa-calendar-alt"></i> <?php echo get_the_date( 'M j, Y' ); ?></span>
						<span>•</span>
						<span><i class="far fa-user"></i> <?php the_author(); ?></span>
						<span>•</span>
						<span><?php echo adamas_get_reading_time(); ?></span>
					</div>
					<h3 class="card-title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h3>
					<p class="card-excerpt">
						<?php echo wp_trim_words( get_the_excerpt(), 20, '...' ); ?>
					</p>
					<a href="<?php the_permalink(); ?>" class="card-read-btn">Read Article <i class="fas fa-arrow-right"></i></a>
				</div>
			</article>
		<?php endwhile; ?>
	</div>

	<?php if ( $query->max_num_pages > 1 ) : ?>
		<div class="adamas-blog-pagination">
			<?php
			echo paginate_links( [
				'total'        => $query->max_num_pages,
				'current'      => intval( $atts['paged'] ),
				'prev_text'    => '<i class="fas fa-arrow-left"></i> Previous',
				'next_text'    => 'Next <i class="fas fa-arrow-right"></i>',
				'type'         => 'plain',
			] );
			?>
		</div>
	<?php endif; ?>

	<?php
	wp_reset_postdata();
	else :
		echo '<p class="no-posts-found">No blog posts found.</p>';
	endif;

	return ob_get_clean();
}
add_shortcode( 'adamas_blog_grid', 'adamas_blog_grid_shortcode' );
