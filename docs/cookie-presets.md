# Cookie Presets - Third-Party Service Integration

## Overview

The Cookie Preset system allows you to automatically configure cookies and scripts for common third-party services (like Google Analytics, Facebook Pixel, etc.) without manual configuration.

## How It Works

### 1. **Admin Adds Service with Preset**
Instead of manually creating each cookie, you:
1. Go to **Admin → Third-Party Services → Create Service**
2. Select a **Service Preset** (e.g., "Google Analytics")
3. Enter the **Service ID** (e.g., `GA_MEASUREMENT_ID`)
4. Save

### 2. **System Automatically Creates Cookies**
When you save the service:
- ✅ All related cookies are **automatically created**
- ✅ Cookies appear in the **cookie list** (for information)
- ✅ Cookies are **linked to the correct category**
- ✅ Scripts are **ready to inject** with your service ID

### 3. **Scripts Are Injected on Consent**
When a user consents to the category:
- ✅ Service script is **injected with your ID**
- ✅ Cookies are **automatically set** by the service
- ✅ Everything works **without manual configuration**

---

## Available Presets

### 1. **Google Analytics**
```yaml
Preset ID: google_analytics
Category: analytics
Service ID Format: GA_MEASUREMENT_ID (e.g., G-XXXXXXXXXX)

Auto-created Cookies:
  - _ga (2 years)
  - _gid (24 hours)
  - _gat (1 minute)

Script Template:
  Google Analytics gtag.js with your measurement ID
```

### 2. **Google Tag Manager**
```yaml
Preset ID: google_tag_manager
Category: analytics
Service ID Format: GTM-XXXXXXX

Auto-created Cookies:
  - _ga (2 years)
  - _gid (24 hours)

Script Template:
  Google Tag Manager container script
```

### 3. **Facebook Pixel**
```yaml
Preset ID: facebook_pixel
Category: marketing
Service ID Format: Facebook Pixel ID (numeric)

Auto-created Cookies:
  - _fbp (3 months)
  - fr (3 months)

Script Template:
  Facebook Pixel initialization script
```

### 4. **Hotjar**
```yaml
Preset ID: hotjar
Category: analytics
Service ID Format: Hotjar Site ID (numeric)

Auto-created Cookies:
  - _hjSessionUser_* (1 year)
  - _hjSession_* (30 minutes)

Script Template:
  Hotjar tracking script
```

### 5. **LinkedIn Insight Tag**
```yaml
Preset ID: linkedin_insight
Category: marketing
Service ID Format: LinkedIn Partner ID (numeric)

Auto-created Cookies:
  - li_sugr (90 days)
  - UserMatchHistory (30 days)

Script Template:
  LinkedIn Insight Tag script
```

### 6. **YouTube**
```yaml
Preset ID: youtube
Category: marketing
Service ID Format: N/A (embedded videos)

Auto-created Cookies:
  - VISITOR_INFO1_LIVE (179 days)
  - YSC (Session)
  - yt-remote-device-id (Persistent)

Script Template:
  None (cookies set by embedded videos)
```

---

## Usage Example

### Step 1: Create Analytics Category
```
Admin → Categories → Create Category
  - Identifier: analytics
  - Name: Analytics Cookies
  - Description: These cookies help us understand how visitors use our site
  - Required: No
  - Default Enabled: No
```

### Step 2: Add Google Analytics Service
```
Admin → Third-Party Services → Create Service
  - Service Preset: Google Analytics
  - Service ID (configValue): G-ABC123XYZ
  - Category: analytics
  - Enabled: Yes
```

### Step 3: Automatic Results
✅ **3 cookies automatically created:**
- `_ga` - Google Analytics
- `_gid` - Google Analytics  
- `_gat` - Google Analytics

✅ **Script ready to inject:**
```html
<script async src="https://www.googletagmanager.com/gtag/js?id=G-ABC123XYZ"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-ABC123XYZ');
</script>
```

### Step 4: Use in Template
```twig
{# Inject scripts when user consents to analytics #}
{{ consent_scripts('analytics')|raw }}
```

---

## Configuration Fields

When creating a third-party service:

| Field | Description | Example |
|-------|-------------|---------|
| **Service Preset** | Select predefined service | Google Analytics |
| **Identifier** | Unique identifier | `google_analytics_main` |
| **Name** | Display name | Google Analytics |
| **Category** | Cookie category | `analytics` |
| **Description** | What the service does | Web analytics tracking |
| **Privacy Policy URL** | Service privacy policy | https://policies.google.com/privacy |
| **Config Key** | What the ID represents | `measurement_id` |
| **Config Value** | **Your service ID** | `G-ABC123XYZ` |
| **Enabled** | Active/inactive | Yes |

---

## How Cookies Are Created

### Automatic Process

