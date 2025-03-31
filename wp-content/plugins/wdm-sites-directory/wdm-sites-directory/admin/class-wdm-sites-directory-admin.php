<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://https://www.wisdmlabs.com
 * @since      1.0.0
 *
 * @package    Wdm_Sites_Directory
 * @subpackage Wdm_Sites_Directory/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wdm_Sites_Directory
 * @subpackage Wdm_Sites_Directory/admin
 * @author     WisdmLabs <info@wisdmlabs.com>
 */
class Wdm_Sites_Directory_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->init_hooks();
	}

	/**
	 * Initialize the hooks for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function init_hooks()
	{
		// Hook into the admin_menu action hook
		add_action('admin_menu', array($this, 'add_tracking_time_settings'));
		add_action('admin_menu', array($this, 'add_team_settings_submenu'));

		// Hook into the wp_ajax_nopriv_save_token and wp_ajax_save_token action hooks
		// add_action('wp_ajax_nopriv_save_token', array($this, 'wdm_save_token'));
		add_action('wp_ajax_save_token', array($this, 'wdm_save_token'));
		// add_action('wp_ajax_nopriv_save_git_token', array($this, 'wdm_save_git_token'));
		add_action('wp_ajax_save_git_token', array($this, 'wdm_save_git_token'));
		add_action('wp_ajax_save_sonar_token', array($this, 'wdm_save_sonar_token'));
		add_action('wp_ajax_validate_sonar_token', array($this, 'wdm_validate_sonar_token'));

		// Hook into the wp_ajax_save_team_data action hook
        add_action('wp_ajax_save_team_data', array($this, 'wdm_save_team_data'));

		add_action('wp_ajax_save_spinup_token', array($this, 'wdm_save_spinup_token'));
		add_action('wp_ajax_validate_spinup_token', array($this, 'validate_spinup_token'));


	}

	public function wdm_save_spinup_token() {
		$data = wp_unslash($_POST); // WordPress may add slashes to POST data, so remove them
	
		// Check if the nonce is set and valid. The nonce is a security feature to prevent CSRF attacks.
		if (isset($data['nonce']) && wp_verify_nonce($data['nonce'], 'tracking_time_api_settings')) {
			update_option('spinup_api_token', $data['token']);
		}
	}
	

	public function add_team_settings_submenu()
    {
        // Add a submenu page under the top-level menu
        add_submenu_page(
            'tracking-time-settings', // Parent slug
            'Team Settings',          // Page title
            'Team Settings',          // Menu title
            'manage_options',         // Capability
            'team-settings',          // Menu slug
            array($this, 'team_settings_page') // Callback function
        );
    }

    public function team_settings_page()
{
    ?>
    <div class="wdm-team-container">
        <h2 class="wdm-team-heading">Team Settings</h2>
        <table id="wdm-team-table">
            <thead>
                <tr>
                    <th>Team Name</th>
                    <th>Team ID</th>
                    <th>Team Leader</th>
                    <th>Team Lead Name</th> <!-- New column header -->
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Team rows will be dynamically added here -->
            </tbody>
        </table>
        <button id="wdm-add-team-button">Add Team</button>
    </div>
    <?php
}
public function validate_spinup_token() {
    $data = wp_unslash($_POST); // WordPress may add slashes to POST data, so remove them

    // Check if the nonce is set and valid. The nonce is a security feature to prevent CSRF attacks.
    if (isset($data['nonce']) && wp_verify_nonce($data['nonce'], 'tracking_time_api_settings')) {
        $token = $data['token'];
        $url = "https://api.spinupwp.app/v1/sites?page=1";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $token,
            "Content-Type: application/json"
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 200) {
            wp_send_json_success(true);
        } else {
            wp_send_json_error('Invalid token');
        }
    } else {
        wp_send_json_error('Invalid nonce');
    }
}


	public function wdm_save_team_data()
	{
		$data = wp_unslash($_POST); // WordPress may add slashes to POST data, so remove them

		// Check if the nonce is set and valid. The nonce is a security feature to prevent CSRF attacks.
		if (isset($data['nonce']) && wp_verify_nonce($data['nonce'], 'tracking_time_api_settings')) {
			$teams = isset($data['teams']) ? $data['teams'] : array();
			update_option('wdm_team_settings', $teams);
			wp_send_json_success(true);
		} else {
			wp_send_json_error('Invalid nonce');
		}
	}


	public function wdm_validate_sonar_token(){
		$data = wp_unslash($_POST); // WordPress may add slashes to POST data, so remove them

		// Check if the nonce is set and valid. The nonce is a security feature to prevent CSRF attacks.
		if (isset($data['nonce']) && wp_verify_nonce($data['nonce'], 'tracking_time_api_settings')) {
			$url = "http://codequality.wisdmlabs.net:9000/api/authentication/validate";
	
			$ch = curl_init($url);
			
			$token = $data['token'];
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				"Authorization: Basic " . $token,
				"Content-Type: application/json"
			]);
		
			$response = curl_exec($ch);
			$response_data = json_decode( $response, true );
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			error_log(print_r($response_data, true));
			if ($httpCode == 200 && $response_data['valid']) {
				error_log('success');
				wp_send_json_success(true);
			} else {
				error_log('success');

				wp_send_json_success(false);
			}
		}
	}

	public function wdm_save_git_token(){
		$data = wp_unslash($_POST); // WordPress may add slashes to POST data, so remove them

		// Check if the nonce is set and valid. The nonce is a security feature to prevent CSRF attacks.
		if (isset($data['nonce']) && wp_verify_nonce($data['nonce'], 'tracking_time_api_settings')) {
			update_option('github_api_token', $data['token']);
		}
	}

	public function wdm_save_sonar_token(){
		$data = wp_unslash($_POST); // WordPress may add slashes to POST data, so remove them
		error_log('inside sonar');
		// Check if the nonce is set and valid. The nonce is a security feature to prevent CSRF attacks.
		if (isset($data['nonce']) && wp_verify_nonce($data['nonce'], 'tracking_time_api_settings')) {
			update_option('sonar_api_token', $data['token']);
		}
	}

	/**
	 * Save the token from the AJAX request.
	 *
	 * @since    1.0.0
	 */
	public function wdm_save_token()
	{
		$data = wp_unslash($_POST); // WordPress may add slashes to POST data, so remove them

		// Check if the nonce is set and valid. The nonce is a security feature to prevent CSRF attacks.
		if (isset($data['nonce']) && wp_verify_nonce($data['nonce'], 'tracking_time_api_settings')) {
			update_option('tracking_time_api_token', $data['token']);
		}
	}

	/**
	 * Add a top-level menu page for Tracking Time Settings.
	 *
	 * @since    1.0.0
	 */
	public function add_tracking_time_settings()
	{
		// Add a top-level menu page
		add_menu_page(
			'WDM Settings', // Page title
			'WDM API Settings',    // Menu title
			'manage_options', // Capability
			'tracking-time-settings', // Menu slug
			array($this, 'tracking_time_settings'), // Callback function
			'dashicons-admin-generic', // Icon URL
			6                  // Position
		);
	}

	
	/**
	 * Callback function to display the content of the custom menu page.
	 *
	 * @since    1.0.0
	 */
	public function tracking_time_settings()
	{
		?>
		
		<div class="wdm-tt-container">
			<h2 class="wdm-tt-heading">API Settings</h2>
			<label for="wdm-tt-bearer-token" class="wdm-tt-label">Tracking Time Token:</label>
			<input type="text" id="wdm-tt-bearer-token" class="wdm-tt-input" placeholder="Enter your bearer token">
			<label for="wdm-git-bearer-token" class="wdm-git-label">Github Token:</label>
			<input type="text" id="wdm-git-bearer-token" class="wdm-git-input" placeholder="Enter your bearer token">
			<label for="wdm-sonar-bearer-token" class="wdm-sonar-label">Sonar Token:</label>
			<input type="text" id="wdm-sonar-bearer-token" class="wdm-sonar-input" placeholder="Enter your bearer token">
			<label for="wdm-spinup-bearer-token" class="wdm-spinup-label">Spinup Token:</label>
			<input type="text" id="wdm-spinup-bearer-token" class="wdm-spinup-input" placeholder="Enter your bearer token">
			<button class="wdm-tt-button">Save Token</button>
		</div>
		<?php
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
		 * defined in Wdm_Sites_Directory_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wdm_Sites_Directory_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wdm-sites-directory-admin.css', array(), $this->version, 'all');
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
		 * defined in Wdm_Sites_Directory_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wdm_Sites_Directory_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script('wdm-swal-js', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', array('jquery'), null, true);

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wdm-sites-directory-admin.js', array('jquery'), $this->version, false);
		$js_arr = array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => wp_create_nonce('tracking_time_api_settings'),
			'ttToken'    => get_option('tracking_time_api_token') ? get_option('tracking_time_api_token') : '',
			'gitToken'    => get_option('github_api_token') ? get_option('github_api_token') : '',
			'sonarToken'  => get_option('sonar_api_token') ? get_option('sonar_api_token') : '',
			'spinupToken' => get_option('spinup_api_token') ? get_option('spinup_api_token') : ''
		);
		
		wp_localize_script($this->plugin_name, 'tracking_time_settings', $js_arr);

		wp_enqueue_script('wdm-team-mapping-js', plugin_dir_url(__FILE__) . 'js/wdm-team-mapping.js', array('jquery'), $this->version, false);
		
		$storedTeams = get_option('wdm_team_settings', []);
		
		$js_arr_2 = array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce'    => wp_create_nonce('tracking_time_api_settings'),
			'storedTeams' => $storedTeams, // Pass the stored teams to JavaScript
			
		);
		wp_localize_script('wdm-team-mapping-js', 'team_settings', $js_arr_2);


	}
}
