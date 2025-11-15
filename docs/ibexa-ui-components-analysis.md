# Ibexa Design System - Available UI Components Analysis

## 🎯 **Components We Can Reuse for Category Management**

### **1. Modal Component** ✅ **PERFECT FIT**
**Path:** `@ibexadesign/ui/component/modal/modal.html.twig`

**Features:**
- ✅ Configurable sizes (small, large, extra-large)
- ✅ Header with title and close button
- ✅ Optional subtitle support
- ✅ Body content block
- ✅ Footer content block
- ✅ Static backdrop option
- ✅ Bootstrap 5 compatible

**Usage Pattern:**
```twig
{% embed '@ibexadesign/ui/component/modal/modal.html.twig' with {
    id: 'add-category-modal',
    title: 'Add Category',
    size: 'large',
} %}
    {% block body_content %}
        {# Form content here #}
    {% endblock %}
    {% block footer_content %}
        <button type="submit" class="btn ibexa-btn ibexa-btn--primary">Save</button>
        <button type="button" class="btn ibexa-btn ibexa-btn--secondary" data-bs-dismiss="modal">Cancel</button>
    {% endblock %}
{% endembed %}
```

**Perfect for:**
- ✅ Add Category modal
- ✅ Edit Category modal
- ✅ Delete confirmation modal

---

### **2. Bulk Delete Confirmation Modal** ✅ **REUSABLE**
**Path:** `@ibexadesign/ui/modal/bulk_delete_confirmation.html.twig`

**Features:**
- ✅ Pre-styled for delete operations
- ✅ No header (clean look)
- ✅ Primary action button
- ✅ Cancel button
- ✅ Trigger mechanism with `data-click`

**Usage Pattern:**
```twig
{% include '@ibexadesign/ui/modal/bulk_delete_confirmation.html.twig' with {
    'id': 'delete-category-modal',
    'message': 'Delete category "Analytics"? This action cannot be undone.',
    'data_click': '#category-delete-form-submit',
}%}
```

**Perfect for:**
- ✅ Delete category confirmation
- ✅ Delete cookie confirmation

---

### **3. Table Component** ✅ **ALREADY USING**
**Path:** `@ibexadesign/ui/component/table/table.html.twig`

**Features:**
- ✅ Header and body rows
- ✅ Action buttons column
- ✅ Empty state message
- ✅ Checkbox support
- ✅ Responsive

**We're already using this!**

---

### **4. Embedded Item Actions** ✅ **USEFUL FOR INLINE ACTIONS**
**Path:** `@ibexadesign/ui/component/embedded_item_actions/embedded_item_actions.html.twig`

**Features:**
- ✅ Three-dot menu button
- ✅ Popup menu with actions
- ✅ Edit, delete, etc. actions
- ✅ Loader state

**Usage Pattern:**
```twig
{% include '@ibexadesign/ui/component/embedded_item_actions/embedded_item_actions.html.twig' with {
    content_id: category.id,
    location_id: category.id,
} %}
```

**Perfect for:**
- ✅ Category row actions (edit/delete dropdown)
- ✅ Cookie row actions

---

### **5. Extra Actions Component** ✅ **ALTERNATIVE TO EMBEDDED**
**Path:** `@ibexadesign/ui/component/extra_actions/extra_actions.html.twig`

**Features:**
- ✅ Action buttons container
- ✅ Dropdown menu support
- ✅ Icon buttons

**Perfect for:**
- ✅ Bulk actions on categories
- ✅ Additional policy actions

---

## 📋 **Recommended Implementation Strategy**

### **Option A: Pure Ibexa Components (Recommended)** ⭐

**Advantages:**
- ✅ No custom JavaScript needed
- ✅ Consistent with Ibexa UI
- ✅ Accessibility built-in
- ✅ Mobile-friendly
- ✅ Maintainable

**Implementation:**

