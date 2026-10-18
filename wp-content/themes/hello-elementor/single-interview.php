<?php
/**
 * Template Name: Q&A / Interview Format
 * Template Post Type: post
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main adamas-blog-single adamas-layout-interview">

	<!-- Split Interview Header -->
	<header class="interview-header-section">
		<div class="interview-header-container">
			<div class="interview-header-left">
				<span class="interview-badge"><i class="fas fa-comments"></i> In-Depth Dialogue</span>
				<h1 class="interview-title"><?php the_title(); ?></h1>
				<div class="entry-meta">
					<span class="meta-date"><i class="far fa-calendar-alt"></i> <?php echo get_the_date( 'F j, Y' ); ?></span>
					<span class="meta-sep">•</span>
					<span class="meta-reading-time"><i class="far fa-clock"></i> <?php echo adamas_get_reading_time(); ?></span>
				</div>
			</div>
			<div class="interview-header-right">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="interview-featured-avatar">
						<?php the_post_thumbnail( 'large', [ 'class' => 'interview-portrait' ] ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</header>

	<div class="interview-body-wrapper">
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'article-interview-content' ); ?>>
			<!-- Key Takeaways Callout -->
			<div class="interview-callout-box">
				<h4><i class="fas fa-lightbulb"></i> Executive Summary & Key Takeaways</h4>
				<p>Explore this engaging interview featuring insights into contemporary thought, leadership, and higher education innovation.</p>
			</div>

			<div class="entry-content interview-qa-content">
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

			<!-- Prev / Next Navigation -->
			<?php adamas_render_post_nav(); ?>

			<!-- Comments Area -->
			<?php
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;
			?>
		</article>

		<!-- Related Posts -->
		<?php adamas_render_related_posts(); ?>
	</div>

</main>

<?php
get_footer();
