# Ibexa Admin UI - List View Component Analysis

> Deep analysis of Ibexa's list view patterns and component architecture

## Component Locations

```
vendor/ibexa/admin-ui/src/bundle/Resources/views/themes/admin/ui/
├── component/
│   ├── table/
│   │   ├── table.html.twig              # Main table component
│   │   ├── table_header.html.twig       # Headline + actions bar
│   │   ├── table_head_cell.html.twig    # Column headers
│   │   ├── table_body_cell.html.twig    # Table cells
│   │   └── empty_table_body_row.html.twig
│   ├── alert/alert.html.twig
│   ├── modal/modal.html.twig
│   ├── context_menu/context_menu.html.twig
│   ├── details/details.html.twig
│   └── macros.html.twig
├── layout.html.twig
├── page_title.html.twig
├── breadcrumbs.html.twig
└── pagination.html.twig
```

## Table Component Deep Dive

### Architecture

The table component uses a **builder pattern** with arrays:

```twig
{% set body_rows = [] %}
{% for item in items %}
    {% set body_row_cols = [] %}
    
    {# Build columns #}
    {% set body_row_cols = body_row_cols|merge([{
        content: 'value',
        raw: false,
    }]) %}
    
    {# Add row #}
    {% set body_rows = body_rows|merge([{
        cols: body_row_cols,
    }]) %}
{% endfor %}
```

### Column Types

#### 1. Checkbox Column
```twig
{
    has_checkbox: true,
    content: form_widget(form.items[item.id]),
    raw: true,
}
```

#### 2. Link Column
```twig
{% set col_raw %}
    <a href="{{ path('route', {'id': item.id}) }}">
        {{ item.name }}
    </a>
{% endset %}
{% set body_row_cols = body_row_cols|merge([{
    content: col_raw,
    raw: true,
}]) %}
```

#### 3. Badge Column
```twig
{% set col_raw %}
    <span class="ibexa-badge ibexa-badge--success">Active</span>
{% endset %}
{% set body_row_cols = body_row_cols|merge([{
    content: col_raw,
    raw: true,
}]) %}
```

#### 4. Action Buttons Column
```twig
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

### Table Parameters

```twig
{% include '@ibexadesign/ui/component/table/table.html.twig' with {
    headline: results_headline(count),
    head_cols: [
        { has_checkbox: true },
        { content: 'Name'|trans|desc('Name') },
        { content: 'ID'|trans|desc('ID') },
        { },  # Empty for actions
    ],
    body_rows: body_rows,
    show_notice: false,
    notice_message: 'Info text',
    is_scrollable: true,
} %}
```

### Bulk Actions Pattern

```twig
{% embed '@ibexadesign/ui/component/table/table.html.twig' with {...} %}
    {% block header %}
        {% embed '@ibexadesign/ui/component/table/table_header.html.twig' %}
            {% block actions %}
                <button id="delete-btn" disabled 
                        data-bs-toggle="modal" 
                        data-bs-target="#delete-modal">
                    Delete
                </button>
            {% endblock %}
        {% endembed %}
    {% endblock %}

    {% block between_header_and_table %}
        {{ form_start(form_delete, {
            'attr': { 
                'class': 'ibexa-toggle-btn-state', 
                'data-toggle-button-id': '#delete-btn' 
            }
        }) }}
    {% endblock %}
{% endembed %}
{{ form_end(form_delete) }}
```

## Alert Component

```twig
{% include '@ibexadesign/ui/component/alert/alert.html.twig' with {
    type: 'info|warning|error|success',
    title: 'Message'|trans|desc('Message'),
    show_close_btn: true,
    size: 'small|medium|large',
} only %}
```

**Types & Icons**:
- `info` → `about` icon
- `warning` → `warning` icon
- `error` → `notice` icon
- `success` → `approved` icon

## Modal Component

```twig
{% embed '@ibexadesign/ui/component/modal/modal.html.twig' with {
    id: 'modal-id',
    title: 'Title'|trans|desc('Title'),
    size: 'small|large|extra-large',
} %}
    {% block body_content %}
        <p>Content</p>
    {% endblock %}
    {% block footer_content %}
        <button class="btn ibexa-btn ibexa-btn--primary">Save</button>
        <button class="btn ibexa-btn ibexa-btn--secondary" data-bs-dismiss="modal">Cancel</button>
    {% endblock %}
{% endembed %}
```

**Delete Modal**:
```twig
{% embed '@ibexadesign/ui/component/modal/modal.html.twig' with {
    class: 'ibexa-modal--send-to-trash',
    no_header: true,
    id: 'delete-modal',
} %}
    {% block body_content %}
        <p>{{ 'confirm.delete'|trans|desc('Delete?') }}</p>
    {% endblock %}
    {% block footer_content %}
        <button class="btn ibexa-btn ibexa-btn--danger">Delete</button>
        <button class="btn ibexa-btn ibexa-btn--secondary" data-bs-dismiss="modal">Cancel</button>
    {% endblock %}
{% endembed %}
```

## Context Menu

```twig
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
```

## Details Component

```twig
{% set properties_items = [
    {
        label: 'Name'|trans|desc('Name'),
        content: item.name,
    },
    {
        label: 'ID'|trans|desc('ID'),
        content: '<code>' ~ item.id ~ '</code>',
        content_raw: '<code>' ~ item.id ~ '</code>',
    },
] %}

