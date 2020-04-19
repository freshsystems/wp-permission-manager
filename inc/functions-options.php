<?php
/**
 * Functions for handling plugin options.
 *
 * @package    Members
 * @subpackage Includes
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @author     Fresh Systems Ltd. <info@freshsystems.co.uk>
 * @copyright  Original work Copyright (c) 2009 - 2018, Justin Tadlock
 * @copyright  Modified work Copyright (c) 2020, Fresh Systems Ltd.
 * @link       https://github.com/freshsystems/wp-permission-manager/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Conditional check to see if denied capabilities should overrule granted capabilities when
 * a user has multiple roles with conflicting cap definitions.
 *
 * @since  1.0.0
 * @access public
 * @return bool
 */
function members_explicitly_deny_caps() {

	return apply_filters( 'members_explicitly_deny_caps', members_get_setting( 'explicit_denied_caps' ) );
}

/**
 * Whether to show human-readable caps.
 *
 * @since  2.0.0
 * @access public
 * @return bool
 */
function members_show_human_caps() {

	return apply_filters( 'members_show_human_caps', members_get_setting( 'show_human_caps' ) );
}

/**
 * Conditional check to see if the role manager is enabled.
 *
 * @since  1.0.0
 * @access public
 * @return bool
 */
function members_multiple_user_roles_enabled() {

	return apply_filters( 'members_multiple_roles_enabled', members_get_setting( 'multi_roles' ) );
}


/**
 * Gets a setting from from the plugin settings in the database.
 *
 * @since  0.2.0
 * @access public
 * @return mixed
 */
function members_get_setting( $option = '' ) {

	$defaults = members_get_default_settings();

	$settings = wp_parse_args( get_option( 'members_settings', $defaults ), $defaults );

	return isset( $settings[ $option ] ) ? $settings[ $option ] : false;
}

/**
 * Returns an array of the default plugin settings.
 *
 * @since  0.2.0
 * @access public
 * @return array
 */
function members_get_default_settings() {

	return array(
		// @since 1.0.0
		'explicit_denied_caps' => true,
		'multi_roles'          => true,
		// @since 2.0.0
		'show_human_caps'      => true,
	);
}
