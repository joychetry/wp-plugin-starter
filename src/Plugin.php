<?php
/**
 * Bootstrap and module registry.
 *
 * @package WpPluginStarter
 */

namespace WpPluginStarter;

use WpPluginStarter\Contracts\Module;

/**
 * Boots the plugin: shared services first, then every enabled module.
 */
final class Plugin {

	/**
	 * Every module the plugin ships. Add new modules here, nowhere else.
	 *
	 * @var list<class-string<Module>>
	 */
	public const MODULES = array();

	/**
	 * The booted instance, so other code can reach the registry.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Module instances keyed by id.
	 *
	 * @var array<string, Module>
	 */
	private array $modules = array();

	/**
	 * Ids of the modules that registered hooks on this request.
	 *
	 * @var list<string>
	 */
	private array $active = array();

	/**
	 * Set up the registry.
	 *
	 * @param Settings $settings       Plugin settings.
	 * @param array    $module_classes Module class names to manage.
	 * @phpstan-param list<class-string<Module>> $module_classes
	 */
	public function __construct( private Settings $settings, array $module_classes = self::MODULES ) {
		foreach ( $module_classes as $class ) {
			$module                         = new $class();
			$this->modules[ $module->id() ] = $module;
		}
	}

	/**
	 * Entry point, hooked to `plugins_loaded` from the main plugin file.
	 */
	public static function boot(): void {
		if ( null !== self::$instance ) {
			return;
		}
		self::$instance = new self( new Settings() );
		self::$instance->register();
	}

	/**
	 * The booted instance, or null before `plugins_loaded`.
	 */
	public static function instance(): ?self {
		return self::$instance;
	}

	/**
	 * Register shared hooks and every enabled module.
	 */
	public function register(): void {
		// Closure so the controller class is only loaded on REST requests.
		add_action( 'rest_api_init', fn() => ( new Rest\SettingsController( $this->settings ) )->register_routes() );

		foreach ( $this->modules as $id => $module ) {
			if ( $module->is_enabled( $this->settings ) ) {
				$module->register();
				$this->active[] = $id;
			}
		}
	}

	/**
	 * All modules keyed by id, enabled or not.
	 *
	 * @return array<string, Module>
	 */
	public function modules(): array {
		return $this->modules;
	}

	/**
	 * Ids of the modules that registered hooks on this request.
	 *
	 * @return list<string>
	 */
	public function active_modules(): array {
		return $this->active;
	}

	/**
	 * Plugin settings.
	 */
	public function settings(): Settings {
		return $this->settings;
	}
}
