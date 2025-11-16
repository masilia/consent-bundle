# Ibexa List View Patterns - Quick Reference

> Essential patterns extracted from analyzing Ibexa's core admin templates

## 📋 Table of Contents

- [List View Types](#list-view-types)
- [Bulk Delete Pattern](#bulk-delete-pattern)
- [Column Types](#column-types)
- [Modal Patterns](#modal-patterns)
- [Quick Start Templates](#quick-start-templates)

---

## List View Types

### Type 1: Full Page List View

Extends main layout, includes breadcrumbs, header, and context menu.

**Used in**: Languages, Sections, Roles, Content Type Groups

```twig
{% extends '@ibexadesign/ui/layout.html.twig' %}
{% from '@ibexadesign/ui/component/macros.html.twig' import results_headline %}
{% form_theme form_delete '@ibexadesign/ui/form_fields.html.twig' %}
{% trans_default_domain 'your_domain' %}

{% block body_class %}ibexa-your-list-view{% endblock %}
{% block breadcrumbs %}...{% endblock %}
{% block header %}...{% endblock %}
{% block context_menu %}...{% endblock %}
{% block content %}
    <section class="container ibexa-container">
        {# Table here #}
    </section>
{% endblock %}
```

### Type 2: Embedded Section List

Standalone section for embedding in detail views (no layout extension).

**Used in**: Policies, Role Assignments

```twig
{% form_theme form_delete '@ibexadesign/ui/form_fields.html.twig' %}
{% trans_default_domain 'your_domain' %}

<section>
    {% set body_rows = [] %}
    {# Build table #}
    {% embed '@ibexadesign/ui/component/table/table.html.twig' %}
        {# ... #}
    {% endembed %}
</section>
```

---

## Bulk Delete Pattern

### Complete Implementation

```twig
{# 1. Delete button in table header (disabled by default) #}
{% embed '@ibexadesign/ui/component/table/table.html.twig' with {...} %}
    {% block header %}
        {% embed '@ibexadesign/ui/component/table/table_header.html.twig' %}
            {% block actions %}
                <button id="delete-items"
                        type="button"
                        class="btn ibexa-btn ibexa-btn--ghost ibexa-btn--small"
                        disabled
                        data-bs-toggle="modal"
                        data-bs-target="#delete-modal">
                    <svg class="ibexa-icon ibexa-icon--small ibexa-icon--trash">
                        <use xlink:href="{{ ibexa_icon_path('trash') }}"></use>
                    </svg>
                    <span class="ibexa-btn__label">
                        {{ 'common.delete'|trans|desc('Delete') }}
                    </span>
                </button>
                
                {# 2. Bulk delete modal #}
                {% include '@ibexadesign/ui/modal/bulk_delete_confirmation.html.twig' with {
                    'id': 'delete-modal',
                    'message': 'confirm.delete'|trans|desc('Delete selected items?'),
                    'data_click': '#form_delete_delete',
                } %}
            {% endblock %}
        {% endembed %}
    {% endblock %}
    
    {# 3. Form with auto-enable button class #}
    {% block between_header_and_table %}
        {{ form_start(form_delete, {
            'action': path('bulk_delete'),
            'attr': { 
                'class': 'ibexa-toggle-btn-state',
                'data-toggle-button-id': '#delete-items'
            }
        }) }}
    {% endblock %}
{% endembed %}
{{ form_end(form_delete) }}
```

### How It Works

1. **Button starts disabled**: `<button id="delete-items" disabled>`
2. **Form has toggle class**: `class: 'ibexa-toggle-btn-state'`
3. **Form points to button**: `data-toggle-button-id: '#delete-items'`
4. **JavaScript enables button** when checkboxes are selected
5. **Modal triggers on click**: `data-bs-toggle="modal" data-bs-target="#delete-modal"`
6. **Modal button clicks form submit**: `data-click: '#form_delete_delete'`

---

## Column Types

### Checkbox Column

```twig
{# Header #}
{ has_checkbox: true }

{# Body #}
{% set col_raw %}
    {{ form_widget(form_delete.items[item.id]) }}
{% endset %}
{% set body_row_cols = body_row_cols|merge([{
    has_checkbox: true,
    content: col_raw,
    raw: true,
}]) %}
```

### Icon Column

```twig
{# Header #}
{ has_icon: true }

{# Body #}
{% set col_raw %}
    <svg class="ibexa-icon ibexa-icon--small">
        <use xlink:href="{{ ibexa_icon_path('icon-name') }}"></use>
    </svg>
{% endset %}
{% set body_row_cols = body_row_cols|merge([{
    has_icon: true,
    content: col_raw,
    raw: true,
}]) %}
```

### Link Column

```twig
{# Header #}
{ content: 'Name'|trans|desc('Name') }

{# Body #}
{% set col_raw %}
    <a href="{{ path('view', {id: item.id}) }}">
        <strong>{{ item.name }}</strong>
    </a>
{% endset %}
{% set body_row_cols = body_row_cols|merge([{
    content: col_raw,
    raw: true,
}]) %}
```

### Badge Column

```twig
{# Header #}
{ content: 'Status'|trans|desc('Status') }

{# Body #}
{% set col_raw %}
    {% if item.isActive %}
        <span class="ibexa-badge ibexa-badge--success">
            {{ 'status.active'|trans|desc('Active') }}
        </span>
    {% else %}
        <span class="ibexa-badge ibexa-badge--secondary">
            {{ 'status.inactive'|trans|desc('Inactive') }}
        </span>
    {% endif %}
{% endset %}
{% set body_row_cols = body_row_cols|merge([{
    content: col_raw,
    raw: true,
}]) %}
```

### Action Buttons Column

```twig
{# Header #}
{ }  {# Empty #}

{# Body - Single Action #}
{% set col_raw %}
    <a class="btn ibexa-btn ibexa-btn--ghost ibexa-btn--no-text"
       href="{{ path('edit', {id: item.id}) }}"
       title="{{ 'common.edit'|trans|desc('Edit') }}">
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

### Multiple Actions

```twig
{% set col_raw %}
    <a class="btn ibexa-btn ibexa-btn--ghost ibexa-btn--no-text"
       href="{{ path('assign', {id: item.id}) }}"
       title="{{ 'action.assign'|trans|desc('Assign') }}">
        <svg class="ibexa-icon ibexa-icon--small">
            <use xlink:href="{{ ibexa_icon_path('assign-user') }}"></use>
        </svg>
    </a>
    <a class="btn ibexa-btn ibexa-btn--ghost ibexa-btn--no-text"
       href="{{ path('copy', {id: item.id}) }}"
       title="{{ 'action.copy'|trans|desc('Copy') }}">
        <svg class="ibexa-icon ibexa-icon--small">
            <use xlink:href="{{ ibexa_icon_path('copy') }}"></use>
        </svg>
    </a>
    <a class="btn ibexa-btn ibexa-btn--ghost ibexa-btn--no-text"
       href="{{ path('edit', {id: item.id}) }}"
       title="{{ 'action.edit'|trans|desc('Edit') }}">
        <svg class="ibexa-icon ibexa-icon--small ibexa-icon--edit">
            <use xlink:href="{{ ibexa_icon_path('edit') }}"></use>
        </svg>
    </a>
{% endset %}
```

---

## Modal Patterns

### Bulk Delete Confirmation

```twig
{% include '@ibexadesign/ui/modal/bulk_delete_confirmation.html.twig' with {
    'id': 'delete-modal',
    'message': 'confirm.delete'|trans|desc('Delete selected items?'),
    'data_click': '#form_delete_delete',
} %}
```

### Custom Delete Label (e.g., "Unassign")

```twig
{% include '@ibexadesign/ui/modal/bulk_delete_confirmation.html.twig' with {
    'id': 'unassign-modal',
    'message': 'confirm.unassign'|trans|desc('Unassign selected users?'),
    'data_click': '#form_unassign_delete',
    'delete_label': 'action.unassign'|trans|desc('Unassign'),
} %}
```

### Custom Edit/Create Modal

```twig
{% embed '@ibexadesign/ui/component/modal/modal.html.twig' with {
    id: 'edit-modal',
    title: 'modal.edit.title'|trans|desc('Edit Item'),
    size: 'large',
} %}
    {% block body_content %}
        {{ form_start(form, {'attr': {'id': 'edit-form'}}) }}
            <div class="ibexa-form-block">
                {{ form_row(form.name, { row_attr: { class: 'ibexa-form-field' } }) }}
                {{ form_row(form.description, { row_attr: { class: 'ibexa-form-field' } }) }}
            </div>
        {{ form_end(form) }}
    {% endblock %}
    
    {% block footer_content %}
        <button type="submit" form="edit-form" class="btn ibexa-btn ibexa-btn--primary">
            <svg class="ibexa-icon ibexa-icon--small">
                <use xlink:href="{{ ibexa_icon_path('checkmark') }}"></use>
            </svg>
            {{ 'common.save'|trans|desc('Save') }}
        </button>
        <button type="button" class="btn ibexa-btn ibexa-btn--secondary" data-bs-dismiss="modal">
            {{ 'common.cancel'|trans|desc('Cancel') }}
        </button>
    {% endblock %}
{% endembed %}
```

---

## Quick Start Templates

### Minimal List View

```twig
{% extends '@ibexadesign/ui/layout.html.twig' %}
{% from '@ibexadesign/ui/component/macros.html.twig' import results_headline %}
{% trans_default_domain 'your_domain' %}

{% block body_class %}ibexa-your-list-view{% endblock %}

{% block breadcrumbs %}
    {% include '@ibexadesign/ui/breadcrumbs.html.twig' with { items: [
        { value: 'breadcrumb.admin'|trans(domain='messages')|desc('Admin') },
        { value: 'Items'|trans|desc('Items') }
    ]} %}
{% endblock %}

{% block header %}
    {% include '@ibexadesign/ui/page_title.html.twig' with {
        title: 'Items'|trans|desc('Items'),
    } %}
{% endblock %}

{% block context_menu %}
    {% set menu_items %}
        <li class="ibexa-context-menu__item ibexa-adaptive-items__item">
            <a href="{{ path('create') }}" class="btn ibexa-btn ibexa-btn--primary">
                <svg class="ibexa-icon ibexa-icon--small ibexa-icon--create">
                    <use xlink:href="{{ ibexa_icon_path('create') }}"></use>
                </svg>
                <span class="ibexa-btn__label">Create</span>
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
        {% for item in items %}
            {% set body_row_cols = [
                { content: '<a href="' ~ path('view', {id: item.id}) ~ '">' ~ item.name ~ '</a>', raw: true },
                { content: item.id },
            ] %}
            {% set body_rows = body_rows|merge([{ cols: body_row_cols }]) %}
        {% endfor %}

        {% include '@ibexadesign/ui/component/table/table.html.twig' with {
            headline: results_headline(items|length),
            head_cols: [
                { content: 'Name'|trans|desc('Name') },
                { content: 'ID'|trans|desc('ID') },
            ],
            body_rows,
        } %}
    </section>
{% endblock %}
```

### List with Bulk Delete

```twig
{# Add form_theme after extends #}
{% form_theme form_delete '@ibexadesign/ui/form_fields.html.twig' %}

{# In content block #}
{% set body_rows = [] %}
{% for item in items %}
    {% set body_row_cols = [
        { has_checkbox: true, content: form_widget(form_delete.items[item.id]), raw: true },
        { content: '<a href="...">' ~ item.name ~ '</a>', raw: true },
        { content: item.id },
        { has_action_btns: true, content: '<a class="btn ibexa-btn ibexa-btn--ghost ibexa-btn--no-text" href="...">...</a>', raw: true },
    ] %}
    {% set body_rows = body_rows|merge([{ cols: body_row_cols }]) %}
{% endfor %}

{% embed '@ibexadesign/ui/component/table/table.html.twig' with {
    headline: results_headline(items|length),
    head_cols: [
        { has_checkbox: true },
        { content: 'Name'|trans|desc('Name') },
        { content: 'ID'|trans|desc('ID') },
        { },
    ],
    body_rows,
} %}
    {% block header %}
        {% embed '@ibexadesign/ui/component/table/table_header.html.twig' %}
            {% block actions %}
                <button id="delete-btn" disabled data-bs-toggle="modal" data-bs-target="#delete-modal"
                        class="btn ibexa-btn ibexa-btn--ghost ibexa-btn--small">
                    <svg class="ibexa-icon ibexa-icon--small ibexa-icon--trash">
                        <use xlink:href="{{ ibexa_icon_path('trash') }}"></use>
                    </svg>
                    <span class="ibexa-btn__label">Delete</span>
                </button>
                {% include '@ibexadesign/ui/modal/bulk_delete_confirmation.html.twig' with {
                    'id': 'delete-modal',
                    'message': 'Delete?'|trans|desc('Delete?'),
                    'data_click': '#form_delete_delete',
                } %}
            {% endblock %}
        {% endembed %}
    {% endblock %}
    {% block between_header_and_table %}
        {{ form_start(form_delete, {
            'attr': { 'class': 'ibexa-toggle-btn-state', 'data-toggle-button-id': '#delete-btn' }
        }) }}
    {% endblock %}
{% endembed %}
{{ form_end(form_delete) }}
```

---

## Common Icons

| Icon | Usage | Path |
|------|-------|------|
| Create | Create button | `ibexa_icon_path('create')` |
| Edit | Edit action | `ibexa_icon_path('edit')` |
| Delete | Delete action | `ibexa_icon_path('trash')` |
| View | View action | `ibexa_icon_path('view')` |
| Copy | Copy action | `ibexa_icon_path('copy')` |
| Assign User | User/group assignment | `ibexa_icon_path('assign-user')` |
| Assign Section | Section assignment | `ibexa_icon_path('assign-section')` |
| Open New Tab | External link | `ibexa_icon_path('open-newtab')` |
| Checkmark | Confirm/success | `ibexa_icon_path('checkmark')` |
| Back | Back navigation | `ibexa_icon_path('back')` |

---

## Button Classes Reference

```css
/* Primary action (blue) */
.ibexa-btn--primary

/* Secondary action (gray) */
.ibexa-btn--secondary

/* Tertiary action (lighter) */
.ibexa-btn--tertiary

/* Ghost action (transparent) */
.ibexa-btn--ghost

/* Danger action (red) */
.ibexa-btn--danger

/* Small size */
.ibexa-btn--small

/* Icon only (no text) */
.ibexa-btn--no-text
```

---

**Last Updated**: November 2025
