<?php
/**
 * Block.
 *
 * @package   Genesis\CustomBlocks
 * @copyright Copyright(c) 2021, Genesis Custom Blocks
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Genesis\CustomBlocks\Blocks;

/**
 * Class Block
 */
class Block {

	/**
	 * Block name (slug).
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Block title.
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * Exclude the block in these post types.
	 *
	 * @var array
	 */
	public $excluded = [];

	/**
	 * Icon.
	 *
	 * @var string
	 */
	public $icon = '';

	/**
	 * Category. An array containing the keys slug, title, and icon.
	 *
	 * @var array
	 */
	public $category = [
		'slug'  => '',
		'title' => '',
		'icon'  => '',
	];

	/**
	 * Block keywords.
	 *
	 * @var string[]
	 */
	public $keywords = [];

	/**
	 * Block fields.
	 *
	 * @var Field[]
	 */
	public $fields = [];

	/**
	 * Whether to display the fields in a modal.
	 *
	 * @var bool
	 */
	public $display_modal = false;

	/**
	 * Template editor CSS.
	 *
	 * @var string
	 */
	public $template_css = '';

	/**
	 * Template editor markup.
	 *
	 * @var string
	 */
	public $template_markup = '';

	/**
	 * Block constructor.
	 *
	 * @param int|bool $post_id Post ID.
	 *
	 * @return void
	 */
	public function __construct( $post_id = false ) {
		if ( ! $post_id ) {
			return;
		}

		$post = get_post( $post_id );

		if ( ! $post instanceof \WP_Post ) {
			return;
		}

		$this->name = $post->post_name;
		$this->from_json( $post->post_content );
	}

	/**
	 * Construct the Block from a JSON blob.
	 *
	 * @param string $json JSON blob.
	 *
	 * @return void
	 */
	public function from_json( $json ) {
		$json = json_decode( $json, true );

		if ( ! isset( $json[ 'genesis-custom-blocks/' . $this->name ] ) ) {
			return;
		}

		$config = $json[ 'genesis-custom-blocks/' . $this->name ];

		$this->from_array( $config );
	}

	/**
	 * Construct the Block from a config array.
	 *
	 * @param array $config An array containing field parameters.
	 *
	 * @return void
	 */
	public function from_array( $config ) {
		$properties = [ 'name', 'title', 'excluded', 'icon' ];
		foreach ( $properties as $property ) {
			if ( isset( $config[ $property ] ) ) {
				$this->$property = $config [ $property ];
			}
		}

		if ( isset( $config['category'] ) ) {
			$this->category = $config['category'];
			if ( ! is_array( $this->category ) ) {
				$this->category = $this->get_category_array_from_slug( $this->category );
			}
		}

		if ( isset( $config['keywords'] ) ) {
			$this->keywords = $config['keywords'];
		}

		if ( isset( $config['displayModal'] ) ) {
			$this->display_modal = $config['displayModal'];
		}

		if ( isset( $config['templateCss'] ) ) {
			$this->template_css = $config['templateCss'];
		}

		if ( isset( $config['templateMarkup'] ) ) {
			$this->template_markup = $config['templateMarkup'];
		}

		if ( isset( $config['fields'] ) ) {
			foreach ( $config['fields'] as $key => $field ) {
				$this->fields[ $key ] = new Field( $field );
			}
		}
	}

	/**
	 * Get the Block as a JSON blob.
	 *
	 * @return string
	 */
	public function to_json() {
		$config['name']           = $this->name;
		$config['title']          = $this->title;
		$config['excluded']       = $this->excluded;
		$config['icon']           = $this->icon;
		$config['category']       = $this->category;
		$config['keywords']       = $this->keywords;
		$config['displayModal']   = $this->display_modal;
		$config['templateCss']    = $this->template_css;
		$config['templateMarkup'] = $this->template_markup;

		$config['fields'] = [];
		foreach ( $this->fields as $key => $field ) {
			$config['fields'][ $key ] = $field->to_array();
		}

		return wp_json_encode( [ 'genesis-custom-blocks/' . $this->name => $config ], JSON_UNESCAPED_UNICODE );
	}

	/**
	 * This is a backwards compatibility fix.
	 *
	 * Block categories used to be saved as strings, but were always included in
	 * the default list of categories, so we can find them.
	 *
	 * It's not possible to use get_block_categories() here, as Block's are
	 * sometimes instantiated before that function is available.
	 *
	 * @param string $slug The category slug to find.
	 *
	 * @return array
	 */
	public function get_category_array_from_slug( $slug ) {
		return [
			'slug'  => $slug,
			'title' => ucwords( $slug, '-' ),
			'icon'  => null,
		];
	}
}
