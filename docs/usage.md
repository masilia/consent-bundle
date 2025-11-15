# Usage Guide

## Backend Usage

### Checking User Consent

```php
use Masilia\ConsentBundle\Service\ConsentManager;

class MyController
{
    public function __construct(
        private ConsentManager $consentManager
    ) {}

    public function index(): Response
    {
        // Check if user has consent for analytics
        if ($this->consentManager->hasConsent('analytics')) {
            // Load analytics scripts
        }

        // Get all consent preferences
        $preferences = $this->consentManager->getConsentPreferences();
        
        return $this->render('page.html.twig', [
            'hasAnalyticsConsent' => $this->consentManager->hasConsent('analytics'),
        ]);
    }
}
```

### Managing Consent Programmatically

```php
use Masilia\ConsentBundle\Service\ConsentManager;
use Symfony\Component\HttpFoundation\Response;

// Accept all cookies
$response = new Response();
$consentManager->acceptAll($response);

// Reject non-essential cookies
$response = new Response();
$consentManager->rejectNonEssential($response);

// Update specific preferences
$response = new Response();
$consentManager->updatePreferences([
    'essential' => true,
    'analytics' => true,
    'marketing' => false,
    'preferences' => true,
], $response);

// Revoke all consent
$response = new Response();
$consentManager->revokeConsent($response);
```

### Script Injection

```php
use Masilia\ConsentBundle\Service\ScriptInjectionService;

class MyController
{
    public function __construct(
        private ScriptInjectionService $scriptInjection
    ) {}

    public function index(): Response
    {
        $policy = $this->consentManager->getActivePolicy();
        $analyticsCategory = $policy->getCategories()->filter(
            fn($cat) => $cat->getIdentifier() === 'analytics'
        )->first();

        // Get scripts for category
        $scripts = $this->scriptInjection->getScriptsForCategory($analyticsCategory);
        
        // Generate script tags
        $scriptTags = $this->scriptInjection->generateScriptTags($analyticsCategory);
        
        return $this->render('page.html.twig', [
            'analyticsScripts' => $scriptTags,
        ]);
    }
}
```

### Listening to Consent Events

```php
use Masilia\ConsentBundle\Event\ConsentChangedEvent;
use Masilia\ConsentBundle\Event\ConsentEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConsentEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ConsentEvents::CONSENT_CHANGED => 'onConsentChanged',
            ConsentEvents::CONSENT_REVOKED => 'onConsentRevoked',
        ];
    }

    public function onConsentChanged(ConsentChangedEvent $event): void
    {
        if ($event->hasConsentChanged('analytics')) {
            if ($event->isConsentGranted('analytics')) {
                // User enabled analytics
                // Initialize tracking
            } else {
                // User disabled analytics
                // Clean up tracking
            }
        }
    }

    public function onConsentRevoked(ConsentChangedEvent $event): void
    {
        // User revoked all consent
        // Clean up all tracking
    }
}
```

## Frontend Usage (React)

### Basic Consent Banner

```tsx
import { useConsent } from '@masilia/consent-bundle-react';

export function ConsentBanner() {
  const { status, loading, acceptAll, rejectAll } = useConsent();

  if (loading || status?.hasConsent) {
    return null;
  }

  return (
    <div className="fixed bottom-0 left-0 right-0 bg-gray-900 text-white p-4">
      <div className="container mx-auto flex items-center justify-between">
        <div className="flex-1">
          <h3 className="text-lg font-semibold mb-2">Cookie Consent</h3>
          <p className="text-sm">
            We use cookies to enhance your experience. 
            By continuing to visit this site you agree to our use of cookies.
          </p>
        </div>
        <div className="flex gap-2 ml-4">
          <button
            onClick={rejectAll}
            className="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded"
          >
            Reject Non-Essential
          </button>
          <button
            onClick={acceptAll}
            className="px-4 py-2 bg-blue-600 hover:bg-blue-500 rounded"
          >
            Accept All
          </button>
        </div>
      </div>
    </div>
  );
}
```

### Preferences Modal

