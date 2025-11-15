# Masilia Consent Bundle

Modern Symfony bundle for GDPR-compliant cookie consent management with React frontend and Ibexa Admin integration.

## Features

- 🍪 **Database-driven** cookie policy management
- ⚛️ **React components** for consent banner and modal
- 🎨 **Ibexa Admin UI** integration for policy management
- 🔒 **GDPR/CCPA compliant** with audit logging
- 📊 **Analytics dashboard** for consent statistics
- 🎯 **Category-based** consent (Essential, Analytics, Marketing, Preferences)
- 🚀 **Dynamic script injection** for third-party services
- 🔌 **Event-driven** architecture for extensibility
- 🌐 **REST API** for frontend integration
- 📝 **Version control** for policy changes

## Requirements

- PHP 8.2 or higher
- Symfony 5.4+ or 6.x or 7.x
- Doctrine ORM 2.14+ or 3.x
- MySQL 5.7+ / PostgreSQL 12+ / MariaDB 10.3+

## Installation

```bash
composer require masilia/consent-bundle
```

## Configuration

```yaml
# config/bundles.php
return [
    // ...
    Masilia\ConsentBundle\MasiliaConsentBundle::class => ['all' => true],
];
```

## Database Setup

```bash
# Run migrations
php bin/console doctrine:migrations:migrate

# Import initial policy from JSON
php bin/console masilia:consent:import config/packages/project/static/data/cookies.json
```

## Usage

### Backend API

```php
// Get consent manager service
$consentManager = $container->get(ConsentManager::class);

// Check if user has consent for a category
if ($consentManager->hasConsent('analytics')) {
    // Load analytics scripts
}

// Get active policy
$policy = $consentManager->getActivePolicy();
```

### React Frontend

```typescript
import { useConsent } from '@masilia/consent-bundle';

function App() {
  const { hasConsent, acceptAll, updatePreferences } = useConsent();
  
  return (
    <ConsentBanner 
      apiEndpoint="/api/consent"
      onAccept={acceptAll}
    />
  );
}
```

### Admin Interface

Navigate to `/admin/consent/policies` in Ibexa Admin to manage:
- Cookie policies
- Categories
- Individual cookies
- Third-party services
- Consent statistics

## Documentation

- [Installation Guide](docs/installation.md)
- [Configuration Reference](docs/configuration.md)
- [API Documentation](docs/api.md)
- [React Components](docs/react-components.md)
- [Ibexa Admin Integration](docs/ibexa-admin.md)

## License

MIT
