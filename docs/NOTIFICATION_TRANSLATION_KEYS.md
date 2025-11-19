# Notification Translation Keys

This document lists all translation keys used for admin notifications in the Consent Bundle.

## Translation Domain

All notification messages use the `masilia_consent` translation domain.

## Pattern

All notifications follow Ibexa's translation pattern:

```php
$this->notificationHandler->success(
    /** @Desc("Message with '%placeholder%'.") */
    'translation.key',
    ['%placeholder%' => $value],
    'masilia_consent'
);
```

## Translation Keys

### Policy Notifications

#### `policy.activate.success`
- **Description**: Policy version '%version%' has been activated.
- **Parameters**: `%version%` - The policy version number
- **Type**: Success
- **Controller**: `PolicyAdminController::activate()`

#### `policy.deactivate.success`
- **Description**: Policy version '%version%' has been deactivated.
- **Parameters**: `%version%` - The policy version number
- **Type**: Success
- **Controller**: `PolicyAdminController::deactivate()`

#### `policy.create.success`
- **Description**: Policy version '%version%' has been created.
- **Parameters**: `%version%` - The policy version number
- **Type**: Success
- **Controller**: `PolicyAdminController::create()`

#### `policy.edit.success`
- **Description**: Policy version '%version%' has been updated.
- **Parameters**: `%version%` - The policy version number
- **Type**: Success
- **Controller**: `PolicyAdminController::edit()`

#### `policy.delete.success`
- **Description**: Policy version '%version%' has been deleted.
- **Parameters**: `%version%` - The policy version number
- **Type**: Success
- **Controller**: `PolicyAdminController::delete()`

#### `policy.delete.error.active`
- **Description**: Cannot delete an active policy. Deactivate it first.
- **Parameters**: None
- **Type**: Error
- **Controller**: `PolicyAdminController::delete()`

---

### Category Notifications

#### `category.create.success`
- **Description**: Category '%name%' has been created.
- **Parameters**: `%name%` - The category name
- **Type**: Success
- **Controller**: `CategoryAdminController::create()`

#### `category.edit.success`
- **Description**: Category '%name%' has been updated.
- **Parameters**: `%name%` - The category name
- **Type**: Success
- **Controller**: `CategoryAdminController::edit()`

#### `category.edit.error.auto_generated`
- **Description**: Cannot edit auto-generated categories from third-party services.
- **Parameters**: None
- **Type**: Error
- **Controller**: `CategoryAdminController::edit()`

#### `category.delete.success`
- **Description**: Category '%name%' has been deleted.
- **Parameters**: `%name%` - The category name
- **Type**: Success
- **Controller**: `CategoryAdminController::delete()`

#### `category.delete.error.auto_generated`
- **Description**: Cannot delete auto-generated categories from third-party services.
- **Parameters**: None
- **Type**: Error
- **Controller**: `CategoryAdminController::delete()`

---

### Cookie Notifications

#### `cookie.create.success`
- **Description**: Cookie '%name%' has been created.
- **Parameters**: `%name%` - The cookie name
- **Type**: Success
- **Controller**: `CookieAdminController::create()`

#### `cookie.edit.success`
- **Description**: Cookie '%name%' has been updated.
- **Parameters**: `%name%` - The cookie name
- **Type**: Success
- **Controller**: `CookieAdminController::edit()`

#### `cookie.edit.error.auto_generated`
- **Description**: Cannot edit auto-generated cookies from third-party services.
- **Parameters**: None
- **Type**: Error
- **Controller**: `CookieAdminController::edit()`

#### `cookie.delete.success`
- **Description**: Cookie '%name%' has been deleted.
- **Parameters**: `%name%` - The cookie name
- **Type**: Success
- **Controller**: `CookieAdminController::delete()`

#### `cookie.delete.error.auto_generated`
- **Description**: Cannot delete auto-generated cookies from third-party services.
- **Parameters**: None
- **Type**: Error
- **Controller**: `CookieAdminController::delete()`

---

### Third-Party Service Notifications

#### `service.create.success`
- **Description**: Service '%name%' has been created.
- **Parameters**: `%name%` - The service name
- **Type**: Success
- **Controller**: `ThirdPartyServiceController::create()`

