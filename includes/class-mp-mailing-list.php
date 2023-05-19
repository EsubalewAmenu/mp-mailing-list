<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/EsubalewAmenu/
 * @since      1.0.0
 *
 * @package    Mp_Mailing_List
 * @subpackage Mp_Mailing_List/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Mp_Mailing_List
 * @subpackage Mp_Mailing_List/includes
 * @author     Esubalew Amenu <esubalew.a2009@gmail.com>
 */
class Mp_Mailing_List {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Mp_Mailing_List_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'MP_MAILING_LIST_VERSION' ) ) {
			$this->version = MP_MAILING_LIST_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'mp-mailing-list';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Mp_Mailing_List_Loader. Orchestrates the hooks of the plugin.
	 * - Mp_Mailing_List_i18n. Defines internationalization functionality.
	 * - Mp_Mailing_List_Admin. Defines all hooks for the admin area.
	 * - Mp_Mailing_List_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mp-mailing-list-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mp-mailing-list-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-mp-mailing-list-admin.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/controller/promotion/email_types_taxonomy.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/controller/promotion/post_type_promotions.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/controller/promotion/submenu.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/controller/promotion/send.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-mp-mailing-list-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/controller/mp_mails.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/controller/mp_contact.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/controller/cta_page.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/controller/community_content.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/controller/mails_about_us.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/controller/become_moderator.php';

		$this->loader = new Mp_Mailing_List_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Mp_Mailing_List_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Mp_Mailing_List_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Mp_Mailing_List_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$Mp_mail_submenu_Admin = new Mp_mail_submenu_Admin();
		$this->loader->add_action('admin_menu', $Mp_mail_submenu_Admin, 'posts_catalog_submenu_page', 1, 1);

		$Mp_mail_promotions_Admin = new Mp_mail_promotions_Admin();
		$this->loader->add_action('init', $Mp_mail_promotions_Admin, 'Mp_mail_promotion_registration_init', 1, 1);

		$Mp_mail_email_types_taxonomy_Admin = new Mp_mail_email_types_taxonomy_Admin();
		$this->loader->add_action('init', $Mp_mail_email_types_taxonomy_Admin, 'wpdocs_create_Mp_mail_email_types_taxonomy', 1, 1);


		$Mp_mail_send_Admin = new Mp_mail_send_Admin();
		// $this->loader->add_filter('post_updated_messages', $Mp_mail_send_Admin, 'change_publish_button_label', 1, 1);


	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Mp_Mailing_List_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$mp_mails = new Mp_mails();
		$this->loader->add_shortcode( 'mp_mails_list_code', $mp_mails, 'mp_mails_list_code' );
		$this->loader->add_shortcode( 'mp_mails_soon_code', $mp_mails, 'mp_mails_soon_code' );
		
		$Mp_contact = new Mp_contact();
		$this->loader->add_shortcode( 'mp_mails_contact_editors_code', $Mp_contact, 'mp_mails_contact_editors_code' );
		$this->loader->add_action('wp_ajax_mp_mails_insert_contact', $Mp_contact, 'wp_ajax_mp_mails_insert_contact');
		$this->loader->add_action('wp_ajax_nopriv_mp_mails_insert_contact', $Mp_contact, 'wp_ajax_mp_mails_insert_contact');
		
		$this->loader->add_action('wp_ajax_mp_gl_save_new_email', $mp_mails, 'wp_ajax_mp_gl_save_new_email');
		$this->loader->add_action('wp_ajax_nopriv_mp_gl_save_new_email', $mp_mails, 'wp_ajax_mp_gl_save_new_email');
		
		$mp_mails_contact_us = new Mp_mails_about_contact();
		$this->loader->add_shortcode( 'mp_mails_contact_us_code', $mp_mails_contact_us, 'mp_mails_contact_us_code' );
		$this->loader->add_action('wp_ajax_mp_mail_insert_contact', $mp_mails_contact_us, 'wp_ajax_mp_mail_insert_contact');
				
		$mp_mails_cta = new Mp_mails_cta_page();
		$this->loader->add_shortcode( 'mp_cta_code', $mp_mails_cta, 'mp_mails_cta_page' );
		$this->loader->add_shortcode( 'mp_mails_interested_in_code', $mp_mails_cta, 'mp_mails_be_moderator' );
		$this->loader->add_action( 'wp_ajax_mp_mails_save_moderator', $mp_mails_cta, 'wp_ajax_mp_mails_save_moderator' );
		
		
		$mp_mails_cc_form = new Mp_mails_community_content();
		$this->loader->add_shortcode( 'mp_mails_cc_code', $mp_mails_cc_form, 'mp_mails_cc_form' );
		$this->loader->add_action( 'wp_ajax_mp_mail_upload_content', $mp_mails_cc_form, 'wp_ajax_mp_mail_upload_content' );
		
		$mp_mails_moderator = new Mp_mails_moderator();
		$this->loader->add_shortcode( 'mp_mails_become_moderator_code', $mp_mails_moderator, 'mp_mails_become_moderator_code' );
		$this->loader->add_action('wp_ajax_mp_mails_become_moderator', $mp_mails_moderator, 'wp_ajax_mp_mails_become_moderator');
		
		

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Mp_Mailing_List_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
