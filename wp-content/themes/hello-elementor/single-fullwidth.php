<?php
/**
 * Template Name: Modern Full-Width Story
 * Template Post Type: post
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$thumb_url = has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_ID(), 'full' ) : '';
?>

<main id="primary" class="site-main adamas-blog-single adamas-layout-fullwidth">

	<!-- Immersive Hero Banner -->
	<section class="fullwidth-hero-banner" <?php if ( $thumb_url ) : ?>style="background-image: linear-gradient(180deg, rgba(35, 57, 76, 0.75) 0%, rgba(10, 20, 30, 0.95) 100%), url('<?php echo esc_url( $thumb_url ); ?>');"<?php endif; ?>>
		<div class="hero-container">
			<div class="entry-category-list">
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

			<h1 class="hero-title"><?php the_title(); ?></h1>

			<div class="hero-meta">
				<div class="meta-author">
					<?php echo get_avatar( get_the_author_meta( 'ID' ), 40, '', '', [ 'class' => 'meta-avatar' ] ); ?>
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
		</div>
	</section>

	<!-- Focused Center Content Column -->
	<div class="fullwidth-content-wrapper">
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'article-focused-content' ); ?>>
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

		<!-- Related Posts Section -->
		<?php adamas_render_related_posts(); ?>
	</div>

</main>

<?php
get_footer();
