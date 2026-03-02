# User Management Reference (Role, Permission, Role-Permission)

## 1) Core Tables and Columns

### `roles`
- `id` (PK, bigint)
- `role_name` (string, unique)

### `permissions`
- `id` (PK, bigint)
- `module_name` (string, unique)

### `role_permission` (pivot)
- `role_id` (FK -> `roles.id`)
- `permission_id` (FK -> `permissions.id`)
- `can_view` (bool, default false)
- `can_add` (bool, default false)
- `can_edit` (bool, default false)
- `can_delete` (bool, default false)
- composite PK: (`role_id`, `permission_id`)

### `user` (managed users table)
- `id` (PK, bigint)
- `name` (string)
- `password` (hashed string)
- `role_id` (FK -> `roles.id`)

---

## 2) SQL Blueprint (MySQL)

```sql
CREATE TABLE roles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role_name VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE permissions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  module_name VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE `user` (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role_id BIGINT UNSIGNED NOT NULL,
  CONSTRAINT fk_user_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

CREATE TABLE role_permission (
  role_id BIGINT UNSIGNED NOT NULL,
  permission_id BIGINT UNSIGNED NOT NULL,
  can_view TINYINT(1) NOT NULL DEFAULT 0,
  can_add TINYINT(1) NOT NULL DEFAULT 0,
  can_edit TINYINT(1) NOT NULL DEFAULT 0,
  can_delete TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (role_id, permission_id),
  CONSTRAINT fk_rp_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
  CONSTRAINT fk_rp_permission FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);
```

---

## 3) Suggested Permission Modules (current project)

```sql
INSERT INTO permissions (module_name) VALUES
('category'),
('sub-category'),
('units'),
('product'),
('supplier'),
('customer'),
('ac-head'),
('purchase'),
('sales'),
('purchase-payment'),
('purchase-return'),
('stock-report'),
('sundry-creditors'),
('users'),
('roles'),
('permissions'),
('role-permissions');
```

---

## 4) Role-Permission Rules Implemented

- Actions: `view`, `add`, `edit`, `delete`
- Access check: action is allowed only if mapping exists with action flag = `1`
- In master modules, legacy fallback to `masters` is still supported
- Current evaluation is additive (OR):
  - if either broad module OR specific module grants action, action is allowed
- User Management routes are admin-only (`role_name = 'admin'`)

---

## 5) Example Role Setup

### Admin full access
```sql
-- for each permission_id
INSERT INTO role_permission (role_id, permission_id, can_view, can_add, can_edit, can_delete)
VALUES (1, ?, 1, 1, 1, 1)
ON DUPLICATE KEY UPDATE can_view=1, can_add=1, can_edit=1, can_delete=1;
```

### View-only user for Units
```sql
INSERT INTO role_permission (role_id, permission_id, can_view, can_add, can_edit, can_delete)
SELECT r.id, p.id, 1, 0, 0, 0
FROM roles r, permissions p
WHERE r.role_name='Only View' AND p.module_name='units'
ON DUPLICATE KEY UPDATE can_view=1, can_add=0, can_edit=0, can_delete=0;
```

---

## 6) Login Session Keys Used

On login:
- `managed_user_id`
- `managed_user_name`
- `role_id`
- `managed_user_role_name`

Used by permission resolver and admin middleware.

---

## 7) Operational Workflow

1. Create role (`roles`)
2. Create module permissions (`permissions`)
3. Map role + module + action flags (`role_permission`)
4. Create user in `user` table with `role_id`
5. Login as user
6. Menu visibility + routes + controller actions follow assigned rights

---

## 8) Quick Seeder (Auto Setup)

This project includes:
- `database/seeders/RolePermissionSetupSeeder.php`

It will:
1. Insert all standard module permissions (if missing)
2. Give full `view/add/edit/delete` for all modules to role `admin` (if exists)

Run:

```bash
php artisan db:seed
```

Or only this seeder:

```bash
php artisan db:seed --class=Database\\Seeders\\RolePermissionSetupSeeder
```
