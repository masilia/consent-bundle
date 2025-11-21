# TranslationResolver Service - Dynamic Language Resolution

## Overview

The `TranslationResolver` service has been updated to use **Ibexa's native language system** instead of hardcoded project-specific mappings. This makes the bundle truly reusable across different Ibexa projects.

## Changes Made

### ❌ Before (Hardcoded)

```php
// Hardcoded language mapping - project-specific!
private const SITEACCESS_LANGUAGES = [
    'site' => 'eng-GB',
    'site_fr' => 'fra-FR',
    'site_ar' => 'ar-AE',
    'africa_integrates' => 'eng-GB',
    'africa_v2x_hub' => 'eng-GB',
    'static_site' => 'eng-GB',
];

private const FALLBACK_LANGUAGE = 'eng-GB';

public function getCurrentLanguage(): string
{
    $siteAccessName = $this->siteAccessService->getCurrent()->name;
    return self::SITEACCESS_LANGUAGES[$siteAccessName] ?? self::FALLBACK_LANGUAGE;
}
```

### ✅ After (Dynamic)

```php
// No hardcoded mappings!

public function getCurrentLanguage(): string
{
    // Automatically gets the default language for current siteaccess
    return $this->languageService->getDefaultLanguageCode();
}

private function getFallbackLanguage(): string
{
    $languages = $this->languageService->loadLanguages();
    
    // Return first enabled language as fallback
    foreach ($languages as $language) {
        if ($language->enabled) {
            return $language->languageCode;
        }
    }
    
    // Ultimate fallback
    return 'eng-GB';
}
```

## Benefits

### 1. **Project-Agnostic** 🌍
- No hardcoded siteaccess names
- Works with any Ibexa project configuration
- Bundle is truly reusable

### 2. **Automatic Language Detection** 🔍
- Uses Ibexa's `LanguageService::getDefaultLanguageCode()`
- Respects siteaccess language configuration
- No manual mapping needed

### 3. **Dynamic Fallback** 🔄
- Fallback language determined from system configuration
- Uses first enabled language in Ibexa
- Not hardcoded to `eng-GB`

### 4. **Dynamic Available Languages** 📋
- `getAvailableLanguages()` loads from Ibexa's language configuration
- Automatically includes all enabled languages
- No need to update code when adding languages

## How It Works

### Language Resolution Flow

```
1. User visits site_fr
   ↓
2. Ibexa determines default language for site_fr
   ↓
3. LanguageService returns 'fra-FR'
   ↓
4. TranslationResolver uses 'fra-FR' to fetch translations
   ↓
5. If fra-FR translation not found:
   ↓
6. Falls back to first enabled language (e.g., 'eng-GB')
   ↓
7. If still not found, uses base entity field
```

### Example: Multi-Project Support

**Project A (Your current setup):**
```yaml
# ibexa.yaml
system:
    site_group:
        languages: [eng-GB, fra-FR, ar-AE]
    site:
        languages: [eng-GB]
    site_fr:
        languages: [fra-FR, eng-GB]
    site_ar:
        languages: [ar-AE, eng-GB]
```

**Project B (Different setup):**
```yaml
# ibexa.yaml
system:
    main_group:
        languages: [ger-DE, eng-US]
    main_de:
        languages: [ger-DE]
    main_en:
        languages: [eng-US]
```

**Same bundle works for both!** No code changes needed. 🎉

## API Reference

### `getCurrentLanguage(): string`
Returns the default language code for the current siteaccess.

**Example:**
```php
// On site_fr
$language = $translationResolver->getCurrentLanguage();
// Returns: 'fra-FR'

// On site_ar
$language = $translationResolver->getCurrentLanguage();
// Returns: 'ar-AE'
```

### `getFallbackLanguage(): string` (private)
Returns the first enabled language in the system as fallback.

**Logic:**
1. Load all languages from Ibexa
2. Return first enabled language
3. Ultimate fallback: `'eng-GB'`

### `getAvailableLanguages(): array`
Returns all enabled languages from Ibexa configuration.

**Example:**
```php
$languages = $translationResolver->getAvailableLanguages();
// Returns: [
//     'eng-GB' => 'English (United Kingdom)',
//     'fra-FR' => 'French (France)',
//     'ar-AE' => 'Arabic (United Arab Emirates)'
// ]
```

### `isRTL(string $languageCode): bool`
Checks if a language is Right-to-Left.

**Example:**
```php
$translationResolver->isRTL('ar-AE'); // true
$translationResolver->isRTL('eng-GB'); // false
$translationResolver->isRTL('fra-FR'); // false
```

## Usage Examples

### In Controllers

