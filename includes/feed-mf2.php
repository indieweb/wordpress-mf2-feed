<?php
/**
 * MF2 Feed Template for displaying an MF2 feed.
 *
 * @package MF2 Feed
 */

header( 'Content-Type: ' . feed_content_type( 'mf2' ), true );

require_once dirname( __FILE__ ) . '/class-mf2-feed-entry.php';
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
	$item                            = new Mf2_Feed_Entry( get_the_ID() );
	$items['items'][0]['children'][] = current( $item->to_mf2() );
}

// filter output
$items = apply_filters( 'mf2_feed_array', $items );
echo Mf2Feed::encode_json( $items );