1. **You save a service** with a preset type
2. **Event subscriber listens** (`ThirdPartyServiceSubscriber`)
3. **Preset is loaded** from `CookiePresetService`
4. **Category is found** based on service category
5. **Cookies are created** for each cookie in the preset
6. **Duplicates are skipped** (won't create if already exists)

### Manual Override

You can still:
- ✅ Create cookies manually
- ✅ Edit auto-created cookies
- ✅ Delete auto-created cookies
- ✅ Add custom cookies to the same category

---

## Script Injection

### How Scripts Are Injected

```php
// ScriptInjectionService
public function generateAllScripts(CookieCategory $category): string
{
    // 1. Get cookie scripts (manual)
    $html = $this->generateScriptTags($category);
    
    // 2. Get service scripts (from presets)
    $html .= $this->getServiceScriptsForCategory($category);
    
    return $html;
}
```

### Script Template Replacement

Service ID is automatically replaced:
```javascript
// Template in preset:
gtag('config', '{{SERVICE_ID}}');

// After replacement:
gtag('config', 'G-ABC123XYZ');
```

---

## Frontend Display

### Cookie List (FO)

Users see all cookies in the preferences modal:

```
Analytics Cookies
├── _ga (Google Analytics) - 2 years
├── _gid (Google Analytics) - 24 hours
└── _gat (Google Analytics) - 1 minute
```

### Cookie List (BO)

Admins see all cookies in the admin panel:

```
Cookies (3)
├── _ga | Google Analytics | Registers a unique ID... | 2 years
├── _gid | Google Analytics | Registers a unique ID... | 24 hours
└── _gat | Google Analytics | Used to throttle... | 1 minute
```

---

## Benefits

### ✅ **For Administrators**
- **No manual cookie entry** - Just select preset and enter ID
- **Automatic updates** - Cookies stay current with service changes
- **Consistent data** - Same format for all installations
- **Time savings** - Setup in seconds instead of minutes

### ✅ **For Developers**
- **Easy integration** - One service = complete setup
- **Script injection** - Automatic with consent
- **Type safety** - Predefined presets prevent errors
- **Extensible** - Easy to add new presets

### ✅ **For Users**
- **Transparency** - See all cookies used
- **Accurate info** - Correct purposes and expiry times
- **GDPR compliance** - Complete cookie disclosure

---

## Adding Custom Presets

### 1. Edit `CookiePresetService.php`

```php
'my_service' => [
    'name' => 'My Custom Service',
    'description' => 'Description of the service',
    'category' => 'analytics', // or 'marketing'
    'privacy_policy_url' => 'https://example.com/privacy',
    'script_template' => <<<'JS'
<script>
  // Your script here
  // Use {{SERVICE_ID}} as placeholder
  myService.init('{{SERVICE_ID}}');
</script>
JS,
    'cookies' => [
        [
            'name' => '_my_cookie',
            'purpose' => 'What this cookie does',
            'expiry' => '1 year',
        ],
    ],
],
```

### 2. Service Will Appear in Dropdown

Automatically available in:
- Admin → Third-Party Services → Create Service → Service Preset

---

## API Reference

### CookiePresetService

```php
// Get all presets
$presets = $presetService->getPresets();

// Get specific preset
$preset = $presetService->getPreset('google_analytics');

// Get cookies for preset
$cookies = $presetService->getCookiesForPreset('google_analytics');

// Get script with ID replaced
$script = $presetService->getScriptForPreset('google_analytics', 'G-ABC123');
```

### ScriptInjectionService

```php
// Generate all scripts for category
$html = $scriptInjection->generateAllScripts($category);

// Get only service scripts
$html = $scriptInjection->getServiceScriptsForCategory($category);

// Check if should inject
$should = $scriptInjection->shouldInjectScripts('analytics');
```

---

## Troubleshooting

### Cookies Not Created

**Problem**: Saved service but cookies didn't appear.

**Solutions**:
1. Check that preset type is selected
2. Verify category exists with correct identifier
3. Check logs for errors
4. Ensure database migration ran

### Script Not Injecting

**Problem**: User consented but script not loading.

**Solutions**:
1. Check user has consented to correct category
2. Verify service is enabled
3. Check service ID is correct
4. Clear Symfony cache

### Duplicate Cookies

**Problem**: Same cookie appears multiple times.

**Solutions**:
- System automatically skips duplicates
- Check if cookies were created manually before
- Delete duplicates manually if needed

---

## Best Practices

1. **Use presets when available** - Faster and more accurate
2. **One service per ID** - Don't create multiple services for same ID
3. **Test consent flow** - Verify scripts inject correctly
4. **Keep IDs secure** - Don't commit real IDs to version control
5. **Document custom presets** - Add comments for future reference

---

## Migration Guide

### From Manual to Preset

If you already have manual cookies:

1. **Create service with preset**
2. **System will skip existing cookies**
3. **Delete old manual cookies** (optional)
4. **Service will manage cookies** going forward

### Example

```
Before:
  - Manually created _ga, _gid, _gat cookies
  - Manually added Google Analytics script

After:
  - Create Google Analytics service with preset
  - System recognizes existing cookies
  - Script injection automated
  - Future updates automatic
```

---

## Summary

The Cookie Preset system transforms third-party service integration from a manual, error-prone process into a simple, automated workflow:

**Old Way:**
1. Research which cookies the service uses
2. Manually create each cookie
3. Find correct script code
4. Configure script injection
5. Update when service changes

**New Way:**
1. Select preset
2. Enter service ID
3. Done! ✅

**Result**: Faster setup, fewer errors, better compliance, happier admins!
