<?php
/**
 * MF2 Feed Template for displaying an JF2 feed.
 *
 * @package MF2 Feed
 */

header( 'Content-Type: ' . feed_content_type( 'jf2feed' ), true );

require_once dirname( __FILE__ ) . '/class-mf2-feed-entry.php';

$items = array(
	'type'    => 'feed',
	'name'    => get_bloginfo( 'name' ),
	'summary' => get_bloginfo( 'description' ),
	'url'     => get_self_link(),
);
if ( ! empty( $featured ) ) {
	$items['featured'] = $featured;
}

while ( have_posts() ) {
	the_post();
	$item                = new Mf2_Feed_Entry( get_the_ID() );
	$items['children'][] = $item->to_jf2();
}

// filter output
$items = apply_filters( 'jf2_feed_array', $items );
echo Mf2Feed::encode_json( $items );
