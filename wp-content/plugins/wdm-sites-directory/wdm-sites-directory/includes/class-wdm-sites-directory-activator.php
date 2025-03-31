<?php

/**
 * Fired during plugin activation
 *
 * @link       https://https://www.wisdmlabs.com
 * @since      1.0.0
 *
 * @package    Wdm_Sites_Directory
 * @subpackage Wdm_Sites_Directory/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wdm_Sites_Directory
 * @subpackage Wdm_Sites_Directory/includes
 * @author     WisdmLabs <info@wisdmlabs.com>
 */
class Wdm_Sites_Directory_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		global $wpdb;  // Access WordPress database object

		// Define table name based on WordPress table prefix
		$table_name = $wpdb->prefix . 'wdm_site_details';

		// Use prepare() to safely insert the table name into the query
		$query = $wpdb->prepare("SHOW TABLES LIKE %s", $table_name);
		$result = $wpdb->get_results($query);

		// Check if the table exists and create it if not
		if (empty($result)) {
			// Table creation query with necessary fields
			$charset_collate = $wpdb->get_charset_collate();
			$sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        site_name VARCHAR(255) NOT NULL,
        sme_name VARCHAR(255) NOT NULL,
        developer_name VARCHAR(255) NOT NULL,
        client_name VARCHAR(255),
        project_name VARCHAR(255),
        git_link VARCHAR(255),
        team_name VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";

			// Run the table creation query
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);  // Creates/updates the table if necessary
		} else {
			// If the table exists, check for missing columns
			$columns_to_check = [
				'tracking_time_link' => 'VARCHAR(255)',
				'sonar_link' => 'VARCHAR(255)',
				'spinup_link' => 'VARCHAR(255)',
				'spinup_site_id' => 'VARCHAR(255)',
				
			];

			// Get the list of existing columns
			$columns = $wpdb->get_results(
				$wpdb->prepare("DESCRIBE $table_name")
			);

			// Create an array to store the existing column names
			$existing_columns = [];
			foreach ($columns as $column) {
				$existing_columns[] = $column->Field;
			}

			// Loop through the columns to check
			foreach ($columns_to_check as $column => $definition) {
				if (!in_array($column, $existing_columns)) {
					// Add the missing column
					$add_column_query = "ALTER TABLE $table_name ADD $column $definition";
					$wpdb->query($add_column_query);
				} 
			}
		}
	}
}
