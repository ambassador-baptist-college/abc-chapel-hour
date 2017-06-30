<?php
/**
Template Name: Podcast RSS
**/

// Query the Podcast Custom Post Type and fetch the latest 100 posts
$args = array(
    'post_type'         => 'chapel_hour',
    'posts_per_page'    => 100,
);
$loop = new WP_Query( $args );

/**
 * Get the current URL taking into account HTTPS and Port
 * @link http://css-tricks.com/snippets/php/get-current-page-url/
 * @version Refactored by @AlexParraSilva
 */
function getCurrentUrl() {
    $url  = isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http';
    $url .= '://' . $_SERVER['SERVER_NAME'];
    $url .= in_array( $_SERVER['SERVER_PORT'], array( '80', '443' ) ) ? '' : ':' . $_SERVER['SERVER_PORT'];
    $url .= $_SERVER['REQUEST_URI'];
    return $url;
}

// Output the XML header
header( 'Content-Type: ' . feed_content_type( 'rss-http' ) . '; charset=' . get_option( 'blog_charset' ), true );
echo '<?xml version="1.0" encoding="' . get_option( 'blog_charset' ) . '"?' . '>';
?>

<?php // Start the iTunes RSS Feed: https://www.apple.com/itunes/podcasts/specs.html ?>
<rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" version="2.0">
  <channel>
    <title>ABC Chapel Hour Broadcast</title>
    <link><?php echo get_bloginfo( 'url' ); ?></link>
    <language><?php echo get_bloginfo ( 'language' ); ?></language>
    <copyright><?php echo date( 'Y' ) . ' ' . get_bloginfo( 'name' ); ?></copyright>

    <itunes:author><?php echo get_bloginfo( 'name' ); ?></itunes:author>
    <itunes:summary><?php the_field( 'chapel_hour_podcast_summary', 'option' ); ?></itunes:summary>
    <description><?php the_field( 'chapel_hour_podcast_description', 'option' ); ?></description>

    <itunes:owner>
      <itunes:name><?php the_field( 'chapel_hour_podcast_owner_name', 'option' ); ?></itunes:name>
      <itunes:email><?php the_field( 'chapel_hour_podcast_owner_email', 'option' ); ?></itunes:email>
    </itunes:owner>

    <itunes:image href="<?php the_field( 'chapel_hour_podcast_artwork', 'option' ); ?>" />

    <itunes:category text="<?php echo str_replace( '&', '&amp;', get_field( 'chapel_hour_podcast_category', 'option' ) ); ?>">
      <itunes:category text="<?php the_field( 'chapel_hour_podcast_subcategory', 'option' ); ?>"/>
    </itunes:category>
    <itunes:explicit>no</itunes:explicit>

    <?php // Start the loop for Podcast posts
    while ( $loop->have_posts() ) : $loop->the_post(); ?>
    <item>
      <title><?php the_title_rss(); ?></title>
      <itunes:author><?php echo get_bloginfo( 'name' ); ?></itunes:author>
      <itunes:summary><?php echo get_the_excerpt(); ?></itunes:summary>
      <?php // Retrieve just the URL of the Featured Image: http://codex.wordpress.org/Function_Reference/wp_get_attachment_image_src
      if (has_post_thumbnail( $post->ID ) ): ?>
        <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ); ?>
        <itunes:image href="<?php echo $image[0]; ?>" />
      <?php endif; ?>

      <?php // Get the file field URL, file_size and date format
        $file_url = get_field( 'media_file' );
        $file_size = get_field( 'media_file_size' );
        $date_format_string = _x( 'D, d M Y H:i:s O', 'Date formating for iTunes feed.' );
      ?>

      <enclosure url="<?php echo $file_url; ?>" length="<?php echo $file_size; ?>" type="audio/mpeg" />
      <guid><?php echo $file_url; ?></guid>
      <pubDate><?php echo get_post_time( $date_format_string ); ?></pubDate>
      <itunes:duration><?php the_field( 'media_duration' ); ?></itunes:duration>
    </item>
    <?php endwhile; ?>

  </channel>

</rss>