#### `service.edit.success`
- **Description**: Service '%name%' has been updated.
- **Parameters**: `%name%` - The service name
- **Type**: Success
- **Controller**: `ThirdPartyServiceController::edit()`

#### `service.delete.success`
- **Description**: Service '%name%' has been deleted.
- **Parameters**: `%name%` - The service name
- **Type**: Success
- **Controller**: `ThirdPartyServiceController::delete()`

#### `service.toggle.success`
- **Description**: Service '%name%' has been %status%.
- **Parameters**: 
  - `%name%` - The service name
  - `%status%` - Either "enabled" or "disabled"
- **Type**: Success
- **Controller**: `ThirdPartyServiceController::toggle()`

---

## Adding Translations

To add translations for these keys, update the translation files:

### English (`src/Resources/translations/masilia_consent.en.yaml`)

```yaml
policy:
    activate:
        success: "Policy version '%version%' has been activated."
    deactivate:
        success: "Policy version '%version%' has been deactivated."
    create:
        success: "Policy version '%version%' has been created."
    edit:
        success: "Policy version '%version%' has been updated."
    delete:
        success: "Policy version '%version%' has been deleted."
        error:
            active: "Cannot delete an active policy. Deactivate it first."

category:
    create:
        success: "Category '%name%' has been created."
    edit:
        success: "Category '%name%' has been updated."
        error:
            auto_generated: "Cannot edit auto-generated categories from third-party services."
    delete:
        success: "Category '%name%' has been deleted."
        error:
            auto_generated: "Cannot delete auto-generated categories from third-party services."

cookie:
    create:
        success: "Cookie '%name%' has been created."
    edit:
        success: "Cookie '%name%' has been updated."
        error:
            auto_generated: "Cannot edit auto-generated cookies from third-party services."
    delete:
        success: "Cookie '%name%' has been deleted."
        error:
            auto_generated: "Cannot delete auto-generated cookies from third-party services."

service:
    create:
        success: "Service '%name%' has been created."
    edit:
        success: "Service '%name%' has been updated."
    delete:
        success: "Service '%name%' has been deleted."
    toggle:
        success: "Service '%name%' has been %status%."
```

### French (`src/Resources/translations/masilia_consent.fr.yaml`)

```yaml
policy:
    activate:
        success: "La politique version '%version%' a été activée."
    deactivate:
        success: "La politique version '%version%' a été désactivée."
    create:
        success: "La politique version '%version%' a été créée."
    edit:
        success: "La politique version '%version%' a été mise à jour."
    delete:
        success: "La politique version '%version%' a été supprimée."
        error:
            active: "Impossible de supprimer une politique active. Désactivez-la d'abord."

category:
    create:
        success: "La catégorie '%name%' a été créée."
    edit:
        success: "La catégorie '%name%' a été mise à jour."
        error:
            auto_generated: "Impossible de modifier les catégories générées automatiquement par les services tiers."
    delete:
        success: "La catégorie '%name%' a été supprimée."
        error:
            auto_generated: "Impossible de supprimer les catégories générées automatiquement par les services tiers."

cookie:
    create:
        success: "Le cookie '%name%' a été créé."
    edit:
        success: "Le cookie '%name%' a été mis à jour."
        error:
            auto_generated: "Impossible de modifier les cookies générés automatiquement par les services tiers."
    delete:
        success: "Le cookie '%name%' a été supprimé."
        error:
            auto_generated: "Impossible de supprimer les cookies générés automatiquement par les services tiers."

service:
    create:
        success: "Le service '%name%' a été créé."
    edit:
        success: "Le service '%name%' a été mis à jour."
    delete:
        success: "Le service '%name%' a été supprimé."
    toggle:
        success: "Le service '%name%' a été %status%."
```

## Benefits

1. **Internationalization**: Easy to translate to multiple languages
2. **Consistency**: All notifications follow the same pattern
3. **Maintainability**: Centralized translation management
4. **Ibexa Compliance**: Follows Ibexa's best practices
5. **Type Safety**: @Desc annotations provide IDE support
6. **Flexibility**: Dynamic parameters allow contextual messages
