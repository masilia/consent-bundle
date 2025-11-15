# Development Guide

This guide explains how to develop and build the React components for the Masilia Consent Bundle.

## Prerequisites

- Node.js 18+ and npm/yarn
- TypeScript knowledge
- React 18+ knowledge

## Setup

1. **Install dependencies:**

```bash
cd packages/masilia/consent-bundle/assets
npm install
```

2. **Start development mode:**

```bash
npm run dev
```

This will watch for changes and recompile TypeScript files automatically.

## Project Structure

```
assets/
├── src/
│   ├── components/          # React components
│   │   ├── ConsentBanner.tsx
│   │   └── PreferencesModal.tsx
│   ├── hooks/               # React hooks
│   │   ├── useConsent.ts
│   │   └── useConsentPolicy.ts
│   ├── services/            # API client
│   │   └── consentApi.ts
│   ├── styles/              # CSS files
│   │   ├── ConsentBanner.css
│   │   └── PreferencesModal.css
│   ├── types/               # TypeScript types
│   │   └── consent.types.ts
│   ├── index.ts             # Main export
│   └── init.tsx             # Auto-initialization
├── dist/                    # Build output (generated)
├── package.json
├── tsconfig.json
├── rollup.config.js
└── .eslintrc.json
```

## Development Workflow

### 1. Make Changes

Edit files in the `src/` directory:

- **Components**: `src/components/`
- **Hooks**: `src/hooks/`
- **Styles**: `src/styles/`
- **Types**: `src/types/`

### 2. Check Types

```bash
npm run build:types
```

This will generate TypeScript declaration files in `dist/`.

### 3. Lint Code

```bash
npm run lint
```

Fix any linting errors before committing.

### 4. Build for Production

```bash
npm run build
```

This runs:
1. `build:types` - Generate TypeScript declarations
2. `build:js` - Bundle JavaScript with Rollup
3. `build:css` - Copy CSS files to dist

### 5. Clean Build

```bash
npm run clean
npm run build
```

## Build Output

The build process generates:

```
dist/
├── index.js              # CommonJS bundle
├── index.esm.js          # ES Module bundle
├── index.d.ts            # TypeScript declarations
├── init.js               # Auto-init CommonJS
├── init.esm.js           # Auto-init ES Module
└── styles/               # CSS files
    ├── ConsentBanner.css
    └── PreferencesModal.css
```

## Adding New Components

1. **Create component file:**

```tsx
// src/components/MyComponent.tsx
import React from 'react';

export interface MyComponentProps {
  // Props here
}

export const MyComponent: React.FC<MyComponentProps> = (props) => {
  return <div>My Component</div>;
};
```

2. **Create styles (optional):**

```css
/* src/styles/MyComponent.css */
.my-component {
  /* Styles here */
}
```

3. **Export from index:**

```typescript
// src/index.ts
export { MyComponent } from './components/MyComponent';
export type { MyComponentProps } from './components/MyComponent';
```

4. **Import styles in component:**

```tsx
import '../styles/MyComponent.css';
```

## Adding New Hooks

1. **Create hook file:**

```typescript
// src/hooks/useMyHook.ts
import { useState, useEffect } from 'react';

export function useMyHook() {
  const [data, setData] = useState(null);
  
  // Hook logic here
  
  return { data };
}
```

2. **Export from index:**

```typescript
// src/index.ts
export { useMyHook } from './hooks/useMyHook';
```

## TypeScript

### Type Definitions

All types are defined in `src/types/consent.types.ts`:

```typescript
export interface CookiePolicy {
  id: number;
  version: string;
  // ...
}
```

### Strict Mode

The project uses TypeScript strict mode. Ensure:

- No implicit `any` types
- All function parameters are typed
- Return types are explicit
- Null checks are performed

### Type Exports

Export types alongside components:

```typescript
export { MyComponent } from './components/MyComponent';
export type { MyComponentProps } from './components/MyComponent';
```

## Styling

### CSS Organization

- One CSS file per component
- Use BEM naming convention
- Prefix classes with `masilia-consent-`

Example:

```css
.masilia-consent-banner { }
.masilia-consent-banner__header { }
.masilia-consent-banner__button { }
.masilia-consent-banner__button--primary { }
```

### CSS Variables

Use CSS custom properties for theming:

```css
.masilia-consent-banner {
  background: var(--primary-color, #007bff);
}
```

### Responsive Design

Use mobile-first approach:

```css
/* Mobile first */
.masilia-consent-banner {
  padding: 1rem;
}

/* Tablet and up */
@media (min-width: 768px) {
  .masilia-consent-banner {
    padding: 2rem;
  }
}
```

## Testing

### Manual Testing

1. **Create test HTML file:**

```html
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="dist/styles/ConsentBanner.css">
  <link rel="stylesheet" href="dist/styles/PreferencesModal.css">
</head>
<body>
  <div id="root"></div>
  <script type="module">
    import { initConsentBanner } from './dist/init.esm.js';
    initConsentBanner({ position: 'bottom', theme: 'light' });
  </script>
</body>
</html>
```

2. **Serve with local server:**

```bash
npx serve .
```

### Integration Testing

Test with actual Symfony backend:

1. Build the package
2. Copy dist files to Symfony public directory
3. Test in browser

## Publishing

### Pre-publish Checklist

- [ ] All tests pass
- [ ] No linting errors
- [ ] Version bumped in package.json
- [ ] CHANGELOG updated
- [ ] README updated
- [ ] Build successful

### Build for Publishing

```bash
npm run clean
npm run build
```

The `prepublishOnly` script will run automatically before publishing.

### Publish to NPM

```bash
npm publish
```

Or for scoped packages:

```bash
npm publish --access public
```

## Troubleshooting

### TypeScript Errors

**Problem**: Type errors in IDE but build works

**Solution**: Restart TypeScript server or rebuild:
```bash
npm run build:types
```

### Build Fails

**Problem**: Rollup build fails

**Solution**: 
1. Check for syntax errors
2. Ensure all imports are correct
3. Clean and rebuild:
```bash
npm run clean && npm run build
```

### Missing Dependencies

**Problem**: Module not found errors

**Solution**:
```bash
rm -rf node_modules package-lock.json
npm install
```

### CSS Not Applied

**Problem**: Styles not showing in built package

**Solution**: Ensure CSS is copied:
```bash
npm run build:css
```

## Best Practices

### Code Style

- Use functional components
- Use hooks instead of class components
- Keep components small and focused
- Extract reusable logic into hooks
- Use TypeScript for all files

### Performance

- Use `React.memo()` for expensive components
- Use `useCallback()` for event handlers
- Use `useMemo()` for expensive calculations
- Lazy load heavy components

### Accessibility

- Use semantic HTML
- Add ARIA labels
- Support keyboard navigation
- Test with screen readers
- Ensure color contrast

### Documentation

- Add JSDoc comments to public APIs
- Document props with TypeScript
- Include usage examples
- Keep README up to date

## Resources

- [React Documentation](https://react.dev)
- [TypeScript Handbook](https://www.typescriptlang.org/docs/)
- [Rollup Documentation](https://rollupjs.org/)
- [ESLint Rules](https://eslint.org/docs/rules/)

## Getting Help

- Check existing issues in the repository
- Read the main bundle documentation
- Ask in team chat
- Create a new issue with details

## License

MIT
