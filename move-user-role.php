<?php
/**
 *
 * @link              https://www.alsvin-tech.com/
 * @since             1.0.0
 * @package           Alsvin_Move_User_Role_Admin
 *
 * @wordpress-plugin
 * Plugin Name:       Move User Roles
 * Plugin URI:        https://www.alsvin-tech.com/
 * Description:       Move site users from one user role to another user role in just two steps.
 * Version:           1.1.3
 * Requires at least: 5.1
 * Requires PHP:      5.6
 * Author:            Alsvin
 * Author URI:        https://profiles.wordpress.org/alsvin/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       alsvin-mur
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MOVE_USER_ROLES_VERSION', '1.1.3' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-move-user-role-activator.php
 */
function activate_move_user_roles() {

}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-move-user-role-deactivator.php
 */
function deactivate_move_user_roles() {

}

register_activation_hook( __FILE__, 'activate_move_user_roles' );
register_deactivation_hook( __FILE__, 'deactivate_move_user_roles' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'class-move-user-roles-admin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_move_user_role() {

	new Alsvin_Move_User_Roles_Admin('move-user-role', MOVE_USER_ROLES_VERSION);

}
run_move_user_role();