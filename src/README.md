You can tell Landlord to automatically scope by a given Tenant by calling addTenant(), either from the Landlord facade, or by injecting an instance of TenantManager().

You can pass in either a tenant column and id:

Landlord::addTenant('tenant_id', 1);
Or an instance of a Tenant model:

$tenant = Tenant::find(1);

Landlord::addTenant($tenant);
If you pass a Model instance, Landlord will use Eloquent’s getForeignKey() method to decide the tenant column name.

You can add as many tenants as you need to, however Landlord will only allow one of each type of tenant at a time.

To remove a tenant and stop scoping by it, simply call removeTenant():

Landlord::removeTenant('tenant_id');

// Or you can again pass a Model instance:
$tenant = Tenant::find(1);

Landlord::removeTenant($tenant);
You can also check whether Landlord currently is scoping by a given tenant:

// As you would expect by now, $tenant can be either a string column name or a Model instance
Landlord::hasTenant($tenant);
And if for some reason you need to, you can retrieve Landlord's tenants:

// $tenants is a Laravel Collection object, in the format 'tenant_id' => 1
$tenants = Landlord::getTenants();
Setting up your Models
To set up a model to be scoped automatically, simply use the BelongsToTenants trait:


use Illuminate\Database\Eloquent\Model;
use HipsterJazzbo\Landlord\BelongsToTenants;

class ExampleModel extends Model
{
    use BelongsToTenants;
}
If you’d like to override the tenants that apply to a particular model, you can set the $tenantColumns property:


use Illuminate\Database\Eloquent\Model;
use HipsterJazzbo\Landlord\BelongsToTenants;

class ExampleModel extends Model
{
    use BelongsToTenants;
    
    public $tenantColumns = ['tenant_id'];
}
Creating new Tenant scoped Models
When you create a new instance of a Model which uses BelongsToTenants, Landlord will automatically add any applicable Tenant ids, if they are not already set:

// 'tenant_id' will automatically be set by Landlord
$model = ExampleModel::create(['name' => 'whatever']);
Querying Tenant scoped Models
After you've added tenants, all queries against a Model which uses BelongsToTenant will be scoped automatically:

// This will only include Models belonging to the current tenant(s)
ExampleModel::all();

// This will fail with a ModelNotFoundForTenantException if it belongs to the wrong tenant
ExampleModel::find(2);
Note: When you are developing a multi tenanted application, it can be confusing sometimes why you keep getting ModelNotFound exceptions for rows that DO exist, because they belong to the wrong tenant.

Landlord will catch those exceptions, and re-throw them as ModelNotFoundForTenantException, to help you out :)

If you need to query across all tenants, you can use allTenants():

// Will include results from ALL tenants, just for this query
ExampleModel::allTenants()->get()
Under the hood, Landlord uses Laravel's anonymous global scopes. This means that if you are scoping by multiple tenants simultaneously, and you want to exclude one of the for a single query, you can do so:

// Will not scope by 'tenant_id', but will continue to scope by any other tenants that have been set
ExampleModel::withoutGlobalScope('tenant_id')->get();
