# Spatie Laravel Packages Setup

This document outlines the Spatie packages that have been installed and configured in your Laravel event ticket backend project.

## 🎯 Installed Packages

### 1. Laravel Permission (spatie/laravel-permission)
**Purpose:** Role and permission management system
- **Version:** ^6.21
- **Config:** `config/permission.php`
- **Tables:** `permissions`, `roles`, `model_has_permissions`, `model_has_roles`, `role_has_permissions`

### 2. Laravel Activity Log (spatie/laravel-activitylog)
**Purpose:** Activity logging and audit trails
- **Version:** ^4.10
- **Tables:** `activity_log`

### 3. Laravel Query Builder (spatie/laravel-query-builder)
**Purpose:** Advanced API filtering, sorting, and including relationships
- **Version:** ^6.3

## 🔧 Configuration

### User Model Updates
The `User` model has been enhanced with:
- `HasRoles` trait for permission management
- `LogsActivity` trait for audit logging
- Activity log configuration for tracking name and email changes

### Permissions & Roles System
Created comprehensive role system with these roles:
- **Super Admin:** Full system access
- **Admin:** User, role, event, ticket, order, and report management
- **Event Manager:** Event and ticket management, order viewing
- **Customer Service:** User support, ticket validation, order management
- **Customer:** Basic event viewing and order creation

### Sample Permissions
- User Management: `view users`, `create users`, `edit users`, `delete users`
- Role Management: `view roles`, `create roles`, `edit roles`, `delete roles`
- Event Management: `view events`, `create events`, `edit events`, `delete events`, `publish events`
- Ticket Management: `view tickets`, `create tickets`, `edit tickets`, `delete tickets`, `validate tickets`
- Order Management: `view orders`, `create orders`, `edit orders`, `delete orders`, `refund orders`
- Reports: `view reports`, `export reports`
- Settings: `manage settings`

## 📁 New Files Created

### Models
- `app/Models/Event.php` - Event model with activity logging

### Controllers
- `app/Http/Controllers/Api/EventController.php` - Advanced API controller using Query Builder

### Policies
- `app/Policies/EventPolicy.php` - Authorization policies using Spatie permissions

### Seeders
- `database/seeders/PermissionSeeder.php` - Seeds roles and permissions

### Migrations
- `create_permission_tables` - Permission system tables
- `create_activity_log_table` - Activity logging tables
- `create_events_table` - Example events table

## 🚀 Usage Examples

### Using Permissions in Controllers
```php
// Check permissions using policies
$this->authorize('viewAny', Event::class);

// Direct permission check
if ($user->can('create events')) {
    // User can create events
}

// Role check
if ($user->hasRole('admin')) {
    // User is an admin
}
```

### Using Query Builder in APIs
```php
$events = QueryBuilder::for(Event::class)
    ->allowedFilters(['title', 'location', 'is_published'])
    ->allowedSorts(['title', 'start_date', 'price'])
    ->allowedIncludes(['creator'])
    ->defaultSort('-created_at')
    ->paginate(15);
```

### Activity Logging
```php
// Automatic logging (configured in model)
$event = Event::create($data); // Logged automatically

// Manual logging
activity()
    ->performedOn($event)
    ->causedBy(auth()->user())
    ->log('Custom event action');

// Retrieving activity logs
$activities = Activity::forSubject($event)->get();
```

### API Usage Examples

#### Filter Events
```
GET /api/events?filter[title]=Laravel&filter[is_published]=1
```

#### Sort Events
```
GET /api/events?sort=-start_date,title
```

#### Include Relationships
```
GET /api/events?include=creator
```

#### Combine All
```
GET /api/events?filter[location]=New York&sort=-start_date&include=creator&per_page=10
```

## 🔒 Security Features

### Permission-Based Authorization
- All API endpoints protected with policies
- Fine-grained permission system
- Role inheritance and management

### Activity Logging
- Automatic change tracking on models
- User attribution for all activities
- Detailed audit trails

### Query Protection
- Whitelisted filters and sorts only
- Prevents SQL injection through structured queries
- Rate limiting ready

## 🛠 Next Steps

1. **Assign Roles to Users:**
   ```php
   $user->assignRole('admin');
   $user->givePermissionTo('create events');
   ```

2. **Add API Routes:**
   ```php
   Route::apiResource('events', EventController::class);
   Route::post('events/{event}/toggle-publish', [EventController::class, 'togglePublish']);
   ```

3. **Customize Permissions:**
   - Modify `PermissionSeeder.php` to add/remove permissions
   - Update policies as needed

4. **Extend Activity Logging:**
   - Add `LogsActivity` trait to other models
   - Configure specific fields to track

5. **Advanced Query Builder:**
   - Add custom filters and sorts
   - Implement search functionality
   - Add relationship includes

## 📚 Documentation Links

- [Spatie Permission](https://spatie.be/docs/laravel-permission)
- [Spatie Activity Log](https://spatie.be/docs/laravel-activitylog)
- [Spatie Query Builder](https://spatie.be/docs/laravel-query-builder)
