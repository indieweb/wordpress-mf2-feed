<?php
/**
 * MF2 Feed Template for displaying an MF2 item.
 *
 * @package MF2 Feed
 */

header( 'Content-Type: ' . feed_content_type( 'mf2' ), true );

require_once MF2_FEED_PLUGIN_DIR . 'includes/class-mf2-feed-entry.php';
$items = array();
$p     = get_post();
if ( $p ) {
	$item           = new Mf2_Feed_Entry( $p );
	$items['items'] = $item->to_mf2();
}

// filter output
$items = apply_filters( 'mf2_feed_array', $items );
echo Mf2_Feed::encode_json( $items );
