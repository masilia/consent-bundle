## React Components

The Masilia Consent Bundle provides ready-to-use React components for displaying cookie consent banners and preference modals.

## Installation

```bash
npm install @masilia/consent-bundle-react react react-dom
# or
yarn add @masilia/consent-bundle-react react react-dom
```

## Quick Start

### Option 1: Auto-Initialize (Easiest)

Add the container div to your HTML with data attributes:

```html
<!-- In your Twig template -->
<div 
  id="masilia-consent-banner" 
  data-position="bottom"
  data-theme="light"
  data-primary-color="#007bff"
></div>

<!-- Include the bundle script -->
<script src="/bundles/masiliaconsent/js/consent.js"></script>
```

The banner will automatically initialize on page load.

### Option 2: Manual Initialization

```typescript
import { initConsentBanner } from '@masilia/consent-bundle-react';

// Initialize with options
const cleanup = initConsentBanner({
  position: 'bottom',
  theme: 'light',
  primaryColor: '#007bff',
  onAcceptAll: () => console.log('User accepted all cookies'),
  onRejectAll: () => console.log('User rejected non-essential cookies'),
  onSavePreferences: () => console.log('User saved custom preferences'),
});

// Later, if needed:
cleanup(); // Unmount the banner
```

### Option 3: Use as React Component

```tsx
import React from 'react';
import { ConsentBanner } from '@masilia/consent-bundle-react';

function App() {
  return (
    <div>
      {/* Your app content */}
      
      <ConsentBanner
        position="bottom"
        theme="light"
        primaryColor="#007bff"
        onAcceptAll={() => console.log('Accepted')}
        onRejectAll={() => console.log('Rejected')}
      />
    </div>
  );
}
```

---

## Components

### ConsentBanner

The main cookie consent banner component.

#### Props

```typescript
interface ConsentBannerProps {
  position?: 'top' | 'bottom';        // Banner position (default: 'bottom')
  theme?: 'light' | 'dark';           // Color theme (default: 'light')
  primaryColor?: string;              // Primary color (default: '#007bff')
  onAcceptAll?: () => void;           // Callback when user accepts all
  onRejectAll?: () => void;           // Callback when user rejects non-essential
  onSavePreferences?: () => void;     // Callback when user saves custom preferences
}
```

#### Example

```tsx
<ConsentBanner
  position="bottom"
  theme="dark"
  primaryColor="#ff6b6b"
  onAcceptAll={() => {
    console.log('User accepted all cookies');
    // Track analytics event
  }}
  onRejectAll={() => {
    console.log('User rejected non-essential cookies');
  }}
  onSavePreferences={() => {
    console.log('User customized preferences');
  }}
/>
```

#### Features

- ✅ Auto-hides when user has existing consent
- ✅ Responsive design (mobile, tablet, desktop)
- ✅ Accessible (ARIA labels, keyboard navigation)
- ✅ Smooth animations
- ✅ Three action buttons: Accept All, Reject All, Customize
- ✅ Opens PreferencesModal when "Customize" is clicked

---

### PreferencesModal

A detailed modal for managing cookie preferences by category.

#### Props

```typescript
interface PreferencesModalProps {
  isOpen: boolean;                    // Control modal visibility
  onClose: () => void;                // Callback when modal closes
  onSave?: () => void;                // Callback when preferences are saved
  theme?: 'light' | 'dark';           // Color theme (default: 'light')
  primaryColor?: string;              // Primary color (default: '#007bff')
}
```

#### Example

```tsx
import { useState } from 'react';
import { PreferencesModal } from '@masilia/consent-bundle-react';

function MyComponent() {
  const [showModal, setShowModal] = useState(false);

  return (
    <>
      <button onClick={() => setShowModal(true)}>
        Manage Cookie Preferences
      </button>

      <PreferencesModal
        isOpen={showModal}
        onClose={() => setShowModal(false)}
        onSave={() => {
          console.log('Preferences saved');
          setShowModal(false);
        }}
        theme="light"
        primaryColor="#007bff"
      />
    </>
  );
}
```

#### Features

- ✅ Tabbed interface (Overview + individual categories)
- ✅ Toggle switches for each category
- ✅ Required categories cannot be disabled
- ✅ Cookie details table (name, provider, purpose, expiry)
- ✅ Three actions: Accept All, Reject All, Save Preferences
- ✅ Fully accessible with keyboard navigation
- ✅ Responsive design
- ✅ Smooth transitions

