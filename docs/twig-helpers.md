# Twig Helper Functions

The Masilia Consent Bundle provides several Twig functions to help you integrate consent management into your templates.

## Available Functions

### 1. `consent_check(categoryIdentifier)`

Check if the user has consented to a specific category.

**Parameters:**
- `categoryIdentifier` (string): The category identifier (e.g., 'analytics', 'marketing')

**Returns:** `boolean`

**Example:**
```twig
{% if consent_check('analytics') %}
    {# User has consented to analytics #}
    <script src="https://www.google-analytics.com/analytics.js"></script>
{% endif %}
```

---

### 2. `consent_has(categoryIdentifier)`

Alias for `consent_check()` - more natural in templates.

**Parameters:**
- `categoryIdentifier` (string): The category identifier

**Returns:** `boolean`

**Example:**
```twig
{% if consent_has('marketing') %}
    <script>
        // Load marketing pixels
        fbq('init', 'YOUR_PIXEL_ID');
    </script>
{% endif %}
```

---

### 3. `consent_scripts(categoryIdentifier)`

Automatically inject all scripts for a category if consent is given.

**Parameters:**
- `categoryIdentifier` (string): The category identifier

**Returns:** `string` (HTML-safe)

**Example:**
```twig
{# Automatically injects all analytics scripts if user consented #}
{{ consent_scripts('analytics') }}

{# This will output nothing if user hasn't consented #}
{{ consent_scripts('marketing') }}
```

**What it does:**
- Checks if user has consented to the category
- If yes, injects all `<script>` tags for cookies in that category
- Handles both external scripts (`src`) and inline scripts (`init_code`)
- Respects `async` attribute on scripts
- Returns empty string if no consent

---

### 4. `consent_banner()`

Render the consent banner placeholder for React to mount.

**Returns:** `string` (HTML-safe)

**Example:**
```twig
{# In your base layout, before closing </body> tag #}
{{ consent_banner() }}
```

**What it does:**
- Returns empty string if user already has preferences for current policy
- Returns a `<div>` placeholder with policy version data attribute
- React component will mount to this div

**Output:**
```html
<div id="masilia-consent-banner" data-policy-version="1.0.0"></div>
```

---

### 5. `consent_policy()`

Get the active cookie policy object.

**Returns:** `CookiePolicy|null`

**Example:**
```twig
{% set policy = consent_policy() %}
{% if policy %}
    <p>Cookie Policy Version: {{ policy.version }}</p>
    <p>Last Updated: {{ policy.updatedAt|date('Y-m-d') }}</p>
{% endif %}
```

---

### 6. `consent_categories()`

Get all categories from the active policy.

**Returns:** `array<CookieCategory>`

**Example:**
```twig
{% set categories = consent_categories() %}
<ul>
    {% for category in categories %}
        <li>
            <strong>{{ category.name }}</strong>
            {% if category.required %}
                <span class="badge">Required</span>
            {% endif %}
            <p>{{ category.description }}</p>
        </li>
    {% endfor %}
</ul>
```

---

### 7. `consent_preferences()`

Get the user's current consent preferences.

**Returns:** `array|null`

**Example:**
```twig
{% set prefs = consent_preferences() %}
{% if prefs %}
    <div class="consent-status">
        <p>Policy Version: {{ prefs.policy_version }}</p>
        <p>Consent Given: {{ prefs.timestamp|date('Y-m-d H:i:s') }}</p>
        <ul>
            {% for category, consented in prefs.categories %}
                <li>
                    {{ category }}: 
                    {% if consented %}
                        <span class="text-success">✓ Accepted</span>
                    {% else %}
                        <span class="text-danger">✗ Rejected</span>
                    {% endif %}
                </li>
            {% endfor %}
        </ul>
    </div>
{% else %}
    <p>No consent preferences set yet.</p>
{% endif %}
```

---

## Common Usage Patterns

### Pattern 1: Conditional Script Loading

```twig
{# Load Google Analytics only if user consented #}
{% if consent_has('analytics') %}
    <script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'GA_MEASUREMENT_ID');
    </script>
{% endif %}
```

### Pattern 2: Automatic Script Injection

```twig
{# In your layout head #}
{{ consent_scripts('necessary') }}
{{ consent_scripts('analytics') }}
{{ consent_scripts('marketing') }}
```

### Pattern 3: Custom Consent Status Display

```twig
<div class="cookie-preferences">
    <h3>Your Cookie Preferences</h3>
    {% for category in consent_categories() %}
        <div class="preference-item">
            <h4>{{ category.name }}</h4>
            <p>{{ category.description }}</p>
            <span class="status">
                {% if consent_has(category.identifier) %}
                    ✓ Enabled
                {% else %}
                    ✗ Disabled
                {% endif %}
            </span>
        </div>
    {% endfor %}
</div>
```

### Pattern 4: Conditional Content Display

```twig
{% if consent_has('marketing') %}
    {# Show personalized content #}
    <div class="personalized-recommendations">
        {{ render_recommendations() }}
    </div>
{% else %}
    {# Show generic content #}
    <div class="generic-content">
        <p>Enable marketing cookies to see personalized recommendations.</p>
    </div>
{% endif %}
```

### Pattern 5: Base Layout Integration

