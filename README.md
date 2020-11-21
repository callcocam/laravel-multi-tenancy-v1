#Add 

#Voce pode criar um grupo

```
// in `app\Http\Kernel.php`

protected $middlewareGroups = [
        // ...
        'tenant' => [
            \Call\Tenant\Middleware\NeedsTenant::class,
            \Call\Tenant\Middleware\EnsureValidTenantSession::class,
            \Call\Tenant\Middleware\TenantConnection::class,
        ],
        'landlord' => [
            \Call\Tenant\Middleware\NeedsTenant::class,
            \Call\Tenant\Middleware\EnsureValidTenantSession::class,
            \Call\Tenant\Middleware\LandlordConnection::class,
        ],
    ];
  

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
        \Call\Tenant\Middleware\LandlordConnection::class,
        \Call\Tenant\Middleware\TenantConnection::class,
    ];

```
#Exemplo de uso

```
// in a routes file
Route::get('/', function () {
    return view('welcome');
})->middleware('tenant');

if(!app()->runningInConsole()){
    Route::middleware(app('currentTenant')->middleware)->prefix(app('currentTenant')->prefix)->group(function (){
        Route::middleware('auth')->group(function (){
            Route::get('', \App\Http\Controllers\DashboardController::class)->name('admin');
            Route::get('/dashboard', \App\Http\Controllers\DashboardController::class)->name('dashboard');
            Route::resource('tenants', \App\Http\Controllers\TenantController::class);
        });
    });
    Route::middleware(app('currentTenant')->middleware)->group(function (){
        require __DIR__.'/auth.php';
    });
}
``` 
#Exemplo de uso database

```
//somente copiar o multtenancy.file
php artisan vendor:publish --tag=tenancy-config
//publicar as migrations (Obrigatorio)
php artisan vendor:publish --tag=tenancy-migrations

// in a database file
//para substituir o database file(opcional)
php artisan vendor:publish --tag=tenancy-config --force

//Obrigatorio se não usar os comando ascima
    'tenant' => [
       'driver' => 'mysql',
       'url' => env('DATABASE_URL'),
       'host' => env('DB_HOST', '127.0.0.1'),
       'port' => env('DB_PORT', '3306'),
       'database' => env('DB_DATABASE_TENANT', 'tenants'),
       'username' => env('DB_USERNAME_TENANT', ''),
       'password' => env('DB_PASSWORD_TENANT', ''),
       'unix_socket' => env('DB_SOCKET_TENANT', ''),
       'charset' => 'utf8mb4',
       'collation' => 'utf8mb4_unicode_ci',
       'prefix' => '',
       'prefix_indexes' => true,
       'strict' => true,
       'engine' => null,
       'options' => extension_loaded('pdo_mysql') ? array_filter([
           PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
       ]) : [],
   ],
   'landlord' => [
       'driver' => 'mysql',
       'url' => env('DATABASE_URL'),
       'host' => env('DB_HOST', '127.0.0.1'),
       'port' => env('DB_PORT', '3306'),
       'database' => env('DB_DATABASE', 'landlord'),
       'username' => env('DB_USERNAME', ''),
       'password' => env('DB_PASSWORD', ''),
       'unix_socket' => env('DB_SOCKET', ''),
       'charset' => 'utf8mb4',
       'collation' => 'utf8mb4_unicode_ci',
       'prefix' => '',
       'prefix_indexes' => true,
       'strict' => true,
       'engine' => null,
       'options' => extension_loaded('pdo_mysql') ? array_filter([
           PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
       ]) : [],
   ],

//add varibles connection tenant

DB_DATABASE_TENANT=tenants
DB_USERNAME_TENANT=root
DB_PASSWORD_TENANT=
``` 

#pegar o current tenant 

```
app('currentTenant')
```


#MIGRATING LANDLORD DATABASES
```
php artisan migrate --path=database/migrations/landlord --database=landlord
php artisan migrate:fresh --seed --path=database/migrations/landlord --database=landlord
```

#MIGRATING TENANT DATABASES
```
php artisan tenants:artisan "migrate --database=tenant"
```
#SEEDING TENANT DATABASES

```
php artisan tenants:artisan "migrate --database=tenant --seed"
php artisan tenants:artisan " migrate:fresh --database=tenant --seed"
```
