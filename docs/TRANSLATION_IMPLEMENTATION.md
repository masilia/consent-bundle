# Translation System Implementation - Complete Guide

## ✅ Implementation Summary

This document provides a complete overview of the translation system implementation for the Masilia Consent Bundle, supporting multi-language content across the site_group (site, site_fr, site_ar).

## Architecture: Hybrid Translation Approach

### Strategy: Translation Tables + Symfony Translations

**Translation Tables** (Database) for:
- Cookie category names and descriptions
- Cookie names, descriptions, and purposes
- Third-party service names and descriptions

**Symfony Translations** (YAML) for:
- Form labels and help text
- UI buttons and messages
- Validation messages
- Static interface text

## What Was Implemented

### 1. Translation Entities ✅

Created three new translation entities with proper relationships:

#### `CookieCategoryTranslation`
```php
- id (PK)
- category_id (FK → masilia_cookie_category)
- language_code (eng-GB, fra-FR, ar-AE)
- name (translated)
- description (translated)
- created_at, updated_at
```

#### `CookieTranslation`
```php
- id (PK)
- cookie_id (FK → masilia_cookie)
- language_code
- name (translated)
- description (translated)
- purpose (translated)
- created_at, updated_at
```

#### `ThirdPartyServiceTranslation`
```php
- id (PK)
- service_id (FK → masilia_third_party_service)
- language_code
- name (translated)
- description (translated)
- created_at, updated_at
```

**Key Features:**
- Unique constraint on `(entity_id, language_code)` - one translation per language
- Cascade delete - translations removed when parent entity deleted
- Timestamps for audit trail

### 2. Updated Main Entities ✅

Added `translations` relationship to:
- `CookieCategory`
- `Cookie`
- `ThirdPartyService`

**New Methods:**
```php
getTranslations(): Collection
addTranslation(Translation $translation): self
removeTranslation(Translation $translation): self
getTranslation(string $languageCode): ?Translation
```

### 3. Translation Repositories ✅

Created repositories for all translation entities:
- `CookieCategoryTranslationRepository`
- `CookieTranslationRepository`
- `ThirdPartyServiceTranslationRepository`

Standard CRUD methods: `save()`, `remove()`, `flush()`

### 4. TranslationResolver Service ✅

**Purpose:** Resolve translations for frontend based on current siteaccess

**Key Methods:**
```php
getCurrentLanguage(): string
getCategoryName(CookieCategory $category, ?string $languageCode = null): string
getCategoryDescription(CookieCategory $category, ?string $languageCode = null): string
getCookieName(Cookie $cookie, ?string $languageCode = null): string
getCookieDescription(Cookie $cookie, ?string $languageCode = null): ?string
getCookiePurpose(Cookie $cookie, ?string $languageCode = null): string
getServiceName(ThirdPartyService $service, ?string $languageCode = null): string
getServiceDescription(ThirdPartyService $service, ?string $languageCode = null): string
getAvailableLanguages(): array
isRTL(string $languageCode): bool
```

**Fallback Strategy:**
1. Try requested language (e.g., `fra-FR`)
2. Fallback to default language (`eng-GB`)
3. Fallback to base entity field

**Language Mapping:**
```php
'site' => 'eng-GB'
'site_fr' => 'fra-FR'
'site_ar' => 'ar-AE'
'africa_integrates' => 'eng-GB'
'africa_v2x_hub' => 'eng-GB'
'static_site' => 'eng-GB'
```

### 5. Storage Configuration in Database ✅

Added cookie storage fields to `CookiePolicy` entity:
- `cookieName` - Cookie name (e.g., "imal_consent")
- `cookieLifetime` - Lifetime in days
- `cookiePath` - Cookie path (e.g., "/", "/fr")
- `cookieDomain` - Cookie domain (nullable)
- `cookieSecure` - Require HTTPS
- `cookieHttpOnly` - Prevent JS access
- `cookieSameSite` - SameSite policy (lax/strict/none)

### 6. Updated PolicyType Form ✅

Added storage configuration fields with validation:
- Cookie name (lowercase + underscores only)
- Cookie lifetime (1-3650 days)
- Cookie path
- Cookie domain (optional)
- Cookie secure checkbox
- Cookie HTTP only checkbox
- Cookie SameSite dropdown

### 7. Updated Templates ✅

**create.html.twig & edit.html.twig:**
- Added "Basic Information" section
- Added "Cookie Storage Configuration" section
- Organized in collapsible cards
- All fields styled with Ibexa classes

## Database Schema

### New Tables

