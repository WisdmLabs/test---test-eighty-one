<?php
// require_once ABSPATH . 'vendor/autoload.php';
// use phpseclib3\Crypt\RSA;
// use phpseclib3\Crypt\PublicKeyLoader;
/**
 * The public-facing functionality of the plugin.
 *
 * @link  https://https://www.wisdmlabs.com
 * @since 1.0.0
 *
 * @package    Wdm_Sites_Directory
 * @subpackage Wdm_Sites_Directory/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wdm_Sites_Directory
 * @subpackage Wdm_Sites_Directory/public
 * @author     WisdmLabs <info@wisdmlabs.com>
 */
class Wdm_Sites_Directory_Public
{
    /**
     * The ID of this plugin.
     *
     * @since  1.0.0
     * @access private
     * @var    string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since  1.0.0
     * @access private
     * @var    string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since 1.0.0
     * @param string $plugin_name The name of the plugin.
     * @param string $version     The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version     = $version;
        $this->init_hooks();
        date_default_timezone_set('Asia/Kolkata');
    }

    /**
     * Initializes the hooks for the plugin.
     *
     * This function registers two shortcodes:
     * 1. 'wdm_site_details_form': Renders the site details form.
     * 2. 'wdm_datatable': Displays a DataTable with dynamic data.
     *
     * The shortcodes map to the methods `wdm_site_details_form` and `wdm_display_datatable` respectively.
     *
     * Usage:
     * - [wdm_site_details_form]: Will output the form for submitting site details.
     * - [wdm_datatable]: Will render the DataTable in the desired location.
     *
     * This method should be called during the initialization of the plugin.
     *
     * @return void
     */
    public function init_hooks()
    {

        // Register the 'wdm_site_details_form' shortcode.
        add_shortcode('wdm_site_details_form', array( $this, 'wdm_site_details_form' ));

        // Register the 'wdm_datatable' shortcode.
        add_shortcode('wdm_datatable', array( $this, 'wdm_display_datatable' ));

        add_action('wp_ajax_update_entry', array( $this, 'wdm_update_entry' ));
        add_action('wp_ajax_nopriv_update_entry', array( $this, 'wdm_update_entry' ));
        add_action('wp_ajax_nopriv_delete_entry', array( $this, 'wdm_delete_entry' ));
        add_action('wp_ajax_delete_entry', array( $this, 'wdm_delete_entry' ));
        add_action('wp_ajax_nopriv_add_entry', array( $this, 'wdm_add_entry' ));
        add_action('wp_ajax_add_entry', array( $this, 'wdm_add_entry' ));
        add_action('wp_ajax_clear_cache', array( $this, 'wdm_clear_cache' ));
    }


    public function wdm_clear_cache(){
        $data = wp_unslash($_POST); // WordPress may add slashes to POST data, so remove them

        // Check if the nonce is set and valid. The nonce is a security feature to prevent CSRF attacks.
        if (isset($data['nonce']) && wp_verify_nonce($data['nonce'], 'information_about_datatable')) {
            error_log('id is: ' . $data['id']);
            $this->clear_spinup_cache($data['id'], 'object');
            $this->clear_spinup_cache($data['id'], 'page');
        }

    }


    public function clear_spinup_cache( $id, $type ) {
        $apiUrl = "https://api.spinupwp.app/v1/sites/$id/$type-cache/purge";
        $bearerToken = get_option('spinup_api_token'); // Replace with your actual token

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $bearerToken",
            "Content-Type: application/json"
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    }