#### **1. Policy View Page - Categories Section**
```twig
{# Categories Card with Add Button #}
<div class="card ibexa-card ibexa-card--light mb-4">
    <div class="card-header ibexa-card__header">
        <h3 class="ibexa-card__title">Categories ({{ policy.categories|length }})</h3>
        <button 
            type="button" 
            class="btn ibexa-btn ibexa-btn--primary ibexa-btn--small"
            data-bs-toggle="modal"
            data-bs-target="#add-category-modal">
            <svg class="ibexa-icon ibexa-icon--small">
                <use xlink:href="{{ ibexa_icon_path('create') }}"></use>
            </svg>
            Add Category
        </button>
    </div>
    <div class="card-body ibexa-card__body">
        {# Categories table with Edit/Delete buttons #}
        {% include '@ibexadesign/ui/component/table/table.html.twig' with {
            head_cols: [...],
            body_rows: [...],
        } %}
    </div>
</div>

{# Add Category Modal #}
{% embed '@ibexadesign/ui/component/modal/modal.html.twig' with {
    id: 'add-category-modal',
    title: 'Add Category',
    size: 'large',
} %}
    {% block body_content %}
        {{ form_start(categoryForm) }}
            {{ form_widget(categoryForm) }}
        {{ form_end(categoryForm) }}
    {% endblock %}
    {% block footer_content %}
        <button type="submit" form="category-form" class="btn ibexa-btn ibexa-btn--primary">
            Create Category
        </button>
        <button type="button" class="btn ibexa-btn ibexa-btn--secondary" data-bs-dismiss="modal">
            Cancel
        </button>
    {% endblock %}
{% endembed %}
```

#### **2. Each Category Row - Actions**
```twig
{# Edit button #}
<button 
    type="button"
    class="btn ibexa-btn ibexa-btn--ghost ibexa-btn--no-text"
    data-bs-toggle="modal"
    data-bs-target="#edit-category-{{ category.id }}">
    <svg class="ibexa-icon ibexa-icon--small">
        <use xlink:href="{{ ibexa_icon_path('edit') }}"></use>
    </svg>
</button>

{# Delete button #}
<button 
    type="button"
    class="btn ibexa-btn ibexa-btn--ghost ibexa-btn--no-text"
    data-bs-toggle="modal"
    data-bs-target="#delete-category-{{ category.id }}">
    <svg class="ibexa-icon ibexa-icon--small ibexa-icon--trash">
        <use xlink:href="{{ ibexa_icon_path('trash') }}"></use>
    </svg>
</button>
```

#### **3. Edit Modal (Per Category)**
```twig
{% for category in policy.categories %}
    {% embed '@ibexadesign/ui/component/modal/modal.html.twig' with {
        id: 'edit-category-' ~ category.id,
        title: 'Edit Category: ' ~ category.name,
        size: 'large',
    } %}
        {% block body_content %}
            {# Pre-filled form with category data #}
            {{ form_start(editForms[category.id]) }}
                {{ form_widget(editForms[category.id]) }}
            {{ form_end(editForms[category.id]) }}
        {% endblock %}
        {% block footer_content %}
            <button type="submit" form="edit-category-form-{{ category.id }}" class="btn ibexa-btn ibexa-btn--primary">
                Save Changes
            </button>
            <button type="button" class="btn ibexa-btn ibexa-btn--secondary" data-bs-dismiss="modal">
                Cancel
            </button>
        {% endblock %}
    {% endembed %}
{% endfor %}
```

#### **4. Delete Confirmation Modal**
```twig
{% for category in policy.categories %}
    {% include '@ibexadesign/ui/modal/bulk_delete_confirmation.html.twig' with {
        'id': 'delete-category-' ~ category.id,
        'message': 'Delete category "' ~ category.name ~ '"? This will also delete all associated cookies.',
        'data_click': '#delete-category-form-' ~ category.id ~ '-submit',
    }%}
    
    {# Hidden delete form #}
    <form id="delete-category-form-{{ category.id }}" method="post" action="{{ path('masilia_consent_admin_category_delete', {id: category.id}) }}" class="d-none">
        <input type="hidden" name="_token" value="{{ csrf_token('delete' ~ category.id) }}">
        <button id="delete-category-form-{{ category.id }}-submit" type="submit"></button>
    </form>
{% endfor %}
```

