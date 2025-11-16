# Masilia Consent Bundle - Translations

This directory contains translation files for the Masilia Consent Bundle.

## Available Languages

- **English** (`masilia_consent.en.yaml`)
- **French** (`masilia_consent.fr.yaml`)
- **Arabic** (`masilia_consent.ar.yaml`)

## Translation Domain

All translations use the domain: `masilia_consent`

## Structure

The translation files are organized into the following sections:

### 1. Banner
User-facing cookie consent banner text:
- `banner.title` - Banner title
- `banner.description` - Banner description
- `banner.accept_all` - Accept all button
- `banner.reject_all` - Reject all button
- `banner.customize` - Customize button
- `banner.aria.*` - ARIA labels for accessibility

### 2. Modal
Cookie preferences modal text:
- `modal.title` - Modal title
- `modal.overview` - Overview tab
- `modal.description` - Modal description
- `modal.accept_all` - Accept all button
- `modal.reject_all` - Reject all button
- `modal.save_preferences` - Save button
- `modal.required_badge` - Required badge text
- `modal.cookies_used` - Cookies table title
- `modal.table.*` - Table column headers
- `modal.aria.*` - ARIA labels

### 3. Policy Form
Admin form for cookie policies:
- `policy.form.*` - Form labels, placeholders, validation messages
- `policy.list.*` - List view texts
- `policy.show.*` - Detail view texts
- `policy.messages.*` - Success/error messages

### 4. Category Form
Admin form for cookie categories:
- `category.form.*` - Form labels, placeholders, validation messages
- `category.list.*` - List view texts
- `category.show.*` - Detail view texts
- `category.messages.*` - Success/error messages

### 5. Cookie Form
Admin form for cookies:
- `cookie.form.*` - Form labels, placeholders, validation messages
- `cookie.list.*` - List view texts
- `cookie.show.*` - Detail view texts
- `cookie.messages.*` - Success/error messages

### 6. Third-Party Service Form
Admin form for third-party services:
- `third_party_service.form.*` - Form labels, placeholders, validation messages
- `third_party_service.list.*` - List view texts
- `third_party_service.show.*` - Detail view texts
- `third_party_service.messages.*` - Success/error messages

### 7. Menu
Admin menu items:
- `menu.consent` - Main menu item
- `menu.policies` - Policies submenu
- `menu.categories` - Categories submenu
- `menu.cookies` - Cookies submenu
- `menu.services` - Services submenu

### 8. Common
Common UI elements:
- `common.actions` - Actions label
- `common.save` - Save button
- `common.cancel` - Cancel button
- `common.back` - Back button
- `common.*` - Other common texts

### 9. Errors
Error messages:
- `error.policy_not_found` - Policy not found
- `error.category_not_found` - Category not found
- `error.api_error` - API error
- `error.*` - Other error messages

## Usage in PHP/Twig

### In Forms

```php
use Symfony\Component\Form\Extension\Core\Type\TextType;

$builder->add('version', TextType::class, [
    'label' => 'policy.form.version',
    'translation_domain' => 'masilia_consent',
]);
```

### In Twig Templates

```twig
{{ 'banner.title'|trans({}, 'masilia_consent') }}
{{ 'policy.messages.created'|trans({}, 'masilia_consent') }}
```

### In Controllers

```php
use Symfony\Contracts\Translation\TranslatorInterface;

public function __construct(
    private TranslatorInterface $translator
) {}

public function someAction(): Response
{
    $message = $this->translator->trans(
        'policy.messages.created',
        [],
        'masilia_consent'
    );
    
    $this->addFlash('success', $message);
}
```

## Usage in React Components

The React components currently use hardcoded English strings. To add translation support:

### Option 1: Pass translations as props

```tsx
<ConsentBanner
  translations={{
    title: 'We value your privacy',
    description: '...',
    acceptAll: 'Accept All',
    rejectAll: 'Reject All',
    customize: 'Customize',
  }}
/>
```

### Option 2: Use a translation library

Install a React translation library like `react-i18next`:

```bash
npm install react-i18next i18next
```

Then configure it to load translations from your backend or include them in the build.

## Adding a New Language

1. Copy an existing translation file (e.g., `masilia_consent.en.yaml`)
2. Rename it with the appropriate locale code (e.g., `masilia_consent.de.yaml` for German)
3. Translate all the values (keep the keys unchanged)
4. Commit the new file

Example:

```yaml
# masilia_consent.de.yaml
banner:
  title: 'Wir respektieren Ihre Privatsphäre'
  description: 'Wir verwenden Cookies...'
  accept_all: 'Alle akzeptieren'
  reject_all: 'Alle ablehnen'
  customize: 'Anpassen'
```

## Translation Keys Reference

### Banner Keys
```
banner.title
banner.description
banner.accept_all
banner.reject_all
banner.customize
banner.aria.accept_all
banner.aria.reject_all
banner.aria.customize
```

### Modal Keys
```
modal.title
modal.overview
modal.description
modal.accept_all
modal.reject_all
modal.save_preferences
modal.close
modal.required_badge
modal.cookies_used
modal.table.name
modal.table.provider
modal.table.purpose
modal.table.expiry
modal.aria.toggle
modal.aria.close
```

### Form Keys Pattern
```
{entity}.form.{field}
{entity}.form.{field}_placeholder
{entity}.form.{field}_required
{entity}.form.{field}_max_length
{entity}.form.{field}_format
{entity}.list.{action}
{entity}.show.{field}
{entity}.messages.{action}
```

Where `{entity}` is one of: `policy`, `category`, `cookie`, `third_party_service`

## Best Practices

1. **Always use translation keys** - Never hardcode text in templates or forms
2. **Use descriptive keys** - Keys should clearly indicate what they translate
3. **Keep keys consistent** - Follow the established naming patterns
4. **Provide context** - Add comments for ambiguous translations
5. **Test all languages** - Verify translations display correctly in the UI
6. **Use placeholders** - For dynamic content: `'Hello %name%'|trans({'%name%': user.name})`

## Validation Messages

Validation messages support placeholders:

```yaml
policy:
  form:
    version_max_length: 'Version must not exceed {{ limit }} characters'
```

Usage:
```php
new Assert\Length([
    'max' => 20,
    'maxMessage' => 'policy.form.version_max_length',
])
```

## Contributing

When adding new features:

1. Add translation keys to all language files
2. Use the `masilia_consent` translation domain
3. Follow the existing key structure
4. Update this README if adding new sections

## Support

For translation issues or requests for new languages, please open an issue on the project repository.
