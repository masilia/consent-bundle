# Database Migration: Add Translation Support

## Overview

This migration adds multi-language translation support for cookie consent content across the site_group (site, site_fr, site_ar). It creates translation tables for `CookieCategory`, `Cookie`, and `ThirdPartyService` entities.

## Migration SQL

### MySQL/MariaDB

```sql
-- Cookie Category Translations
CREATE TABLE masilia_cookie_category_translation (
    id INT AUTO_INCREMENT NOT NULL,
    category_id INT NOT NULL,
    language_code VARCHAR(10) NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY(id),
    UNIQUE INDEX category_language_unique (category_id, language_code),
    INDEX IDX_CATEGORY (category_id),
    CONSTRAINT FK_CATEGORY_TRANSLATION_CATEGORY 
        FOREIGN KEY (category_id) 
        REFERENCES masilia_cookie_category (id) 
        ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

-- Cookie Translations
CREATE TABLE masilia_cookie_translation (
    id INT AUTO_INCREMENT NOT NULL,
    cookie_id INT NOT NULL,
    language_code VARCHAR(10) NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    purpose TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY(id),
    UNIQUE INDEX cookie_language_unique (cookie_id, language_code),
    INDEX IDX_COOKIE (cookie_id),
    CONSTRAINT FK_COOKIE_TRANSLATION_COOKIE 
        FOREIGN KEY (cookie_id) 
        REFERENCES masilia_cookie (id) 
        ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;

-- Third Party Service Translations
CREATE TABLE masilia_third_party_service_translation (
    id INT AUTO_INCREMENT NOT NULL,
    service_id INT NOT NULL,
    language_code VARCHAR(10) NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY(id),
    UNIQUE INDEX service_language_unique (service_id, language_code),
    INDEX IDX_SERVICE (service_id),
    CONSTRAINT FK_SERVICE_TRANSLATION_SERVICE 
        FOREIGN KEY (service_id) 
        REFERENCES masilia_third_party_service (id) 
        ON DELETE CASCADE
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
```

### PostgreSQL

```sql
-- Cookie Category Translations
CREATE TABLE masilia_cookie_category_translation (
    id SERIAL PRIMARY KEY,
    category_id INTEGER NOT NULL,
    language_code VARCHAR(10) NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    CONSTRAINT category_language_unique UNIQUE (category_id, language_code),
    CONSTRAINT FK_CATEGORY_TRANSLATION_CATEGORY 
        FOREIGN KEY (category_id) 
        REFERENCES masilia_cookie_category (id) 
        ON DELETE CASCADE
);

CREATE INDEX IDX_CATEGORY ON masilia_cookie_category_translation (category_id);

-- Cookie Translations
CREATE TABLE masilia_cookie_translation (
    id SERIAL PRIMARY KEY,
    cookie_id INTEGER NOT NULL,
    language_code VARCHAR(10) NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    purpose TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    CONSTRAINT cookie_language_unique UNIQUE (cookie_id, language_code),
    CONSTRAINT FK_COOKIE_TRANSLATION_COOKIE 
        FOREIGN KEY (cookie_id) 
        REFERENCES masilia_cookie (id) 
        ON DELETE CASCADE
);

CREATE INDEX IDX_COOKIE ON masilia_cookie_translation (cookie_id);

-- Third Party Service Translations
CREATE TABLE masilia_third_party_service_translation (
    id SERIAL PRIMARY KEY,
    service_id INTEGER NOT NULL,
    language_code VARCHAR(10) NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    CONSTRAINT service_language_unique UNIQUE (service_id, language_code),
    CONSTRAINT FK_SERVICE_TRANSLATION_SERVICE 
        FOREIGN KEY (service_id) 
        REFERENCES masilia_third_party_service (id) 
        ON DELETE CASCADE
);

CREATE INDEX IDX_SERVICE ON masilia_third_party_service_translation (service_id);
```

## Doctrine Migration Command

```bash
# Generate migration
ddev exec bin/console doctrine:migrations:diff

# Review the generated migration file, then execute
ddev exec bin/console doctrine:migrations:migrate
```

## Data Migration

