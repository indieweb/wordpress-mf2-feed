<?php
/**
 * MF2 Feed Template for displaying an MF2 item.
 *
 * @package MF2 Feed
 */

header( 'Content-Type: ' . feed_content_type( 'mf2' ), true );

require_once dirname( __FILE__ ) . '/class-mf2-feed-entry.php';
$items          = array();
$item           = new Mf2_Feed_Entry( get_the_ID() );
$item           = $item->to_mf2();
$items          = array();
$items['items'] = $item;

// filter output
$items = apply_filters( 'mf2_feed_array', $items );
echo Mf2Feed::encode_json( $items );
