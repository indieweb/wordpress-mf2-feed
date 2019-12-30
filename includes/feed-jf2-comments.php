<?php
/**
 * MF2 Feed Template for displaying an JF2 item.
 *
 * @package MF2 Feed
 */

header( 'Content-Type: ' . feed_content_type( 'jf2' ), true );

require_once dirname( __FILE__ ) . '/class-mf2-feed-entry.php';
$items = array();
$p     = get_post();
if ( $p ) {
	$item  = new Mf2_Feed_Entry( $p );
	$items = $item->to_jf2();
}

// filter output
$items = apply_filters( 'jf2_feed_array', $items );
echo Mf2Feed::encode_json( $items );