{% include '@ibexadesign/ui/component/details/details.html.twig' with {
    headline: 'Information'|trans()|desc('Information'),
    items: properties_items,
} only %}
```

## Page Title

```twig
{% embed '@ibexadesign/ui/page_title.html.twig' with {
    title: 'Page Title'|trans|desc('Title'),
} %}
    {% block bottom %}
        <span class="ibexa-icon-tag">ID: {{ item.id }}</span>
    {% endblock %}
{% endembed %}
```

## Breadcrumbs

```twig
{% include '@ibexadesign/ui/breadcrumbs.html.twig' with { items: [
    { value: 'breadcrumb.admin'|trans(domain='messages')|desc('Admin') },
    { value: 'Items'|trans|desc('Items'), url: path('list') },
    { value: 'View'|trans|desc('View') }
]} %}
```

## Button Classes

```css
.ibexa-btn--primary          /* Blue - main actions */
.ibexa-btn--secondary        /* Gray - secondary actions */
.ibexa-btn--tertiary         /* Light - back/cancel */
.ibexa-btn--ghost            /* Transparent - subtle actions */
.ibexa-btn--danger           /* Red - delete */
.ibexa-btn--small            /* Smaller size */
.ibexa-btn--no-text          /* Icon only */
```

## Badge Classes

```css
.ibexa-badge--success        /* Green */
.ibexa-badge--warning        /* Yellow */
.ibexa-badge--info           /* Blue */
.ibexa-badge--secondary      /* Gray */
.ibexa-badge--complementary  /* Purple */
```

## Icon Usage

```twig
<svg class="ibexa-icon ibexa-icon--small">
    <use xlink:href="{{ ibexa_icon_path('icon-name') }}"></use>
</svg>
```

**Sizes**: `--tiny`, `--small`, `--medium`, `--small-medium`

**Common Icons**: `create`, `edit`, `trash`, `view`, `back`, `checkmark`, `circle-close`, `about-info`, `warning`, `approved`

## Complete List View Template

```twig
{% extends '@ibexadesign/ui/layout.html.twig' %}
{% from '@ibexadesign/ui/component/macros.html.twig' import results_headline %}
{% form_theme form_delete '@ibexadesign/ui/form_fields.html.twig' %}
{% trans_default_domain 'your_domain' %}

{% block body_class %}ibexa-your-list-view{% endblock %}

{% block breadcrumbs %}
    {% include '@ibexadesign/ui/breadcrumbs.html.twig' with { items: [
        { value: 'breadcrumb.admin'|trans(domain='messages')|desc('Admin') },
        { value: 'Items'|trans|desc('Items') }
    ]} %}
{% endblock %}

