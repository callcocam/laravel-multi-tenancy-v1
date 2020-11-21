<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Call\Tenant\Providers;

use Call\Tenant\Actions\MakeQueueTenantAwareAction;
use Illuminate\Support\ServiceProvider;
use Call\Tenant\Commands\TenantsArtisanCommand;
use Call\Tenant\Concerns\UsesMultitenancyConfig;
use Call\Tenant\Models\Concerns\UsesTenantModel;
use Call\Tenant\Tasks\TasksCollection;
use Call\Tenant\TenantFinder\TenantFinder;

class MultitenancyServiceProvider extends ServiceProvider
{
    use UsesTenantModel, UsesMultitenancyConfig;

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this
                ->registerPublishables();
        }

        $this
            ->bootCommands()
            ->registerTenantFinder()
            ->registerTasksCollection()
            ->configureRequests()
            ->configureQueue();
        $this->publishConfig();
        $this->publishMigrations();
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/multitenancy.php', 'multitenancy'
        );
        $this->mergeConfigFrom(
            __DIR__.'/../../config/database.php', 'database'
        );
    }

    protected function registerPublishables(): self
    {
        return $this;
    }

    protected function determineCurrentTenant(): void
    {
        if (! config('multitenancy.tenant_finder')) {
            return;
        }
        /** @var \Call\Tenant\TenantFinder\TenantFinder $tenantFinder */
        $tenantFinder = app(TenantFinder::class);
        $tenant = $tenantFinder->findForRequest(request());
        optional($tenant)->makeCurrent();
    }

    protected function bootCommands(): self
    {
        $this->commands([
            TenantsArtisanCommand::class,
        ]);

        return $this;
    }

    protected function registerTasksCollection(): self
    {
        $this->app->singleton(TasksCollection::class, function () {
            $taskClassNames = config('multitenancy.switch_tenant_tasks');

            return new TasksCollection($taskClassNames);
        });
        return $this;
    }

    protected function registerTenantFinder(): self
    {

        if (config('multitenancy.tenant_finder')) {
            $this->app->bind(TenantFinder::class, config('multitenancy.tenant_finder'));
        }

        return $this;
    }

    protected function configureRequests(): self
    {
        if (! $this->app->runningInConsole()) {
            $this->determineCurrentTenant();
        }

        return $this;
    }

    protected function configureQueue(): self
    {

        $this
            ->getMultitenancyActionClass(
                'make_queue_tenant_aware_action',
                MakeQueueTenantAwareAction::class
            )
            ->execute();

        return $this;
    }

    /**
     * Publish the config file.
     *
     * @return void
     */
    protected function publishConfig()
    {
        $this->publishes([
            __DIR__.'/../../config/multitenancy.php' => config_path('multitenancy.php'),
            __DIR__.'/../../config/database.php' => config_path('database.php'),
        ], 'tenancy-config');
    }

    /**
     * Publish the migration files.
     *
     * @return void
     */
    protected function publishMigrations()
    {
        $this->publishes([
            __DIR__.'/../../database/migrations/' => database_path('migrations'),
        ], 'tenancy-migrations');

    }
}