After creating the translation tables, migrate existing content to English translations:

```php
<?php
// src/Command/MigrateTranslationsCommand.php

namespace Masilia\ConsentBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Masilia\ConsentBundle\Entity\CookieCategoryTranslation;
use Masilia\ConsentBundle\Entity\CookieTranslation;
use Masilia\ConsentBundle\Entity\ThirdPartyServiceTranslation;
use Masilia\ConsentBundle\Repository\CookieCategoryRepository;
use Masilia\ConsentBundle\Repository\CookieRepository;
use Masilia\ConsentBundle\Repository\ThirdPartyServiceRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MigrateTranslationsCommand extends Command
{
    protected static $defaultName = 'masilia:consent:migrate-translations';

    public function __construct(
        private readonly CookieCategoryRepository $categoryRepository,
        private readonly CookieRepository $cookieRepository,
        private readonly ThirdPartyServiceRepository $serviceRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Migrate existing content to English translations');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $io->title('Migrating existing content to English translations');
        
        // Migrate categories
        $categories = $this->categoryRepository->findAll();
        $io->section(sprintf('Migrating %d categories...', count($categories)));
        
        foreach ($categories as $category) {
            // Check if English translation already exists
            if ($category->getTranslation('eng-GB')) {
                continue;
            }
            
            $translation = new CookieCategoryTranslation();
            $translation->setCategory($category);
            $translation->setLanguageCode('eng-GB');
            $translation->setName($category->getName());
            $translation->setDescription($category->getDescription());
            
            $this->entityManager->persist($translation);
            $io->writeln(sprintf('  - Created English translation for category: %s', $category->getName()));
        }
        
        // Migrate cookies
        $cookies = $this->cookieRepository->findAll();
        $io->section(sprintf('Migrating %d cookies...', count($cookies)));
        
        foreach ($cookies as $cookie) {
            // Check if English translation already exists
            if ($cookie->getTranslation('eng-GB')) {
                continue;
            }
            
            $translation = new CookieTranslation();
            $translation->setCookie($cookie);
            $translation->setLanguageCode('eng-GB');
            $translation->setName($cookie->getName());
            $translation->setPurpose($cookie->getPurpose());
            
            $this->entityManager->persist($translation);
            $io->writeln(sprintf('  - Created English translation for cookie: %s', $cookie->getName()));
        }
        
        // Migrate third-party services
        $services = $this->serviceRepository->findAll();
        $io->section(sprintf('Migrating %d third-party services...', count($services)));
        
        foreach ($services as $service) {
            // Check if English translation already exists
            if ($service->getTranslation('eng-GB')) {
                continue;
            }
            
            $translation = new ThirdPartyServiceTranslation();
            $translation->setService($service);
            $translation->setLanguageCode('eng-GB');
            $translation->setName($service->getName());
            $translation->setDescription($service->getDescription());
            
            $this->entityManager->persist($translation);
            $io->writeln(sprintf('  - Created English translation for service: %s', $service->getName()));
        }
        
        $this->entityManager->flush();
        
        $io->success('All existing content has been migrated to English translations!');
        $io->note('You can now add French (fra-FR) and Arabic (ar-AE) translations via the admin interface.');
        
        return Command::SUCCESS;
    }
}
```

Run the migration command:
```bash
ddev exec bin/console masilia:consent:migrate-translations
```

## Language Codes

The system uses the following language codes:

| Language | Code | SiteAccess |
|----------|------|------------|
| English | `eng-GB` | site, africa_integrates, africa_v2x_hub, static_site |
| French | `fra-FR` | site_fr |
| Arabic | `ar-AE` | site_ar |

## Translation Workflow

### 1. Create Content in English
```php
// Create category with English translation
$category = new CookieCategory();
$category->setIdentifier('analytics');
$category->setName('Analytics'); // Base name (fallback)
$category->setDescription('Analytics cookies'); // Base description (fallback)

$englishTranslation = new CookieCategoryTranslation();
$englishTranslation->setCategory($category);
$englishTranslation->setLanguageCode('eng-GB');
$englishTranslation->setName('Analytics Cookies');
$englishTranslation->setDescription('These cookies help us understand how visitors interact with our website.');

$category->addTranslation($englishTranslation);
```

