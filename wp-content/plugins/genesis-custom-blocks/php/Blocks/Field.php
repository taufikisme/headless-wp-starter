<?php
/**
 * Block Field.
 *
 * @package   Genesis\CustomBlocks
 * @copyright Copyright(c) 2021, Genesis Custom Blocks
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Genesis\CustomBlocks\Blocks;

/**
 * Class Field
 */
class Field {

	/**
	 * Field name (slug).
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Field label.
	 *
	 * @var string
	 */
	public $label = '';

	/**
	 * Field control type.
	 *
	 * @var string
	 */
	public $control = 'text';

	/**
	 * Field variable type.
	 *
	 * @var string
	 */
	public $type = 'string';

	/**
	 * Field order.
	 *
	 * @var int
	 */
	public $order = 0;

	/**
	 * Field settings.
	 *
	 * @var array
	 */
	public $settings = [];

	/**
	 * Field constructor.
	 *
	 * @param array $config An associative array with keys corresponding to the Field's properties.
	 */
	public function __construct( $config = [] ) {
		$this->from_array( $config );
	}

	/**
	 * Get field properties as an array, ready to be stored as JSON.
	 *
	 * @return array
	 */
	public function to_array() {
		$config = [
			'name'    => $this->name,
			'label'   => $this->label,
			'control' => $this->control,
			'type'    => $this->type,
			'order'   => $this->order,
		];

		$config = array_merge(
			$config,
			$this->settings
		);

		/**
		 * The field properties, converted to a config array.
		 *
		 * @param array $config   The field config.
		 * @param array $settings The field settings.
		 */
		return apply_filters( 'genesis_custom_blocks_field_to_array', $config, $this->settings );
	}

	/**
	 * Set field properties from an array, after being stored as JSON.
	 *
	 * @param array $config An array containing field parameters.
	 */
	public function from_array( $config ) {
		$properties = [ 'name', 'label', 'control', 'type', 'order', 'settings' ];
		foreach ( $properties as $property ) {
			if ( isset( $config[ $property ] ) ) {
				$this->$property = $config[ $property ];
			}
		}

		if ( ! isset( $config['type'] ) && isset( $config['control'] ) ) {
			$control = genesis_custom_blocks()->block_post->get_control( $config['control'] );
			if ( $control ) {
				$this->type = $control->type;
			}
		}

		// Add any other non-default keys to the settings array.
		$field_settings = array_diff( array_keys( $config ), $properties );

		foreach ( $field_settings as $settings_key ) {
			$this->settings[ $settings_key ] = $config[ $settings_key ];
		}

		/**
		 * The field settings, parsed from an array.
		 *
		 * @param array $settings The field settings.
		 */
		$this->settings = apply_filters( 'genesis_custom_blocks_settings_from_array', $this->settings );
	}

	/**
	 * Return the value with the correct variable type.
	 *
	 * @param mixed $value The value to typecast.
	 * @return mixed
	 */
	public function cast_value( $value ) {
		switch ( $this->type ) {
			case 'string':
				$value = strval( $value );
				break;
			case 'boolean':
				if ( 1 === $value ) {
					$value = true;
				}
				break;
			case 'integer':
				$value = intval( $value );
				break;
			case 'array':
				if ( ! $value ) {
					$value = [];
				} else {
					$value = (array) $value;
				}
				break;
		}

		if ( 'textarea' === $this->control ) {
			$value = strval( $value );
			if ( isset( $this->settings['new_lines'] ) ) {
				if ( 'autop' === $this->settings['new_lines'] ) {
					$value = wpautop( $value );
				}
				if ( 'autobr' === $this->settings['new_lines'] ) {
					$value = nl2br( $value );
				}
			}
		}

		return $value;
	}

	/**
	 * Gets the field value as a string.
	 *
	 * @param mixed $value The field value.
	 *
	 * @return string $value The value to echo.
	 */
	public function cast_value_to_string( $value ) {
		if ( is_array( $value ) ) {
			return implode( ', ', $value );
		}

		if ( true === $value ) {
			return __( 'Yes', 'genesis-custom-blocks' );
		}

		if ( false === $value ) {
			return __( 'No', 'genesis-custom-blocks' );
		}

		return strval( $value );
	}
}
