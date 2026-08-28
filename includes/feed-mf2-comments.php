<?php
/**
 * MF2 Feed Template for displaying an MF2 item.
 *
 * @package MF2 Feed
 */

defined( 'ABSPATH' ) || exit;

header( 'Content-Type: ' . feed_content_type( 'mf2' ), true );

$items = array();
$item  = Mf2_Feed_Entry::from_post( get_post() );
if ( $item ) {
	$items['items'] = $item->to_mf2();
}

// filter output
$items = apply_filters( 'mf2_feed_array', $items );
echo Mf2_Feed::encode_json( $items );