### 2. Add French Translation
```php
$frenchTranslation = new CookieCategoryTranslation();
$frenchTranslation->setCategory($category);
$frenchTranslation->setLanguageCode('fra-FR');
$frenchTranslation->setName('Cookies Analytiques');
$frenchTranslation->setDescription('Ces cookies nous aident à comprendre comment les visiteurs interagissent avec notre site web.');

$category->addTranslation($frenchTranslation);
```

### 3. Add Arabic Translation
```php
$arabicTranslation = new CookieCategoryTranslation();
$arabicTranslation->setCategory($category);
$arabicTranslation->setLanguageCode('ar-AE');
$arabicTranslation->setName('ملفات تعريف الارتباط التحليلية');
$arabicTranslation->setDescription('تساعدنا ملفات تعريف الارتباط هذه على فهم كيفية تفاعل الزوار مع موقعنا.');

$category->addTranslation($arabicTranslation);
```

## Frontend Usage

### Using TranslationResolver Service

```php
use Masilia\ConsentBundle\Service\TranslationResolver;

class ConsentController
{
    public function __construct(
        private readonly TranslationResolver $translationResolver
    ) {
    }
    
    public function showConsent(CookieCategory $category): Response
    {
        // Automatically gets translation for current siteaccess language
        $name = $this->translationResolver->getCategoryName($category);
        $description = $this->translationResolver->getCategoryDescription($category);
        
        // Or specify language explicitly
        $frenchName = $this->translationResolver->getCategoryName($category, 'fra-FR');
        
        return $this->render('consent/dialog.html.twig', [
            'categoryName' => $name,
            'categoryDescription' => $description,
        ]);
    }
}
```

### In Twig Templates

```twig
{# The service automatically detects current language #}
{{ translationResolver.getCategoryName(category) }}
{{ translationResolver.getCategoryDescription(category) }}

{# For cookies #}
{{ translationResolver.getCookieName(cookie) }}
{{ translationResolver.getCookiePurpose(cookie) }}

{# For third-party services #}
{{ translationResolver.getServiceName(service) }}
{{ translationResolver.getServiceDescription(service) }}
```

## Fallback Strategy

The `TranslationResolver` uses a three-level fallback:

1. **Requested language** (e.g., `fra-FR`)
2. **Default language** (`eng-GB`)
3. **Base entity field** (original name/description)

Example:
```
User on site_fr (French):
1. Try fra-FR translation → Found ✓
2. Return French translation

User on site_fr (French) but no French translation:
1. Try fra-FR translation → Not found ✗
2. Try eng-GB translation → Found ✓
3. Return English translation

No translations at all:
1. Try fra-FR translation → Not found ✗
2. Try eng-GB translation → Not found ✗
3. Return base entity name ✓
```

## RTL Support for Arabic

The `TranslationResolver` includes RTL detection:

```php
$isRTL = $translationResolver->isRTL('ar-AE'); // true
```

Use in templates:
```twig
<div class="consent-dialog {{ translationResolver.isRTL(translationResolver.getCurrentLanguage()) ? 'rtl' : 'ltr' }}">
    {# Content automatically flips for Arabic #}
</div>
```

## Rollback

To rollback this migration:

```sql
DROP TABLE IF EXISTS masilia_cookie_category_translation;
DROP TABLE IF EXISTS masilia_cookie_translation;
DROP TABLE IF EXISTS masilia_third_party_service_translation;
```

## Testing

After migration, verify:

1. **English translations exist** for all categories, cookies, and services
2. **French translations can be added** via admin interface
3. **Arabic translations can be added** via admin interface
4. **Frontend displays correct language** based on siteaccess
5. **Fallback works** when translation is missing
6. **RTL layout works** for Arabic siteaccess

## Next Steps

1. Run database migration
2. Run data migration command to create English translations
3. Add French translations via admin interface
4. Add Arabic translations via admin interface
5. Test on all siteaccesses (site, site_fr, site_ar)
6. Verify RTL layout for Arabic
7. Ensure GDPR compliance for French translations
