# Masilia Consent Bundle - React Components

React hooks and utilities for integrating cookie consent management in your frontend application.

## Installation

```bash
npm install @masilia/consent-bundle-react
# or
yarn add @masilia/consent-bundle-react
```

## Usage

### Basic Example with Hooks

```tsx
import { useConsent, useConsentPolicy } from '@masilia/consent-bundle-react';

function ConsentBanner() {
  const { status, loading, acceptAll, rejectAll } = useConsent();
  const { policy } = useConsentPolicy();

  if (loading || status?.hasConsent) return null;

  return (
    <div className="consent-banner">
      <h3>Cookie Consent</h3>
      <p>We use cookies to enhance your experience.</p>
      <button onClick={acceptAll}>Accept All</button>
      <button onClick={rejectAll}>Reject Non-Essential</button>
    </div>
  );
}
```

### Custom Preferences Modal

```tsx
import { useState } from 'react';
import { useConsent, useConsentPolicy } from '@masilia/consent-bundle-react';

function ConsentModal({ onClose }) {
  const { updatePreferences } = useConsent();
  const { categories } = useConsentPolicy();
  const [preferences, setPreferences] = useState<Record<string, boolean>>({});

  const handleSave = async () => {
    await updatePreferences(preferences);
    onClose();
  };

  return (
    <div className="consent-modal">
      <h2>Cookie Preferences</h2>
      {categories.map(category => (
        <div key={category.id}>
          <label>
            <input
              type="checkbox"
              checked={preferences[category.id] ?? category.defaultEnabled}
              disabled={category.required}
              onChange={(e) => setPreferences({
                ...preferences,
                [category.id]: e.target.checked
              })}
            />
            {category.name}
          </label>
          <p>{category.description}</p>
        </div>
      ))}
      <button onClick={handleSave}>Save Preferences</button>
    </div>
  );
}
```

### Check Consent for Specific Category

```tsx
import { useConsent } from '@masilia/consent-bundle-react';

function AnalyticsComponent() {
  const { hasConsent } = useConsent();

  if (!hasConsent('analytics')) {
    return <p>Analytics disabled</p>;
  }

  // Load analytics scripts
  return <div>Analytics enabled</div>;
}
```

## API Reference

### `useConsent(apiBaseUrl?: string)`

Hook for managing user consent.

**Returns:**
- `status`: Current consent status
- `loading`: Loading state
- `error`: Error object if any
- `hasConsent(category?)`: Check if user has consent
- `acceptAll()`: Accept all cookies
- `rejectAll()`: Reject non-essential cookies
- `updatePreferences(categories)`: Update specific preferences
- `revokeConsent()`: Revoke all consent
- `refresh()`: Refresh consent status

### `useConsentPolicy(apiBaseUrl?: string)`

Hook for fetching cookie policy data.

**Returns:**
- `policy`: Full policy object
- `categories`: Array of cookie categories
- `services`: Array of third-party services
- `loading`: Loading state
- `error`: Error object if any

### `ConsentApi`

Low-level API client for direct API calls.

```typescript
import { consentApi } from '@masilia/consent-bundle-react';

// Get policy
const policy = await consentApi.getPolicy();

// Get status
const status = await consentApi.getStatus();

// Accept all
await consentApi.acceptAll();

// Save preferences
await consentApi.savePreferences({ analytics: true, marketing: false });
```

## TypeScript Support

Full TypeScript support with type definitions included.

```typescript
import type { 
  CookiePolicy, 
  ConsentStatus, 
  ConsentPreferences 
} from '@masilia/consent-bundle-react';
```

## License

MIT