```php
use Masilia\ConsentBundle\Service\TranslationResolver;

class ConsentController extends AbstractController
{
    public function __construct(
        private readonly TranslationResolver $translationResolver
    ) {
    }
    
    public function showDialog(CookiePolicy $policy): Response
    {
        // Automatically uses current siteaccess language
        $currentLanguage = $this->translationResolver->getCurrentLanguage();
        
        $categories = [];
        foreach ($policy->getCategories() as $category) {
            $categories[] = [
                'name' => $this->translationResolver->getCategoryName($category),
                'description' => $this->translationResolver->getCategoryDescription($category),
            ];
        }
        
        return $this->render('consent/dialog.html.twig', [
            'categories' => $categories,
            'currentLanguage' => $currentLanguage,
            'isRTL' => $this->translationResolver->isRTL($currentLanguage),
        ]);
    }
}
```

### In Twig Templates

```twig
{# Automatic language detection #}
<div class="consent-dialog {{ translationResolver.isRTL(translationResolver.getCurrentLanguage()) ? 'rtl' : 'ltr' }}">
    <h2>{{ translationResolver.getCategoryName(category) }}</h2>
    <p>{{ translationResolver.getCategoryDescription(category) }}</p>
</div>

{# Show available languages #}
<select name="language">
    {% for code, name in translationResolver.getAvailableLanguages() %}
        <option value="{{ code }}" {{ code == translationResolver.getCurrentLanguage() ? 'selected' : '' }}>
            {{ name }}
        </option>
    {% endfor %}
</select>
```

## Configuration Requirements

### Ibexa Language Configuration

Ensure your languages are properly configured in Ibexa:

```yaml
# ibexa.yaml
ibexa:
    system:
        site_group:
            languages: [eng-GB, fra-FR, ar-AE]
        
        site:
            languages: [eng-GB]
        
        site_fr:
            languages: [fra-FR, eng-GB]  # fra-FR is default, eng-GB is fallback
        
        site_ar:
            languages: [ar-AE, eng-GB]   # ar-AE is default, eng-GB is fallback
```

### Language Service

The bundle automatically uses Ibexa's `LanguageService`:
- No additional configuration needed
- Works with Ibexa's dependency injection
- Respects Ibexa's language settings

## Migration Notes

If you were using the old hardcoded version:

1. **No code changes needed** in your controllers or templates
2. **API remains the same** - all public methods unchanged
3. **Behavior is identical** - just more flexible
4. **Works with any Ibexa project** - not tied to specific siteaccesses

## Testing

### Test Language Detection

```php
// Test on different siteaccesses
// Visit: https://example.com/ (site)
$language = $translationResolver->getCurrentLanguage();
// Should return: 'eng-GB'

// Visit: https://example.com/fr (site_fr)
$language = $translationResolver->getCurrentLanguage();
// Should return: 'fra-FR'

// Visit: https://example.com/ar (site_ar)
$language = $translationResolver->getCurrentLanguage();
// Should return: 'ar-AE'
```

### Test Fallback

```php
// Create category with only English translation
$category = new CookieCategory();
$englishTranslation = new CookieCategoryTranslation();
$englishTranslation->setLanguageCode('eng-GB');
$englishTranslation->setName('Analytics');
$category->addTranslation($englishTranslation);

// On site_fr (French siteaccess)
$name = $translationResolver->getCategoryName($category);
// Should return: 'Analytics' (fallback to English)
```

### Test RTL

```php
// Test RTL detection
$translationResolver->isRTL('ar-AE');  // true
$translationResolver->isRTL('ar-SA');  // true
$translationResolver->isRTL('eng-GB'); // false
$translationResolver->isRTL('fra-FR'); // false
```

## Advantages Over Hardcoded Approach

| Aspect | Hardcoded | Dynamic (Current) |
|--------|-----------|-------------------|
| **Reusability** | ❌ Project-specific | ✅ Works everywhere |
| **Maintenance** | ❌ Update code for new languages | ✅ Automatic |
| **Configuration** | ❌ In PHP code | ✅ In Ibexa config |
| **Flexibility** | ❌ Fixed mappings | ✅ Adapts to project |
| **Testing** | ❌ Mock siteaccesses | ✅ Use Ibexa's system |
| **Bundle Distribution** | ❌ Requires customization | ✅ Ready to use |

## Conclusion

The `TranslationResolver` service is now **truly project-agnostic** and leverages Ibexa's native language system. This makes the Masilia Consent Bundle:

✅ **Reusable** across different Ibexa projects  
✅ **Maintainable** without code changes  
✅ **Flexible** to any language configuration  
✅ **Professional** following Ibexa best practices  

No more hardcoded project-specific values! 🎉