    /**
     * Adds a new entry to the 'wdm_site_details' table in the WordPress database.
     *
     * This function is used to process a POST request containing information about a site
     * and its associated details. It verifies the nonce for security, sanitizes the input
     * data, and inserts the new entry into the 'wdm_site_details' table.
     *
     * @since  1.0.0
     * @access public
     *
     * @return void This function does not return a value directly, but sends a JSON response.
     * If the insertion is successful, it sends a JSON success response with the new entry's ID.
     * If there's an issue, no response is sent back (but this can be extended for error handling).
     */
    public function wdm_add_entry()
    {
        // Sanitize the incoming POST data by removing slashes.
        $data = wp_unslash($_POST); // WordPress may add slashes to POST data, so remove them

        // Check if the nonce is set and valid. The nonce is a security feature to prevent CSRF attacks.
        if (isset($data['nonce']) && wp_verify_nonce($data['nonce'], 'information_about_datatable')) {

            // Extract the new entry data from the POST request.
            $new_entry = $data['new_entry'];
			error_log ( 'data is: ' . print_r($new_entry, true));
            // Sanitize each input field to prevent potential XSS or SQL injection vulnerabilities.
            $site_name               = sanitize_text_field($new_entry['wdm_site_name']);
            $sme_name                = sanitize_text_field($new_entry['wdm_sme_name']);
            $developer_name          = sanitize_text_field($new_entry['wdm_developer_name']);
            $client_name             = sanitize_text_field($new_entry['wdm_client_name']);
            $project_name            = sanitize_text_field($new_entry['wdm_project_name']);
            $tracking_time_link      = sanitize_text_field($new_entry['wdm_tracking_time_link']);
            $git_link                = sanitize_text_field($new_entry['wdm_git_link']);
            $sonar_link                = sanitize_text_field($new_entry['wdm_sonar_link']);
            $spinup_link                = sanitize_text_field($new_entry['wdm_spinup_link']);
            $php_version             = sanitize_text_field($new_entry['wdm_spinup_version']);
            $domain                  = sanitize_text_field($new_entry['wdm_spinup_domain']);
            $team_name               = sanitize_text_field($new_entry['wdm_team_name']);
            $to_add_on_tracking_time = sanitize_text_field($new_entry['wdm_add_project_tracking_time']);
            $to_add_git_repo         = sanitize_text_field($new_entry['wdm_add_git_repo']);
            $to_add_sonar_project    = sanitize_text_field($new_entry['wdm_add_sonar']);
            $to_add_spinup_project    = sanitize_text_field($new_entry['wdm_add_spinup']);

            $if_added                    = false;
            $tracking_time_error_message = '';
            $project_id                  = '';
            $git_repo_error_message      = '';
            $sonar_url_error_message     = '';
            $spinup_link_error_message  = '';
            $spin_up_id                 = '';

            $combined_name = $client_name . ' - ' . $project_name;
            $combined_slug_name = str_replace(' ', '-', $client_name . ' - ' . $project_name);

            if (! $tracking_time_link && $client_name && $project_name && $to_add_on_tracking_time == 'true') {
                $if_added = $this->addProject($combined_name, $team_name);
                if (! $if_added) {
                    $tracking_time_error_message = 'Something went wrong';
                } elseif (isset($if_added['status']) && $if_added['status'] != 200) {
                    $tracking_time_error_message = isset($if_added['message']) ? $if_added['message'] : 'Something went wrong';
                } elseif (isset($if_added['project_id'])) {
                    $project_id         = $if_added['project_id'];
                    $tracking_time_link = 'https://pro.trackingtime.co/#/project/' . $project_id . '/list';
                }
            }

            if (! $git_link && $client_name && $project_name && $to_add_git_repo == 'true') {
                error_log('create git repo');
                $git_link = $this->createGitRepo($combined_slug_name, $team_name, $sme_name);
                error_log('git repo url ' . $git_link);
                if (! $git_link) {
                    $git_repo_error_message = 'Something went wrong';
                }
            }

            if (! $spinup_link && $client_name && $project_name && $to_add_spinup_project == 'true') {
                $spin_up_id = $this->createSpinupSite($php_version, $domain);
                if ($spin_up_id) {
                    $spinup_link = 'https://spinupwp.app/wisdmlabs-sme/sites/' . $spin_up_id;
                }
            }
            error_log('add sonar ' . $to_add_sonar_project);
            if (! $sonar_link && $client_name && $project_name && $to_add_sonar_project == 'true') {
                $sonar_key = $this->createSonarProject($combined_name, $combined_slug_name);
                if ($sonar_key) {
                    $sonar_link = "http://codequality.wisdmlabs.net:9000/dashboard?id=" . $sonar_key;
                    $sonar_project_secret = $this->createSonarProjectSecret($sonar_key);

                    if ($sonar_project_secret) {
                        $git_publicKeys = $this->getGitRepoKeys($sonar_key);
                        if ($git_publicKeys && isset($git_publicKeys['key'])) {
                            $sonar_token_value = $this-> encryptWithPublicKey($git_publicKeys['key'], $sonar_project_secret);
                            $sonar_host_url_value = $this-> encryptWithPublicKey($git_publicKeys['key'], "http://codequality.wisdmlabs.net:9000");
                            if ($sonar_token_value && $sonar_host_url_value) {
                                $git_sonar_token_added = $this->createGitRepoSecret($git_publicKeys['key_id'], $sonar_key, 'SONAR_TOKEN', $sonar_token_value);
                                $git_sonar_host_url_added = $this->createGitRepoSecret($git_publicKeys['key_id'], $sonar_key, 'SONAR_HOST_URL', $sonar_host_url_value);
                                if ($git_sonar_token_added && $git_sonar_host_url_added) {
                                    $git_sonar_file_addded = $this->addFilesToGitRepo($sonar_key, 'sonar');
                                    $git_yml_file_addded = $this->addFilesToGitRepo($sonar_key, 'yml');
                                }
                            }
                        }
                    }


                }
                error_log('sonar url ' . $sonar_link);

            }

            global $wpdb;  // Access the global WordPress database object.

            // Define the table name, using the WordPress table prefix to ensure proper table reference.
            $table_name = $wpdb->prefix . 'wdm_site_details';

            error_log('inserting into db');

            // Check if the required fields (site name, SME name, and developer name) are filled out
            if (! empty($site_name) && ! empty($sme_name) && ! empty($developer_name)) {
                // Insert the sanitized data into the database table
                $result = $wpdb->insert(
                    $table_name,  // Target table
                    array(  // Data to insert as key-value pairs
                        'site_name'          => $site_name,
                        'sme_name'           => $sme_name,
                        'developer_name'     => $developer_name,
                        'client_name'        => $client_name,
                        'project_name'       => $project_name,
                        'tracking_time_link' => $tracking_time_link,
                        'git_link'           => $git_link,
                        'sonar_link'           => $sonar_link,
                        'team_name'          => $team_name,
						'spinup_link'        => $spinup_link,
                        'spinup_site_id'     => $spin_up_id
                    )
                );
            }
            error_log('inserted into db');
            error_log(print_r($result, true));

            // If the insertion was successful, return a success response
            if ($result !== false) {
                // Retrieve the ID of the newly inserted entry
                $new_entry_id = $wpdb->insert_id;
                error_log('getting new entry id ' . $new_entry_id);
                // Send a JSON success response with the new entry's ID and the GitHub repository URL
                wp_send_json_success(
                    array(
                        'new_entry_id'     => $new_entry_id,
                        'tt_message'       => $tracking_time_error_message,
                        'project_id'       => $project_id,
                        'tracking_time_link' => $tracking_time_link,
                        'git_repo_message' => $git_repo_error_message,
                        'git_repo_url'     => $git_link, // Include the GitHub repository URL in the response
                        'sonar_message'    => $sonar_url_error_message,
                        'sonar_url'        => $sonar_link,
                        'spinup_url'        => $spinup_link,
                        'spinup_id'        => $spin_up_id
                    )
                );
            } else {
                // Send a JSON error response if the insertion failed
                error_log('Failed to insert entry into the database.');
                wp_send_json_error(
                    array(
                        'message'          => 'Failed to insert entry into the database.',
                        'tt_message'       => $tracking_time_error_message,
                        'git_repo_message' => $git_repo_error_message,
                    )
                );
            }
        } else {
            // Send a JSON error response if the nonce verification failed
            wp_send_json_error(
                array(
                    'message' => 'Nonce verification failed.',
                )
            );
        }
    }

