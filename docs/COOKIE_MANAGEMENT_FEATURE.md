# Cookie Management Feature

## Overview

Added complete CRUD functionality for cookies within each category, following Ibexa's admin UI patterns.

## Files Created/Modified

### 1. New Controller: `CookieAdminController.php`

**Location**: `/src/Controller/Admin/CookieAdminController.php`

**Routes**:
- `POST /admin/consent/cookie/category/{categoryId}/create` - Create cookie
- `POST /admin/consent/cookie/{id}/edit` - Edit cookie
- `POST /admin/consent/cookie/{id}/delete` - Delete cookie

**Features**:
- ParamConverter for automatic category entity resolution
- Form handling with validation error display
- Flash messages for user feedback
- Automatic redirect back to policy view

### 2. Updated: `PolicyAdminController.php`

**Changes**:
- Added `Cookie` and `CookieType` imports
- Created `addCookieForms` array for each category
- Created `editCookieForms` nested array for each cookie
- Passed forms to template

**Code**:
```php
foreach ($policy->getCategories() as $category) {
    // Add cookie form
    $newCookie = new Cookie();
    $newCookie->setCategory($category);
    $addCookieForms[$category->id] = $this->createForm(CookieType::class, $newCookie, [
        'action' => $this->generateUrl('masilia_consent_admin_cookie_create', [
            'categoryId' => $category->getId()
        ]),
    ])->createView();
    
    // Edit cookie forms
    $editCookieForms[$category->id] = [];
    foreach ($category->getCookies() as $cookie) {
        $editCookieForms[$category->id][$cookie->id] = $this->createForm(...);
    }
}
```

### 3. Updated: `view_refactored.html.twig`

#### A. Cookie Table - Added Action Buttons

Each cookie row now has Edit and Delete buttons:

```twig
{% set actions_col %}
    <button type="button" class="btn ibexa-btn ibexa-btn--ghost ibexa-btn--no-text"
            data-bs-toggle="modal" data-bs-target="#edit-cookie-{{ cookie.id }}">
        <svg class="ibexa-icon ibexa-icon--small ibexa-icon--edit">
            <use xlink:href="{{ ibexa_icon_path('edit') }}"></use>
        </svg>
    </button>
    <button type="button" class="btn ibexa-btn ibexa-btn--ghost ibexa-btn--no-text"
            data-bs-toggle="modal" data-bs-target="#delete-cookie-{{ cookie.id }}">
        <svg class="ibexa-icon ibexa-icon--small ibexa-icon--trash">
            <use xlink:href="{{ ibexa_icon_path('trash') }}"></use>
        </svg>
    </button>
{% endset %}
```

#### B. Category Header - Added "Add Cookie" Button

Updated `category_header_tools` macro:

```twig
<button type="button" class="btn ibexa-btn ibexa-btn--primary ibexa-btn--small"
        data-bs-toggle="modal" data-bs-target="#add-cookie-{{ category.id }}">
    <svg class="ibexa-icon ibexa-icon--small ibexa-icon--create">
        <use xlink:href="{{ ibexa_icon_path('create') }}"></use>
    </svg>
    <span class="ibexa-btn__label">Add Cookie</span>
</button>
```

#### C. Category Description Display

Added description display above cookie table:

```twig
{% if category.description %}
    <div class="ibexa-table-description">
        <p>{{ category.description }}</p>
    </div>
{% endif %}
```

#### D. Cookie Modals

**Add Cookie Modal** (per category):
- Large modal with form
- Fields: name, provider, purpose, expiry
- Optional script section: scriptSrc, scriptAsync, initCode
- Position field
- Form ID: `add-cookie-form-{categoryId}`

**Edit Cookie Modal** (per cookie):
- Same structure as add modal
- Pre-filled with cookie data
- Form ID: `edit-cookie-form-{cookieId}`

