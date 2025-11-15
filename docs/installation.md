# Installation Guide

## Requirements

- PHP 8.2 or higher
- Symfony 5.4+ or 6.x or 7.x
- Doctrine ORM 2.14+ or 3.x
- MySQL 5.7+ / PostgreSQL 12+ / MariaDB 10.3+
- Node.js 18+ (for React components)

## Step 1: Install the Bundle

### Using Composer (Local Path)

```bash
# Add repository to composer.json
composer config repositories.masilia-consent path packages/masilia/consent-bundle

# Require the bundle
composer require masilia/consent-bundle:@dev
```

### Using Composer (Private Repository)

```bash
composer require masilia/consent-bundle
```

## Step 2: Enable the Bundle

Add the bundle to `config/bundles.php`:

```php
<?php

return [
    // ...
    Masilia\ConsentBundle\MasiliaConsentBundle::class => ['all' => true],
];
```

## Step 3: Configure the Bundle

Create `config/packages/masilia_consent.yaml`:

```yaml
masilia_consent:
    storage:
        cookie_name: 'masilia_consent'
        cookie_lifetime: 365  # days
        cookie_path: '/'
        cookie_domain: null
        cookie_secure: true
        cookie_http_only: true
        cookie_same_site: 'lax'
    
    logging:
        enabled: true
        log_ip_address: true
        log_user_agent: true
        anonymize_ip: false
    
    api:
        base_path: '/api/consent'
        cors_enabled: false
        cors_origins: []
    
    admin:
        enabled: true
        route_prefix: '/admin/consent'
```

## Step 4: Update Database Schema

```bash
# Run migrations
php bin/console doctrine:migrations:migrate

# Or create migration manually
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

## Step 5: Import Initial Policy

```bash
# Import from your cookies.json file
php bin/console masilia:consent:import \
    ibexa/config/packages/project/static/data/cookies.json \
    --activate
```

## Step 6: Configure Routes

Add routes to `config/routes.yaml`:

```yaml
masilia_consent:
    resource: '@MasiliaConsentBundle/Resources/config/routes.yaml'
```

## Step 7: Install React Components (Optional)

```bash
cd packages/masilia/consent-bundle/assets
npm install
npm run build
```

## Step 8: Verify Installation

### Check API Endpoints

```bash
# Get active policy
curl http://localhost/api/consent/policy

# Get consent status
curl http://localhost/api/consent/status
```

### Access Admin Interface

Navigate to: `http://localhost/admin/consent/policy`

## Troubleshooting

### Bundle Not Found

Ensure the bundle is properly registered in `config/bundles.php`.

### Database Errors

Check that migrations have been run:
```bash
php bin/console doctrine:migrations:status
```

### API Returns 404

Verify routes are loaded:
```bash
php bin/console debug:router | grep consent
```

### Admin Interface Not Accessible

Ensure you're logged into Ibexa Admin and have proper permissions.

## Next Steps

- [Configuration Reference](configuration.md)
- [API Documentation](api.md)
- [React Components Guide](react-components.md)
- [Ibexa Admin Integration](ibexa-admin.md)
