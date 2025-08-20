# Quick Start Guide: Roles and Permissions

This guide shows you how to quickly set up and use the roles and permissions system.

## 🚀 Quick Setup

### 1. Run Migrations and Seeders
```bash
# Run the migrations
php artisan migrate

# Seed roles and permissions
php artisan db:seed --class=PermissionSeeder

# Or run all seeders (includes test users)
php artisan db:seed
```

### 2. Create Your First Admin User
```bash
# If you have an existing user
php artisan user:assign-role admin@yourapp.com super-admin

# Or create using factory in tinker
php artisan tinker
>>> User::factory()->superAdmin()->create(['email' => 'admin@yourapp.com', 'name' => 'Administrator'])
```

## 📝 Basic Usage Examples

### In Your Controllers

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        // Check if user can view events
        if (!$request->user()->can('view events')) {
            abort(403, 'You do not have permission to view events');
        }

        // Your logic here
        return response()->json(['events' => []]);
    }

    public function store(Request $request)
    {
        // Using authorization
        $this->authorize('create', Event::class); // Uses EventPolicy

        // Or direct permission check
        if (!$request->user()->can('create events')) {
            abort(403, 'You cannot create events');
        }

        // Your logic here
    }

    public function adminPanel(Request $request)
    {
        // Check if user can access admin features
        if (!$request->user()->canAccessAdmin()) {
            abort(403, 'Admin access required');
        }

        // Admin logic here
    }
}
```

### In Your Blade Templates

```blade
{{-- Check user role --}}
@if(auth()->user()->isAdmin())
    <div class="admin-panel">
        <h2>Admin Controls</h2>
        <!-- Admin-only content -->
    </div>
@endif

{{-- Check specific permission --}}
@can('create events')
    <a href="{{ route('events.create') }}" class="btn btn-primary">
        Create Event
    </a>
@endcan

{{-- Check role using helper --}}
@if(auth()->user()->canManageEvents())
    <div class="event-management">
        <!-- Event management tools -->
    </div>
@endif
```

### In Your Routes

```php
<?php

// routes/api.php

use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\RoleController;

// Public routes
Route::get('/events', [EventController::class, 'index']);

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    
    // Role-based protection
    Route::middleware(['role:admin,super-admin'])->group(function () {
        Route::apiResource('roles', RoleController::class);
        Route::post('roles/{role}/permissions/assign', [RoleController::class, 'assignPermissions']);
    });
    
    // Permission-based protection
    Route::middleware(['permission:create events'])->group(function () {
        Route::post('/events', [EventController::class, 'store']);
    });
    
    // Event managers and admins can manage events
    Route::middleware(['role:event-manager,admin,super-admin'])->group(function () {
        Route::put('/events/{event}', [EventController::class, 'update']);
        Route::delete('/events/{event}', [EventController::class, 'destroy']);
    });
});
```

## 🎯 Common Use Cases

### 1. Assign Role to New User
```php
// In your UserController or registration logic
$user = User::create($userData);
$user->assignRole('customer'); // Default role for new users
```

### 2. Check User Capabilities
```php
$user = auth()->user();

// Check if user can perform actions
if ($user->canManageEvents()) {
    // Show event management interface
}

if ($user->canViewReports()) {
    // Show reports menu
}

// Get user's available actions for a model
$availableActions = $user->getAvailableActions('events');
// Returns: ['view', 'create', 'edit'] (based on permissions)
```

### 3. Query Users by Role
```php
// Get all admins
$admins = User::admins()->get();

// Get event managers
$eventManagers = User::eventManagers()->get();

// Get users who can manage events
$eventUsers = User::canManageEvents()->get();

// Get users with elevated privileges
$privilegedUsers = User::withElevatedPrivileges()->get();
```

### 4. Role Management via Service
```php
use App\Services\RolePermissionService;

$service = app(RolePermissionService::class);

// Create a new role
$role = $service->createRole('content-manager', [
    'view events',
    'create events',
    'edit events'
]);

// Assign role to user
$service->assignRoleToUser($user, 'content-manager');

// Get user permissions grouped by category
$permissions = $service->getUserEffectivePermissions($user);
```

## 🔧 Management Commands

### Create Roles and Permissions
```bash
# Create a new role
php artisan role:create "content-manager"

# Create role with permissions
php artisan role:create "moderator" --permissions="view events" --permissions="edit events"

# Create a new permission
php artisan permission:create "moderate comments"
```

### Assign Roles
```bash
# Assign role to user by email
php artisan user:assign-role john@example.com admin
php artisan user:assign-role jane@example.com event-manager
```

### List Roles and Permissions
```bash
# List all roles
php artisan role:list

# List roles with their permissions
php artisan role:list --permissions
```

## 🎨 Frontend Integration

### API Usage Examples

```javascript
// Get user's roles and permissions
const getUserProfile = async () => {
    const response = await fetch('/api/user/profile', {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    });
    
    const user = await response.json();
    
    // Check if user can access admin
    if (user.can_access_admin) {
        showAdminMenu();
    }
    
    // Show available actions
    user.available_actions.events.forEach(action => {
        showEventAction(action); // 'view', 'create', 'edit', etc.
    });
};

// Manage roles via API
const assignRole = async (userId, roleName) => {
    await fetch(`/api/users/${userId}/roles`, {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ role: roleName })
    });
};
```

### Vue.js Example
```vue
<template>
    <div>
        <!-- Show create button only if user can create events -->
        <button v-if="canCreateEvents" @click="createEvent">
            Create Event
        </button>
        
        <!-- Admin panel for admins only -->
        <AdminPanel v-if="isAdmin" />
        
        <!-- Role badge -->
        <span class="badge" :class="roleClass">
            {{ userRoleDisplay }}
        </span>
    </div>
</template>

<script>
export default {
    computed: {
        canCreateEvents() {
            return this.user.permissions.includes('create events');
        },
        
        isAdmin() {
            return this.user.roles.includes('admin') || this.user.roles.includes('super-admin');
        },
        
        userRoleDisplay() {
            return this.user.primary_role_display; // 'Administrator', 'Event Manager', etc.
        },
        
        roleClass() {
            const roleClasses = {
                'super-admin': 'badge-danger',
                'admin': 'badge-warning',
                'event-manager': 'badge-info',
                'customer-service': 'badge-secondary',
                'customer': 'badge-light'
            };
            return roleClasses[this.user.primary_role] || 'badge-light';
        }
    }
};
</script>
```

## 📊 Testing Examples

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Database\Seeders\PermissionSeeder;

class RolePermissionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    public function test_admin_can_create_events()
    {
        $admin = User::factory()->admin()->create();
        
        $this->actingAs($admin)
            ->post('/api/events', ['title' => 'Test Event'])
            ->assertStatus(201);
    }

    public function test_customer_cannot_create_events()
    {
        $customer = User::factory()->customer()->create();
        
        $this->actingAs($customer)
            ->post('/api/events', ['title' => 'Test Event'])
            ->assertStatus(403);
    }

    public function test_role_helper_methods()
    {
        $admin = User::factory()->admin()->create();
        
        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->canManageEvents());
        $this->assertTrue($admin->canAccessAdmin());
        $this->assertEquals('Administrator', $admin->getRoleDisplayName());
    }
}
```

This quick start guide should get you up and running with the roles and permissions system quickly! For more detailed documentation, see the complete `ROLES_PERMISSIONS_GUIDE.md` file.

