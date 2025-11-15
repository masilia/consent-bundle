# Masilia Consent Bundle - Templates Updated to Ibexa Design System

## ✅ Completed Templates (100%)

All admin templates have been updated to match the Ibexa design system standards.

### **Policy Templates**

#### 1. `admin/policy/list.html.twig` ✅
**Features:**
- Extends `@ibexadesign/ui/layout.html.twig`
- Breadcrumb navigation
- Page title with icon (`ibexa_icon_path('cookie')`)
- Context menu with Statistics link
- Ibexa table component with proper structure
- Translation support (`trans_default_domain 'masilia_consent'`)
- Bootstrap modals for activate/deactivate/delete actions
- Flash message support
- Proper badge styling for status indicators

**Key Components:**
- `@ibexadesign/ui/breadcrumbs.html.twig`
- `@ibexadesign/ui/page_title.html.twig`
- `@ibexadesign/ui/component/context_menu/context_menu.html.twig`
- `@ibexadesign/ui/component/table/table.html.twig`

#### 2. `admin/policy/view.html.twig` ✅
**Features:**
- Detailed policy information in Ibexa cards
- Two-column layout with `ibexa-details` component
- Nested cards for categories
- Embedded tables for cookies and third-party services
- Activate modal for inactive policies
- Back navigation in context menu
- Proper badge usage for status indicators

**Key Components:**
- `ibexa-card` with `ibexa-card--light`
- `ibexa-details` for key-value pairs
- Nested table components
- Modal dialogs

### **Category Templates**

#### 3. `admin/category/list.html.twig` ✅
**Features:**
- Full Ibexa design system integration
- Table with category information
- Badges for required/optional and enabled/disabled status
- Navigation back to policy
- Translation support

**Columns:**
- Name (linked to view)
- Identifier (code formatted)
- Description
- Cookie count
- Required status
- Default enabled status
- Actions (view button)

#### 4. `admin/category/view.html.twig` ✅
**Features:**
- Detailed category information card
- Two-column layout for category details
- Cookies table with script indicators
- Accordion component for cookie details
- Script source and init code display
- Breadcrumb navigation through policy hierarchy

**Key Components:**
- `ibexa-details` for information display
- Table component for cookies list
- Bootstrap accordion for expandable cookie details
- Code blocks for script display

### **Statistics Templates**

#### 5. `admin/statistics/dashboard.html.twig` ✅
**Features:**
- Overview cards with total consents, categories tracked, and period
- Date range information alert
- Category statistics table with acceptance rates
- Progress bars for visual representation
- Detailed breakdown cards for each category
- Export options section (placeholder for future functionality)

**Key Components:**
- Alert component for date range
- Overview cards with display-4 headings
- Table with progress bars
- Grid layout for category breakdown cards
- Dual-color progress bars (success/danger)

## 🎨 Design System Compliance

### **Layout Structure**
```twig
{% extends '@ibexadesign/ui/layout.html.twig' %}
{% trans_default_domain 'masilia_consent' %}

{% block body_class %}ibexa-consent-*-view{% endblock %}
{% block breadcrumbs %}...{% endblock %}
{% block title %}...{% endblock %}
{% block header %}...{% endblock %}
{% block context_menu %}...{% endblock %}
{% block content %}...{% endblock %}
```

### **Common Patterns Used**

1. **Breadcrumbs:**
   ```twig
   {% include '@ibexadesign/ui/breadcrumbs.html.twig' with { items: [...] } %}
   ```

2. **Page Title:**
   ```twig
   {% include '@ibexadesign/ui/page_title.html.twig' with {
       title: '...',
       icon_path: ibexa_icon_path('...')
   } %}
   ```

3. **Context Menu:**
   ```twig
   {% set menu_items %}
       <li class="ibexa-context-menu__item ibexa-adaptive-items__item">
           <a href="..." class="btn ibexa-btn ibexa-btn--ghost">...</a>
       </li>
   {% endset %}
   {{ include('@ibexadesign/ui/component/context_menu/context_menu.html.twig', {
       menu_items: menu_items,
   }) }}
   ```

4. **Tables:**
   ```twig
   {% include '@ibexadesign/ui/component/table/table.html.twig' with {
       headline: results_headline(...),
       head_cols: [...],
       body_rows: [...],
       empty_table_info_text: '...'
   } %}
   ```

5. **Cards:**
   ```twig
   <div class="card ibexa-card ibexa-card--light">
       <div class="card-header ibexa-card__header">
           <h3 class="ibexa-card__title">...</h3>
       </div>
       <div class="card-body ibexa-card__body">...</div>
   </div>
   ```

