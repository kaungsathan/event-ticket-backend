# Roles and Permissions System Guide

This guide provides comprehensive documentation for the roles and permissions system implemented in the Event Ticket Backend.

## 📋 Table of Contents

1. [Overview](#overview)
2. [Available Roles](#available-roles)
3. [Available Permissions](#available-permissions)
4. [Usage Examples](#usage-examples)
5. [Artisan Commands](#artisan-commands)
6. [API Endpoints](#api-endpoints)
7. [Service Classes](#service-classes)
8. [Helper Traits](#helper-traits)
9. [Middleware](#middleware)
10. [Testing](#testing)

## 🎯 Overview

The system uses the Spatie Laravel Permission package to provide a comprehensive role-based access control (RBAC) system with the following features:

- **Role-based permissions**: Users can be assigned roles that inherit permissions
- **Direct permissions**: Users can be assigned permissions directly
- **Hierarchical roles**: Roles have different priority levels
- **API management**: Full CRUD operations via API endpoints
- **Artisan commands**: Command-line management tools
- **Helper methods**: Easy-to-use helper methods and traits
- **Middleware protection**: Route-level access control

## 👥 Available Roles

### Super Admin (`super-admin`)
- **Priority**: Highest (5)
- **Description**: Full system access, can perform any action
- **Permissions**: All permissions
- **Use Case**: System administrators

### Admin (`admin`)
- **Priority**: High (4)
- **Description**: Administrative access to most system features
- **Permissions**: User, role, event, ticket, order, and report management
- **Use Case**: Business administrators, managers

### Event Manager (`event-manager`)
- **Priority**: Medium (3)
- **Description**: Manages events and related tickets
- **Permissions**: Event and ticket management, order viewing, reports
- **Use Case**: Event organizers, content managers

### Customer (`customer`)
- **Priority**: Low (1)
- **Description**: Basic user with limited access
- **Permissions**: View events, create orders
- **Use Case**: End users, event attendees

## 🔐 Available Permissions

### User Management
- `view users` - View user listings and details
- `create users` - Create new user accounts
- `edit users` - Edit existing user accounts
- `delete users` - Delete user accounts

### Role Management
- `view roles` - View role listings and details
- `create roles` - Create new roles
- `edit roles` - Edit existing roles
- `delete roles` - Delete roles

### Event Management
- `view events` - View event listings and details
- `create events` - Create new events
- `edit events` - Edit existing events
- `delete events` - Delete events
- `publish events` - Publish/unpublish events

### Ticket Management
- `view tickets` - View ticket listings and details
- `create tickets` - Create new tickets
- `edit tickets` - Edit existing tickets
- `delete tickets` - Delete tickets
- `validate tickets` - Validate ticket authenticity

### Order Management
- `view orders` - View order listings and details
- `create orders` - Create new orders
- `edit orders` - Edit existing orders
- `delete orders` - Delete orders
- `refund orders` - Process order refunds

### Report Management
- `view reports` - View system reports
- `export reports` - Export reports to various formats

### Settings
- `manage settings` - Manage system settings

## 🚀 Usage Examples

### Basic Permission Checks

```php
// Check if user has specific permission
if ($user->can('create events')) {
    // User can create events
}

// Check if user has any of multiple permissions
if ($user->hasAnyPermission(['edit events', 'delete events'])) {
    // User can edit or delete events
}

// Check if user has all permissions
if ($user->hasAllPermissions(['view events', 'create events'])) {
    // User has both permissions
}
```

### Role-Based Checks

```php
// Check specific role
if ($user->hasRole('admin')) {
    // User is an admin
}

// Check multiple roles
if ($user->hasAnyRole(['admin', 'super-admin'])) {
    // User is admin or super admin
}

// Using helper methods
if ($user->isAdmin()) {
    // User has admin privileges
}

if ($user->isSuperAdmin()) {
    // User is super admin
}
```

### Using Helper Methods

```php
// Get user's primary role
$primaryRole = $user->getPrimaryRole(); // 'super-admin'

// Get role display name
$displayName = $user->getRoleDisplayName(); // 'Super Administrator'

// Check if user can access admin panel
if ($user->canAccessAdmin()) {
    // User can access admin features
}

// Get available actions for a model
$actions = $user->getAvailableActions('events'); // ['view', 'create', 'edit', 'delete']
```

### Using Query Scopes

```php
// Get all admin users
$admins = User::admins()->get();

// Get users who can manage events
$eventManagers = User::canManageEvents()->get();

// Get users by role priority
$usersByPriority = User::byRolePriority()->get();

// Get recently active admins
$recentAdmins = User::recentlyActiveWithRole('admin', 30)->get();
```

## 🛠 Artisan Commands

### Create Role
```bash
# Create a basic role
php artisan role:create "content-manager"

# Create role with permissions
php artisan role:create "moderator" --permissions="view events" --permissions="edit events"
```

### Create Permission
```bash
# Create a new permission
php artisan permission:create "moderate comments"
```

### Assign Role to User
```bash
# Assign role to user by email
php artisan user:assign-role admin@example.com admin
```

### List Roles and Permissions
```bash
# List all roles
php artisan role:list

# List roles with their permissions
php artisan role:list --permissions
```

## 🌐 API Endpoints

### Roles Management

#### Get All Roles
```http
GET /api/roles
Authorization: Bearer {token}
```

#### Create Role
```http
POST /api/roles
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "moderator",
    "permissions": ["view events", "edit events"]
}
```

#### Get Role Details
```http
GET /api/roles/{role}
Authorization: Bearer {token}
```

#### Update Role
```http
PUT /api/roles/{role}
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "senior-moderator",
    "permissions": ["view events", "edit events", "delete events"]
}
```

#### Delete Role
```http
DELETE /api/roles/{role}
Authorization: Bearer {token}
```

#### Get Available Permissions
```http
GET /api/roles/permissions
Authorization: Bearer {token}
```

#### Assign Permissions to Role
```http
POST /api/roles/{role}/permissions/assign
Authorization: Bearer {token}
Content-Type: application/json

{
    "permissions": ["publish events", "manage settings"]
}
```

#### Revoke Permissions from Role
```http
POST /api/roles/{role}/permissions/revoke
Authorization: Bearer {token}
Content-Type: application/json

{
    "permissions": ["delete events"]
}
```

## 🔧 Service Classes

### RolePermissionService

The `RolePermissionService` provides a high-level API for managing roles and permissions:

```php
use App\Services\RolePermissionService;

$service = app(RolePermissionService::class);

// Create role with permissions
$role = $service->createRole('moderator', ['view events', 'edit events']);

// Assign role to user
$service->assignRoleToUser($user, 'moderator');

// Get user's effective permissions
$permissions = $service->getUserEffectivePermissions($user);

// Sync user roles
$service->syncUserRoles($user, ['moderator', 'customer']);

// Get users by role
$admins = $service->getUsersByRole('admin');

// Get permissions by category
$categorizedPermissions = $service->getPermissionsByCategory();
```

## 🎭 Helper Traits

### HasPermissionHelpers

Adds convenient methods to the User model:

```php
// Role checks
$user->isSuperAdmin();
$user->isAdmin();
$user->isEventManager();
$user->isCustomerService();
$user->isCustomer();

// Capability checks
$user->canManageUsers();
$user->canManageEvents();
$user->canManageTickets();
$user->canManageOrders();
$user->canViewReports();

// Utility methods
$user->getPrimaryRole();
$user->getRoleDisplayName();
$user->canAccessAdmin();
$user->getAvailableActions('events');
```

### HasRoleScopes

Adds query scopes for filtering users:

```php
// Get users by role
User::admins()->get();
User::eventManagers()->get();
User::customers()->get();

// Get users by capability
User::canManageEvents()->get();
User::canViewReports()->get();

// Get users with elevated privileges
User::withElevatedPrivileges()->get();

// Get users excluding certain roles
User::excludingRoles(['customer'])->get();

// Order by role priority
User::byRolePriority()->get();
```

## 🛡 Middleware

### Role Middleware

Protect routes based on user roles:

```php
// In routes/web.php or routes/api.php
Route::middleware(['auth', 'role:admin,super-admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
});

// Single role
Route::get('/events/create', [EventController::class, 'create'])
    ->middleware('role:event-manager');
```

### Permission Middleware

Protect routes based on specific permissions:

```php
// Multiple permissions (user needs ANY of them)
Route::middleware(['auth', 'permission:create events,edit events'])->group(function () {
    Route::resource('events', EventController::class);
});

// Single permission
Route::delete('/users/{user}', [UserController::class, 'destroy'])
    ->middleware('permission:delete users');
```

### Registering Middleware

Add to `app/Http/Kernel.php`:

```php
protected $middlewareAliases = [
    // ... other middleware
    'role' => \App\Http\Middleware\RoleMiddleware::class,
    'permission' => \App\Http\Middleware\PermissionMiddleware::class,
];
```

## 🧪 Testing

### Using Factories

Create test users with specific roles:

```php
// Create users with predefined roles
$superAdmin = User::factory()->superAdmin()->create();
$admin = User::factory()->admin()->create();
$eventManager = User::factory()->eventManager()->create();
$customer = User::factory()->customer()->create();

// Create user with custom role
$moderator = User::factory()->withRole('moderator')->create();

// Create user with specific permissions
$user = User::factory()->withPermissions(['view events', 'create orders'])->create();
```

### Testing Permissions

```php
// Test role assignment
$user = User::factory()->create();
$user->assignRole('admin');

$this->assertTrue($user->hasRole('admin'));
$this->assertTrue($user->can('create events'));

// Test permission checks
$this->actingAs($user)
    ->get('/api/events')
    ->assertStatus(200);

// Test unauthorized access
$customer = User::factory()->customer()->create();
$this->actingAs($customer)
    ->post('/api/events', $eventData)
    ->assertStatus(403);
```

### Database Seeding for Tests

```php
// In your test setup
public function setUp(): void
{
    parent::setUp();
    
    // Seed roles and permissions
    $this->seed(PermissionSeeder::class);
    
    // Create test user
    $this->user = User::factory()->admin()->create();
}
```

## 📊 Best Practices

### 1. Use Service Layer
Always use the `RolePermissionService` for complex operations:

```php
// Good
$service = app(RolePermissionService::class);
$service->assignRoleToUser($user, 'admin');

// Avoid direct model manipulation in controllers
$user->assignRole('admin'); // Use this only in simple cases
```

### 2. Check Permissions in Policies

```php
// In EventPolicy.php
public function create(User $user)
{
    return $user->can('create events');
}

// In Controller
public function store(Request $request)
{
    $this->authorize('create', Event::class);
    // ... create event
}
```

### 3. Use Helper Methods

```php
// Good - readable and maintainable
if ($user->canManageEvents()) {
    // Handle event management
}

// Avoid - harder to maintain
if ($user->hasAnyPermission(['view events', 'create events', 'edit events', 'delete events'])) {
    // Handle event management
}
```

### 4. Consistent Role Names

- Use kebab-case for role names: `event-manager`, `customer-service`
- Use descriptive permission names: `create events`, `manage settings`
- Group permissions by entity: `view users`, `edit users`, `delete users`

### 5. Error Handling

```php
try {
    $service->assignRoleToUser($user, 'admin');
} catch (\InvalidArgumentException $e) {
    // Handle role assignment errors
    return response()->json(['error' => $e->getMessage()], 422);
} catch (\ModelNotFoundException $e) {
    // Handle not found errors
    return response()->json(['error' => 'User or role not found'], 404);
}
```

## 🔄 Migration and Setup

### Run Migrations and Seeders

```bash
# Run migrations
php artisan migrate

# Seed roles and permissions
php artisan db:seed --class=PermissionSeeder

# Or run all seeders
php artisan db:seed
```

### Create First Super Admin

```bash
# Create super admin user
php artisan user:assign-role superadmin@yourapp.com super-admin
```

This comprehensive roles and permissions system provides a solid foundation for managing access control in your Event Ticket Backend application. The modular design allows for easy extension and customization as your application grows.
