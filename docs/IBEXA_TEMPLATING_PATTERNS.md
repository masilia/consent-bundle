# Ibexa Admin UI Templating Patterns

This document outlines the standard templating patterns used in Ibexa Admin UI and how they should be applied in the Consent Bundle.

## Table of Contents

- [Overview](#overview)
- [List View Pattern](#list-view-pattern)
- [Detail View Pattern](#detail-view-pattern)
- [Components Reference](#components-reference)
- [Button Styling](#button-styling)
- [Form Integration](#form-integration)
- [Translation Patterns](#translation-patterns)

---

## Overview

All templates in the Consent Bundle should follow Ibexa's design system to ensure consistency with the admin interface. This includes:

- Using Ibexa UI components instead of custom HTML
- Following Ibexa's button and styling conventions
- Implementing proper accessibility patterns
- Using translation keys with fallback descriptions

---

## List View Pattern

### Standard Structure

All list views should follow this structure:

```twig
{% extends '@ibexadesign/ui/layout.html.twig' %}

{% from '@ibexadesign/ui/component/macros.html.twig' import results_headline %}
{% form_theme form_delete '@ibexadesign/ui/form_fields.html.twig' %}
{% trans_default_domain 'masilia_consent' %}

{% block body_class %}ibexa-consent-{name}-list-view{% endblock %}

{% block breadcrumbs %}
    {% include '@ibexadesign/ui/breadcrumbs.html.twig' with { items: [
        { value: 'breadcrumb.admin'|trans(domain='messages')|desc('Admin') },
        { value: 'your.list.title'|trans|desc('Your Items') }
    ]} %}
{% endblock %}

{% block title %}{{ 'your.list.title'|trans|desc('Your Items') }}{% endblock %}

{% block header %}
    {% include '@ibexadesign/ui/page_title.html.twig' with {
        title: 'your.list.title'|trans|desc('Your Items'),
    } %}
{% endblock %}

{% block context_menu %}
    {% set menu_items %}
        <li class="ibexa-context-menu__item ibexa-adaptive-items__item">
            <a href="{{ path('your.create') }}" class="ibexa-btn ibexa-btn--primary">
                <svg class="ibexa-icon ibexa-icon--small ibexa-icon--create">
                    <use xlink:href="{{ ibexa_icon_path('create') }}"></use>
                </svg>
                <span class="ibexa-btn__label">{{ 'your.create'|trans|desc('Create') }}</span>
            </a>
        </li>
    {% endset %}
    {{ include('@ibexadesign/ui/component/context_menu/context_menu.html.twig', {
        menu_items: menu_items,
    }) }}
{% endblock %}

{% block content %}
    <section class="container ibexa-container">
        {% set body_rows = [] %}
        
        {% for item in pager.currentPageResults %}
            {% set body_row_cols = [] %}

            {# Checkbox column #}
            {% set col_raw %}
                {{ form_widget(form_delete.items[item.id]) }}
            {% endset %}
            {% set body_row_cols = body_row_cols|merge([{
                has_checkbox: true,
                content: col_raw,
                raw: true,
            }]) %}

            {# Name column with link #}
            {% set col_raw %}
                <a href="{{ path('your.view', {'id': item.id}) }}">
                    {{ item.name }}
                </a>
            {% endset %}
            {% set body_row_cols = body_row_cols|merge([{
                content: col_raw,
                raw: true,
            }]) %}

            {# Data columns #}
            {% set body_row_cols = body_row_cols|merge([
                { content: item.identifier },
                { content: item.id },
            ]) %}

            {# Actions column #}
            {% set col_raw %}
                <a
                    title="{{ 'common.edit'|trans|desc('Edit') }}"
                    href="{{ path('your.edit', {'id': item.id}) }}"
                    class="ibexa-btn ibexa-btn--ghost ibexa-btn--no-text"
                >
                    <svg class="ibexa-icon ibexa-icon--small ibexa-icon--edit">
                        <use xlink:href="{{ ibexa_icon_path('edit') }}"></use>
                    </svg>
                </a>
            {% endset %}
            {% set body_row_cols = body_row_cols|merge([{
                has_action_btns: true,
                content: col_raw,
                raw: true,
            }]) %}

            {% set body_rows = body_rows|merge([{ cols: body_row_cols }]) %}
        {% endfor %}

        {# Render table #}
        {% embed '@ibexadesign/ui/component/table/table.html.twig' with {
            headline: results_headline(pager.getNbResults()),
            head_cols: [
                { has_checkbox: true },
                { content: 'your.column.name'|trans|desc('Name') },
                { content: 'your.column.identifier'|trans|desc('Identifier') },
                { content: 'your.column.id'|trans|desc('ID') },
                { },
            ],
            body_rows,
        } %}
            {% block header %}
                {% embed '@ibexadesign/ui/component/table/table_header.html.twig' %}
                    {% block actions %}
                        <button
                            id="delete-items"
                            type="button"
                            class="ibexa-btn ibexa-btn--ghost ibexa-btn--small"
                            disabled
                            data-bs-toggle="modal"
                            data-bs-target="#delete-modal"
                        >
                            <svg class="ibexa-icon ibexa-icon--small ibexa-icon--trash">
                                <use xlink:href="{{ ibexa_icon_path('trash') }}"></use>
                            </svg>
                            <span class="ibexa-btn__label">{{ 'common.delete'|trans|desc('Delete') }}</span>
                        </button>
                        {% include '@ibexadesign/ui/modal/bulk_delete_confirmation.html.twig' with {
                            'id': 'delete-modal',
                            'message': 'your.delete.confirm'|trans|desc('Delete items?'),
                            'data_click': '#form_delete_delete',
                        } %}
                    {% endblock %}
                {% endembed %}
            {% endblock %}

            {% block between_header_and_table %}
                {{ form_start(form_delete, {
                    'action': path('your.bulk_delete'),
                    'attr': { 
                        'class': 'ibexa-toggle-btn-state', 
                        'data-toggle-button-id': '#delete-items' 
                    }
                }) }}
            {% endblock %}
        {% endembed %}
        {{ form_end(form_delete) }}

        {# Pagination #}
        {% if pager.haveToPaginate %}
            {% include '@ibexadesign/ui/pagination.html.twig' with {
                'pager': pager,
            } %}
        {% endif %}
    </section>
{% endblock %}

{% block javascripts %}
    {{ encore_entry_script_tags('masilia-consent-list-js', null, 'ibexa') }}
{% endblock %}
```

### Column Types

#### 1. Checkbox Column
```twig
{% set col_raw %}
    {{ form_widget(form_delete.items[item.id], {
        "disabled": not deletable[item.id]
    }) }}
{% endset %}
{% set body_row_cols = body_row_cols|merge([{
    has_checkbox: true,
    content: col_raw,
    raw: true,
}]) %}
```

#### 2. Icon Column
```twig
{% set col_raw %}
    <svg class="ibexa-icon ibexa-icon--small">
        <use xlink:href="{{ ibexa_icon_path('your-icon') }}"></use>
    </svg>
{% endset %}
{% set body_row_cols = body_row_cols|merge([{
    has_icon: true,
    content: col_raw,
    raw: true,
}]) %}
```

#### 3. Link Column
```twig
{% set col_raw %}
    <a href="{{ path('your.view', {'id': item.id}) }}">
        {{ item.name }}
    </a>
{% endset %}
{% set body_row_cols = body_row_cols|merge([{
    content: col_raw,
    raw: true,
}]) %}
```

#### 4. Simple Data Columns
```twig
{% set body_row_cols = body_row_cols|merge([
    { content: item.identifier },
    { content: item.id },
    { content: item.date|ibexa_full_datetime },
]) %}
```

#### 5. Centered Column
```twig
{% set col_raw %}
    <span class="badge bg-success">Active</span>
{% endset %}
{% set body_row_cols = body_row_cols|merge([{
    content: col_raw,
    center_content: true,
    raw: true,
}]) %}
```

#### 6. Actions Column
```twig
{% set col_raw %}
    <a
        title="{{ 'common.edit'|trans|desc('Edit') }}"
        href="{{ path('your.edit', {'id': item.id}) }}"
        class="ibexa-btn ibexa-btn--ghost ibexa-btn--no-text"
    >
        <svg class="ibexa-icon ibexa-icon--small ibexa-icon--edit">
            <use xlink:href="{{ ibexa_icon_path('edit') }}"></use>
        </svg>
    </a>
{% endset %}
{% set body_row_cols = body_row_cols|merge([{
    has_action_btns: true,
    content: col_raw,
    raw: true,
}]) %}
```

---

## Detail View Pattern

### Standard Structure

```twig
{% extends '@ibexadesign/ui/layout.html.twig' %}

{% trans_default_domain 'masilia_consent' %}
{% import _self as macros %}

{% block body_class %}ibexa-consent-{name}-view{% endblock %}

{% block breadcrumbs %}
    {% include '@ibexadesign/ui/breadcrumbs.html.twig' with { items: [
        { value: 'breadcrumb.admin'|trans(domain='messages')|desc('Admin') },
        { value: 'your.list.title'|trans|desc('Your Items'), url: path('your.list') },
        { value: item.name }
    ]} %}
{% endblock %}

{% block title %}{{ item.name }}{% endblock %}

{% block header %}
    {% embed '@ibexadesign/ui/page_title.html.twig' with {
        title: 'your.view.title'|trans|desc('Item Details'),
    } %}
        {% block bottom %}
            <span class="ibexa-icon-tag">
                {{ 'your.identifier'|trans({'%id%': item.id})|desc('ID: %id%') }}
            </span>
        {% endblock %}
    {% endembed %}
{% endblock %}

{% block context_menu %}
    {% set menu_items %}
        <li class="ibexa-context-menu__item ibexa-adaptive-items__item">
            <a href="{{ path('your.list') }}" class="ibexa-btn ibexa-btn--tertiary">
                <svg class="ibexa-icon ibexa-icon--small">
                    <use xlink:href="{{ ibexa_icon_path('back') }}"></use>
                </svg>
                <span class="ibexa-btn__label">{{ 'common.back'|trans|desc('Back') }}</span>
            </a>
        </li>
        <li class="ibexa-context-menu__item ibexa-adaptive-items__item">
            <a href="{{ path('your.edit', {id: item.id}) }}" class="ibexa-btn ibexa-btn--secondary">
                <svg class="ibexa-icon ibexa-icon--small ibexa-icon--edit">
                    <use xlink:href="{{ ibexa_icon_path('edit') }}"></use>
                </svg>
                <span class="ibexa-btn__label">{{ 'common.edit'|trans|desc('Edit') }}</span>
            </a>
        </li>
    {% endset %}
    {{ include('@ibexadesign/ui/component/context_menu/context_menu.html.twig', {
        menu_items: menu_items,
    }) }}
{% endblock %}

{% block content %}
    <div class="ibexa-content-container">
        {# Information Section using Details Component #}
        {% set properties_items = [
            {
                label: 'your.field.name'|trans|desc('Name'),
                content: item.name,
            },
            {
                label: 'your.field.identifier'|trans|desc('Identifier'),
                content: '<code>' ~ item.identifier ~ '</code>',
            },
        ] %}

        {% include '@ibexadesign/ui/component/details/details.html.twig' with {
            headline: 'your.information'|trans|desc('Information'),
            items: properties_items,
        } only %}

        {# Data Section with Table #}
        <section>
            {% include '@ibexadesign/ui/component/table/table_header.html.twig' with {
                headline: 'your.data.title'|trans|desc('Related Data'),
                actions: macros.add_button(),
            } %}

            {% if items is not empty %}
                {% set body_rows = [] %}
                {% for data_item in items %}
                    {# Build table rows #}
                {% endfor %}

                {% include '@ibexadesign/ui/component/table/table.html.twig' with {
                    head_cols: [
                        { content: 'column.name'|trans|desc('Name') },
                        { content: 'column.value'|trans|desc('Value') },
                    ],
                    body_rows: body_rows,
                } %}
            {% else %}
                {% include '@ibexadesign/ui/component/alert/alert.html.twig' with {
                    type: 'info',
                    title: 'your.no_data'|trans|desc('No data available.'),
                } only %}
            {% endif %}
        </section>
    </div>
{% endblock %}

{# Macros for reusable components #}
{% macro add_button() %}
    <button 
        type="button" 
        class="ibexa-btn ibexa-btn--primary ibexa-btn--small"
        data-bs-toggle="modal"
        data-bs-target="#add-modal">
        <svg class="ibexa-icon ibexa-icon--small ibexa-icon--create">
            <use xlink:href="{{ ibexa_icon_path('create') }}"></use>
        </svg>
        <span class="ibexa-btn__label">{{ 'common.add'|trans|desc('Add') }}</span>
    </button>
{% endmacro %}
```

### Using Details Component

The details component displays key-value pairs in a structured layout:

```twig
{% set properties_items = [
    {
        label: 'field.label'|trans|desc('Label'),
        content: value,
    },
    {
        label: 'field.html'|trans|desc('HTML Field'),
        content: '<strong>' ~ value ~ '</strong>',
    },
] %}

{% include '@ibexadesign/ui/component/details/details.html.twig' with {
    headline: 'section.title'|trans|desc('Section Title'),
    items: properties_items,
} only %}
```

### Using Table Header with Actions

```twig
{% include '@ibexadesign/ui/component/table/table_header.html.twig' with {
    headline: 'section.title'|trans|desc('Section Title'),
    actions: macros.header_actions(),
    show_notice: true,
    notice_message: 'info.message'|trans|desc('Information message'),
} %}
```

---

## Components Reference

### Core Components

| Component | Path | Usage |
|-----------|------|-------|
| **Layout** | `@ibexadesign/ui/layout.html.twig` | Base layout for all pages |
| **Page Title** | `@ibexadesign/ui/page_title.html.twig` | Page heading with optional bottom block |
| **Breadcrumbs** | `@ibexadesign/ui/breadcrumbs.html.twig` | Navigation breadcrumb trail |
| **Context Menu** | `@ibexadesign/ui/component/context_menu/context_menu.html.twig` | Top-right action buttons |

### Table Components

| Component | Path | Usage |
|-----------|------|-------|
| **Table** | `@ibexadesign/ui/component/table/table.html.twig` | Main table structure |
| **Table Header** | `@ibexadesign/ui/component/table/table_header.html.twig` | Section header with actions |

### UI Components

| Component | Path | Usage |
|-----------|------|-------|
| **Alert** | `@ibexadesign/ui/component/alert/alert.html.twig` | Info, warning, error, success messages |
| **Details** | `@ibexadesign/ui/component/details/details.html.twig` | Key-value pairs display |
| **Modal** | `@ibexadesign/ui/component/modal/modal.html.twig` | Modal dialogs |
| **Pagination** | `@ibexadesign/ui/pagination.html.twig` | Page navigation |

### Alert Component

```twig
{% include '@ibexadesign/ui/component/alert/alert.html.twig' with {
    type: 'info|warning|error|success',
    title: 'message'|trans|desc('Message'),
    subtitle: 'optional subtitle',
    show_close_btn: true|false,
    size: 'small|medium|large',
    class: 'additional-classes',
} only %}
```

**Icon Mapping:**
- `info` → `about` icon
- `warning` → `warning` icon
- `error` → `notice` icon
- `success` → `approved` icon

### Modal Component

```twig
{% embed '@ibexadesign/ui/component/modal/modal.html.twig' with {
    id: 'modal-id',
    title: 'modal.title'|trans|desc('Modal Title'),
    size: 'small|large',
} %}
    {% block body_content %}
        {# Modal content #}
    {% endblock %}
    {% block footer_content %}
        <button type="submit" class="ibexa-btn ibexa-btn--primary">
            {{ 'common.save'|trans|desc('Save') }}
        </button>
        <button type="button" class="ibexa-btn ibexa-btn--secondary" data-bs-dismiss="modal">
            {{ 'common.cancel'|trans|desc('Cancel') }}
        </button>
    {% endblock %}
{% endembed %}
```

---

## Button Styling

### Button Classes

**IMPORTANT:** Never use `btn` class alone. Always use `ibexa-btn` classes.

```twig
{# ❌ WRONG #}
<button class="btn btn-primary">Button</button>

{# ✅ CORRECT #}
<button class="ibexa-btn ibexa-btn--primary">Button</button>
```

### Button Variants

```twig
{# Primary Action (Create, Submit) #}
<button class="ibexa-btn ibexa-btn--primary">
    <svg class="ibexa-icon ibexa-icon--small ibexa-icon--create">
        <use xlink:href="{{ ibexa_icon_path('create') }}"></use>
    </svg>
    <span class="ibexa-btn__label">Create</span>
</button>

{# Secondary Action (Edit, Update) #}
<button class="ibexa-btn ibexa-btn--secondary">
    <svg class="ibexa-icon ibexa-icon--small ibexa-icon--edit">
        <use xlink:href="{{ ibexa_icon_path('edit') }}"></use>
    </svg>
    <span class="ibexa-btn__label">Edit</span>
</button>

{# Tertiary Action (Back, Cancel) #}
<button class="ibexa-btn ibexa-btn--tertiary">
    <svg class="ibexa-icon ibexa-icon--small">
        <use xlink:href="{{ ibexa_icon_path('back') }}"></use>
    </svg>
    <span class="ibexa-btn__label">Back</span>
</button>

{# Ghost (Subtle, Table Actions) #}
<button class="ibexa-btn ibexa-btn--ghost">
    <svg class="ibexa-icon ibexa-icon--small ibexa-icon--trash">
        <use xlink:href="{{ ibexa_icon_path('trash') }}"></use>
    </svg>
    <span class="ibexa-btn__label">Delete</span>
</button>

{# Icon-Only Ghost #}
<button class="ibexa-btn ibexa-btn--ghost ibexa-btn--no-text" title="Edit">
    <svg class="ibexa-icon ibexa-icon--small ibexa-icon--edit">
        <use xlink:href="{{ ibexa_icon_path('edit') }}"></use>
    </svg>
</button>

{# Danger (Delete Confirmation) #}
<button class="ibexa-btn ibexa-btn--danger">
    <svg class="ibexa-icon ibexa-icon--small">
        <use xlink:href="{{ ibexa_icon_path('trash') }}"></use>
    </svg>
    <span class="ibexa-btn__label">Delete</span>
</button>

{# Warning #}
<button class="ibexa-btn ibexa-btn--warning">
    <span class="ibexa-btn__label">Deactivate</span>
</button>

{# Small Size #}
<button class="ibexa-btn ibexa-btn--primary ibexa-btn--small">
    <span class="ibexa-btn__label">Small Button</span>
</button>
```

### Button Groups

For action buttons in table headers or detail views:

```twig
<div class="ibexa-extra-actions__btns">
    <button class="ibexa-btn ibexa-btn--secondary ibexa-btn--small">Edit</button>
    <button class="ibexa-btn ibexa-btn--secondary ibexa-btn--small">Delete</button>
</div>
```

---

## Form Integration

### Form Theme

Always set the form theme to use Ibexa styling:

```twig
{% form_theme form '@ibexadesign/ui/form_fields.html.twig' %}
```

### Form in Modal

```twig
{% form_theme form '@ibexadesign/ui/form_fields.html.twig' %}
{% embed '@ibexadesign/ui/component/modal/modal.html.twig' with {
    id: 'form-modal',
    title: 'form.title'|trans|desc('Form Title'),
    size: 'large',
} %}
    {% block body_content %}
        {{ form_widget(form) }}
    {% endblock %}
    {% block footer_content %}
        <button type="submit" form="{{ form.vars.id }}" class="ibexa-btn ibexa-btn--primary">
            <svg class="ibexa-icon ibexa-icon--small">
                <use xlink:href="{{ ibexa_icon_path('checkmark') }}"></use>
            </svg>
            {{ 'common.save'|trans|desc('Save') }}
        </button>
        <button type="button" class="ibexa-btn ibexa-btn--secondary" data-bs-dismiss="modal">
            {{ 'common.cancel'|trans|desc('Cancel') }}
        </button>
    {% endblock %}
{% endembed %}
```

### Bulk Delete Form

```twig
{% form_theme form_delete '@ibexadesign/ui/form_fields.html.twig' %}

{# Form wraps table via between_header_and_table block #}
{% block between_header_and_table %}
    {{ form_start(form_delete, {
        'action': path('your.bulk_delete'),
        'attr': { 
            'class': 'ibexa-toggle-btn-state', 
            'data-toggle-button-id': '#delete-button' 
        }
    }) }}
{% endblock %}

{# After table #}
{{ form_end(form_delete) }}
```

---

## Translation Patterns

### Translation Keys

Always use translation keys with fallback descriptions:

```twig
{{ 'translation.key'|trans|desc('Fallback text') }}

{# With parameters #}
{{ 'translation.key'|trans({'%param%': value})|desc('Text with %param%') }}

{# With domain #}
{{ 'translation.key'|trans(domain='masilia_consent')|desc('Fallback') }}
```

### Translation Domain

Set the default domain at the top of each template:

```twig
{% trans_default_domain 'masilia_consent' %}
```

### Common Translation Keys

```yaml
# Common actions
common.create: Create
common.edit: Edit
common.delete: Delete
common.save: Save
common.cancel: Cancel
common.back: Back
common.actions: Actions

# List view
list.title: Items
list.empty: No items found
list.count: "%count% items"

# Form
form.save: Save Changes
form.create: Create Item
form.required: This field is required

# Messages
message.success.created: Item created successfully
message.success.updated: Item updated successfully
message.success.deleted: Item deleted successfully
message.error.not_found: Item not found
```

---

## Badges and Status

### Using Ibexa Badges

```twig
{# Success/Active #}
<span class="ibexa-badge ibexa-badge--success">Active</span>

{# Warning #}
<span class="ibexa-badge ibexa-badge--warning">Required</span>

{# Info/Complementary #}
<span class="ibexa-badge ibexa-badge--complementary">Default</span>

{# Error/Danger #}
<span class="ibexa-badge ibexa-badge--danger">Inactive</span>

{# Neutral #}
<span class="ibexa-badge ibexa-badge--info">Draft</span>
```

### Icon Tags

For metadata display in headers:

```twig
{% block header %}
    {% embed '@ibexadesign/ui/page_title.html.twig' with {
        title: 'Page Title',
    } %}
        {% block bottom %}
            <span class="ibexa-icon-tag">
                {{ 'metadata.info'|trans|desc('Metadata Info') }}
            </span>
        {% endblock %}
    {% endembed %}
{% endblock %}
```

---

## Best Practices

### 1. Always Use Components

❌ **Don't** create custom HTML for common UI elements:
```twig
<div class="alert alert-info">Message</div>
```

✅ **Do** use Ibexa components:
```twig
{% include '@ibexadesign/ui/component/alert/alert.html.twig' with {
    type: 'info',
    title: 'Message',
} only %}
```

### 2. Use Macros for Reusable Elements

```twig
{% import _self as macros %}

{% macro action_buttons(item) %}
    <div class="ibexa-extra-actions__btns">
        <button class="ibexa-btn ibexa-btn--secondary ibexa-btn--small">
            {{ 'common.edit'|trans|desc('Edit') }}
        </button>
    </div>
{% endmacro %}

{# Usage #}
{{ macros.action_buttons(item) }}
```

### 3. Consistent Naming

- **Routes**: `masilia_consent_admin_{entity}_{action}`
- **Templates**: `admin/{entity}/{action}.html.twig`
- **Translation keys**: `{entity}.{context}.{key}`
- **CSS classes**: `ibexa-consent-{entity}-{context}`

### 4. Accessibility

- Always add `title` attributes to icon-only buttons
- Use semantic HTML elements
- Include proper ARIA attributes (handled by components)
- Ensure keyboard navigation works

### 5. Performance

- Use `only` keyword when including components to prevent variable leakage
- Minimize template complexity
- Use macros for repeated code

---

## Examples from Consent Bundle

### Policy List View

See: `src/Resources/views/admin/policy/list.html.twig`

Key features:
- Standard list structure
- Bulk delete functionality
- Action buttons (view, activate, deactivate, delete)
- Pagination
- Flash messages with alert component

### Policy Detail View (Refactored)

See: `src/Resources/views/admin/policy/view_refactored.html.twig`

Key features:
- Details component for policy information
- Section-based layout for categories
- Table component for cookies
- Macros for reusable buttons
- Modal forms for CRUD operations
- Alert component for empty states

### Service Forms

See: `src/Resources/views/admin/service/create.html.twig`

Key features:
- Form theme integration
- Alert component for informational messages
- Proper button styling
- Card-based layout for form sections

---

## Migration Checklist

When updating existing templates:

- [ ] Replace `btn` classes with `ibexa-btn` classes
- [ ] Use Ibexa alert component instead of Bootstrap alerts
- [ ] Use details component for key-value displays
- [ ] Use table components for data tables
- [ ] Add translation keys with fallback descriptions
- [ ] Use proper button variants (primary, secondary, tertiary, ghost)
- [ ] Add macros for reusable elements
- [ ] Ensure forms use Ibexa form theme
- [ ] Use `only` keyword with component includes
- [ ] Add proper breadcrumbs and page titles

---

## Resources

- **Ibexa Admin UI Bundle**: `/ibexa/vendor/ibexa/admin-ui/src/bundle/Resources/views/themes/admin/`
- **UI Components**: `/ibexa/vendor/ibexa/admin-ui/src/bundle/Resources/views/themes/admin/ui/component/`
- **Consent Bundle Templates**: `/packages/masilia/consent-bundle/src/Resources/views/admin/`

---

## Support

For questions or issues related to templating patterns, refer to:
- Ibexa documentation: https://doc.ibexa.co
- This project's README.md
- Code examples in the Ibexa admin-ui bundle