6. **Details Lists:**
   ```twig
   <dl class="ibexa-details">
       <div class="ibexa-details__item">
           <dt class="ibexa-details__item-label">...</dt>
           <dd class="ibexa-details__item-value">...</dd>
       </div>
   </dl>
   ```

7. **Icons:**
   ```twig
   <svg class="ibexa-icon ibexa-icon--small">
       <use xlink:href="{{ ibexa_icon_path('icon-name') }}"></use>
   </svg>
   ```

8. **Buttons:**
   ```twig
   <a href="..." class="btn ibexa-btn ibexa-btn--ghost ibexa-btn--no-text">
       <svg class="ibexa-icon ibexa-icon--small">
           <use xlink:href="{{ ibexa_icon_path('...') }}"></use>
       </svg>
   </a>
   ```

## 📋 Translation Keys

All templates use the `masilia_consent` translation domain with fallback descriptions:

```twig
{{ 'policy.list.title'|trans|desc('Cookie Policies') }}
```

### **Translation Keys by Section**

**Policy:**
- `policy.list.title`, `policy.view.title`, `policy.information`
- `policy.version`, `policy.last_updated`, `policy.cookie_prefix`, `policy.expiration_days`
- `policy.categories`, `policy.third_party_services`, `policy.status`
- `policy.activate`, `policy.deactivate`, `policy.delete`, `policy.cancel`
- `policy.activate.confirm`, `policy.activate.message`
- `policy.back_to_list`, `policy.statistics`

**Category:**
- `category.list.title`, `category.information`, `category.name`, `category.identifier`
- `category.description`, `category.required`, `category.default_enabled`, `category.position`
- `category.cookies`, `category.back_to_policy`

**Cookie:**
- `cookie.name`, `cookie.provider`, `cookie.purpose`, `cookie.expiry`
- `cookie.script_src`, `cookie.init_code`, `cookie.async`, `cookie.has_script`

**Statistics:**
- `statistics.title`, `statistics.total_consents`, `statistics.categories_tracked`
- `statistics.by_category`, `statistics.accepted`, `statistics.rejected`, `statistics.total`
- `statistics.acceptance_rate`, `statistics.export`, `statistics.date_range`

**Service:**
- `service.name`, `service.category`, `service.description`
- `service.privacy_policy`, `service.view_policy`, `service.enabled`, `service.disabled`

## 🧪 Testing

To test the templates:

```bash
# Clear cache
ddev exec "php bin/console cache:clear"

# Access the admin interface
ddev launch /admin/consent/policy
ddev launch /admin/consent/statistics
```

## 📦 Files Created/Updated

```
packages/masilia/consent-bundle/src/Resources/views/admin/
├── policy/
│   ├── list.html.twig      ✅ Updated
│   └── view.html.twig      ✅ Updated
├── category/
│   ├── list.html.twig      ✅ Created
│   └── view.html.twig      ✅ Created
└── statistics/
    └── dashboard.html.twig ✅ Created
```

## 🎯 Next Steps

1. ✅ **Templates Updated** - All admin templates match Ibexa design system
2. ⏳ **Create Symfony Forms** - PolicyType, CategoryType, CookieType, ThirdPartyServiceType
3. ⏳ **Add CRUD Controllers** - Create/Edit actions for policies and categories
4. ⏳ **Twig Extension** - Helper functions for consent checking and script injection
5. ⏳ **Event Subscriber** - Handle consent change events
6. ⏳ **React Components** - ConsentBanner and PreferencesModal
7. ⏳ **Validation** - Form validation and error handling
8. ⏳ **Documentation** - Update README with new features

## 🎨 Design Highlights

- **Consistent Styling**: All templates use Ibexa's card, table, and button components
- **Responsive Layout**: Bootstrap grid system with proper breakpoints
- **Accessibility**: Proper ARIA attributes, semantic HTML, keyboard navigation
- **User Experience**: Clear navigation, breadcrumbs, context menus, modals for confirmations
- **Visual Feedback**: Progress bars, badges, icons, color-coded status indicators
- **Translation Ready**: All text uses translation filters with fallback descriptions
- **Icon System**: Consistent use of Ibexa icon sprites
- **Modular Components**: Reusable Ibexa components for tables, cards, and forms

---

**Status**: ✅ All templates completed and following Ibexa design system standards
**Date**: November 15, 2025
**Version**: 1.0.0