{% block title %}{{ 'Items'|trans|desc('Items') }}{% endblock %}

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
        {% for item in pager.currentPageResults %}
            {% set body_row_cols = [] %}

            {# Checkbox #}
            {% set col_raw %}{{ form_widget(form_delete.items[item.id]) }}{% endset %}
            {% set body_row_cols = body_row_cols|merge([{
                has_checkbox: true, content: col_raw, raw: true,
            }]) %}

            {# Name with link #}
            {% set col_raw %}<a href="{{ path('view', {id: item.id}) }}">{{ item.name }}</a>{% endset %}
            {% set body_row_cols = body_row_cols|merge([{content: col_raw, raw: true}]) %}

            {# Data columns #}
            {% set body_row_cols = body_row_cols|merge([
                { content: item.id },
                { content: item.createdAt|date('Y-m-d') },
            ]) %}

            {# Actions #}
            {% set col_raw %}
                <a class="btn ibexa-btn ibexa-btn--ghost ibexa-btn--no-text" 
                   href="{{ path('edit', {id: item.id}) }}" title="Edit">
                    <svg class="ibexa-icon ibexa-icon--small ibexa-icon--edit">
                        <use xlink:href="{{ ibexa_icon_path('edit') }}"></use>
                    </svg>
                </a>
            {% endset %}
            {% set body_row_cols = body_row_cols|merge([{
                has_action_btns: true, content: col_raw, raw: true,
            }]) %}

            {% set body_rows = body_rows|merge([{ cols: body_row_cols }]) %}
        {% endfor %}

        {% embed '@ibexadesign/ui/component/table/table.html.twig' with {
            headline: results_headline(pager.getNbResults()),
            head_cols: [
                { has_checkbox: true },
                { content: 'Name'|trans|desc('Name') },
                { content: 'ID'|trans|desc('ID') },
                { content: 'Created'|trans|desc('Created') },
                { },
            ],
            body_rows,
        } %}
            {% block header %}
                {% embed '@ibexadesign/ui/component/table/table_header.html.twig' %}
                    {% block actions %}
                        <button id="delete-btn" disabled data-bs-toggle="modal" 
                                data-bs-target="#delete-modal" 
                                class="btn ibexa-btn ibexa-btn--ghost ibexa-btn--small">
                            <svg class="ibexa-icon ibexa-icon--small ibexa-icon--trash">
                                <use xlink:href="{{ ibexa_icon_path('trash') }}"></use>
                            </svg>
                            <span class="ibexa-btn__label">Delete</span>
                        </button>
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

        {% if pager.haveToPaginate %}
            {% include '@ibexadesign/ui/pagination.html.twig' with {'pager': pager} %}
        {% endif %}
    </section>
{% endblock %}

{% block javascripts %}
    {{ encore_entry_script_tags('your-list-js', null, 'ibexa') }}
{% endblock %}
```

## Advanced List View Patterns

### Pattern 1: Standalone Section List (No Layout Extension)

Some lists are embedded in detail views and don't extend the main layout:

```twig
{% form_theme form '@ibexadesign/ui/form_fields.html.twig' %}
{% trans_default_domain 'your_domain' %}

<section>
    {% set body_rows = [] %}
    {# Build rows... #}
    
    {% embed '@ibexadesign/ui/component/table/table.html.twig' with {...} %}
        {% block header %}
            {% embed '@ibexadesign/ui/component/table/table_header.html.twig' %}
                {% block actions %}
                    <a href="{{ path('create') }}" class="btn ibexa-btn ibexa-btn--tertiary ibexa-btn--small">
                        Create
                    </a>
                    <button id="delete-btn" disabled>Delete</button>
                {% endblock %}
            {% endembed %}
        {% endblock %}
        
        {% block between_header_and_table %}
            {{ form_start(form_delete, {...}) }}
        {% endblock %}
    {% endembed %}
    
    {{ form_end(form_delete) }}
    
    {% if pager.haveToPaginate %}
        {% include '@ibexadesign/ui/pagination.html.twig' with {
            'pager': pager,
            'paginaton_params': {
                'routeName': route_name,
                'routeParams': {'_fragment': 'section-id', 'id': item.id},
                'pageParameter': '[page]',
            }
        } %}
    {% endif %}
</section>
```

### Pattern 2: Icon Column

```twig
{# Header #}
{ has_icon: true }

{# Body #}
{% set col_raw %}
    <svg class="ibexa-icon ibexa-icon--small">
        <use xlink:href="{{ ibexa_content_type_group_icon(item.identifier) }}"></use>
    </svg>
{% endset %}
{% set body_row_cols = body_row_cols|merge([{
    has_icon: true,
    content: col_raw,
    raw: true,
}]) %}
```

### Pattern 3: Multiple Action Buttons

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
{% set body_row_cols = body_row_cols|merge([{
    has_action_btns: true,
    content: col_raw,
    raw: true,
}]) %}
```

### Pattern 4: Conditional Disabled Checkboxes

```twig
{% set show_table_notice = false %}

{% for item in items %}
    {% set col_raw %}
        {% if can_delete %}
            {% if not show_table_notice and not deletable[item.id] %}
                {% set show_table_notice = true %}
            {% endif %}
            
            {{ form_widget(form_delete.items[item.id], {
                'disabled': not deletable[item.id]
            }) }}
        {% else %}
            {% do form_delete.items.setRendered %}
        {% endif %}
    {% endset %}
    {# ... #}
{% endfor %}

{# Show notice in table #}
{% embed '@ibexadesign/ui/component/table/table.html.twig' with {
    show_notice: show_table_notice,
    notice_message: 'cannot_delete_notice'|trans|desc('Some items cannot be deleted.'),
} %}
```

### Pattern 5: Complex Cell Content (Lists)

```twig
{% set col_raw %}
    {%- if item.limitations is not empty -%}
        <ul class="list-unstyled m-0">
            {%- for limitation in item.limitations -%}
                <li>
                    <span class="font-weight-bold" 
                          title="{{ 'tooltip'|trans({'%id%': limitation.id})|desc('Tooltip') }}">
                        {{ limitation.name }}:
                    </span>
                    {{ limitation.value }}
                </li>
            {%- endfor -%}
        </ul>
    {%- else -%}
        {{- 'none'|trans|desc('None') -}}
    {%- endif -%}
{% endset %}
{% set body_row_cols = body_row_cols|merge([{
    content: col_raw,
    raw: true,
}]) %}
```

### Pattern 6: Link with External Icon

```twig
{% set col_raw %}
    <a href="{{ view_url }}">{{ item.url|u.truncate(50) }}</a>
    <a href="{{ item.url }}" target="_blank">
        <svg class="ibexa-icon ibexa-icon--small">
            <use xlink:href="{{ ibexa_icon_path('open-newtab') }}"></use>
        </svg>
    </a>
{% endset %}
{% set body_row_cols = body_row_cols|merge([{
    content: col_raw,
    raw: true,
}]) %}
```

### Pattern 7: Empty Table with Custom Messages

```twig
{% include '@ibexadesign/ui/component/table/table.html.twig' with {
    head_cols: [...],
    body_rows: body_rows,
    empty_table_info_text: 'no_items.info'|trans|desc('No items found'),
    empty_table_action_text: 'no_items.action'|trans|desc('Add your first item to get started.'),
} %}
```

### Pattern 8: Form Position Variations

**Standard** (form wraps table via `between_header_and_table`):
```twig
{% embed '@ibexadesign/ui/component/table/table.html.twig' with {...} %}
    {% block between_header_and_table %}
        {{ form_start(form_delete, {...}) }}
    {% endblock %}
{% endembed %}
{{ form_end(form_delete) }}
```

**Alternative** (form starts before table):
```twig
{{ form_start(form_delete, {...}) }}
{% embed '@ibexadesign/ui/component/table/table.html.twig' with {...} %}
    {# No between_header_and_table block #}
{% endembed %}
{{ form_end(form_delete) }}
```

## Bulk Delete Confirmation Modal

### Standard Usage

```twig
{% include '@ibexadesign/ui/modal/bulk_delete_confirmation.html.twig' with {
    'id': 'delete-modal',
    'message': 'confirm.delete'|trans|desc('Delete selected items?'),
    'data_click': '#form_delete_delete',
} %}
```

### With Custom Delete Label

```twig
{% include '@ibexadesign/ui/modal/bulk_delete_confirmation.html.twig' with {
    'id': 'unassign-modal',
    'message': 'confirm.unassign'|trans|desc('Unassign selected users?'),
    'data_click': '#form_unassign_delete',
    'delete_label': 'action.unassign'|trans|desc('Unassign'),
} %}
```

### Modal Implementation

The bulk delete modal is a specialized wrapper around the base modal:

```twig
{# Location: @ibexadesign/ui/modal/bulk_delete_confirmation.html.twig #}
{% embed '@ibexadesign/ui/component/modal/modal.html.twig' with {
    class: 'ibexa-modal--send-to-trash',
    no_header: true,
    id,
    message,
} %}
    {% block body_content %}
        {{ message }}
    {% endblock %}
    {% block footer_content %}
        <button class="btn ibexa-btn ibexa-btn--primary ibexa-btn--trigger" 
                data-click="{{ data_click }}">
            {{ delete_label|default('modal.delete'|trans|desc('Delete')) }}
        </button>
        <button type="button" class="btn ibexa-btn ibexa-btn--secondary" 
                data-bs-dismiss="modal">
            {{ 'modal.cancel'|trans|desc('Cancel') }}
        </button>
    {% endblock %}
{% endembed %}
```

### Integration with Bulk Actions

```twig
{# 1. Delete button triggers modal #}
<button id="delete-items"
        type="button"
        class="btn ibexa-btn ibexa-btn--ghost ibexa-btn--small"
        disabled
        data-bs-toggle="modal"
        data-bs-target="#delete-modal">
    <svg class="ibexa-icon ibexa-icon--small ibexa-icon--trash">
        <use xlink:href="{{ ibexa_icon_path('trash') }}"></use>
    </svg>
    <span class="ibexa-btn__label">{{ 'common.delete'|trans|desc('Delete') }}</span>
</button>

{# 2. Modal with data-click pointing to form submit button #}
{% include '@ibexadesign/ui/modal/bulk_delete_confirmation.html.twig' with {
    'id': 'delete-modal',
    'message': 'confirm.delete'|trans|desc('Delete selected items?'),
    'data_click': '#form_delete_delete',  {# Points to form submit button ID #}
} %}

{# 3. Form with toggle button state #}
{{ form_start(form_delete, {
    'action': path('bulk_delete'),
    'attr': { 
        'class': 'ibexa-toggle-btn-state',
        'data-toggle-button-id': '#delete-items'  {# Enables button when checkboxes selected #}
    }
}) }}
```

## Additional Icon Types

```twig
{# User/Group assignment #}
<use xlink:href="{{ ibexa_icon_path('assign-user') }}"></use>

{# Copy action #}
<use xlink:href="{{ ibexa_icon_path('copy') }}"></use>

{# Open in new tab #}
<use xlink:href="{{ ibexa_icon_path('open-newtab') }}"></use>

{# Relations #}
<svg class="ibexa-icon ibexa-icon--relations ibexa-icon--small">
    <use xlink:href="{{ ibexa_icon_path('assign-section') }}"></use>
</svg>
```

## Pagination with Custom Parameters

### Standard Pagination
```twig
{% if pager.haveToPaginate %}
    {% include '@ibexadesign/ui/pagination.html.twig' with {
        'pager': pager
    } %}
{% endif %}
```

### With Fragment and Custom Route
```twig
{% if pager.haveToPaginate %}
    {% include '@ibexadesign/ui/pagination.html.twig' with {
        'pager': pager,
        'paginaton_params': {
            'routeName': route_name,
            'routeParams': {'_fragment': 'policies', 'roleId': role.id},
            'pageParameter': '[policyPage]',
        }
    } %}
{% endif %}
```

### With Search Parameters
```twig
{% if urls.haveToPaginate %}
    {% include '@ibexadesign/ui/pagination.html.twig' with {
        'pager': urls,
        'paginaton_params': {'pageParameter': '[search_data][page]'}
    } %}
{% endif %}
```

## Button Variations in Table Headers

### Primary Action (Create)
```twig
<a href="{{ path('create') }}" class="btn ibexa-btn ibexa-btn--primary">
    <svg class="ibexa-icon ibexa-icon--small ibexa-icon--create">
        <use xlink:href="{{ ibexa_icon_path('create') }}"></use>
    </svg>
    <span class="ibexa-btn__label">Create</span>
</a>
```

### Secondary Action (Add/Assign)
```twig
<a href="{{ path('assign') }}" class="btn ibexa-btn ibexa-btn--tertiary ibexa-btn--small">
    <svg class="ibexa-icon ibexa-icon--small ibexa-icon--relations">
        <use xlink:href="{{ ibexa_icon_path('assign-user') }}"></use>
    </svg>
    <span class="ibexa-btn__label">Assign</span>
</a>
```

### Destructive Action (Delete)
```twig
<button id="delete-btn" disabled 
        class="btn ibexa-btn ibexa-btn--ghost ibexa-btn--small"
        data-bs-toggle="modal" 
        data-bs-target="#delete-modal">
    <svg class="ibexa-icon ibexa-icon--small ibexa-icon--trash">
        <use xlink:href="{{ ibexa_icon_path('trash') }}"></use>
    </svg>
    <span class="ibexa-btn__label">Delete</span>
</button>
```

## Complete Patterns Summary

### List View Checklist

- ✅ Extends `@ibexadesign/ui/layout.html.twig` (or standalone `<section>`)
- ✅ Import `results_headline` macro
- ✅ Set form theme: `{% form_theme form '@ibexadesign/ui/form_fields.html.twig' %}`
- ✅ Set translation domain: `{% trans_default_domain 'domain' %}`
- ✅ Set `body_class` block (if extending layout)
- ✅ Include breadcrumbs (if extending layout)
- ✅ Page title in header block (if extending layout)
- ✅ Context menu with Create button (if extending layout)
- ✅ Build table rows with proper column types
- ✅ Bulk delete form with `ibexa-toggle-btn-state` class
- ✅ Bulk delete modal with `data-click` attribute
- ✅ Pagination if needed
- ✅ JavaScript entry point (if extending layout)

### Column Types Reference

| Type | Header | Body | Use Case |
|------|--------|------|----------|
| Checkbox | `{ has_checkbox: true }` | `{ has_checkbox: true, content: form_widget(...), raw: true }` | Bulk selection |
| Icon | `{ has_icon: true }` | `{ has_icon: true, content: '<svg>...</svg>', raw: true }` | Visual identifiers |
| Link | `{ content: 'Name'\|trans }` | `{ content: '<a>...</a>', raw: true }` | Navigation |
| Badge | `{ content: 'Status'\|trans }` | `{ content: '<span class="ibexa-badge">...</span>', raw: true }` | Status indicators |
| Actions | `{ }` (empty) | `{ has_action_btns: true, content: '<button>...</button>', raw: true }` | Row actions |
| Centered | `{ center_content: true }` | `{ center_content: true, content: 'Value' }` | Centered data |

### Form Integration Patterns

**Pattern A** - Form wraps table:
```twig
{{ form_start(form, {...}) }}
{% embed '@ibexadesign/ui/component/table/table.html.twig' %}
{% endembed %}
{{ form_end(form) }}
```

**Pattern B** - Form in between_header_and_table:
```twig
{% embed '@ibexadesign/ui/component/table/table.html.twig' %}
    {% block between_header_and_table %}
        {{ form_start(form, {...}) }}
    {% endblock %}
{% endembed %}
{{ form_end(form) }}
```

---

**Last Updated**: November 2025