    public function createSpinupSite( $php_version, $domain_prefix ){
        $apiUrl = "https://api.spinupwp.app/v1/sites";
        $authToken = get_option('spinup_api_token'); // Replace with your actual Bearer token

        $postData = [
            'server_id' => 32991,
            'domain' => "$domain_prefix.wisdmlabs.net",
            'site_user' => $domain_prefix,
            'php_version' => $php_version ? $php_version : '8.2',
            'installation_method' => 'wp',
            'database' => [
                'name' => $domain_prefix,
                'username' => $domain_prefix,
                'password' => $domain_prefix,
                'table_prefix' => 'wp'
            ],
            'wordpress' => [
                'title' => $domain_prefix,
                'admin_user' => 'wisdmlabs',
                'admin_password' => 'admin@admin',
                'admin_email' => 'wisdmlabs@wdm.com'
            ],
            'page_cache' => ['enabled' => true],
            'https' => ['enabled' => true]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $authToken,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

        $response = curl_exec($ch);
        $response_data = json_decode($response, true);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        error_log('spinup response: ' . $response);

        if ($httpCode == 201 && isset($response_data["data"]) && isset($response_data["data"]["id"])) {
            return $response_data["data"]["id"];
        }
        else {
            return false;
        }
        

    }

    public function addFilesToGitRepo($project_name, $file_name)
	{
		$branch = "release"; // Hardcoded branch name
		$apiUrl = '';

		if ($file_name == 'sonar') {
			$apiUrl = "https://api.github.com/repos/WisdmLabs/$project_name/contents/sonar-project.properties?ref=$branch";
		} else {
			$apiUrl = "https://api.github.com/repos/WisdmLabs/$project_name/contents/.github/workflows/build.yml?ref=$branch";
		}

		$bearerToken = get_option('github_api_token'); // GitHub token

		$commitMessage = "";
		if ($file_name == 'sonar') {
			$fileContent = base64_encode("sonar.projectKey=$project_name");
			$commitMessage = "Adding sonar properties file";
		} else {
			$commitMessage = "Adding build.yml file";
			
			$fileContent = "name: Build

on:
  push:
    branches:
      - release


jobs:
  build:
    name: Build
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
        with:
          fetch-depth: 0  # Shallow clones should be disabled for a better relevancy of analysis
      - uses: sonarsource/sonarqube-scan-action@master
        env:
          SONAR_TOKEN: \${{ secrets.SONAR_TOKEN }}
          SONAR_HOST_URL: \${{ secrets.SONAR_HOST_URL }}
      # If you wish to fail your job when the Quality Gate is red, uncomment the
      # following lines. This would typically be used to fail a deployment.
      # - uses: sonarsource/sonarqube-quality-gate-action@master
      #   timeout-minutes: 5
      #   env:
      #     SONAR_TOKEN: \${{ secrets.SONAR_TOKEN }}
";
			$fileContent = base64_encode($fileContent);
		}

		// Step 1: Check if the file exists in the `release` branch to get its SHA
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $apiUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: Bearer $bearerToken",
			"User-Agent: PHP-cURL",
			"Accept: application/vnd.github.v3+json"
		]);

		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$sha = null;
		if ($httpCode === 200) {
			$responseData = json_decode($response, true);
			$sha = $responseData['sha'];
		}

		// Step 2: Create or update the file in `release` branch
		$data = [
			"message" => $commitMessage,
			"content" => $fileContent,
			"branch" => $branch
		];

