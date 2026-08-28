<?php
/**
 * MF2 Feed Template for displaying an JF2 item.
 *
 * @package MF2 Feed
 */

defined( 'ABSPATH' ) || exit;

header( 'Content-Type: ' . feed_content_type( 'jf2' ), true );

$items = array();
$p     = get_post();
if ( $p ) {
	$item  = new Mf2_Feed_Entry( $p );
	$items = $item->to_jf2();
}

// filter output
$items = apply_filters( 'jf2_feed_array', $items );
echo Mf2_Feed::encode_json( $items, 'jf2' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON output.
