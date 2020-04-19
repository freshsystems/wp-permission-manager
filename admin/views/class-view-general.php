<?php
/**
 * Handles the general settings view.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @author     Fresh Systems Ltd. <info@freshsystems.co.uk>
 * @copyright  Original work Copyright (c) 2009 - 2018, Justin Tadlock
 * @copyright  Modified work Copyright (c) 2020, Fresh Systems Ltd.
 * @link       https://github.com/freshsystems/wp-permission-manager/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Members\Admin;

/**
 * Sets up and handles the general settings view.
 *
 * @since  2.0.0
 * @access public
 */
class View_General extends View {

	/**
	 * Holds an array the plugin settings.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    array
	 */
	public $settings = array();

	/**
	 * Enqueue scripts/styles.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue() {

		wp_enqueue_script( 'members-settings' );
	}

	/**
	 * Registers the plugin settings.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	function register_settings() {

		// Get the current plugin settings w/o the defaults.
		$this->settings = get_option( 'members_settings' );

		// Register the setting.
		register_setting( 'members_settings', 'members_settings', array( $this, 'validate_settings' ) );

		/* === Settings Sections === */

		// Add settings sections.
		add_settings_section( 'roles_caps',          esc_html__( 'Roles and Capabilities', 'members' ), array( $this, 'section_roles_caps' ), 'members-settings' );

		/* === Settings Fields === */

		// Role manager fields.
		add_settings_field( 'enable_multi_roles',   esc_html__( 'Multiple User Roles', 'members' ), array( $this, 'field_enable_multi_roles'   ), 'members-settings', 'roles_caps' );
		add_settings_field( 'explicit_denied_caps', esc_html__( 'Capabilities',        'members' ), array( $this, 'field_explicit_denied_caps' ), 'members-settings', 'roles_caps' );

	}

	/**
	 * Validates the plugin settings.
	 *
	 * @since  2.0.0
	 * @access public
	 * @param  array  $input
	 * @return array
	 */
	function validate_settings( $settings ) {

		// Validate true/false checkboxes.
		$settings['explicit_denied_caps'] = ! empty( $settings['explicit_denied_caps'] ) ? true : false;
		$settings['show_human_caps']      = ! empty( $settings['show_human_caps'] )      ? true : false;
		$settings['multi_roles']          = ! empty( $settings['multi_roles'] )          ? true : false;

		// Return the validated/sanitized settings.
		return $settings;
	}

	/**
	 * Role/Caps section callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function section_roles_caps() { ?>

		<p class="description">
			<?php esc_html_e( 'Your roles and capabilities will not revert back to their previous settings after deactivating or uninstalling this plugin, so use this feature wisely.', 'members' ); ?>
		</p>
	<?php }


	/**
	 * Explicit denied caps field callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function field_explicit_denied_caps() { ?>

		<fieldset>

			<p>
				<label>
					<input type="checkbox" name="members_settings[explicit_denied_caps]" value="true" <?php checked( members_explicitly_deny_caps() ); ?> />
					<?php esc_html_e( 'Denied capabilities should always overrule granted capabilities.', 'members' ); ?>
				</label>
			</p>

			<p>
				<label>
					<input type="checkbox" name="members_settings[show_human_caps]" value="true" <?php checked( members_show_human_caps() ); ?> />
					<?php esc_html_e( 'Show human-readable capabilities when possible.', 'members' ); ?>
				</label>
			</p>

		</fieldset>
	<?php }

	/**
	 * Multiple roles field callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function field_enable_multi_roles() { ?>

		<label>
			<input type="checkbox" name="members_settings[multi_roles]" value="true" <?php checked( members_multiple_user_roles_enabled() ); ?> />
			<?php esc_html_e( 'Allow users to be assigned more than a single role.', 'members' ); ?>
		</label>
	<?php }


	/**
	 * Renders the settings page.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function template() { ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'members_settings' ); ?>
			<?php do_settings_sections( 'members-settings' ); ?>
			<?php submit_button( esc_attr__( 'Update Settings', 'members' ), 'primary' ); ?>
		</form>

	<?php }

	/**
	 * Adds help tabs.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function add_help_tabs() {

		// Get the current screen.
		$screen = get_current_screen();

		// Roles/Caps help tab.
		$screen->add_help_tab(
			array(
				'id'       => 'roles-caps',
				'title'    => esc_html__( 'Role and Capabilities', 'members' ),
				'callback' => array( $this, 'help_tab_roles_caps' )
			)
		);

		// Set the help sidebar.
		$screen->set_help_sidebar( members_get_help_sidebar_text() );
	}

	/**
	 * Displays the roles/caps help tab.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function help_tab_roles_caps() { ?>

		<p>
			<?php esc_html_e( 'The role manager allows you to manage roles on your site by giving you the ability to create, edit, and delete any role. Note that changes to roles do not change settings for the Members plugin. You are literally changing data in your WordPress database. This plugin feature merely provides an interface for you to make these changes.', 'members' ); ?>
		</p>

		<p>
			<?php esc_html_e( 'The multiple user roles feature allows you to assign more than one role to each user from the edit user screen.', 'members' ); ?>
		</p>

		<p>
			<?php esc_html_e( 'Tick the checkbox for denied capabilities to always take precedence over granted capabilities when there is a conflict. This is only relevant when using multiple roles per user.', 'members' ); ?>
		</p>

		<p>
			<?php esc_html_e( 'Tick the checkbox to show human-readable capabilities when possible. Note that custom capabilities and capabilities from third-party plugins will show the machine-readable capability name unless they are registered.', 'members' ); ?>
		</p>
	<?php }

}
