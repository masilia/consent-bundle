# Quick Start Guide

Get up and running with Masilia Consent Bundle in 5 minutes.

## 1. Install the Bundle

```bash
cd /home/said/workspace/masilia/imal-back/ibexa

# Add local repository
composer config repositories.masilia-consent path ../packages/masilia/consent-bundle

# Install bundle
composer require masilia/consent-bundle:@dev
```

## 2. Enable the Bundle

Add to `config/bundles.php`:

```php
Masilia\ConsentBundle\MasiliaConsentBundle::class => ['all' => true],
```

## 3. Configure Routes

Add to `config/routes.yaml`:

```yaml
masilia_consent:
    resource: '@MasiliaConsentBundle/Resources/config/routes.yaml'
```

## 4. Create Configuration

Create `config/packages/masilia_consent.yaml`:

```yaml
masilia_consent:
    storage:
        cookie_name: 'imal_consent'
        cookie_lifetime: 365
    logging:
        enabled: true
    api:
        base_path: '/api/consent'
    admin:
        enabled: true
```

## 5. Run Migrations

```bash
# Copy migration to your project
cp ../packages/masilia/consent-bundle/migrations/Version20251113000000.php \
   migrations/

# Run migration
php bin/console doctrine:migrations:migrate --no-interaction
```

## 6. Import Your Cookie Policy

```bash
php bin/console masilia:consent:import \
    config/packages/project/static/data/cookies.json \
    --activate
```

## 7. Test the API

```bash
# Get active policy
curl http://localhost/api/consent/policy | jq

# Get consent status
curl http://localhost/api/consent/status | jq

# Accept all cookies
curl -X POST http://localhost/api/consent/accept | jq
```

## 8. Access Admin Interface

Navigate to: `http://localhost/admin/consent/policy`

You should see your imported cookie policy!

## 9. Integrate React Components (Optional)

```bash
cd ../packages/masilia/consent-bundle/assets
npm install
npm run build
```

Then in your React app:

```tsx
import { useConsent } from '@masilia/consent-bundle-react';

function App() {
  const { status, acceptAll, rejectAll } = useConsent();
  
  if (!status?.hasConsent) {
    return (
      <div className="consent-banner">
        <button onClick={acceptAll}>Accept All</button>
        <button onClick={rejectAll}>Reject</button>
      </div>
    );
  }
  
  return <YourApp />;
}
```

## 10. Use in Your Code

```php
use Masilia\ConsentBundle\Service\ConsentManager;

class MyController
{
    public function __construct(
        private ConsentManager $consentManager
    ) {}

    public function index(): Response
    {
        if ($this->consentManager->hasConsent('analytics')) {
            // Load Google Analytics
        }
        
        return $this->render('page.html.twig');
    }
}
```

## Troubleshooting

### "Class not found" errors
```bash
composer dump-autoload
php bin/console cache:clear
```

### Database errors
```bash
php bin/console doctrine:schema:validate
php bin/console doctrine:migrations:status
```

### API returns 404
```bash
php bin/console debug:router | grep consent
```

## Next Steps

- [Full Installation Guide](docs/installation.md)
- [Usage Examples](docs/usage.md)
- [API Documentation](docs/api.md)
- [React Components](assets/README.md)

## Support

For issues or questions, check the documentation in the `docs/` directory.
