# Masilia Consent Bundle - Installation Complete! 🎉

## ✅ What Was Installed

### 1. Bundle Structure
- **Location**: `/home/said/workspace/masilia/imal-back/packages/masilia/consent-bundle/`
- **Namespace**: `Masilia\ConsentBundle`
- **Type**: Symfony Bundle for Cookie Consent Management

### 2. Database Tables Created
✅ `masilia_cookie_policy` - Cookie policy versions
✅ `masilia_cookie_category` - Categories (essential, analytics, marketing, preferences)
✅ `masilia_cookie` - Individual cookie definitions
✅ `masilia_third_party_service` - Third-party service configurations
✅ `masilia_consent_log` - GDPR-compliant consent audit log

### 3. Initial Data Imported
✅ Policy Version: **1.0.0**
✅ **4 Categories** imported:
   - Essential Cookies (3 cookies)
   - Analytics Cookies (3 cookies)
   - Marketing Cookies (2 cookies)
   - Preference Cookies (2 cookies)
✅ **2 Third-Party Services**:
   - Google Analytics
   - Facebook Pixel

### 4. Configuration Files
✅ `/ibexa/config/bundles.php` - Bundle registered
✅ `/ibexa/config/packages/masilia_consent.yaml` - Bundle configuration
✅ `/ibexa/config/routes/masilia_consent.yaml` - API and Admin routes
✅ `/ibexa/composer.json` - Autoload configured

## 🚀 Available Endpoints

### API Endpoints (for React Frontend)
```
GET    /api/consent/policy              # Get active policy
GET    /api/consent/status              # Get user consent status
GET    /api/consent/categories          # Get categories list
GET    /api/consent/scripts/{category}  # Get scripts for category
POST   /api/consent/accept              # Accept all cookies
POST   /api/consent/reject              # Reject non-essential
POST   /api/consent/preferences         # Save custom preferences
DELETE /api/consent/revoke              # Revoke all consent
GET    /api/consent/check/{category}    # Check specific category
```

### Admin Interface (Ibexa Admin)
```
GET    /admin/consent/policy            # List all policies
GET    /admin/consent/policy/{id}       # View policy details
POST   /admin/consent/policy/{id}/activate   # Activate policy
POST   /admin/consent/policy/{id}/deactivate # Deactivate policy
POST   /admin/consent/policy/{id}/delete     # Delete policy
GET    /admin/consent/statistics        # View consent statistics
```

## 📝 Quick Usage Examples

### Backend (PHP)
```php
use Masilia\ConsentBundle\Service\ConsentManager;

// Check if user has consent
if ($consentManager->hasConsent('analytics')) {
    // Load analytics scripts
}

// Get consent preferences
$preferences = $consentManager->getConsentPreferences();
```

### Frontend (React)
```typescript
import { useConsent } from '@masilia/consent-bundle-react';

function App() {
  const { hasConsent, acceptAll, rejectAll } = useConsent();
  
  if (!hasConsent()) {
    return <ConsentBanner onAccept={acceptAll} onReject={rejectAll} />;
  }
}
```

### CLI Commands
```bash
# Import policy from JSON
ddev exec "php bin/console masilia:consent:import path/to/cookies.json --activate"

# Export policy to JSON
ddev exec "php bin/console masilia:consent:export output.json --pretty"

# Check migrations status
ddev exec "php bin/console doctrine:migrations:status"
```

## 🌐 Access Points

### Test API
```bash
# Via browser
https://imal.ddev.site/api/consent/policy

# Via curl
ddev exec "curl https://imal.ddev.site/api/consent/policy"
```

### Admin Interface
```
https://admin.imal.ddev.site/admin/consent/policy
```
*(Requires Ibexa Admin login)*

## 📚 Documentation

- **Quick Start**: `/packages/masilia/consent-bundle/QUICKSTART.md`
- **Installation Guide**: `/packages/masilia/consent-bundle/docs/installation.md`
- **Usage Examples**: `/packages/masilia/consent-bundle/docs/usage.md`
- **React Components**: `/packages/masilia/consent-bundle/assets/README.md`
- **Main README**: `/packages/masilia/consent-bundle/README.md`

## 🔧 Configuration

Current configuration at `/ibexa/config/packages/masilia_consent.yaml`:
```yaml
masilia_consent:
    storage:
        cookie_name: 'imal_consent'
        cookie_lifetime: 365
    logging:
        enabled: true
        log_ip_address: true
        log_user_agent: true
    api:
        base_path: '/api/consent'
    admin:
        enabled: true
```

## ✨ Features Implemented

✅ **Database-driven** policy management
✅ **REST API** for React integration
✅ **Ibexa Admin UI** integration
✅ **GDPR-compliant** consent logging
✅ **Version control** for policies
✅ **Category-based** consent (essential, analytics, marketing, preferences)
✅ **Script injection** for third-party services
✅ **Event system** for extensibility
✅ **CLI commands** for import/export
✅ **TypeScript support** for React
✅ **Modern PHP 8.2+** with attributes

## 🎯 Next Steps

### 1. Build React Components
```bash
cd packages/masilia/consent-bundle/assets
npm install
npm run build
```

### 2. Integrate Frontend
- Create consent banner component
- Create preferences modal
- Use provided React hooks (`useConsent`, `useConsentPolicy`)

### 3. Customize Admin UI
- Add custom branding to admin templates
- Configure menu items in Ibexa Admin
- Set up user permissions

### 4. Test Everything
```bash
# Test API endpoints
ddev launch /api/consent/policy

# Test admin interface
ddev launch /admin/consent/policy

# Test consent flow
# 1. Visit site without consent
# 2. Accept/reject cookies
# 3. Check consent status
# 4. View statistics in admin
```

## 🐛 Troubleshooting

### Cache Issues
```bash
ddev exec "php bin/console cache:clear"
```

### Database Issues
```bash
ddev exec "php bin/console doctrine:schema:validate"
```

### Routes Not Found
```bash
ddev exec "php bin/console debug:router | grep consent"
```

### Autoload Issues
```bash
ddev composer dump-autoload
```

## 📊 Database Schema

Tables created:
- `masilia_cookie_policy` (1 active policy)
- `masilia_cookie_category` (4 categories)
- `masilia_cookie` (10 cookies)
- `masilia_third_party_service` (2 services)
- `masilia_consent_log` (empty, ready for logging)

## 🎉 Success!

Your Masilia Consent Bundle is now fully installed and operational!

- ✅ Database migrated
- ✅ Initial policy imported and activated
- ✅ API endpoints ready
- ✅ Admin interface accessible
- ✅ React integration prepared

**Ready to build your cookie consent UI!** 🚀
