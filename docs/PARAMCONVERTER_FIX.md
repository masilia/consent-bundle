# ParamConverter Fix for Policy Routes

## Problem

**Error**: `Unable to guess how to get a Doctrine instance from the request information for parameter "policy".`

**URL**: `/admin/consent/category/policy/1/create`

## Root Cause

Symfony's ParamConverter couldn't automatically resolve the `policy` entity parameter because:

1. Route parameter name: `{policyId}` 
2. Method parameter name: `$policy`
3. **Mismatch**: ParamConverter expects them to match by default

## Solution

Add explicit `#[ParamConverter]` attribute to map the route parameter to the entity parameter.

### Files Modified

#### 1. CategoryAdminController.php

```php
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

// List action
#[Route('/policy/{policyId}', name: 'list', methods: ['GET'], requirements: ['policyId' => '\d+'])]
#[ParamConverter('policy', options: ['id' => 'policyId'])]
public function list(CookiePolicy $policy): Response

// Create action
#[Route('/policy/{policyId}/create', name: 'create', methods: ['POST'], requirements: ['policyId' => '\d+'])]
#[ParamConverter('policy', options: ['id' => 'policyId'])]
public function create(Request $request, CookiePolicy $policy): Response
```

#### 2. ThirdPartyServiceController.php

```php
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

// List action
#[Route('/policy/{policyId}', name: 'list', requirements: ['policyId' => '\d+'], methods: ['GET'])]
#[ParamConverter('policy', options: ['id' => 'policyId'])]
public function list(CookiePolicy $policy): Response

// Create action
#[Route('/policy/{policyId}/create', name: 'create', requirements: ['policyId' => '\d+'], methods: ['POST'])]
#[ParamConverter('policy', options: ['id' => 'policyId'])]
public function create(Request $request, CookiePolicy $policy): Response
```

## How ParamConverter Works

### Default Behavior (No Annotation)
```php
#[Route('/policy/{policy}')]
public function action(CookiePolicy $policy)
```
✅ Works automatically - parameter names match

### Custom Mapping (With Annotation)
```php
#[Route('/policy/{policyId}')]
#[ParamConverter('policy', options: ['id' => 'policyId'])]
public function action(CookiePolicy $policy)
```
✅ Works with explicit mapping

## Alternative Solutions

### Option 1: Rename Route Parameter (Not Recommended)
```php
// Change route parameter to match method parameter
#[Route('/policy/{policy}')]
public function action(CookiePolicy $policy)
```
❌ Less semantic - `{policy}` looks like a slug, not an ID

### Option 2: Use ParamConverter (Recommended) ✅
```php
#[Route('/policy/{policyId}')]
#[ParamConverter('policy', options: ['id' => 'policyId'])]
public function action(CookiePolicy $policy)
```
✅ Clear and explicit
✅ Semantic route parameter name
✅ Type-safe entity parameter

### Option 3: Manual Repository Fetch
```php
#[Route('/policy/{policyId}')]
public function action(int $policyId, CookiePolicyRepository $repo)
{
    $policy = $repo->find($policyId);
    if (!$policy) {
        throw $this->createNotFoundException();
    }
}
```
❌ More boilerplate code
❌ Manual error handling

## Testing

After applying the fix, test these URLs:

- ✅ `/admin/consent/category/policy/1/create` (POST)
- ✅ `/admin/consent/category/policy/1` (GET - list)
- ✅ `/admin/consent/service/policy/1/create` (POST)
- ✅ `/admin/consent/service/policy/1` (GET - list)

## Dependencies

Ensure `sensio/framework-extra-bundle` is installed:

```bash
composer require sensio/framework-extra-bundle
```

Or in Symfony 6.2+, use native attributes:

```php
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

#[Route('/policy/{policyId}')]
public function action(
    #[MapEntity(id: 'policyId')] CookiePolicy $policy
): Response
```

---

**Fixed**: November 2025