---

## Hooks

### useConsent

Hook for managing consent state and actions.

```typescript
import { useConsent } from '@masilia/consent-bundle-react';

function MyComponent() {
  const {
    status,              // Current consent status
    loading,             // Loading state
    error,               // Error state
    hasConsent,          // Check if user has consent (optionally for specific category)
    acceptAll,           // Accept all cookies
    rejectAll,           // Reject non-essential cookies
    rejectNonEssential,  // Alias for rejectAll
    updatePreferences,   // Update specific preferences
    revokeConsent,       // Revoke all consent
    getPreferences,      // Get current preferences
    refresh,             // Refresh consent status
  } = useConsent();

  return (
    <div>
      {loading && <p>Loading...</p>}
      {error && <p>Error: {error.message}</p>}
      
      {hasConsent() ? (
        <p>You have given consent</p>
      ) : (
        <button onClick={acceptAll}>Accept Cookies</button>
      )}
      
      {hasConsent('analytics') && (
        <p>Analytics enabled</p>
      )}
    </div>
  );
}
```

### useConsentPolicy

Hook for fetching the active cookie policy.

```typescript
import { useConsentPolicy } from '@masilia/consent-bundle-react';

function MyComponent() {
  const { policy, loading, error } = useConsentPolicy();

  if (loading) return <p>Loading policy...</p>;
  if (error) return <p>Error loading policy</p>;
  if (!policy) return <p>No active policy</p>;

  return (
    <div>
      <h2>Cookie Policy v{policy.version}</h2>
      <ul>
        {policy.categories.map(category => (
          <li key={category.id}>
            {category.name}: {category.description}
          </li>
        ))}
      </ul>
    </div>
  );
}
```

---

## Styling

### Default Styles

The components come with default CSS that you can import:

```typescript
import '@masilia/consent-bundle-react/dist/styles/ConsentBanner.css';
import '@masilia/consent-bundle-react/dist/styles/PreferencesModal.css';
```

### Custom Styling

You can override styles using CSS variables or custom classes:

```css
/* Override primary color globally */
:root {
  --primary-color: #ff6b6b;
}

/* Custom banner styles */
.masilia-consent-banner {
  font-family: 'Your Custom Font', sans-serif;
}

.masilia-consent-banner__button--primary {
  border-radius: 20px;
}

/* Custom modal styles */
.masilia-consent-modal {
  max-width: 900px;
}
```

### Theme Customization

```tsx
// Light theme with custom color
<ConsentBanner
  theme="light"
  primaryColor="#10b981"
/>

// Dark theme with custom color
<ConsentBanner
  theme="dark"
  primaryColor="#8b5cf6"
/>
```

---

## Advanced Usage

### Custom API Base URL

```typescript
import { ConsentApi } from '@masilia/consent-bundle-react';

// Create custom API instance
const customApi = new ConsentApi('https://your-api.com');

// Use with hooks
const { acceptAll } = useConsent('https://your-api.com');
```

### Programmatic Control

```typescript
import { useConsent } from '@masilia/consent-bundle-react';

function CookieManager() {
  const { updatePreferences, getPreferences } = useConsent();

  const enableAnalytics = async () => {
    const current = getPreferences();
    await updatePreferences({
      ...current?.categories,
      analytics: true,
    });
  };

  const disableMarketing = async () => {
    const current = getPreferences();
    await updatePreferences({
      ...current?.categories,
      marketing: false,
    });
  };

  return (
    <div>
      <button onClick={enableAnalytics}>Enable Analytics</button>
      <button onClick={disableMarketing}>Disable Marketing</button>
    </div>
  );
}
```

### Custom Event Handlers

```tsx
<ConsentBanner
  onAcceptAll={() => {
    // Track in analytics
    gtag('event', 'consent_accepted', {
      event_category: 'cookies',
      event_label: 'all',
    });
    
    // Show notification
    toast.success('Cookie preferences saved');
  }}
  onRejectAll={() => {
    gtag('event', 'consent_rejected', {
      event_category: 'cookies',
      event_label: 'non_essential',
    });
  }}
  onSavePreferences={() => {
    gtag('event', 'consent_customized', {
      event_category: 'cookies',
      event_label: 'custom',
    });
  }}
/>
```

---

## Integration Examples

### Next.js

