<?php
/**
 * MF2 Feed Template for displaying an MF2 feed.
 *
 * @package MF2 Feed
 */

defined( 'ABSPATH' ) || exit;

header( 'Content-Type: ' . feed_content_type( 'mf2' ), true );

$items = array(
	'items' => array(
		array(
			'type'       => array( 'h-feed' ),
			'properties' => array(
				'name'    => array( get_bloginfo( 'name' ) ),
				'summary' => array( get_bloginfo( 'description' ) ),
				'url'     => array( get_self_link() ),
			),
		),
	),
);

$featured = get_site_icon_url();
if ( ! empty( $featured ) ) {
	$items['items'][0]['properties']['featured'] = array( $featured );
}

while ( have_posts() ) {
	the_post();
	$item = Mf2_Feed_Entry::from_post( get_post() );
	if ( $item ) {
		$items['items'][0]['children'][] = current( $item->to_mf2() );
	}
}

// filter output
$items = apply_filters( 'mf2_feed_array', $items );
echo Mf2_Feed::encode_json( $items );