---

### **Option B: AJAX + Ibexa Modals (More Dynamic)**

**Advantages:**
- ✅ No page reload
- ✅ Better UX
- ✅ Real-time updates

**Requires:**
- ❌ Custom JavaScript
- ❌ AJAX endpoints
- ❌ More complexity

**When to use:**
- If you need instant feedback
- If you want to avoid page reloads
- If you're building a SPA-like experience

---

## 🎨 **Recommended Workflow**

### **Creating a Policy:**
1. User clicks "Create Policy" button
2. Form shows basic info only (version, prefix, expiration, active)
3. Message: "Categories can be added after policy creation"
4. On save → Redirect to Policy View page

### **Managing Categories (Policy View):**
1. Categories card shows:
   - **"Add Category"** button (opens modal)
   - Table of existing categories
   - Each row has **Edit** and **Delete** buttons

2. **Add Category Flow:**
   - Click "Add Category" → Modal opens
   - Fill form → Click "Create Category"
   - Form submits → Page reloads with new category
   - Success flash message

3. **Edit Category Flow:**
   - Click Edit icon → Modal opens with pre-filled form
   - Modify → Click "Save Changes"
   - Form submits → Page reloads with updated category
   - Success flash message

4. **Delete Category Flow:**
   - Click Delete icon → Confirmation modal opens
   - Confirm → Category deleted
   - Page reloads → Success flash message

---

## 🔧 **Implementation Checklist**

### **Phase 1: Update Controllers**
- [ ] Remove category collection from PolicyType form
- [ ] Create separate endpoints for category modals:
  - `GET /admin/consent/policy/{id}/category/add-modal` (returns modal HTML)
  - `GET /admin/consent/category/{id}/edit-modal` (returns modal HTML)
- [ ] Keep existing POST endpoints for form submission

### **Phase 2: Update Templates**
- [ ] Update `policy/create.html.twig` - Remove categories section
- [ ] Update `policy/edit.html.twig` - Remove categories section
- [ ] Update `policy/view.html.twig`:
  - Add "Add Category" button with modal
  - Add Edit/Delete modals for each category
  - Use Ibexa modal components

### **Phase 3: Test**
- [ ] Create policy without categories
- [ ] Add categories via modal
- [ ] Edit categories via modal
- [ ] Delete categories via modal
- [ ] Test all flash messages
- [ ] Test CSRF protection

---

## 📦 **Components Summary**

| Component | Path | Use Case |
|-----------|------|----------|
| **Modal** | `@ibexadesign/ui/component/modal/modal.html.twig` | Add/Edit forms |
| **Delete Confirmation** | `@ibexadesign/ui/modal/bulk_delete_confirmation.html.twig` | Delete actions |
| **Table** | `@ibexadesign/ui/component/table/table.html.twig` | Category list |
| **Embedded Actions** | `@ibexadesign/ui/component/embedded_item_actions/` | Row actions |

---

## ✅ **Benefits of This Approach**

1. **100% Ibexa Native** - No custom components
2. **Consistent UX** - Matches Ibexa admin patterns
3. **Accessible** - WCAG compliant out of the box
4. **Mobile-Friendly** - Responsive modals
5. **Maintainable** - Uses standard Ibexa components
6. **No JavaScript** - Pure server-side rendering (Option A)
7. **Fast Implementation** - Reuse existing components

---

## 🚀 **Next Steps**

1. **Approve this approach**
2. **Implement Phase 1** (Controllers)
3. **Implement Phase 2** (Templates)
4. **Test thoroughly**
5. **Commit changes**

**Estimated Time:** 2-3 hours for full implementation

---

**Recommendation:** Go with **Option A (Pure Ibexa Components)** for simplicity, maintainability, and consistency with Ibexa's design system.