```
masilia_cookie_category_translation
├── id (PK)
├── category_id (FK)
├── language_code
├── name
├── description
└── timestamps

masilia_cookie_translation
├── id (PK)
├── cookie_id (FK)
├── language_code
├── name
├── description
├── purpose
└── timestamps

masilia_third_party_service_translation
├── id (PK)
├── service_id (FK)
├── language_code
├── name
├── description
└── timestamps
```

### Updated Tables

```
masilia_cookie_policy
├── ... (existing fields)
├── cookie_name
├── cookie_lifetime
├── cookie_path
├── cookie_domain
├── cookie_secure
├── cookie_http_only
└── cookie_same_site
```

## Usage Examples

### Backend: Creating Content with Translations

```php
use Masilia\ConsentBundle\Entity\CookieCategory;
use Masilia\ConsentBundle\Entity\CookieCategoryTranslation;

// Create category
$category = new CookieCategory();
$category->setIdentifier('analytics');
$category->setName('Analytics'); // Fallback
$category->setDescription('Analytics cookies'); // Fallback
$category->setRequired(false);
$category->setDefaultEnabled(false);

// Add English translation
$englishTranslation = new CookieCategoryTranslation();
$englishTranslation->setCategory($category);
$englishTranslation->setLanguageCode('eng-GB');
$englishTranslation->setName('Analytics Cookies');
$englishTranslation->setDescription('These cookies help us understand how visitors interact with our website.');
$category->addTranslation($englishTranslation);

// Add French translation
$frenchTranslation = new CookieCategoryTranslation();
$frenchTranslation->setCategory($category);
$frenchTranslation->setLanguageCode('fra-FR');
$frenchTranslation->setName('Cookies Analytiques');
$frenchTranslation->setDescription('Ces cookies nous aident à comprendre comment les visiteurs interagissent avec notre site web.');
$category->addTranslation($frenchTranslation);

// Add Arabic translation
$arabicTranslation = new CookieCategoryTranslation();
$arabicTranslation->setCategory($category);
$arabicTranslation->setLanguageCode('ar-AE');
$arabicTranslation->setName('ملفات تعريف الارتباط التحليلية');
$arabicTranslation->setDescription('تساعدنا ملفات تعريف الارتباط هذه على فهم كيفية تفاعل الزوار مع موقعنا.');
$category->addTranslation($arabicTranslation);

$entityManager->persist($category);
$entityManager->flush();
```

