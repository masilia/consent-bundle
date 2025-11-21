# SiteAccess Strategy for Cookie Policy Management

## Overview

This document explains the strategy for managing cookie policies across multiple siteaccesses with individual storage configuration per policy.

## Current SiteAccess Configuration

Your Ibexa installation has **6 siteaccesses** organized into **4 groups**:

### 1. Site Group (Multi-language main site)
- `site` - English (eng-GB)
- `site_fr` - French (fra-FR) - GDPR compliance required
- `site_ar` - Arabic (ar-AE) - RTL layout
- **Shared host**, different URI paths (`/`, `/fr`, `/ar`)
- Translation siteaccesses enabled

### 2. Static Group
- `static_site` - English
- Separate host
- Minimal cookie usage

### 3. Africa Integrates Group
- `africa_integrates` - English
- Separate brand/domain
- Own content tree and design

### 4. Africa V2X Hub Group
- `africa_v2x_hub` - English
- Separate project/domain
- Own content tree and design

## Strategy: Individual SiteAccess Storage

### ✅ Why Individual SiteAccess (Not Groups)?

#### 1. **Legal & Compliance Differences**
```
site_fr (French):
  - GDPR compliance required
  - Stricter cookie consent rules
  - May need different cookie domain/path

site_ar (Arabic):
  - Different regional laws
  - RTL considerations
  - May need localized cookie names

site (English):
  - UK/International laws
  - Different compliance requirements
```

#### 2. **Technical Requirements**
```yaml
# Different siteaccesses may need:
site:
  cookie_name: 'imal_consent'
  cookie_domain: 'example.com'
  cookie_path: '/'

site_fr:
  cookie_name: 'imal_consent_fr'
  cookie_domain: 'example.com'
  cookie_path: '/fr'  # Scoped to French section

africa_integrates:
  cookie_name: 'africa_consent'
  cookie_domain: 'africa-integrates.com'
  cookie_path: '/'
```

#### 3. **Brand Separation**
- Each brand (main site, Africa Integrates, V2X Hub) needs isolated cookie namespaces
- Prevents cookie conflicts between domains
- Allows independent policy management

## Database Schema

### CookiePolicy Entity Fields

```php
// Basic Information
private string $version;              // e.g., "1.0.0"
private string $cookiePrefix;         // e.g., "consent_"
private int $expirationDays;          // e.g., 365
private ?string $siteAccess;          // e.g., "site_fr" or NULL for all
private bool $isActive;               // Only one active policy per siteaccess

// Storage Configuration (per policy/siteaccess)
private string $cookieName;           // e.g., "imal_consent"
private int $cookieLifetime;          // e.g., 365 days
private string $cookiePath;           // e.g., "/" or "/fr"
private ?string $cookieDomain;        // e.g., "example.com" or NULL
private bool $cookieSecure;           // true for HTTPS
private bool $cookieHttpOnly;         // true to prevent JS access
private string $cookieSameSite;       // "lax", "strict", or "none"
```

## Implementation Examples

### Example 1: Separate Policy per SiteAccess

```
Policy 1:
  version: "1.0.0"
  siteAccess: "site"
  cookieName: "imal_consent"
  cookiePath: "/"
  cookieDomain: "example.com"
  isActive: true

Policy 2:
  version: "1.0.0"
  siteAccess: "site_fr"
  cookieName: "imal_consent_fr"
  cookiePath: "/fr"
  cookieDomain: "example.com"
  cookieSecure: true  # GDPR requirement
  isActive: true

Policy 3:
  version: "1.0.0"
  siteAccess: "africa_integrates"
  cookieName: "africa_consent"
  cookiePath: "/"
  cookieDomain: "africa-integrates.com"
  isActive: true
```

### Example 2: Global Policy with SiteAccess Override

```
Policy 1 (Global):
  version: "1.0.0"
  siteAccess: NULL  # Applies to all siteaccesses
  cookieName: "imal_consent"
  cookiePath: "/"
  isActive: true

Policy 2 (French Override):
  version: "1.0.0"
  siteAccess: "site_fr"  # Overrides global for French
  cookieName: "imal_consent_fr"
  cookiePath: "/fr"
  cookieSecure: true
  isActive: true
```

## Policy Selection Logic

### Frontend Service

