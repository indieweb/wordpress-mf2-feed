<?php
/**
 * MF2 Feed Template for displaying an MF2 item as Microformats2 HTML.
 *
 * Renders the same entry as the MF2 JSON item feed, but as HTML that every
 * Microformats2 parser understands and every browser can display.
 *
 * @package MF2 Feed
 */

defined( 'ABSPATH' ) || exit;

header( 'Content-Type: ' . feed_content_type( 'mf2-html' ) . '; charset=' . get_option( 'blog_charset' ), true );

$item = Mf2_Feed_Entry::from_post( get_post() );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="robots" content="noindex, follow" />
<title><?php echo esc_html( wp_strip_all_tags( $item->name ) ); ?></title>
<link rel="canonical" href="<?php echo esc_url( $item->url ); ?>" />
</head>
<body>
<article class="h-entry">
	<?php if ( $item->name ) : ?>
	<h1 class="p-name"><?php echo esc_html( wp_strip_all_tags( $item->name ) ); ?></h1>
	<?php endif; ?>

	<p>
		<a class="u-url u-uid" href="<?php echo esc_url( $item->url ); ?>"><time class="dt-published" datetime="<?php echo esc_attr( $item->published ); ?>"><?php echo esc_html( $item->published ); ?></time></a>
		(<time class="dt-updated" datetime="<?php echo esc_attr( $item->updated ); ?>"><?php echo esc_html( $item->updated ); ?></time>)
	</p>

	<p class="p-author h-card">
		<?php if ( ! empty( $item->author['photo'] ) ) : ?>
		<img class="u-photo" src="<?php echo esc_url( $item->author['photo'] ); ?>" alt="" width="48" height="48" />
		<?php endif; ?>
		<a class="u-url p-name" href="<?php echo esc_url( $item->author['url'] ); ?>"><?php echo esc_html( $item->author['name'] ); ?></a>
	</p>

	<?php if ( $item->featured ) : ?>
	<img class="u-featured" src="<?php echo esc_url( $item->featured ); ?>" alt="" />
	<?php endif; ?>

	<?php if ( $item->summary ) : ?>
	<p class="p-summary"><?php echo esc_html( $item->summary ); ?></p>
	<?php endif; ?>

	<?php if ( ! empty( $item->content['html'] ) ) : ?>
	<div class="e-content"><?php echo wp_kses_post( $item->content['html'] ); ?></div>
	<?php endif; ?>

	<?php if ( $item->category ) : ?>
	<ul>
		<?php foreach ( $item->category as $category ) : ?>
		<li class="p-category"><?php echo esc_html( $category ); ?></li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
</article>
</body>
</html>
