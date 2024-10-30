<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Move_User_Role
 * @subpackage Move_User_Role/admin
 * @author     Alsvin <alsvin.tech@gmail.com>
 */
if( !class_exists('Alsvin_Move_User_Roles_Admin') ) {
	class Alsvin_Move_User_Roles_Admin {
		/**
		 * The ID of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string $plugin_name The ID of this plugin.
		 */
		private $plugin_name;

		/**
		 * The version of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string $version The current version of this plugin.
		 */
		private $version;

		private $plugin_title;
		private $setting_menu_slug;
		private $options = array();


		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 * @param      string $plugin_name The name of this plugin.
		 * @param      string $version The version of this plugin.
		 */
		public function __construct($plugin_name, $version)
		{

			$this->plugin_name = $plugin_name;
			$this->version = $version;

			$this->plugin_title = __('Move User Roles', 'alsvin-mur');
			$this->setting_menu_slug = "{$plugin_name}-settings";
			$this->options = get_option( $this->setting_menu_slug, array() );

			add_action('admin_menu', [$this, 'admin_menu'] );
			add_action('admin_enqueue_scripts', [$this, 'enqueue_styles'] );
			add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts'] );
			//add_action('admin_init', [$this, 'add_settings_page'] );
			add_action('admin_init', [$this, 'save_settings_page'] );
			add_action( 'wp_ajax_mur_search_users', [$this, 'search_users_cb'] );
		}





		public function save_settings_page() {

			if( isset($_POST['alsvin_mur_submit']) && check_admin_referer( $this->setting_menu_slug )) {

				if ( ( !isset( $_POST['mur_user_roles']['from'] ) || empty( $_POST['mur_user_roles']['from'] ) ) || (! isset( $_POST['mur_user_roles']['to'] ) || empty( $_POST['mur_user_roles']['to'] )) ) {
					$type    = 'error';
					$message = 'Please select "Existing" and "New" user roles';

				} else {
					//print_r($_POST); exit;
					$type    = 'updated';
					$message = 'User roles moved successfully';

					$from_user_roles = isset( $_POST['mur_user_roles']['from'] ) ? (array) $_POST['mur_user_roles']['from'] : array();
					$to_user_roles   = isset( $_POST['mur_user_roles']['to'] ) ? (array) $_POST['mur_user_roles']['to'] : array();
					$selected_user_ids   = isset( $_POST['mur_select_users'] ) ? (array) $_POST['mur_select_users'] : array();

					//Added required sanitizing
					$from_user_roles = array_map('sanitize_text_field', $from_user_roles);
					$to_user_roles = array_map('sanitize_text_field', $to_user_roles);
					$selected_user_ids = array_map('absint', $selected_user_ids);

					if ( is_array( $from_user_roles ) ) {
						foreach ( $from_user_roles as $from_user_role ) {

							if ( wp_roles()->is_role( $from_user_role ) === false ) {
								continue;
							}

							$users = get_users( array(
								'role'   => sanitize_text_field( $from_user_role ),
								'fields' => 'ID',
								'include' => $selected_user_ids
							) );

							if ( ! empty( $users ) ) {
								foreach ( $users as $user_id ) {
									foreach ( $to_user_roles as $to_user_role ) {
										$this->move_user_role( $user_id, $from_user_role, $to_user_role );
									}
								}
							}
						}
					}
				}

				add_settings_error(
					$this->setting_menu_slug,
					esc_attr( 'settings_updated' ),
					$message,
					$type
				);
			}
		}

		private function move_user_role($user_id, $from_role, $to_role) {
			$user = new WP_User( $user_id );
			$user->remove_role($from_role);
			if( !in_array( $to_role, (array) $user->roles) ) {
				$user->add_role( $to_role );
			}
		}

		/**
		 * Register the stylesheets for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_styles()
		{

			/**
			 * This function is provided for demonstration purposes only.
			 *
			 * An instance of this class should be passed to the run() function
			 * defined in Move_User_Role_Loader as all of the hooks are defined
			 * in that particular class.
			 *
			 * The Move_User_Role_Loader will then create the relationship
			 * between the defined hooks and the functions defined in this
			 * class.
			 */

			wp_enqueue_style($this->plugin_name . '-jquery-multi-select', plugin_dir_url(__FILE__) . 'css/jquery-multi-select.css', array(), $this->version, 'all');
			wp_enqueue_style($this->plugin_name . '-select2', 'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css', array(), $this->version, 'all');
			wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/move-user-role-admin.css', array(), $this->version, 'all');

		}

		/**
		 * Register the JavaScript for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_scripts()
		{

			/**
			 * This function is provided for demonstration purposes only.
			 *
			 * An instance of this class should be passed to the run() function
			 * defined in Move_User_Role_Loader as all of the hooks are defined
			 * in that particular class.
			 *
			 * The Move_User_Role_Loader will then create the relationship
			 * between the defined hooks and the functions defined in this
			 * class.
			 */

			wp_enqueue_script($this->plugin_name . '-jquery-multi-select', plugin_dir_url(__FILE__) . 'js/jquery-multi-select.js', array('jquery'), $this->version, true);
			wp_enqueue_script($this->plugin_name . '-select2', 'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js', array('jquery'), $this->version, true);
			wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/move-user-role-admin.js', array('jquery'), $this->version, true);

		}

		public function admin_menu()
		{
			add_submenu_page(
				'tools.php',
				$this->plugin_title,
				$this->plugin_title,
				'manage_options',
				$this->setting_menu_slug,
				[$this, 'settings']
			);
		}


		public function settings() {
			?>
			<!-- Create a header in the default WordPress 'wrap' container -->
			<div class="wrap">
				<h2><?php _e('Move User Roles', 'alsvin-mur'); ?></h2>
				<?php settings_errors($this->setting_menu_slug); ?>

				<form method="post" action="">
					<?php
					$this->add_settings_field();
					submit_button(sprintf('%s %s', __('Move'), __('Users')),'primary','alsvin_mur_submit');
					wp_nonce_field( $this->setting_menu_slug );
					?>
				</form>

			</div><!-- /.wrap -->
			<?php
		}

		private function get_user_roles() {
			global $wp_roles;

			$user_roles = [];
			foreach ($wp_roles->roles as $role => $role_details) {
				$user_roles[$role] = $role_details['name'];
			}

			return $user_roles;
		}

		public function add_settings_field() {
            $field_name = 'mur_user_roles';
			$existing_roles = $this->get_user_roles();

			?>
            <p>&nbsp;</p>
            <p class="description"><strong><?php _e('Example'); ?>:</strong> <?php _e('To move all users with "Subscriber" role to "Editor" role', 'alsvin-mur'); ?> </p>
            <p class="description">1. <?php _e('Select <strong>Subscriber</strong> role in <strong>Existing Roles</strong> ','alsvin-mur'); ?> </p>
            <p class="description">2. <?php _e('Select <strong>Editor</strong> role in <strong>New Roles to Assign</strong>', 'alsvin-mur'); ?> </p>
            <p class="description">3. <?php _e('Select <strong>Specific Users</strong> in <strong>Move selected user(s) only</strong> <em>(optional)</em>', 'alsvin-mur'); ?> </p>
            <p>&nbsp;</p>

			<table class="table mur-table" cellspacing="0">
				<tr>
					<td class="mur-header"><?php _e('Existing Roles', 'alsvin-mur'); ?></td>
					<td class="mur-header"><?php _e('New Roles to Assign', 'alsvin-mur'); ?></td>
				</tr>
				<tr>
					<td>
						<select class="mur-multi-select" multiple="multiple" name="<?php echo $field_name; ?>[from][]">
							<?php //echo sprintf('<option value="%s" %s>%s</option>', 'no-role', selected('no-role', $field_value, false), __('No Role', 'alsvin-mur')); ?>
							<?php foreach ($existing_roles as $existing_role => $existing_role_name): ?>
								<?php echo sprintf('<option value="%s">%s</option>', $existing_role, $existing_role_name); ?>
							<?php endforeach;?>
						</select>
					</td>
					<td>
						<select class="mur-multi-select" multiple="multiple" name="<?php echo $field_name; ?>[to][]">
							<?php //echo sprintf('<option value="%s" %s>%s</option>', 'no-role', selected('no-role', $field_value, false), __('No Role')); ?>
							<?php foreach ($existing_roles as $existing_role => $existing_role_name): ?>
								<?php echo sprintf('<option value="%s">%s</option>', $existing_role, $existing_role_name); ?>
							<?php endforeach;?>
						</select>
					</td>
				</tr>
			</table>
            <table class="form-table">
                <tr>
                    <th><label for="mur_users_select"><?php _e( 'Move selected user(s) only:', 'ldqie' ); ?></label></th>
                    <td>
                        <select class="alsvin-mur-select2" id="mur_select_users" name="mur_select_users[]" multiple="multiple" data-placeholder="<?php _e('Type username to search', 'alsvin-mur'); ?>" >
                        </select>
                        <p class="description"><?php _e('Search and select users here if you want to change roles of some specific users only. Keep it blank to change role of all users found in selected existing roles above.', 'alsvin-mur'); ?></p>
                    </td>
                </tr>
            </table>

			<?php
		}

		public function search_users_cb() {

			if( !wp_doing_ajax() ) {
				return;
			}

			/*if( !check_ajax_referer('ld-cms-nonce', 'security')) {
				return;
			}*/

			$per_page_count = 10;

			$page = isset($_GET['page']) ? absint($_GET['page']) : 1;

			if( $page==1 ){
				$offset = 0;
			} else {
				$offset = ($page-1) * $per_page_count;
			}

			$search_term = sanitize_text_field($_GET['search']);

			// WP_User_Query arguments
			$args = array(
				'order' => 'ASC',
				'orderby' => 'display_name',
				'search' => '*' . esc_attr($search_term) . '*',
				'number' => $per_page_count,
				'offset' => $offset
			);

			$current_user_id = get_current_user_id();

			$args['exclude'] = [$current_user_id];

			$user_query = new WP_User_Query( $args );

			$users = [];
			if ( $user_query instanceof WP_User_Query ) {
				$users = $user_query->get_results();
			}

			$total_count = $user_query->total_users;
			$data = array();

			// Check for results
			if (!empty($users)) {
				// loop through each user
				foreach ($users as $user) {
                    $data[] = array('id' => $user->ID, 'text' => $user->user_login);
				}
			}

			if(empty($data)) {
				$total_count = 0;
			}

			$response = array('data' => $data, 'total_count' => $total_count);

			wp_send_json( $response );
		}
	}
}