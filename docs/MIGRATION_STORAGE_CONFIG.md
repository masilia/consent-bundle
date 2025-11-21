# Database Migration: Add Storage Configuration to Cookie Policy

## Overview

This migration adds cookie storage configuration fields to the `masilia_cookie_policy` table, allowing each policy to define its own cookie settings (name, lifetime, path, domain, security flags).

## Migration SQL

### MySQL/MariaDB

```sql
ALTER TABLE masilia_cookie_policy 
ADD COLUMN cookie_name VARCHAR(100) NOT NULL DEFAULT 'imal_consent' AFTER site_access,
ADD COLUMN cookie_lifetime INT NOT NULL DEFAULT 365 AFTER cookie_name,
ADD COLUMN cookie_path VARCHAR(255) NOT NULL DEFAULT '/' AFTER cookie_lifetime,
ADD COLUMN cookie_domain VARCHAR(255) NULL AFTER cookie_path,
ADD COLUMN cookie_secure BOOLEAN NOT NULL DEFAULT TRUE AFTER cookie_domain,
ADD COLUMN cookie_http_only BOOLEAN NOT NULL DEFAULT TRUE AFTER cookie_secure,
ADD COLUMN cookie_same_site VARCHAR(20) NOT NULL DEFAULT 'lax' AFTER cookie_http_only;
```

### PostgreSQL

```sql
ALTER TABLE masilia_cookie_policy 
ADD COLUMN cookie_name VARCHAR(100) NOT NULL DEFAULT 'imal_consent',
ADD COLUMN cookie_lifetime INTEGER NOT NULL DEFAULT 365,
ADD COLUMN cookie_path VARCHAR(255) NOT NULL DEFAULT '/',
ADD COLUMN cookie_domain VARCHAR(255) NULL,
ADD COLUMN cookie_secure BOOLEAN NOT NULL DEFAULT TRUE,
ADD COLUMN cookie_http_only BOOLEAN NOT NULL DEFAULT TRUE,
ADD COLUMN cookie_same_site VARCHAR(20) NOT NULL DEFAULT 'lax';
```

## Doctrine Migration Command

If using Doctrine Migrations:

```bash
# Generate migration
ddev exec bin/console doctrine:migrations:diff

# Review the generated migration file, then execute
ddev exec bin/console doctrine:migrations:migrate
```

## Manual Migration Steps

1. **Backup your database** before running any migration

2. Connect to your database:
```bash
ddev mysql
# or
ddev exec mysql -u db -p db
```

3. Run the appropriate SQL command for your database system

4. Verify the columns were added:
```sql
DESCRIBE masilia_cookie_policy;
-- or for PostgreSQL:
\d masilia_cookie_policy
```

## Field Details

| Column | Type | Nullable | Default | Purpose |
|--------|------|----------|---------|---------|
| `cookie_name` | VARCHAR(100) | NO | 'imal_consent' | Name of the consent cookie |
| `cookie_lifetime` | INT | NO | 365 | Cookie lifetime in days |
| `cookie_path` | VARCHAR(255) | NO | '/' | Cookie path scope |
| `cookie_domain` | VARCHAR(255) | YES | NULL | Cookie domain (NULL = current domain) |
| `cookie_secure` | BOOLEAN | NO | TRUE | Require HTTPS |
| `cookie_http_only` | BOOLEAN | NO | TRUE | Prevent JavaScript access |
| `cookie_same_site` | VARCHAR(20) | NO | 'lax' | SameSite policy: 'lax', 'strict', 'none' |

## Data Migration (Optional)

If you want to set specific values for existing policies:

```php
<?php
// src/Command/MigratePolicyStorageCommand.php

namespace Masilia\ConsentBundle\Command;

use Masilia\ConsentBundle\Repository\CookiePolicyRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MigratePolicyStorageCommand extends Command
{
    protected static $defaultName = 'masilia:consent:migrate-storage';

    public function __construct(
        private readonly CookiePolicyRepository $policyRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $policies = $this->policyRepository->findAll();
        
        foreach ($policies as $policy) {
            $siteAccess = $policy->getSiteAccess();
            
            // Set cookie name based on siteaccess
            if ($siteAccess === 'site_fr') {
                $policy->setCookieName('imal_consent_fr');
                $policy->setCookiePath('/fr');
            } elseif ($siteAccess === 'site_ar') {
                $policy->setCookieName('imal_consent_ar');
                $policy->setCookiePath('/ar');
            } elseif ($siteAccess === 'africa_integrates') {
                $policy->setCookieName('africa_consent');
                $policy->setCookieDomain('africa-integrates.com');
            } elseif ($siteAccess === 'africa_v2x_hub') {
                $policy->setCookieName('v2x_consent');
            } elseif ($siteAccess === 'static_site') {
                $policy->setCookieName('static_consent');
            } else {
                // Default for main site or global
                $policy->setCookieName('imal_consent');
            }
            
            $io->success(sprintf(
                'Updated policy %s (siteaccess: %s) with cookie name: %s',
                $policy->getVersion(),
                $siteAccess ?? 'global',
                $policy->getCookieName()
            ));
        }
        
        $this->policyRepository->flush();
        
        $io->success('All policies migrated successfully!');
        
        return Command::SUCCESS;
    }
}
```

Run the migration command:
```bash
ddev exec bin/console masilia:consent:migrate-storage
```

## Rollback

To rollback this migration:

```sql
ALTER TABLE masilia_cookie_policy 
DROP COLUMN cookie_name,
DROP COLUMN cookie_lifetime,
DROP COLUMN cookie_path,
DROP COLUMN cookie_domain,
DROP COLUMN cookie_secure,
DROP COLUMN cookie_http_only,
DROP COLUMN cookie_same_site;
```

## Testing

After migration, verify:

1. **Existing policies still work**:
```sql
SELECT id, version, site_access, cookie_name, cookie_lifetime 
FROM masilia_cookie_policy;
```

2. **New policies can be created** with custom storage settings

3. **Form displays all fields** in admin interface

4. **Cookie configuration is applied** correctly on frontend

## Example Configurations

### French Site (GDPR Compliant)
```
cookie_name: imal_consent_fr
cookie_lifetime: 365
cookie_path: /fr
cookie_domain: example.com
cookie_secure: true
cookie_http_only: true
cookie_same_site: lax
```

### Africa Integrates
```
cookie_name: africa_consent
cookie_lifetime: 365
cookie_path: /
cookie_domain: africa-integrates.com
cookie_secure: true
cookie_http_only: true
cookie_same_site: lax
```

### Static Site (Minimal)
```
cookie_name: static_consent
cookie_lifetime: 180
cookie_path: /
cookie_domain: null
cookie_secure: true
cookie_http_only: true
cookie_same_site: strict
```

## Notes

- All fields have sensible defaults, so existing policies will continue to work
- The `cookie_domain` field is nullable - NULL means "use current domain"
- `cookie_same_site` accepts: 'lax', 'strict', or 'none'
- For 'none', `cookie_secure` must be true (HTTPS required)
- These settings are now managed per-policy in the database, not in YAML config

## Next Steps

1. Run the database migration
2. (Optional) Run the data migration command to set siteaccess-specific values
3. Update existing policies via admin interface if needed
4. Test cookie behavior on each siteaccess
5. Remove or deprecate the old YAML storage configuration
