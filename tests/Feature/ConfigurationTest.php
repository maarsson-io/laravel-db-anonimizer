<?php

namespace Maarsson\LaravelDbAnonimizer\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Maarsson\LaravelDbAnonimizer\LaravelDbAnonimizerServiceProvider;
use Maarsson\LaravelDbAnonimizer\Tests\TestCase;

class ConfigurationTest extends TestCase
{
    private string $configDirectory;

    /**
     * Redirect publishing into an isolated directory without touching the consumer's config.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->configDirectory = sys_get_temp_dir() . '/anonymization-config-' . bin2hex(random_bytes(8));
        File::makeDirectory($this->configDirectory);
        $this->app->useConfigPath($this->configDirectory);
    }

    /**
     * Remove only the temporary publishing directory created by this test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        File::deleteDirectory($this->configDirectory);
        parent::tearDown();
    }

    /**
     * The package provider should be registered in the standalone Laravel test application.
     *
     * @return void
     */
    public function test_provider_is_registered(): void
    {
        $this->assertInstanceOf(
            LaravelDbAnonimizerServiceProvider::class,
            $this->app->getProvider(LaravelDbAnonimizerServiceProvider::class)
        );
    }

    /**
     * Missing consumer configuration should receive the package's defaults.
     *
     * @return void
     */
    public function test_loads_default_configuration(): void
    {
        config(['anonymization' => []]);
        $this->app->register(LaravelDbAnonimizerServiceProvider::class, true);

        $this->assertSame('secret', config('anonymization.default_password'));
        $this->assertSame('_anonymized', config('anonymization.database_suffix'));
        $this->assertSame([], config('anonymization.consistent_tables'));
        $this->assertSame(['failed_jobs', 'password_resets', 'personal_access_tokens'], config('anonymization.truncate_tables'));
        $this->assertSame('null', config('anonymization.anonymize_tables.users.remember_token'));
    }

    /**
     * Consumer overrides should replace whole table sections instead of restoring default rules.
     *
     * @return void
     */
    public function test_preserves_consumer_configuration_and_empty_sections(): void
    {
        $rules = ['custom_table' => ['name' => 'fake_name']];
        config(['anonymization' => [
            'default_password' => 'custom-test-password',
            'truncate_tables' => [],
            'anonymize_tables' => $rules,
        ],
        ]);
        $this->app->register(LaravelDbAnonimizerServiceProvider::class, true);

        $this->assertSame('custom-test-password', config('anonymization.default_password'));
        $this->assertSame([], config('anonymization.truncate_tables'));
        $this->assertSame($rules, config('anonymization.anonymize_tables'));
        $this->assertSame('_anonymized', config('anonymization.database_suffix'));
    }

    /**
     * The publish tag should copy the package configuration to the consumer's config path.
     *
     * @return void
     */
    public function test_publishes_configuration_via_artisan(): void
    {
        $this->app->register(LaravelDbAnonimizerServiceProvider::class, true);
        $this->assertSame(0, Artisan::call('vendor:publish', ['--tag' => 'laravel-db-anonimizer-config', '--no-interaction' => true]));

        $destination = $this->configDirectory . '/anonymization.php';
        $this->assertFileExists($destination);
        $this->assertSame(
            file_get_contents(dirname(__DIR__, 2) . '/config/anonymization.php'),
            file_get_contents($destination)
        );
    }

    /**
     * Publishing without force should preserve a consumer's existing configuration file.
     *
     * @return void
     */
    public function test_publishing_preserves_existing_configuration(): void
    {
        $destination = $this->configDirectory . '/anonymization.php';
        $content = "<?php\n\nreturn ['default_password' => 'existing-test-password'];\n";
        file_put_contents($destination, $content);
        $this->app->register(LaravelDbAnonimizerServiceProvider::class, true);
        $this->assertSame(0, Artisan::call('vendor:publish', ['--tag' => 'laravel-db-anonimizer-config', '--no-interaction' => true]));

        $this->assertSame($content, file_get_contents($destination));
    }
}
