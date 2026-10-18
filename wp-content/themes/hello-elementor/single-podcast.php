<?php
/**
 * Template Name: Video & Podcast Spotlight
 * Template Post Type: post
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// Check for video or audio in post content
$content = get_the_content();
?>

<main id="primary" class="site-main adamas-blog-single adamas-layout-podcast">

	<!-- Dark Podcast / Video Header -->
	<section class="podcast-spotlight-hero">
		<div class="podcast-hero-inner">
			<div class="entry-category-list">
				<span class="podcast-tag-badge"><i class="fas fa-podcast"></i> Podcast & Video Spotlight</span>
				<?php
				$categories = get_the_category();
				if ( ! empty( $categories ) ) :
					foreach ( $categories as $cat ) :
				?>
					<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="category-badge hero-badge">
						<?php echo esc_html( $cat->name ); ?>
					</a>
				<?php
					endforeach;
				endif;
				?>
			</div>

			<h1 class="podcast-title"><?php the_title(); ?></h1>

			<div class="hero-meta">
				<div class="meta-author">
					<?php echo get_avatar( get_the_author_meta( 'ID' ), 36, '', '', [ 'class' => 'meta-avatar' ] ); ?>
					<span>Hosted by <strong><?php the_author(); ?></strong></span>
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

			<!-- Featured Media Showcase -->
			<div class="podcast-player-container">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="podcast-thumbnail-card">
						<?php the_post_thumbnail( 'full', [ 'class' => 'podcast-featured-img' ] ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<div class="podcast-content-wrapper">
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'article-podcast-content' ); ?>>
			<!-- Episode Summary & Show Notes -->
			<div class="podcast-notes-header">
				<h3><i class="fas fa-headphones-alt"></i> Episode Show Notes & Highlights</h3>
				<div class="notes-line"></div>
			</div>

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

			<!-- Host & Speaker Info -->
			<?php adamas_render_author_card(); ?>

			<!-- Prev / Next Navigation -->
			<?php adamas_render_post_nav(); ?>

			<!-- Comments Area -->
			<?php
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;
			?>
		</article>

		<!-- Related Episodes -->
		<?php adamas_render_related_posts(); ?>
	</div>

</main>

<?php
get_footer();