		if ($sha) {
			$data["sha"] = $sha;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, str_replace("?ref=$branch", "", $apiUrl)); // Remove ref query param for PUT
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: Bearer $bearerToken",
			"User-Agent: PHP-cURL",
			"Accept: application/vnd.github.v3+json",
			"Content-Type: application/json"
		]);

		$response = curl_exec($ch);
		$response_data = json_decode($response, true);
		error_log('Add files to git repo: ' . $file_name);
		error_log(print_r($response_data, true));
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		// Output response
		if ($httpCode === 201 || $httpCode === 200) {
			return "File successfully created/updated in the release branch.";
		} else {
			return "Failed to upload file. Response: " . print_r($response_data, true);
		}
	}


    public function createGitRepoSecret($key_id, $repo_name, $secret_name, $secret_value)
    {
        $apiUrl = "https://api.github.com/repos/WisdmLabs/$repo_name/actions/secrets/$secret_name";

        $bearerToken = get_option('github_api_token'); // Replace with actual token

        $data = [
            "encrypted_value" => $secret_value,
            "key_id" => $key_id
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $bearerToken",
            "User-Agent: PHP-cURL",
            "Accept: application/vnd.github.v3+json",
            "Content-Type: application/json"
        ]);

        $response = curl_exec($ch);
        $response_data = json_decode($response, true);
        error_log('create git secret' . $secret_name);
        error_log(print_r($response_data, true));
        error_log(curl_getinfo($ch, CURLINFO_HTTP_CODE));

        if (curl_errno($ch)) {
            error_log(print_r('Curl error: ' . curl_error($ch), true));
            return false;
        } elseif (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 201) {
            error_log(print_r('Response: ' . $response, true));
            return true;
        }
        return false;
        curl_close($ch);
    }

    public function createSonarProject($project_name, $project_key)
    {
        $url = "http://codequality.wisdmlabs.net:9000/api/projects/create";

        // Initialize cURL session
        $ch = curl_init($url);

        // Data to be sent in the POST request
        $postData = http_build_query([
            'name' => $project_name,
            'project' => str_replace(' ', '_', $project_key),
			'mainBranch' => 'release'
        ]);

        $token = get_option('sonar_api_token');

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Basic " . $token,
            "Content-Type: application/x-www-form-urlencoded"
        ]);

        // Execute cURL session
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Decode response
        $response_data = json_decode($response, true);
        error_log(print_r($response_data, true));
        error_log(print_r($response_data['project']['key'], true));
        if ($httpCode == 200) {
            if ($response_data && isset($response_data['project']) && isset($response_data['project']['key'])) {

                return $response_data['project']['key'];
            }
            // wp_send_json_success(['message' => 'Project created successfully', 'response' => $response_data]);
            else {
                return false;
            }
        }
    }

    public function createSonarProjectSecret($project_key)
    {
        $apiUrl = "http://codequality.wisdmlabs.net:9000/api/user_tokens/generate?name=$project_key&projectKey=$project_key&type=PROJECT_ANALYSIS_TOKEN";

        $bearerToken = get_option('sonar_api_token'); // Replace with actual token

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Basic " . $bearerToken,
            "Content-Type: application/x-www-form-urlencoded"
        ]);

        $response = curl_exec($ch);
        $response_data = json_decode($response, true);
        error_log('create sonar secret');
        error_log(print_r($response_data, true));

        if (curl_errno($ch)) {
            error_log(print_r('Curl error: ' . curl_error($ch), true));
            return false;
        } else {
            error_log(print_r('Response: ' . $response, true));
            return $response_data['token'];
        }

        curl_close($ch);

    }

    public function getGitRepoKeys($project_name)
    {
        $apiUrl = "https://api.github.com/repos/WisdmLabs/$project_name/actions/secrets/public-key";

        $bearerToken = get_option('github_api_token');

        if (! $bearerToken) {
            return false;
        }


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $bearerToken",
            "User-Agent: PHP-cURL"
        ]);

        $response = curl_exec($ch);
        $response_data = json_decode($response, true);
        error_log('get public keys');
        error_log(print_r($response_data, true));

        if (curl_errno($ch)) {
            error_log(print_r('Curl error: ' . curl_error($ch), true));
            return false;
        } else {
            error_log(print_r('Response: ' . $response, true));
            return $response_data;
        }

        curl_close($ch);

    }
    public function create_github_secret()
    {

        error_log('hi');
        // Repository details
        $owner = "omkar-kharade";
        $repo = "test-project-one";
        // GitHub API endpoint for public key
        $publicKeyUrl = "https://api.github.com/repos/{owner}/{repo}/actions/secrets/public-key";

        // GitHub API endpoint for creating a secret
        $createSecretUrl = "https://api.github.com/repos/{owner}/{repo}/actions/secrets/SONAR_TOKEN";

        // Personal access token
        $token = get_option('github_api_token');


        // Secret details
        $secretName = "SONAR_TOKEN";
        $secretValue = "xyz";

        // Headers for API requests
        $headers = [
            "Authorization" => "token $token",
            "Accept" => "application/vnd.github.v3+json"
        ];

        // Step 1: Get the public key
        $response = wp_remote_get(str_replace(['{owner}', '{repo}'], [$owner, $repo], $publicKeyUrl), [
            'headers' => $headers
        ]);

        error_log(print_r($response, true));

        if (is_wp_error($response)) {
            return "Failed to retrieve public key: " . $response->get_error_message();
        }

        $publicKeyData = json_decode(wp_remote_retrieve_body($response), true);
        $publicKey = $publicKeyData['key'];
        $keyId = $publicKeyData['key_id'];

        // Step 2: Encrypt the secret value
        $rsa = PublicKeyLoader::load($publicKey)->withPadding(RSA::ENCRYPTION_PKCS1);
        $encryptedValue = base64_encode($rsa->encrypt($secretValue));

        // Step 3: Create the secret
        $payload = [
            "encrypted_value" => $encryptedValue,
            "key_id" => $keyId
        ];

        $response = wp_remote_post(str_replace(['{owner}', '{repo}'], [$owner, $repo], $createSecretUrl), [
            'method' => 'PUT',
            'headers' => $headers,
            'body' => json_encode($payload)
        ]);

        if (is_wp_error($response)) {
            return "Failed to create secret: " . $response->get_error_message();
        }

        $statusCode = wp_remote_retrieve_response_code($response);
        if ($statusCode == 201) {
            return "Secret created successfully!";
        } else {
            return "Failed to create secret: " . wp_remote_retrieve_body($response);
        }
    }

    public function createGitRepo($projectName, $team_name, $sme_name)
    {
        // Base API URL
        $url = 'https://api.github.com/orgs/WisdmLabs/repos';

        // Authorization token
        $authToken = get_option('github_api_token');

        if (! $authToken) {
            return false;
        }

        // Initialize cURL
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true); // Set the request method to POST
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            json_encode(
                array(
                    'name'        => $projectName,
                    'description' => 'This is a new repository',
                    'private'     => false,
                    "auto_init"   => true
                )
            )
        );
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Authorization: Bearer ' . $authToken,
                'Content-Type: application/json',
                'User-Agent: WdmSitesDirectory/1.0',
            )
        );

        // Execute the API call
        $response      = curl_exec($ch);
        $response_data = json_decode($response, true);
        $error         = curl_errno($ch);

        error_log($response);
        // Close cURL resource
        curl_close($ch);
        // error_log( 'github response :' .$response_data);
        // error_log( 'github response name :' .$response_data['name']);

		
        if ($error || ! isset($response_data['html_url'])) {
            return false;
        } else {
            if ($team_name) {
                $this->assignTeamToProject($response_data['name'], strtolower($team_name));
                foreach (get_option('wdm_team_settings') as $team) {
                    error_log('giveAdminAccessToTeamLead');
                    if ($team['name'] == $team_name ) {
                        $this->giveAdminAccessToTeamLead($response_data['name'], $team['leader']);
                    }

					else if ($team['leadName'] == $sme_name) {
						$this->giveAdminAccessToTeamLead($response_data['name'], $team['leader']);
					}
					
                }
            }
            error_log('creating release branch');

			$this->createGithubReleaseBranch($response_data['name']);
            error_log(print_r($response_data, true));
            return $response_data['html_url'];
        }


    }


	public function createGithubReleaseBranch($repo) {
		
	
		// Step 1: Get the latest commit SHA of the base branch
		$baseBranchUrl = "https://api.github.com/repos/WisdmLabs/$repo/git/ref/heads/main";
		
		$token = get_option('github_api_token');
		$ch = curl_init($baseBranchUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: Bearer $token",
			"User-Agent: PHP-cURL-Request",
			"Accept: application/vnd.github+json"
		]);
		
		$response = curl_exec($ch);
		curl_close($ch);
		
		$data = json_decode($response, true);
		if (!isset($data['object']['sha'])) {
			return false;
		}
		
		$commitSha = $data['object']['sha'];
	
		// Step 2: Create a new branch
		$createBranchUrl = "https://api.github.com/repos/WisdmLabs/$repo/git/refs";
		$postData = json_encode([
			"ref" => "refs/heads/release",
			"sha" => $commitSha
		]);
	
		$ch = curl_init($createBranchUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: Bearer $token",
			"User-Agent: PHP-cURL-Request",
			"Accept: application/vnd.github+json",
			"Content-Type: application/json"
		]);
		
		$response = curl_exec($ch);
		curl_close($ch);
	
		$result = json_decode($response, true);
		
		if (isset($result['ref'])) {
			return true;
		} else {
			return false;
		}
	}
	

    public function giveAdminAccessToTeamLead($repo_name, $team_lead)
    {
        $token = get_option('github_api_token');

        // GitHub API URL
        $url = "https://api.github.com/repos/WisdmLabs/$repo_name/collaborators/$team_lead";

        // Set the request headers
        $headers = [
            'Authorization: Bearer ' . $token,
            'Accept: application/vnd.github.v3+json',
            'User-Agent: PHP-Request'
        ];

        // Set the request body
        $data = json_encode([
            'permission' => 'admin' // Available options: pull, push, admin
        ]);

        // Initialize cURL
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); // Use PUT request
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the request
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close cURL
        curl_close($ch);

        // Output the response
        if ($http_code === 204) {
            return true;
        } else {
            return false;
        }
    }

    public function assignTeamToProject($repo_name, $teamName)
    {

        // GitHub API credentials
        $token = get_option('github_api_token');
        $org = "WisdmLabs"; // Replace with your GitHub Organization name
        $team_slug = $teamName; // Replace with the team slug
        $owner = "WisdmLabs"; // Replace with the repository owner (username or org name)
        $repo = $repo_name; // Replace with the repository name

        // API URL
        $url = "https://api.github.com/orgs/$org/teams/$team_slug/repos/$owner/$repo";

        // Request headers
        $headers = [
            "Authorization: Bearer $token",
            "Accept: application/vnd.github.v3+json",
            "User-Agent: PHP-Script" // Required for GitHub API requests
        ];

        // Request body
        $data = json_encode([
            "permission" => "push" // Change to "pull", "push", "admin", etc.
        ]);

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute request and get response
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Check response
        if ($http_code == 204) {
            error_log("Team assigned successfully." . $response);
        } else {
            error_log("Failed to assign team: " . $response);
        }
    }


    /**
     * Deletes a specific entry from the 'wdm_site_details' table in the WordPress database.
     *
     * This function processes a POST request to delete an entry based on its ID.
     * It verifies the nonce for security, retrieves the ID of the entry to be deleted,
     * and then performs the deletion in the 'wdm_site_details' table.
     *
     * @since  1.0.0
     * @access public
     *
     * @return void This function does not return a value directly. It performs the delete action
     * and sends a response if needed, but this can be extended for further response handling.
     */
    public function wdm_delete_entry()
    {
        // Sanitize the incoming POST data by removing slashes
        $data = wp_unslash($_POST); // WordPress may add slashes to POST data, so remove them

        // Verify that the nonce is set and valid. This is a security feature to prevent CSRF attacks.
        if (isset($data['nonce']) && wp_verify_nonce($data['nonce'], 'information_about_datatable')) {
            // Retrieve the ID of the entry to be deleted
            $id = $data['id'];

            // Ensure that the ID is valid and not empty
            if (! empty($id)) {
                global $wpdb;  // Access the global WordPress database object

                // Define the table name using the WordPress table prefix for consistency
                $table_name = $wpdb->prefix . 'wdm_site_details';

                // Perform the deletion of the entry with the specified ID
                $wpdb->delete(
                    $table_name,  // The table from which the entry will be deleted
                    array(        // Condition to find the entry to delete
                        'id' => $id, // Use the 'id' to match the specific entry
                    )
                );
            }
        }
    }


    /**
     * Updates an entry in the 'wdm_site_details' table based on the provided updated data.
     *
     * This function processes a POST request to update an existing entry's details in the
     * 'wdm_site_details' table. It verifies the nonce for security, retrieves the updated
     * data, and then performs the update based on the ID of the entry being modified.
     *
     * @since  1.0.0
     * @access public
     *
     * @return void This function does not return a value directly. It performs the update action
     * and logs the result. Additional response handling can be added as needed.
     */
    public function wdm_update_entry()
    {

        // Sanitize the incoming POST data by removing slashes
        $data = wp_unslash($_POST); // Remove any slashes that WordPress might add to POST data

        // Verify that the nonce is set and valid. This is a security feature to prevent CSRF attacks.
        if (isset($data['nonce']) && wp_verify_nonce($data['nonce'], 'information_about_datatable')) {

            // Retrieve the updated data, which is an array containing the updated values
            $updated_data = $data['updated_data'];

            global $wpdb;  // Access the global WordPress database object

            // Define table name using the WordPress table prefix to ensure compatibility with other WordPress installations
            $table_name = $wpdb->prefix . 'wdm_site_details';

            // Extract values from the updated data array based on their index
            $id                 = $updated_data[0]['value'];
            $site_name          = $updated_data[1]['value'];
            $sme_name           = $updated_data[2]['value'];
            $developer_name     = $updated_data[3]['value'];
            $client_name        = $updated_data[4]['value'];
            $project_name       = $updated_data[5]['value'];
            $tracking_time_link = $updated_data[6]['value'];
            $git_link           = $updated_data[7]['value'];
            $sonar_link           = $updated_data[8]['value'];
            $spinup_link           = $updated_data[9]['value'];
            $team_name          = $updated_data[10]['value'];
            $last_modified      = date('Y-m-d H:i:s');

            // Prepare the data array to be used in the update query
            $new_data = array(
                'site_name'          => sanitize_text_field($site_name),
                'sme_name'           => sanitize_text_field($sme_name),
                'developer_name'     => sanitize_text_field($developer_name),
                'client_name'        => sanitize_text_field($client_name),
                'project_name'       => sanitize_text_field($project_name),
                'tracking_time_link' => sanitize_text_field($tracking_time_link),
                'git_link'           => esc_url($git_link),  // Ensure the Git link is a valid URL
                'sonar_link'           => esc_url($sonar_link),  // Ensure the Git link is a valid URL
                'spinup_link'           => esc_url($spinup_link),  // Ensure the Git link is a valid URL
				'team_name'          => sanitize_text_field($team_name),
                'last_modified'      => $last_modified,
            );

            // Define the condition (WHERE clause) for the update query, based on the entry's ID
            $where = array(
                'id' => $id,
            );

            // Ensure values are properly escaped to prevent SQL injection
            $updated = $wpdb->update($table_name, $new_data, $where);

            // Log the result for debugging purposes (this could be replaced with proper response handling)
            if ($updated !== false) {
                error_log("Entry with ID {$id} updated successfully.");
            } else {
                error_log("Failed to update entry with ID {$id}.");
            }
        } else {
            // If nonce verification fails, log an error for debugging
            error_log('Nonce verification failed for update entry.');
        }
    }



    /**
     * Displays a DataTable with site details fetched from the database.
     *
     * This function retrieves data from the `wdm_site_details` table and displays it in an HTML table format.
     * The data includes site name, SME name, developer name, client name, and project name.
     * It also checks if any data is available and returns a message if no data is found.
     *
     * @global wpdb $wpdb WordPress database object for interacting with the database.
     * @return string The HTML table output or a message if no data is available.
     */
    public function wdm_display_datatable()
    {

        global $wpdb;  // Access WordPress database object

        // Fetch data from the wdm_site_details table
        $results = $wpdb->get_results("SELECT id, site_name, sme_name, developer_name, client_name, project_name, tracking_time_link, git_link, sonar_link, team_name, spinup_link, spinup_site_id FROM {$wpdb->prefix}wdm_site_details", ARRAY_A);
		// error_log(print_r($results,true));
        // Check if there is any data
        if (empty($results)) {
            return '<p>No site details available.</p>';
        }

        ob_start();  // Start output buffering

        ?>
		<style>
        .wdm-dropdown {
            position: relative;
            display: inline-block;
            width: 200px;
        }

        .wdm-dropdown-toggle {
            display: block;
            width: 100%;
            padding: 10px;
            background: #f1f1f1;
            border: 1px solid #ccc;
            border-radius: 4px;
            cursor: pointer;
            text-align: left;
        }

        .wdm-dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            max-height: 150px;
            overflow-y: auto;
            background: white;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 10;
        }

        .wdm-dropdown-menu.active {
            display: block;
        }

        .wdm-dropdown-menu label {
            display: block;
            padding: 5px 10px;
            cursor: pointer;
        }

        .wdm-dropdown-menu label:hover {
            background: #f0f0f0;
        }

        .wdm-dropdown-menu input[type="checkbox"] {
            margin-right: 10px;
        }
    </style>
	<!-- <h1>Custom Multi-select Dropdown</h1>
    <div class="wdm-dropdown">
        <div class="wdm-dropdown-toggle">Select options</div>
        <div class="wdm-dropdown-menu">
            <label><input type="checkbox" value="Option 1"> Option 1</label>
            <label><input type="checkbox" value="Option 2"> Option 2</label>
            <label><input type="checkbox" value="Option 3"> Option 3</label>
            <label><input type="checkbox" value="Option 4"> Option 4</label>
            <label><input type="checkbox" value="Option 5"> Option 5</label>
        </div>
    </div>

    <script>
        const dropdownToggle = document.querySelector('.wdm-dropdown-toggle');
        const dropdownMenu = document.querySelector('.wdm-dropdown-menu');

        // Toggle dropdown menu
        dropdownToggle.addEventListener('click', () => {
            dropdownMenu.classList.toggle('active');
        });

        // Capture selected options
        dropdownMenu.addEventListener('change', () => {
            const selectedOptions = Array.from(dropdownMenu.querySelectorAll('input[type="checkbox"]:checked'))
                .map(checkbox => checkbox.value);
            dropdownToggle.textContent = selectedOptions.length > 0 ? selectedOptions.join(', ') : 'Select options';
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('active');
            }
        });
    </script> -->
    </select>
		<table id="wdm-datatable" class="wdm-datatable display" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th></th>
					<th><?php _e('Site Name'); ?></th>
					<th><?php _e('SME Name'); ?></th>
					<th><?php _e('Developer Name'); ?></th>
					<th><?php _e('Client Name'); ?></th>
					<th><?php _e('Project Name'); ?></th>
					<th><?php _e('Tracking Time Link'); ?></th>
					<th><?php _e('Git Link'); ?></th>
					<th><?php _e('Sonar Link'); ?></th>
					<th><?php _e('Spinup Link'); ?></th>
					<th><?php _e('Team Name'); ?></th>
					<th><?php _e('Actions'); ?></th>
                    <th></th>
				</tr>
				<tr class="search-row">
					<td></td>
					<td><input placeholder="Search"></td>
					<td><input placeholder="Search"></td>
					<td><input placeholder="Search"></td>
					<td><input placeholder="Search"></td>
					<td><input placeholder="Search"></td>
					<td><input placeholder="Search"></td>
					<td><input placeholder="Search"></td>
					<td><input placeholder="Search"></td>
					<td><input placeholder="Search"></td>
					<td><input placeholder="Search"></td>
					<td></td>
                    <td></td>
				</tr>
			</thead>
			<tbody>
		<?php
        foreach ($results as $row) {
            ?>
					<tr>
						<td> <?php _e($row['id']); ?></td>
						<td> <?php _e($row['site_name']); ?> </td>
						<td> <?php _e($row['sme_name']); ?></td>
						<td> <?php _e($row['developer_name']); ?></td>
						<td> <?php _e($row['client_name']); ?></td>
						<td> <?php _e($row['project_name']); ?> </td>
						<td><?php _e($row['tracking_time_link']); ?></td>
						<td><?php _e($row['git_link']);?></td>
						<td><?php _e($row['sonar_link']); ?></td>
						<td><?php _e($row['spinup_link']); ?></td>
						<td> <?php _e($row['team_name']); ?></td>
						<td>
                            <button class="wdm-entry-edit"><i class="fas fa-edit"></i></button>
                            <button class="wdm-entry-delete"><i class="fas fa-trash-alt"></i></button>
                            <?php if($row['spinup_site_id'] != ''){ ?>
                            <button class="wdm-entry-spinup-cache"><i class="fas fa-sync"></i></button>
                            <?php } ?>   
                        </td>
                            

                        <td><?php _e($row['spinup_site_id']) ?></td>
					</tr>
			<?php
        }
        ?>
			</tbody>
			<tfoot>
				<tr>
					<th></th>
					</th>
					<th><?php _e('Site Name'); ?></th>
					<th><?php _e('SME Name'); ?></th>
					<th><?php _e('Developer Name'); ?></th>
					<th><?php _e('Client Name'); ?></th>
					<th><?php _e('Project Name'); ?></th>
					<th><?php _e('Tracking Time Link'); ?></th>
					<th><?php _e('Git Link'); ?></th>
					<th><?php _e('Sonar Link'); ?></th>
					<th><?php _e('Spinup Link'); ?></th>
					<th><?php _e('Team Name'); ?></th>
					<th><?php _e('Actions'); ?></th>
                    <th></th>
				</tr>
			</tfoot>
		</table>
		<?php
        return ob_get_clean();  // Return the buffered content (form HTML)
    }



    /**
     * Displays the site details form and handles form submission.
     *
     * This function checks if the `wdm_site_details` table exists in the WordPress database.
     * If it doesn't exist, the table is created. It then displays a form for users to input
     * site details. Upon form submission, it sanitizes the input data and inserts it into the
     * database. If the form is successfully submitted, a success message is displayed, otherwise
     * an error message is shown if required fields are missing.
     *
     * @global wpdb $wpdb WordPress database object for interacting with the database.
     * @return string The HTML output of the form or messages after form submission.
     */
    public function wdm_site_details_form()
    {
        // Output the form HTML
        ob_start();  // Start output buffering
        ?>
		<div class="wdm-site-details-form">
			<p class="wdm-form-field">
				<label for="wdm_site_name" class="wdm-label"><?php _e('Site Name (required)'); ?></label><br>
				<input type="text" id="wdm_site_name" name="wdm_site_name" class="wdm-input" required>
			</p>
			
			<p class="wdm-form-field">
				<label for="wdm_sme_name" class="wdm-label"><?php _e('SME Name (required)'); ?></label><br>
				<select id="wdm_sme_name" name="wdm_sme_name" class="wdm-input">
					<option value="Shamali">Shamali</option>
					<option value="Shruti">Shruti</option>
					<option value="Akshay">Akshay</option>
					<option value="Nikhil">Nikhil</option>
					<option value="Foram">Foram</option>
				</select>
			</p>
			<p class="wdm-form-field">
				<label for="wdm_developer_name" class="wdm-label"><?php _e('Developer Name (required)'); ?></label><br>
				<input type="text" id="wdm_developer_name" name="wdm_developer_name" class="wdm-input" required>
			</p>
			<p class="wdm-form-field">
				<label for="wdm_client_name" class="wdm-label"><?php _e('Client Name'); ?></label><br>
				<input type="text" id="wdm_client_name" name="wdm_client_name" class="wdm-input">
			</p>
			<p class="wdm-form-field">
				<label for="wdm_project_name" class="wdm-label"><?php _e('Project Name'); ?></label><br>
				<input type="text" id="wdm_project_name" name="wdm_project_name" class="wdm-input">
			</p>
			<p class="wdm-form-field wdm-form-single-line">
				<input type="checkbox" id="wdm_add_project_tracking_time" name="wdm_add_project_tracking_time" class="wdm-input wdm-disabled-checkbox" disabled>
				<label for="wdm_add_project_tracking_time" class="wdm-label wdm-disabled-label"><?php _e('Do you want to add this project on tracking time?'); ?></label>
			</p>
			<p class="wdm-form-field wdm-form-single-line">
				<input type="checkbox" id="wdm_add_git_repo" name="wdm_add_git_repo" class="wdm-input wdm-disabled-checkbox" disabled>
				<label for="wdm_add_git_repo" class="wdm-label wdm-disabled-label"><?php _e('Do you want to create a Git repository?'); ?></label>
			</p>
			<p class="wdm-form-field wdm-form-single-line">
				<input type="checkbox" id="wdm_add_sonar" name="wdm_add_sonar" class="wdm-input wdm-disabled-checkbox" disabled>
				<label for="wdm_add_sonar" class="wdm-label wdm-disabled-label"><?php _e('Do you want to create a Sonar Project?'); ?></label>
			</p>
			<p class="wdm-form-field wdm-form-single-line">
				<input type="checkbox" id="wdm_add_spinup" name="wdm_add_spinup" class="wdm-input wdm-disabled-checkbox" disabled>
				<label for="wdm_add_spinup" class="wdm-label wdm-disabled-label"><?php _e('Do you want to create a Spinup Site?'); ?></label>
			</p>
            <div class = "wdm-spinup-settings-container hidden" style="display: none;">
                <p class="wdm-spinup-settings">Spinup Settings</p>
                <p class="wdm-form-field">
                    <label for="wdm_spinup_version" class="wdm-label"><?php _e('PHP version'); ?></label><br>

                    <select id="wdm_spinup_version" ignore="true" name="wdm_spinup_version" class="wdm-input">
                        <option value="7.4">7.4</option>
                        <option value="8.0">8.0</option>
                        <option value="8.1">8.1</option>
                        <option value="8.2">8.2</option>
                        <option value="8.3">8.3</option>
                    </select>
                </p>
                
                <p class="wdm-form-field">
                    <label for="wdm_spinup_domain" class="wdm-label"><?php _e('Domain'); ?></label><br>
                    <input type="text" id="wdm_spinup_domain" ignore="true" name="wdm_spinup_domain" class="wdm-input">
                </p>
            </div>
			<p class="wdm-form-field">
				<label for="wdm_tracking_time_link" class="wdm-label"><?php _e('Tracking Time Link'); ?></label><br>
				<input type="text" id="wdm_tracking_time_link" name="wdm_tracking_time_link" class="wdm-input">
			</p>
			<p class="wdm-form-field">
				<label for="wdm_git_link" class="wdm-label"><?php _e('Git link'); ?></label><br>
				<input type="text" id="wdm_git_link" name="wdm_git_link" class="wdm-input">
			</p>
			<p class="wdm-form-field">
				<label for="wdm_sonar_link" class="wdm-label"><?php _e('Sonar link'); ?></label><br>
				<input type="text" id="wdm_sonar_link" name="wdm_sonar_link" class="wdm-input">
			</p>
			<p class="wdm-form-field">
				<label for="wdm_spinup_link" class="wdm-label"><?php _e('Spinup link'); ?></label><br>
				<input type="text" id="wdm_spinup_link" name="wdm_spinup_link" class="wdm-input">
			</p>
			<p class="wdm-form-field">
				<label for="wdm_team_name" class="wdm-label"><?php _e('Team Name'); ?></label><br>
				<select id="wdm_team_name" name="wdm_team_name" class="wdm-input">
					<option value="Orion" leader_name="Shamali">Orion</option>
					<option value="Phoenix" leader_name="Shruti">Phoenix</option>
					<option value="Cygnus" leader_name="Akshay">Cygnus</option>
					<option value="Volans" leader_name="Nikhil">Volans</option>
					<option value="Techops" leader_name="Foram">Techops</option>
				</select>
			</p>
			<p>
				<button class="wdm-submit-btn"><?php _e('Submit'); ?></button>
			</p>
		</div>
		<?php
        return ob_get_clean();  // Return the buffered content (form HTML)
    }

    public function addProject($projectName, $team_name)
    {
        // Base API URL
        $baseUrl = 'https://app.trackingtime.co/api/v4/projects/add';

        $team_name_parameter = '';
        // if ($team_name && $team_name == 'Orion'){
        // 	$team_name_parameter = '&custom_fields=[{"id":"824992","value":"33327"}]';
        // }
        // else if ($team_name && $team_name == 'Phoenix'){
        // 	$team_name_parameter = '&custom_fields=[{"id":"824992","value":"33328"}]';
        // }

        foreach (get_option('wdm_team_settings') as $team) {
            if (($team['name'] == $team_name) && $team['id']) {
                $team_name_parameter = '&custom_fields=[{"id":"824992","value":"'.$team['id'].'"}]';
                break;
            }
        }

        // URL encode the project name to handle special characters
        $encodedProjectName = urlencode($projectName);

        // Full API URL with the project name as a query parameter
        $url = $baseUrl . '?name=' . $encodedProjectName. '&is_public=true' . $team_name_parameter;

        // Basic Auth token
        $authToken = get_option('tracking_time_api_token');

        if (! $authToken) {
            return false;
        }

        // Initialize cURL
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true); // Set the request method to POST
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Authorization: Basic ' . $authToken,
                'Content-Type: application/json',
            )
        );

        // Execute the API call
        $response      = curl_exec($ch);
        $response_data = json_decode($response, true);
        $error         = curl_errno($ch);

        $status_code = '';
        $project_id  = '';
        $message     = '';

        if ($response_data && isset($response_data['response']) && isset($response_data['response']['status'])) {
            $status_code = $response_data['response']['status'];
            $message     = $response_data['response']['message'];
            if ($status_code == 200) {
                if (isset($response_data['data']) && isset($response_data['data']['id'])) {
                    $project_id = $response_data['data']['id'];
                }
            }
        }

        // error_log('response'.print_r($response_data,true));
        // error_log(print_r($error,true));
        // Close cURL resource
        curl_close($ch);

        $return_value = array(
            'status'     => $status_code,
            'project_id' => $project_id,
            'message'    => $message,
        );

        // error_log(print_r($response_data, true));
        // error_log(print_r($error, true));

        // Check for errors
        if ($error || (! $error && (! $status_code && ! $project_id && ! $message))) {
            return false;
        } else {
            // Print the response
            return $return_value;
        }
    }



    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since 1.0.0
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

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wdm-sites-directory-public.css', array(), $this->version, 'all');
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css', array(), '6.5.0', 'all');
        wp_enqueue_style('wdm-datatablee-button-css', 'https://cdn.datatables.net/buttons/3.2.2/css/buttons.dataTables.css', array(), '6.5.0', 'all');
    }

    public function encryptWithPublicKey($publicKeyBase64, $secretValue)
    {
        // $publicKeyBase64 = "FXkCSdxggP6fV7YBZZC8hE3YPlVMels/kBj9gFn5AD0="; // Replace with the actual public key
        $publicKey = base64_decode($publicKeyBase64);

        // The secret you want to encrypt
        // $secretValue = "http://codequality.wisdmlabs.net:9000";

        // Encrypt the secret using the public key
        if (sodium_crypto_box_publickey_from_secretkey($publicKey) === false) {
            die("Invalid public key provided.");
            error('invalid');
        }

        $encrypted = sodium_crypto_box_seal($secretValue, $publicKey);

        // Convert the encrypted value to Base64 for transmission
        $encryptedBase64 = base64_encode($encrypted);

        error_log('encrypting key');
        error_log($encryptedBase64);
        return $encryptedBase64;
    }
    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts()
    {

        // $this->createSonarProjectSecret('test_auto_2_-_test_auto_three');
        // $this->encryptWithPublicKey('abc','abcc');
        // $this->getGitRepoKeys('test_auto_2_-_test_auto_three');
        // $this->createGitRepoSecret('abc','abc','abc');
        // $this->addFilesToGitRepo('test---test-project-one','sodaanar');

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
        wp_enqueue_style('notify-css', 'https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css');
        wp_enqueue_script('notify-js', 'https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js', array('jquery'), null, true);

        wp_enqueue_script('wdm-swal-js-public', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', array('jquery'), null, true);

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wdm-sites-directory-public.js', array( 'jquery' ), $this->version, false);
        // Enqueue popup.js script
        wp_enqueue_script('popup-js', 'https://cdn.jsdelivr.net/npm/@simondmc/popup-js@1.4.3/popup.min.js', array( 'jquery' ), '1.4.3', true);
        wp_enqueue_style('wdm-datatables-css', 'https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css');

        wp_enqueue_script('wdm-datatables-js', 'https://cdn.datatables.net/2.2.2/js/dataTables.min.js', array( 'jquery' ), null, true);
        wp_enqueue_script('wdm-swal-js', 'https://unpkg.com/sweetalert/dist/sweetalert.min.js', array( 'jquery' ), null, true);

        wp_enqueue_script('datatables-buttons', 'https://cdn.datatables.net/buttons/3.2.2/js/dataTables.buttons.js', array('jquery'), null, true);
        wp_enqueue_script('datatables-buttons-dt', 'https://cdn.datatables.net/buttons/3.2.2/js/buttons.dataTables.js', array('datatables-buttons'), null, true);
        wp_enqueue_script('jszip', 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js', array(), null, true);
        wp_enqueue_script('pdfmake', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js', array(), null, true);
        wp_enqueue_script('vfs_fonts', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js', array('pdfmake'), null, true);
        wp_enqueue_script('buttons-html5', 'https://cdn.datatables.net/buttons/3.2.2/js/buttons.html5.min.js', array('datatables-buttons-dt', 'jszip', 'pdfmake', 'vfs_fonts'), null, true);
        wp_enqueue_script('buttons-print', 'https://cdn.datatables.net/buttons/3.2.2/js/buttons.print.min.js', array('datatables-buttons-dt'), null, true);

        

        $js_arr = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('information_about_datatable'),
        );
        wp_localize_script($this->plugin_name, 'datatable_info', $js_arr);
    }
}