**Delete Cookie Modal** (per cookie):
- Uses `ibexa-modal--send-to-trash` pattern
- No header (`no_header: true`)
- Confirmation message
- CSRF protection
- Danger button for delete action

## Form Structure

### Cookie Form Fields

```twig
<div class="ibexa-form-block">
    {{ form_row(form.name) }}
    {{ form_row(form.provider) }}
    {{ form_row(form.purpose) }}
    {{ form_row(form.expiry) }}
    <hr>
    <h3>Script Configuration (Optional)</h3>
    {{ form_row(form.scriptSrc) }}
    {{ form_row(form.scriptAsync) }}
    {{ form_row(form.initCode) }}
    <hr>
    {{ form_row(form.position) }}
</div>
```

### Form Submission

All forms use:
- `form_start()` / `form_end()` pattern
- Explicit form IDs for submit button targeting
- `ibexa-form-block` wrapper
- `ibexa-form-field` row attributes
- Proper CSRF protection

## UI/UX Features

### Button Hierarchy

1. **Add Cookie** - Primary button (blue) in category header
2. **Edit Cookie** - Ghost button (transparent) in table row
3. **Delete Cookie** - Ghost button (transparent) in table row
4. **Modal Submit** - Primary button (blue)
5. **Modal Delete** - Danger button (red)
6. **Modal Cancel** - Secondary button (gray)

### Icons

- `create` - Add cookie
- `edit` - Edit cookie
- `trash` - Delete cookie
- `checkmark` - Submit/Save

### Responsive Design

- Tables are scrollable on mobile
- Modals are responsive (large size)
- Forms adapt to screen size
- Touch-friendly button targets

## Translation Keys

### New Keys Added

```yaml
cookie:
  add: 'Add Cookie'
  add.title: 'Add Cookie to %category%'
  edit.title: 'Edit Cookie: %name%'
  delete.message: 'Delete cookie "%name%"? This action cannot be undone.'
  form:
    create: 'Create Cookie'
    save: 'Save Changes'
    script_section: 'Script Configuration (Optional)'
```

## Testing Checklist

- [ ] Add cookie button appears in category header
- [ ] Add cookie modal opens with empty form
- [ ] Add cookie form submits and creates cookie
- [ ] Cookie appears in table with edit/delete buttons
- [ ] Edit cookie modal opens with pre-filled data
- [ ] Edit cookie form submits and updates cookie
- [ ] Delete cookie modal shows confirmation
- [ ] Delete cookie removes cookie from database
- [ ] Flash messages appear for all actions
- [ ] Form validation works (required fields)
- [ ] Script fields are optional
- [ ] Position field accepts numbers only
- [ ] CSRF tokens validate correctly
- [ ] Redirects back to policy view after actions

## Database Operations

### Create
```php
$cookie = new Cookie();
$cookie->setCategory($category);
// ... set other fields from form
$cookieRepository->save($cookie, true);
```

### Update
```php
// Cookie loaded by ParamConverter
// Form updates entity automatically
$cookieRepository->save($cookie, true);
```

### Delete
```php
$cookieRepository->remove($cookie, true);
```

## Security

- ✅ CSRF protection on all forms
- ✅ ParamConverter validates entity existence
- ✅ Form validation on all fields
- ✅ Cascade delete (cookies deleted when category deleted)
- ✅ Flash messages don't expose sensitive data

## Performance Considerations

- Forms are created per-category/per-cookie in controller
- All forms rendered in single page load
- No AJAX (follows Ibexa pattern)
- Forms only submitted on user action

## Future Enhancements

1. **Bulk Actions**: Select multiple cookies for deletion
2. **Drag & Drop**: Reorder cookies by dragging
3. **Import/Export**: Import cookies from JSON/CSV
4. **Templates**: Cookie templates for common services
5. **Validation**: URL validation for scriptSrc
6. **Preview**: Preview cookie consent banner with current cookies

---

**Implemented**: November 2025