```twig
{# templates/base.html.twig #}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{% block title %}Welcome!{% endblock %}</title>
    
    {# Always load necessary scripts #}
    {{ consent_scripts('necessary') }}
    
    {# Conditionally load analytics #}
    {{ consent_scripts('analytics') }}
    
    {% block stylesheets %}{% endblock %}
</head>
<body>
    {% block body %}{% endblock %}
    
    {# Load marketing scripts at end of body #}
    {{ consent_scripts('marketing') }}
    
    {# Render consent banner #}
    {{ consent_banner() }}
    
    {% block javascripts %}{% endblock %}
</body>
</html>
```

---

## Advanced Examples

### Example 1: Cookie Policy Page

```twig
{# templates/cookie_policy.html.twig #}
{% extends 'base.html.twig' %}

{% block body %}
    <div class="container">
        <h1>Cookie Policy</h1>
        
        {% set policy = consent_policy() %}
        {% if policy %}
            <p class="lead">Version {{ policy.version }} - Last updated: {{ policy.updatedAt|date('F j, Y') }}</p>
            
            <h2>Cookie Categories</h2>
            {% for category in consent_categories() %}
                <div class="card mb-3">
                    <div class="card-header">
                        <h3>{{ category.name }}</h3>
                        {% if category.required %}
                            <span class="badge bg-warning">Required</span>
                        {% endif %}
                        <span class="badge {% if consent_has(category.identifier) %}bg-success{% else %}bg-secondary{% endif %}">
                            {% if consent_has(category.identifier) %}Enabled{% else %}Disabled{% endif %}
                        </span>
                    </div>
                    <div class="card-body">
                        <p>{{ category.description }}</p>
                        
                        <h4>Cookies in this category:</h4>
                        <ul>
                            {% for cookie in category.cookies %}
                                <li>
                                    <strong>{{ cookie.name }}</strong> ({{ cookie.provider }})
                                    <br>
                                    <small>{{ cookie.purpose }} - Expires: {{ cookie.expiry }}</small>
                                </li>
                            {% endfor %}
                        </ul>
                    </div>
                </div>
            {% endfor %}
            
            <button type="button" class="btn btn-primary" onclick="openConsentModal()">
                Manage Cookie Preferences
            </button>
        {% else %}
            <p>No active cookie policy found.</p>
        {% endif %}
    </div>
{% endblock %}
```

### Example 2: Consent Status Widget

```twig
{# templates/widgets/consent_status.html.twig #}
<div class="consent-widget">
    {% set prefs = consent_preferences() %}
    {% if prefs %}
        <div class="alert alert-info">
            <h5>Cookie Consent Status</h5>
            <p>You gave consent on {{ prefs.timestamp|date('F j, Y') }}</p>
            <p>Policy version: {{ prefs.policy_version }}</p>
            <button class="btn btn-sm btn-outline-primary" onclick="openConsentModal()">
                Update Preferences
            </button>
        </div>
    {% else %}
        <div class="alert alert-warning">
            <p>You haven't set your cookie preferences yet.</p>
            <button class="btn btn-sm btn-primary" onclick="openConsentModal()">
                Set Preferences
            </button>
        </div>
    {% endif %}
</div>
```

---

## Best Practices

### 1. Always Check Consent Before Loading Scripts

```twig
{# ✓ Good #}
{% if consent_has('analytics') %}
    <script src="analytics.js"></script>
{% endif %}

{# ✗ Bad - loads without checking #}
<script src="analytics.js"></script>
```

### 2. Use `consent_scripts()` for Automatic Injection

```twig
{# ✓ Good - automatic and respects consent #}
{{ consent_scripts('analytics') }}

{# ✗ Less ideal - manual and error-prone #}
{% if consent_has('analytics') %}
    {% for cookie in category.cookies %}
        {% if cookie.scriptSrc %}
            <script src="{{ cookie.scriptSrc }}"></script>
        {% endif %}
    {% endfor %}
{% endif %}
```

### 3. Place Banner at End of Body

```twig
{# ✓ Good - doesn't block page rendering #}
<body>
    {% block content %}{% endblock %}
    {{ consent_banner() }}
</body>

{# ✗ Bad - may block rendering #}
<body>
    {{ consent_banner() }}
    {% block content %}{% endblock %}
</body>
```

### 4. Cache-Aware Templates

```twig
{# Use ESI or similar for dynamic consent checks in cached pages #}
{{ render_esi(controller('App\\Controller\\ConsentController::status')) }}
```

---

## Troubleshooting

### Scripts Not Loading

**Problem:** Scripts don't load even after consent is given.

**Solutions:**
1. Clear browser cookies and try again
2. Check category identifier matches exactly (case-sensitive)
3. Verify policy is active in admin panel
4. Check browser console for errors

### Banner Not Showing

**Problem:** Consent banner doesn't appear.

**Solutions:**
1. Verify `{{ consent_banner() }}` is in your template
2. Check if user already has preferences (banner won't show)
3. Verify active policy exists
4. Check React component is loaded

### Consent Not Persisting

**Problem:** User consent is lost on page reload.

**Solutions:**
1. Check cookie storage is enabled in browser
2. Verify cookie prefix in configuration
3. Check cookie expiration settings
4. Ensure HTTPS is used (for secure cookies)

---

## See Also

- [Installation Guide](installation.md)
- [Configuration Reference](configuration.md)
- [API Documentation](api.md)
- [React Components](react-components.md)