```tsx
// app/layout.tsx
import { ConsentBanner } from '@masilia/consent-bundle-react';
import '@masilia/consent-bundle-react/dist/styles/ConsentBanner.css';
import '@masilia/consent-bundle-react/dist/styles/PreferencesModal.css';

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en">
      <body>
        {children}
        <ConsentBanner position="bottom" theme="light" />
      </body>
    </html>
  );
}
```

### Gatsby

```tsx
// gatsby-browser.js
import React from 'react';
import { ConsentBanner } from '@masilia/consent-bundle-react';
import '@masilia/consent-bundle-react/dist/styles/ConsentBanner.css';
import '@masilia/consent-bundle-react/dist/styles/PreferencesModal.css';

export const wrapPageElement = ({ element }) => {
  return (
    <>
      {element}
      <ConsentBanner position="bottom" theme="light" />
    </>
  );
};
```

### Create React App

```tsx
// src/App.tsx
import React from 'react';
import { ConsentBanner } from '@masilia/consent-bundle-react';
import '@masilia/consent-bundle-react/dist/styles/ConsentBanner.css';
import '@masilia/consent-bundle-react/dist/styles/PreferencesModal.css';

function App() {
  return (
    <div className="App">
      {/* Your app content */}
      <ConsentBanner position="bottom" theme="light" />
    </div>
  );
}

export default App;
```

### Symfony + Webpack Encore

```javascript
// assets/app.js
import { initConsentBanner } from '@masilia/consent-bundle-react';
import '@masilia/consent-bundle-react/dist/styles/ConsentBanner.css';
import '@masilia/consent-bundle-react/dist/styles/PreferencesModal.css';

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
  initConsentBanner({
    position: 'bottom',
    theme: 'light',
    primaryColor: '#007bff',
  });
});
```

---

## Accessibility

The components are built with accessibility in mind:

- ✅ **ARIA attributes**: Proper roles, labels, and descriptions
- ✅ **Keyboard navigation**: Full keyboard support
- ✅ **Focus management**: Logical tab order
- ✅ **Screen reader friendly**: Descriptive labels
- ✅ **Reduced motion**: Respects `prefers-reduced-motion`
- ✅ **Color contrast**: WCAG AA compliant
- ✅ **Semantic HTML**: Proper heading hierarchy

### Keyboard Shortcuts

- **Tab**: Navigate between elements
- **Enter/Space**: Activate buttons and toggles
- **Escape**: Close modal
- **Arrow keys**: Navigate tabs in modal

---

## Browser Support

- ✅ Chrome (last 2 versions)
- ✅ Firefox (last 2 versions)
- ✅ Safari (last 2 versions)
- ✅ Edge (last 2 versions)
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

---

## TypeScript Support

Full TypeScript support with type definitions included:

```typescript
import type {
  ConsentBannerProps,
  PreferencesModalProps,
  CookiePolicy,
  CookieCategory,
  ConsentPreferences,
} from '@masilia/consent-bundle-react';

const bannerProps: ConsentBannerProps = {
  position: 'bottom',
  theme: 'light',
  primaryColor: '#007bff',
};
```

---

## Troubleshooting

### Banner Not Showing

**Problem**: Banner doesn't appear on page.

**Solutions**:
1. Check if user already has consent (banner auto-hides)
2. Verify active policy exists in database
3. Check browser console for errors
4. Ensure CSS is imported

### Styles Not Applied

**Problem**: Components look unstyled.

**Solutions**:
1. Import CSS files
2. Check CSS load order
3. Verify no CSS conflicts
4. Check browser dev tools for CSS errors

### Modal Not Opening

**Problem**: Clicking "Customize" doesn't open modal.

**Solutions**:
1. Check `isOpen` prop is controlled correctly
2. Verify no z-index conflicts
3. Check browser console for errors
4. Ensure modal is rendered in DOM

---

## Performance

### Bundle Size

- **ConsentBanner**: ~5KB (gzipped)
- **PreferencesModal**: ~8KB (gzipped)
- **Total CSS**: ~6KB (gzipped)

### Optimization Tips

```typescript
// Code splitting
const ConsentBanner = lazy(() => import('@masilia/consent-bundle-react').then(m => ({ default: m.ConsentBanner })));

// Lazy load modal
const [showModal, setShowModal] = useState(false);
const PreferencesModal = lazy(() => import('@masilia/consent-bundle-react').then(m => ({ default: m.PreferencesModal })));
```

---

## See Also

- [Twig Helpers](twig-helpers.md)
- [Events System](events.md)
- [Configuration Reference](configuration.md)
- [API Documentation](api.md)
