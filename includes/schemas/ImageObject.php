<?php
/**
 * The ImageObject Schema.
 *
 * @package Schemify
 * @link    http://schema.org/ImageObject
 */

namespace Schemify\Schemas;

class ImageObject extends MediaObject {

	/**
	 * The properties this schema may utilize.
	 *
	 * @var array $properties
	 */
	protected static $properties = array(
		'caption',
		'exifData',
		'representativeOfPage',
		'thumbnail',
	);

	/**
	 * Get the image caption.
	 *
	 * @param int $post_id The attachment ID.
	 * @return int The image caption.
	 */
	function getCaption( $post_id ) {
		return get_the_excerpt( $post_id );
	}

	/**
	 * Since there's a caption property, the description moves to post_content.
	 *
	 * @param int $post_id The attachment ID.
	 * @return int The image description.
	 */
	function getDescription( $post_id ) {
		$attachment = get_post( $post_id );

		return $attachment->post_content;
	}

	/**
	 * Retrieve the EXIF data for this image.
	 *
	 * @param int $post_id The attachment ID.
	 * @return array EXIF data stored for the attachment.
	 */
	protected function getExifData( $post_id ) {

		// Only pull this data if this is a top-level image object.
		if ( ! $this->isMain ) {
			return null;
		}

		$meta   = wp_get_attachment_metadata( $post_id );
		$values = array();

		if ( $meta ) {
			foreach ( $meta['image_meta'] as $name => $value ) {
				if ( ! $value ) {
					continue;
				}

				$values[] = array(
					'@type' => 'PropertyValue',
					'name'  => $name,
					'value' => $value,
				);
			}
		}

		return $values;
	}

	/**
	 * Get the height of an image.
	 *
	 * @param int $post_id The attachment ID.
	 * @return int The height of the image.
	 */
	function getHeight( $post_id ) {
		$image = wp_get_attachment_image_src( $post_id, 'full' );

		return $image ? $image[2] : null;
	}

	/**
	 * Retrieve the image for a post.
	 *
	 * @param int $post_id The post ID.
	 * @return ImageObject An image object representing the post.
	 */
	public function getImage( $post_id ) {
		return null;
	}

	/**
	 * Get the width of an image.
	 *
	 * @param int $post_id The attachment ID.
	 * @return int The width of the image.
	 */
	function getWidth( $post_id ) {
		$image = wp_get_attachment_image_src( $post_id, 'full' );

		return $image ? $image[1] : null;
	}
}