```php
class PolicyResolver
{
    public function getActivePolicy(string $siteAccess): ?CookiePolicy
    {
        // 1. Try to find policy for specific siteaccess
        $policy = $this->policyRepository->findActiveBySiteAccess($siteAccess);
        
        if ($policy) {
            return $policy;
        }
        
        // 2. Fallback to global policy (siteAccess = NULL)
        return $this->policyRepository->findActiveGlobal();
    }
    
    public function getCookieConfiguration(CookiePolicy $policy): array
    {
        return [
            'name' => $policy->getCookieName(),
            'lifetime' => $policy->getCookieLifetime(),
            'path' => $policy->getCookiePath(),
            'domain' => $policy->getCookieDomain(),
            'secure' => $policy->isCookieSecure(),
            'httponly' => $policy->isCookieHttpOnly(),
            'samesite' => $policy->getCookieSameSite(),
        ];
    }
}
```

### Repository Method

```php
class CookiePolicyRepository extends ServiceEntityRepository
{
    public function findActiveBySiteAccess(string $siteAccess): ?CookiePolicy
    {
        return $this->createQueryBuilder('p')
            ->where('p.siteAccess = :siteAccess')
            ->andWhere('p.isActive = :active')
            ->setParameter('siteAccess', $siteAccess)
            ->setParameter('active', true)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
    public function findActiveGlobal(): ?CookiePolicy
    {
        return $this->createQueryBuilder('p')
            ->where('p.siteAccess IS NULL')
            ->andWhere('p.isActive = :active')
            ->setParameter('active', true)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
```

## Recommended Setup for Your Site

### Option A: Minimal (Recommended for Start)

```
1. Global Policy (NULL siteAccess)
   - Covers: site, site_fr, site_ar
   - Cookie name: imal_consent
   - Path: /
   - Domain: your-main-domain.com

2. Africa Integrates Policy
   - SiteAccess: africa_integrates
   - Cookie name: africa_consent
   - Domain: africa-integrates.com

3. Africa V2X Hub Policy
   - SiteAccess: africa_v2x_hub
   - Cookie name: v2x_consent
   - Domain: africa-integrates.com (or separate)

4. Static Site Policy
   - SiteAccess: static_site
   - Cookie name: static_consent
   - Minimal cookies
```

### Option B: Full Separation (For Strict Compliance)

```
1. English Policy (site)
2. French Policy (site_fr) - GDPR specific
3. Arabic Policy (site_ar) - Regional specific
4. Africa Integrates Policy (africa_integrates)
5. Africa V2X Hub Policy (africa_v2x_hub)
6. Static Site Policy (static_site)
```

## Migration Path

### Phase 1: Database Migration
```sql
ALTER TABLE masilia_cookie_policy 
ADD COLUMN cookie_name VARCHAR(100) NOT NULL DEFAULT 'imal_consent',
ADD COLUMN cookie_lifetime INT NOT NULL DEFAULT 365,
ADD COLUMN cookie_path VARCHAR(255) NOT NULL DEFAULT '/',
ADD COLUMN cookie_domain VARCHAR(255) NULL,
ADD COLUMN cookie_secure BOOLEAN NOT NULL DEFAULT TRUE,
ADD COLUMN cookie_http_only BOOLEAN NOT NULL DEFAULT TRUE,
ADD COLUMN cookie_same_site VARCHAR(20) NOT NULL DEFAULT 'lax';
```

### Phase 2: Update Existing Policies
```php
// Migration script to set default values for existing policies
$policies = $policyRepository->findAll();
foreach ($policies as $policy) {
    $policy->setCookieName('imal_consent');
    $policy->setCookieLifetime(365);
    $policy->setCookiePath('/');
    $policy->setCookieSecure(true);
    $policy->setCookieHttpOnly(true);
    $policy->setCookieSameSite('lax');
}
$entityManager->flush();
```

### Phase 3: Create SiteAccess-Specific Policies
- Create policies for each siteaccess or brand
- Configure storage settings per policy
- Test cookie behavior on each siteaccess

## Benefits of This Approach

✅ **Flexibility**: Each siteaccess can have unique cookie configuration  
✅ **Compliance**: Meet different legal requirements per region/language  
✅ **Isolation**: Separate brands don't share cookie namespaces  
✅ **Scalability**: Easy to add new siteaccesses with their own policies  
✅ **Maintainability**: All configuration in database, no code changes needed  
✅ **Testing**: Can test different configurations per siteaccess  

## Conclusion

**Use individual siteaccess storage** because:
1. Your siteaccesses have different legal requirements (GDPR for French)
2. Separate brands need isolated cookie namespaces
3. Different domains/paths require different cookie configurations
4. Provides maximum flexibility for future expansion

This strategy allows you to manage everything from the admin interface without touching configuration files.
