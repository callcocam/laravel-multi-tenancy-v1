<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Call\Tenant\Models;

use App\Models\AbstractModel;
use Call\Tenant\Actions\ForgetCurrentTenantAction;
use Call\Tenant\Actions\MakeTenantCurrentAction;
use Call\Tenant\Models\Concerns\UsesLandlordConnection;
use Call\Tenant\TenantCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends AbstractModel
{
    use UsesLandlordConnection, HasFactory;

    protected $fillable = ['name', 'domain', 'prefix', 'database', 'middleware', 'status', 'description'];

    protected $casts = [
        'middleware' => 'array'
    ];

    /**
     * @return $this
     * @throws \Call\Tenant\Exceptions\InvalidConfiguration
     */
    public function makeCurrent(): self
    {
        if ($this->isCurrent()) {
            return $this;
        }

        static::forgetCurrent();

        $this
            ->getMultitenancyActionClass('make_tenant_current_action', MakeTenantCurrentAction::class)
            ->execute($this);

        return $this;
    }

    public function forget(): self
    {
        $this
            ->getMultitenancyActionClass('forget_current_tenant_action', ForgetCurrentTenantAction::class)
            ->execute($this);

        return $this;
    }

    public static function current(): ?self
    {
        $containerKey = config('multitenancy.current_tenant_container_key');

        if (!app()->has($containerKey)) {
            return null;
        }

        return app($containerKey);
    }

    public static function checkCurrent(): bool
    {
        return static::current() !== null;
    }

    public function isCurrent(): bool
    {
        return optional(static::current())->id === $this->id;
    }

    public static function forgetCurrent(): ?Tenant
    {
        $currentTenant = static::current();

        if (is_null($currentTenant)) {
            return null;
        }

        $currentTenant->forget();

        return $currentTenant;
    }

    public function getDatabaseName(): string
    {
        return $this->database;
    }

    public function newCollection(array $models = []): TenantCollection
    {
        return new TenantCollection($models);
    }

    public function execute(callable $callable)
    {
        $originalCurrentTenant = Tenant::current();

        $this->makeCurrent();

        return tap($callable($this), static function () use ($originalCurrentTenant) {
            $originalCurrentTenant
                ? $originalCurrentTenant->makeCurrent()
                : Tenant::forgetCurrent();
        });
    }

    public function getTenantIdAttribute(){
        return $this->getKey();
    }

    public function isTenant()
    {

        return false;
    }
}
