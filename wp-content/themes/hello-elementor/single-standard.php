<?php
/**
 * Template Name: Standard Editorial (With Sidebar)
 * Template Post Type: post
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main adamas-blog-single adamas-layout-standard">
	<div class="adamas-blog-container">

		<!-- Article Header -->
		<header class="entry-header">
			<div class="entry-category-list">
				<?php
				$categories = get_the_category();
				if ( ! empty( $categories ) ) :
					foreach ( $categories as $cat ) :
				?>
					<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="category-badge">
						<?php echo esc_html( $cat->name ); ?>
					</a>
				<?php
					endforeach;
				endif;
				?>
			</div>

			<h1 class="entry-title"><?php the_title(); ?></h1>

			<div class="entry-meta">
				<div class="meta-author">
					<?php echo get_avatar( get_the_author_meta( 'ID' ), 36, '', '', [ 'class' => 'meta-avatar' ] ); ?>
					<span>By <strong><?php the_author(); ?></strong></span>
				</div>
				<span class="meta-sep">•</span>
				<div class="meta-date">
					<i class="far fa-calendar-alt"></i> <?php echo get_the_date( 'F j, Y' ); ?>
				</div>
				<span class="meta-sep">•</span>
				<div class="meta-reading-time">
					<i class="far fa-clock"></i> <?php echo adamas_get_reading_time(); ?>
				</div>
			</div>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="entry-featured-image">
				<?php the_post_thumbnail( 'full', [ 'class' => 'featured-img' ] ); ?>
				<?php if ( get_the_post_thumbnail_caption() ) : ?>
					<figcaption class="featured-img-caption"><?php echo get_the_post_thumbnail_caption(); ?></figcaption>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<!-- Main 2-Column Content Layout -->
		<div class="adamas-blog-layout-grid">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'article-main-content' ); ?>>
				<div class="entry-content">
					<?php the_content(); ?>
				</div>

				<?php if ( has_tag() ) : ?>
					<div class="entry-tags">
						<span class="tags-label"><i class="fas fa-tags"></i> Tags:</span>
						<?php the_tags( '', ' ', '' ); ?>
					</div>
				<?php endif; ?>

				<!-- Social Sharing -->
				<?php adamas_render_social_share(); ?>

				<!-- Author Card -->
				<?php adamas_render_author_card(); ?>

				<!-- Prev / Next Story Nav -->
				<?php adamas_render_post_nav(); ?>

				<!-- Comments Area -->
				<?php
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;
				?>
			</article>

			<!-- Sidebar -->
			<div class="adamas-sidebar-col">
				<?php adamas_render_sidebar(); ?>
			</div>
		</div>

		<!-- Related Posts Section (Full Width across bottom) -->
		<?php adamas_render_related_posts(); ?>

	</div>
</main>

<?php
get_footer();