```tsx
import { useState } from 'react';
import { useConsent, useConsentPolicy } from '@masilia/consent-bundle-react';

export function ConsentModal({ isOpen, onClose }) {
  const { updatePreferences, status } = useConsent();
  const { categories, loading } = useConsentPolicy();
  const [preferences, setPreferences] = useState<Record<string, boolean>>(
    status?.preferences?.categories || {}
  );

  const handleSave = async () => {
    await updatePreferences(preferences);
    onClose();
  };

  if (!isOpen || loading) return null;

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div className="bg-white rounded-lg p-6 max-w-2xl w-full max-h-[80vh] overflow-y-auto">
        <h2 className="text-2xl font-bold mb-4">Cookie Preferences</h2>
        
        {categories.map(category => (
          <div key={category.id} className="mb-4 p-4 border rounded">
            <div className="flex items-start justify-between">
              <div className="flex-1">
                <h3 className="font-semibold text-lg">
                  {category.name}
                  {category.required && (
                    <span className="ml-2 text-xs bg-red-100 text-red-800 px-2 py-1 rounded">
                      Required
                    </span>
                  )}
                </h3>
                <p className="text-sm text-gray-600 mt-1">
                  {category.description}
                </p>
                
                {category.cookies.length > 0 && (
                  <details className="mt-2">
                    <summary className="text-sm text-blue-600 cursor-pointer">
                      View cookies ({category.cookies.length})
                    </summary>
                    <ul className="mt-2 text-xs space-y-1">
                      {category.cookies.map((cookie, idx) => (
                        <li key={idx} className="text-gray-500">
                          <strong>{cookie.name}</strong> - {cookie.purpose}
                        </li>
                      ))}
                    </ul>
                  </details>
                )}
              </div>
              
              <label className="flex items-center ml-4">
                <input
                  type="checkbox"
                  checked={preferences[category.id] ?? category.defaultEnabled}
                  disabled={category.required}
                  onChange={(e) => setPreferences({
                    ...preferences,
                    [category.id]: e.target.checked
                  })}
                  className="w-5 h-5"
                />
              </label>
            </div>
          </div>
        ))}
        
        <div className="flex justify-end gap-2 mt-6">
          <button
            onClick={onClose}
            className="px-4 py-2 border rounded hover:bg-gray-50"
          >
            Cancel
          </button>
          <button
            onClick={handleSave}
            className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500"
          >
            Save Preferences
          </button>
        </div>
      </div>
    </div>
  );
}
```

### Conditional Rendering Based on Consent

```tsx
import { useConsent } from '@masilia/consent-bundle-react';

export function AnalyticsComponent() {
  const { hasConsent } = useConsent();

  if (!hasConsent('analytics')) {
    return (
      <div className="p-4 bg-yellow-50 border border-yellow-200 rounded">
        <p>Analytics are disabled. Enable them in your cookie preferences.</p>
      </div>
    );
  }

  return (
    <div>
      {/* Your analytics dashboard */}
    </div>
  );
}
```

## Twig Templates

### Display Consent Status

```twig
{% if consent_manager.hasConsent('analytics') %}
    {# Load analytics scripts #}
    {{ consent_scripts('analytics')|raw }}
{% endif %}
```

### Consent Banner (Twig)

```twig
{% if not consent_manager.hasConsent() %}
<div class="consent-banner">
    <h3>Cookie Consent</h3>
    <p>We use cookies to enhance your experience.</p>
    <div id="consent-banner-root"></div>
</div>
{% endif %}
```

## CLI Commands

### Import Policy

```bash
# Import and activate
php bin/console masilia:consent:import cookies.json --activate

# Import without activating
php bin/console masilia:consent:import cookies.json

# Force overwrite existing version
php bin/console masilia:consent:import cookies.json --force --activate
```

### Export Policy

```bash
# Export active policy
php bin/console masilia:consent:export output.json --pretty

# Export specific version
php bin/console masilia:consent:export output.json --version=1.0.0 --pretty
```

## Admin Interface

### Managing Policies

1. Navigate to `/admin/consent/policy`
2. View list of all policies
3. Click "View" to see policy details
4. Click "Activate" to make a policy active
5. Click "Delete" to remove inactive policies

### Viewing Statistics

1. Navigate to `/admin/consent/statistics`
2. View consent acceptance rates by category
3. See total consents over time
4. Export consent logs for compliance

## Best Practices

1. **Always check consent before loading scripts**
   ```php
   if ($consentManager->hasConsent('analytics')) {
       // Load analytics
   }
   ```

2. **Use events for cleanup**
   ```php
   public function onConsentRevoked(ConsentChangedEvent $event): void
   {
       // Clear tracking cookies
       // Stop analytics
   }
   ```

3. **Keep policies up to date**
   - Update policy version when adding new cookies
   - Import new policy and activate it
   - Users will be prompted to re-consent

4. **Test consent flow**
   - Test accept all
   - Test reject all
   - Test custom preferences
   - Test script injection

5. **Monitor consent logs**
   - Review statistics regularly
   - Ensure GDPR compliance
   - Export logs for audits
