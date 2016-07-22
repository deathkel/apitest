<?php
namespace ApiTest;

use Illuminate\Support\ServiceProvider;

class AlipayServiceProvider extends ServiceProvider
{

	/**
	 * boot process
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__ . '/frontend/static' => base_path('public/api'),
			__DIR__ . '/frontend/blade/index.blade.php'=>base_path('resource/views/api')
		]);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{

	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [

		];
	}
}