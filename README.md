

# Ladder 🪜
Ladder simplifies role and permission management by avoiding storing everything in the database. 
Inspired by [Jetstream](https://jetstream.laravel.com/features/teams.html#roles-permissions), 
it offers a static approach, reducing queries and ensuring immutability for easy modifications.

## Install
```bash
composer require eneadm/ladder
```

Once Ladder is installed, create a new LadderServiceProvider to manage roles and permissions. 
You can do so effortlessly with this command:

```bash
php artisan ladder:install
```

Lastly, execute the `migration` command to create a single pivot `user_role` table, assigning roles to users.

```bash
php artisan migrate
```

## Use

Before using Ladder add the `HasRole` trait to your `App\Models\User` model. 
By doing so this trait will provide the necessary methods to manage roles and permissions. 

```php
use Ladder\HasRole;
 
class User extends Authenticatable
{
    use HasRole;
}
```

### `HasRole` trait in detail

```php
// Access all of user's roles...
$user->roles : Illuminate\Database\Eloquent\Collection

// Determine if the user has the given role... 
$user->hasRole(string $role) : bool

// Access all permissions for a given role belonging to the user...
$user->rolePermissions(string $role) : ?array

// Determine if the user role has a given permission...
$user->hasRolePermission(string $role, string $permission) : bool

// Determine if the user has a given permission...
$user->hasPermission(string $permission) : bool
```

### Roles & Permissions
Users receive roles with permissions defined in `App\Providers\LadderServiceProvider` using `Ladder::role` method. This involves specifying a role's slug, name, permissions, and description. For instance, in a blog app, role definitions could be:
```php
Ladder::role('admin', 'Administrator', [
    'create',
    'read',
    'update',
    'delete',
])->description('Administrator users can perform any action.');

Ladder::role('editor', 'Editor', [
    'read',
    'create',
    'update',
])->description('Editor users have the ability to read, create, and update.');
```

### Authorization
For request authorization, utilize the `Ladder\HasRole` trait's hasPermission method to check user's role permissions. Generally, verifying granular permissions is more important than roles. Roles group permissions and are mainly for presentation. Use the `hasPermission` method within authorization policies.
```php
/**
 * Determine whether the user can update a post.
 */
public function update(User $user, Post $post): bool
{
    return $user->hasPermission('post:update');
}
```

## License
Ladder is free software distributed under the terms of the [MIT license](https://github.com/eneadm/ladder/blob/main/LICENSE.md).