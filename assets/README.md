# Masilia Consent Bundle - React Components

Beautiful, accessible React components and hooks for GDPR-compliant cookie consent management.

## Features

✅ Ready-to-use ConsentBanner and PreferencesModal components  
✅ React hooks for consent management  
✅ TypeScript support  
✅ Accessible (WCAG AA)  
✅ Responsive design  
✅ Light/Dark themes  
✅ Customizable styling  

## Installation

```bash
npm install @masilia/consent-bundle-react react react-dom
# or
yarn add @masilia/consent-bundle-react react react-dom
```

## Quick Start

### Option 1: Use Pre-built Components

```tsx
import { ConsentBanner } from '@masilia/consent-bundle-react';
import '@masilia/consent-bundle-react/dist/styles/ConsentBanner.css';
import '@masilia/consent-bundle-react/dist/styles/PreferencesModal.css';

function App() {
  return (
    <div>
      {/* Your app content */}
      <ConsentBanner 
        position="bottom" 
        theme="light"
        primaryColor="#007bff"
      />
    </div>
  );
}
```

### Option 2: Auto-Initialize

```html
<!-- Add to your HTML -->
<div 
  id="masilia-consent-banner" 
  data-position="bottom"
  data-theme="light"
></div>

<script src="/path/to/consent-bundle.js"></script>
```

### Option 3: Manual Initialization

```typescript
import { initConsentBanner } from '@masilia/consent-bundle-react';

initConsentBanner({
  position: 'bottom',
  theme: 'light',
  primaryColor: '#007bff',
});
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

## Components

### `ConsentBanner`

Pre-built consent banner component.

**Props:**
```typescript
interface ConsentBannerProps {
  position?: 'top' | 'bottom';
  theme?: 'light' | 'dark';
  primaryColor?: string;
  onAcceptAll?: () => void;
  onRejectAll?: () => void;
  onSavePreferences?: () => void;
}
```

**Example:**
```tsx
<ConsentBanner
  position="bottom"
  theme="dark"
  primaryColor="#ff6b6b"
  onAcceptAll={() => console.log('Accepted')}
/>
```

### `PreferencesModal`

Detailed preferences modal with category toggles.

**Props:**
```typescript
interface PreferencesModalProps {
  isOpen: boolean;
  onClose: () => void;
  onSave?: () => void;
  theme?: 'light' | 'dark';
  primaryColor?: string;
}
```

**Example:**
```tsx
const [showModal, setShowModal] = useState(false);

<PreferencesModal
  isOpen={showModal}
  onClose={() => setShowModal(false)}
  theme="light"
/>
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
