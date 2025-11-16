# Ibexa Admin UI Templating Patterns

> **Comprehensive guide for creating admin interfaces that follow Ibexa design system standards**

This document provides detailed patterns and best practices for building admin UI templates in Ibexa DXP.

---

## Table of Contents

- [Overview](#overview)
- [List View Pattern](#list-view-pattern)
- [Detail View Pattern](#detail-view-pattern)
- [Components Reference](#components-reference)
- [Button Styling](#button-styling)
- [Translation Patterns](#translation-patterns)
- [Best Practices](#best-practices)

---

## Overview

### Core Principles

1. **Consistency**: Follow Ibexa's established patterns
2. **Accessibility**: Use semantic HTML and ARIA attributes
3. **Responsiveness**: Mobile-first approach
4. **Modularity**: Reusable components and blocks
5. **Translation**: All text must be translatable with fallback descriptions

### Base Template Structure

```twig
{% extends '@ibexadesign/ui/layout.html.twig' %}
{% trans_default_domain 'your_domain' %}

{% block body_class %}ibexa-your-view-name{% endblock %}
{% block breadcrumbs %}...{% endblock %}
{% block title %}...{% endblock %}
{% block header %}...{% endblock %}
{% block context_menu %}...{% endblock %}
{% block content %}...{% endblock %}
{% block javascripts %}...{% endblock %}
```

---

## List View Pattern

### Essential Imports

```twig
{% from '@ibexadesign/ui/component/macros.html.twig' import results_headline %}
{% form_theme form_delete '@ibexadesign/ui/form_fields.html.twig' %}
```

### Building Table Rows

```twig
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
```

### Rendering Table with Bulk Actions

```twig
{% embed '@ibexadesign/ui/component/table/table.html.twig' with {
    headline: results_headline(pager.getNbResults()),
    head_cols: [
        { has_checkbox: true },
        { content: 'your.column.name'|trans|desc('Name') },
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
                    <span class="ibexa-btn__label">
                        {{ 'common.delete'|trans|desc('Delete') }}
                    </span>
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
```

---

## Detail View Pattern

### Page Header with Tags

```twig
{% block header %}
    {% embed '@ibexadesign/ui/page_title.html.twig' with {
        title: 'your.view.title'|trans|desc('Item Details'),
    } %}
        {% block bottom %}
            <span class="ibexa-icon-tag">
                {{ 'your.identifier'|trans({'%id%': item.identifier})|desc('ID: %id%') }}
            </span>
        {% endblock %}
    {% endembed %}
{% endblock %}
```

### Context Menu Actions

```twig
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
```

### Details Section

```twig
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
    headline: 'your.information'|trans()|desc('Information'),
    items: properties_items,
} only %}
```

### Related Data with Table

```twig
<section>
    {% include '@ibexadesign/ui/component/table/table_header.html.twig' with {
        headline: 'your.related_items'|trans()|desc('Related Items'),
        actions: add_button_html,
    } %}

    {% if items is not empty %}
        {% include '@ibexadesign/ui/component/table/table.html.twig' with {
            head_cols: [...],
            body_rows: body_rows,
        } %}
    {% else %}
        {% include '@ibexadesign/ui/component/alert/alert.html.twig' with {
            type: 'info',
            title: 'your.no_items'|trans|desc('No items found.'),
        } only %}
    {% endif %}
</section>
```

---

## Components Reference

### Alert Component

```twig
{% include '@ibexadesign/ui/component/alert/alert.html.twig' with {
    type: 'info|warning|error|success',
    title: 'Alert message'|trans|desc('Message'),
    show_close_btn: true|false,
    class: 'additional-classes',
} only %}
```

### Table Component

```twig
{% include '@ibexadesign/ui/component/table/table.html.twig' with {
    headline: 'Table Title',
    head_cols: [
        { has_checkbox: true },
        { content: 'Column'|trans|desc('Column') },
        { },
    ],
    body_rows: body_rows,
    show_notice: false,
    notice_message: 'Notice text',
} %}
```

### Modal Component

```twig
{% embed '@ibexadesign/ui/component/modal/modal.html.twig' with {
    id: 'modal-id',
    title: 'Modal Title'|trans|desc('Title'),
    size: 'large',
} %}
    {% block body_content %}
        {{ form_widget(form) }}
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

### Button Variants

```twig
{# Primary - Main actions #}
<button class="ibexa-btn ibexa-btn--primary">
    <span class="ibexa-btn__label">Save</span>
</button>

{# Secondary - Secondary actions #}
<button class="ibexa-btn ibexa-btn--secondary">
    <span class="ibexa-btn__label">Edit</span>
</button>

{# Tertiary - Back/Cancel #}
<button class="ibexa-btn ibexa-btn--tertiary">
    <span class="ibexa-btn__label">Back</span>
</button>

{# Ghost - Subtle actions #}
<button class="ibexa-btn ibexa-btn--ghost">
    <span class="ibexa-btn__label">Action</span>
</button>

{# Ghost Icon-only #}
<button class="ibexa-btn ibexa-btn--ghost ibexa-btn--no-text">
    <svg class="ibexa-icon ibexa-icon--small">
        <use xlink:href="{{ ibexa_icon_path('edit') }}"></use>
    </svg>
</button>

{# Danger - Delete #}
<button class="ibexa-btn ibexa-btn--danger">
    <span class="ibexa-btn__label">Delete</span>
</button>

{# Small size #}
<button class="ibexa-btn ibexa-btn--primary ibexa-btn--small">
    <span class="ibexa-btn__label">Small</span>
</button>
```

**Important**: Never use `btn` class alone - always use `ibexa-btn` classes!

---

## Translation Patterns

### Standard Translation

```twig
{{ 'translation.key'|trans|desc('Fallback text') }}
{{ 'translation.key'|trans({'%param%': value})|desc('Text with %param%') }}
```

### With Domain

```twig
{{ 'breadcrumb.admin'|trans(domain='messages')|desc('Admin') }}
```

### Translation Domain

```twig
{% trans_default_domain 'your_domain' %}
```

---

## Best Practices

### 1. Always Use Components

✅ **Do:**
```twig
{% include '@ibexadesign/ui/component/alert/alert.html.twig' with {
    type: 'info',
    title: 'message'|trans|desc('Message'),
} only %}
```

❌ **Don't:**
```twig
<div class="alert alert-info">Message</div>
```

### 2. Use `only` Keyword

```twig
{% include 'component.html.twig' with {...} only %}
```

### 3. Permission Checks

```twig
{% if can_create %}
    {# Show create button #}
{% endif %}
```

### 4. Pagination

```twig
{% if pager.haveToPaginate %}
    {% include '@ibexadesign/ui/pagination.html.twig' with {
        'pager': pager,
    } %}
{% endif %}
```

### 5. Form Theming

```twig
{% form_theme form '@ibexadesign/ui/form_fields.html.twig' %}
```

### 6. Icon Usage

```twig
<svg class="ibexa-icon ibexa-icon--small">
    <use xlink:href="{{ ibexa_icon_path('icon-name') }}"></use>
</svg>
```

### 7. Macros for Reusable Components

```twig
{% macro action_button(item) %}
    <button class="ibexa-btn ibexa-btn--ghost ibexa-btn--no-text">
        <svg class="ibexa-icon ibexa-icon--small">
            <use xlink:href="{{ ibexa_icon_path('edit') }}"></use>
        </svg>
    </button>
{% endmacro %}
```

---

## Common Patterns Summary

### List View Checklist

- ✅ Extends `@ibexadesign/ui/layout.html.twig`
- ✅ Import `results_headline` macro
- ✅ Set `body_class` block
- ✅ Include breadcrumbs
- ✅ Page title in header block
- ✅ Context menu with Create button
- ✅ Build table rows with proper column types
- ✅ Bulk delete form wraps table
- ✅ Pagination if needed
- ✅ JavaScript entry point

### Detail View Checklist

- ✅ Breadcrumbs with navigation path
- ✅ Page title with optional tags
- ✅ Context menu with Back/Edit buttons
- ✅ Details component for key information
- ✅ Sections for related data
- ✅ Tables or alerts for empty states
- ✅ Modals for actions

### Form View Checklist

- ✅ Form theme set
- ✅ Two-column layout (form + sidebar)
- ✅ Card components for sections
- ✅ Alerts for help text
- ✅ Action buttons in sidebar
- ✅ Proper form_row usage with `ibexa-form-field` class

---

## Resources

- **Ibexa Documentation**: https://doc.ibexa.co/
- **Component Library**: `/vendor/ibexa/admin-ui/src/bundle/Resources/views/themes/admin/ui/component/`
- **Example Templates**: `/vendor/ibexa/admin-ui/src/bundle/Resources/views/themes/admin/`

---

**Last Updated**: November 2025
**Version**: 1.0