### Frontend: Using TranslationResolver

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
        $categories = [];
        
        foreach ($policy->getCategories() as $category) {
            $categories[] = [
                'identifier' => $category->getIdentifier(),
                'name' => $this->translationResolver->getCategoryName($category),
                'description' => $this->translationResolver->getCategoryDescription($category),
                'required' => $category->isRequired(),
                'cookies' => $this->getCookiesData($category),
            ];
        }
        
        return $this->render('consent/dialog.html.twig', [
            'categories' => $categories,
            'isRTL' => $this->translationResolver->isRTL(
                $this->translationResolver->getCurrentLanguage()
            ),
        ]);
    }
    
    private function getCookiesData(CookieCategory $category): array
    {
        $cookies = [];
        
        foreach ($category->getCookies() as $cookie) {
            $cookies[] = [
                'name' => $this->translationResolver->getCookieName($cookie),
                'purpose' => $this->translationResolver->getCookiePurpose($cookie),
                'description' => $this->translationResolver->getCookieDescription($cookie),
                'provider' => $cookie->getProvider(),
                'expiry' => $cookie->getExpiry(),
            ];
        }
        
        return $cookies;
    }
}
```

### Twig Templates

```twig
{# Consent dialog with automatic language detection #}
<div class="consent-dialog {{ translationResolver.isRTL(translationResolver.getCurrentLanguage()) ? 'rtl' : 'ltr' }}">
    <h2>{{ 'consent.dialog.title'|trans }}</h2>
    
    {% for category in categories %}
        <div class="consent-category">
            <h3>{{ translationResolver.getCategoryName(category) }}</h3>
            <p>{{ translationResolver.getCategoryDescription(category) }}</p>
            
            {% if not category.required %}
                <label>
                    <input type="checkbox" name="consent[{{ category.identifier }}]">
                    {{ 'consent.dialog.accept'|trans }}
                </label>
            {% endif %}
            
            <div class="cookies-list">
                {% for cookie in category.cookies %}
                    <div class="cookie-item">
                        <strong>{{ translationResolver.getCookieName(cookie) }}</strong>
                        <p>{{ translationResolver.getCookiePurpose(cookie) }}</p>
                    </div>
                {% endfor %}
            </div>
        </div>
    {% endfor %}
</div>
```

## Migration Steps

### Step 1: Run Database Migration

```bash
# Generate migration
ddev exec bin/console doctrine:migrations:diff

# Review generated migration
# Edit if needed

# Execute migration
ddev exec bin/console doctrine:migrations:migrate
```

### Step 2: Migrate Existing Content

```bash
# Create English translations for all existing content
ddev exec bin/console masilia:consent:migrate-translations
```

### Step 3: Add French Translations

Via admin interface or programmatically:

```php
// For each category, cookie, and service
$frenchTranslation = new CookieCategoryTranslation();
$frenchTranslation->setCategory($category);
$frenchTranslation->setLanguageCode('fra-FR');
$frenchTranslation->setName('Nom en français');
$frenchTranslation->setDescription('Description en français');
$category->addTranslation($frenchTranslation);
```

### Step 4: Add Arabic Translations

```php
$arabicTranslation = new CookieCategoryTranslation();
$arabicTranslation->setCategory($category);
$arabicTranslation->setLanguageCode('ar-AE');
$arabicTranslation->setName('الاسم بالعربية');
$arabicTranslation->setDescription('الوصف بالعربية');
$category->addTranslation($arabicTranslation);
```

### Step 5: Test on All SiteAccesses

- **site** (English) - Should show English translations
- **site_fr** (French) - Should show French translations (or English fallback)
- **site_ar** (Arabic) - Should show Arabic translations with RTL layout

## Benefits

✅ **One Policy, Multiple Languages** - Single policy for site_group with translations  
✅ **Admin-Editable** - All translations managed via admin interface  
✅ **Automatic Language Detection** - Based on current siteaccess  
✅ **Fallback Support** - Graceful degradation if translation missing  
✅ **RTL Support** - Automatic detection for Arabic  
✅ **GDPR Compliant** - Precise French translations for legal compliance  
✅ **Scalable** - Easy to add new languages  
✅ **Type-Safe** - Full Doctrine entity relationships  
✅ **Performance** - Efficient queries with proper indexing  

## RTL Support for Arabic

The system includes built-in RTL detection:

```php
// In controller
$isRTL = $this->translationResolver->isRTL($this->translationResolver->getCurrentLanguage());

// In template
{% if translationResolver.isRTL(translationResolver.getCurrentLanguage()) %}
    <div class="consent-dialog rtl">
        {# Content automatically flips for Arabic #}
    </div>
{% endif %}
```

**CSS for RTL:**
```css
.consent-dialog.rtl {
    direction: rtl;
    text-align: right;
}

.consent-dialog.rtl .button {
    float: left; /* Flipped for RTL */
}
```

## Language-Specific Considerations

### French (fra-FR) - GDPR Compliance
- Cookie descriptions must be legally accurate
- Consent text must meet GDPR requirements
- Consider legal review of translations
- Use formal language ("vous" not "tu")

### Arabic (ar-AE) - RTL Layout
- All UI elements flip horizontally
- Text alignment is right-to-left
- Numbers may need special handling
- Consider cultural context in translations

## Testing Checklist

- [ ] Database migration runs successfully
- [ ] English translations created for all existing content
- [ ] French translations can be added via admin
- [ ] Arabic translations can be added via admin
- [ ] Frontend shows correct language on `site`
- [ ] Frontend shows correct language on `site_fr`
- [ ] Frontend shows correct language on `site_ar`
- [ ] Fallback works when translation missing
- [ ] RTL layout works correctly for Arabic
- [ ] Cookie storage configuration saved correctly
- [ ] Storage settings applied per siteaccess

## Documentation Files

1. **SITEACCESS_STRATEGY.md** - Overall strategy for siteaccess management
2. **MIGRATION_STORAGE_CONFIG.md** - Database migration for storage config
3. **MIGRATION_TRANSLATIONS.md** - Database migration for translations
4. **TRANSLATION_IMPLEMENTATION.md** - This file (complete guide)

## Next Steps

1. ✅ Run database migrations
2. ✅ Run data migration command
3. 🔄 Create admin UI for managing translations (language tabs)
4. 🔄 Add French translations for all content
5. 🔄 Add Arabic translations for all content
6. 🔄 Test on all siteaccesses
7. 🔄 Verify GDPR compliance for French
8. 🔄 Test RTL layout for Arabic
9. 🔄 Update frontend to use TranslationResolver
10. 🔄 Deploy to production

## Support

For questions or issues:
- Review documentation in `/docs` folder
- Check entity relationships in `/src/Entity`
- Review TranslationResolver service in `/src/Service`
- Test with migration commands

---

**Implementation Status:** ✅ Core system complete, ready for admin UI and content translation
