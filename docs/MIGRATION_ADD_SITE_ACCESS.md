# Database Migration: Add Site Access to Cookie Policy

## Overview

This migration adds a `site_access` column to the `masilia_cookie_policy` table to support restricting policies to specific Ibexa siteaccesses.

## Migration SQL

### MySQL/MariaDB

```sql
ALTER TABLE masilia_cookie_policy 
ADD COLUMN site_access VARCHAR(100) NULL 
AFTER is_active;
```

### PostgreSQL

```sql
ALTER TABLE masilia_cookie_policy 
ADD COLUMN site_access VARCHAR(100) NULL;
```

## Doctrine Migration Command

If using Doctrine Migrations, generate and run the migration:

```bash
# Generate migration
ddev exec bin/console doctrine:migrations:diff

# Review the generated migration file, then execute
ddev exec bin/console doctrine:migrations:migrate
```

## Manual Migration Steps

1. **Backup your database** before running any migration
2. Connect to your database
3. Run the appropriate SQL command for your database system
4. Verify the column was added:

```sql
DESCRIBE masilia_cookie_policy;
-- or for PostgreSQL:
\d masilia_cookie_policy
```

## Rollback

To rollback this migration:

```sql
ALTER TABLE masilia_cookie_policy DROP COLUMN site_access;
```

## Field Details

- **Column Name**: `site_access`
- **Type**: `VARCHAR(100)`
- **Nullable**: `YES`
- **Default**: `NULL`
- **Purpose**: Store the Ibexa siteaccess name to restrict policy scope

## Notes

- The field is nullable to maintain backward compatibility
- Existing policies will have `NULL` for `site_access`, meaning they apply to all sites
- When `NULL`, the policy is considered global and applies to all siteaccesses
- The dropdown in the admin form is populated from Ibexa's `SiteAccessServiceInterface`

## Testing

After migration, verify:

1. Existing policies still work (with `site_access = NULL`)
2. New policies can be created with a specific siteaccess
3. Policies can be edited to change or remove siteaccess restriction
4. The dropdown shows all available siteaccesses from your Ibexa configuration
