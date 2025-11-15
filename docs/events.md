# Event System

The Masilia Consent Bundle dispatches events when user consent changes, allowing you to react to consent updates in your application.

## ConsentChangedEvent

This event is dispatched whenever a user's consent preferences change.

### Event Class

```php
Masilia\ConsentBundle\Event\ConsentChangedEvent
```

### When It's Dispatched

- User gives initial consent
- User updates consent preferences
- User revokes all consent
- User accepts a new policy version

### Event Methods

#### `getOldPreferences(): ?ConsentPreferences`

Get the user's previous consent preferences (null if first time).

```php
$oldPrefs = $event->getOldPreferences();
if ($oldPrefs) {
    $oldVersion = $oldPrefs->getPolicyVersion();
    $oldCategories = $oldPrefs->getCategories();
}
```

#### `getNewPreferences(): ?ConsentPreferences`

Get the user's new consent preferences (null if revoked).

```php
$newPrefs = $event->getNewPreferences();
if ($newPrefs) {
    $newVersion = $newPrefs->getPolicyVersion();
    $newCategories = $newPrefs->getCategories();
}
```

#### `hasConsentChanged(string $category): bool`

Check if consent changed for a specific category.

```php
if ($event->hasConsentChanged('analytics')) {
    // Analytics consent was modified
}
```

#### `isConsentGranted(string $category): bool`

Check if consent is currently granted for a category.

```php
if ($event->isConsentGranted('marketing')) {
    // User has consented to marketing
}
```

#### `isConsentRevoked(string $category): bool`

Check if consent was revoked for a category.

```php
if ($event->isConsentRevoked('analytics')) {
    // User revoked analytics consent
    // Clean up tracking data
}
```

---

## Built-in Event Subscriber

The bundle includes a `ConsentEventSubscriber` that automatically:

- Logs all consent changes for audit trail
- Handles initial consent
- Handles consent updates
- Handles consent revocation

### Configuration

Enable/disable logging in your configuration:

```yaml
# config/packages/masilia_consent.yaml
masilia_consent:
    logging:
        enabled: true  # Set to false to disable consent logging
```

### Log Levels

- **INFO**: Initial consent, consent revocation, preference updates
- **DEBUG**: Individual category changes
- **WARNING**: Complete consent revocation

### Log Context

All log entries include:
- Policy versions (old and new)
- Category changes (granted/revoked)
- Timestamps
- Detailed change information

---

## Creating Custom Event Listeners

You can create your own event listeners to react to consent changes.

### Example 1: Analytics Integration

```php
<?php

namespace App\EventListener;

use Masilia\ConsentBundle\Event\ConsentChangedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: ConsentChangedEvent::class)]
class AnalyticsConsentListener
{
    public function __invoke(ConsentChangedEvent $event): void
    {
        // Enable analytics if consent granted
        if ($event->isConsentGranted('analytics')) {
            $this->enableAnalytics();
        }
        
        // Disable analytics if consent revoked
        if ($event->isConsentRevoked('analytics')) {
            $this->disableAnalytics();
        }
    }
    
    private function enableAnalytics(): void
    {
        // Initialize Google Analytics, etc.
    }
    
    private function disableAnalytics(): void
    {
        // Clear tracking cookies, disable tracking
    }
}
```

### Example 2: User Profile Update

```php
<?php

namespace App\EventListener;

use Masilia\ConsentBundle\Event\ConsentChangedEvent;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\Security;

#[AsEventListener(event: ConsentChangedEvent::class)]
class UserConsentProfileListener
{
    public function __construct(
        private UserRepository $userRepository,
        private Security $security
    ) {
    }
    
    public function __invoke(ConsentChangedEvent $event): void
    {
        $user = $this->security->getUser();
        if (!$user) {
            return;
        }
        
        $preferences = $event->getNewPreferences();
        if (!$preferences) {
            return;
        }
        
        // Store consent in user profile
        $user->setConsentPolicyVersion($preferences->getPolicyVersion());
        $user->setConsentCategories($preferences->getCategories());
        $user->setConsentTimestamp($preferences->getTimestamp());
        
        $this->userRepository->save($user);
    }
}
```

### Example 3: Marketing Automation

```php
<?php

namespace App\EventListener;

use Masilia\ConsentBundle\Event\ConsentChangedEvent;
use App\Service\MarketingService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: ConsentChangedEvent::class, priority: -10)]
class MarketingConsentListener
{
    public function __construct(
        private MarketingService $marketingService
    ) {
    }
    
    public function __invoke(ConsentChangedEvent $event): void
    {
        if ($event->hasConsentChanged('marketing')) {
            if ($event->isConsentGranted('marketing')) {
                // Subscribe to marketing emails
                $this->marketingService->subscribe();
            } else {
                // Unsubscribe from marketing emails
                $this->marketingService->unsubscribe();
            }
        }
    }
}
```

### Example 4: Data Cleanup

```php
<?php

namespace App\EventListener;

use Masilia\ConsentBundle\Event\ConsentChangedEvent;
use App\Service\DataCleanupService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: ConsentChangedEvent::class)]
class DataCleanupListener
{
    public function __construct(
        private DataCleanupService $cleanupService
    ) {
    }
    
    public function __invoke(ConsentChangedEvent $event): void
    {
        // Clean up data when consent is revoked
        $old = $event->getOldPreferences();
        $new = $event->getNewPreferences();
        
        if (!$old || !$new) {
            return;
        }
        
        foreach ($old->getCategories() as $category => $consented) {
            if ($consented && !$new->hasConsent($category)) {
                // Consent was revoked for this category
                $this->cleanupService->cleanupCategoryData($category);
            }
        }
    }
}
```

---

## Event Subscriber (Alternative to Listener)

You can also create event subscribers for more complex logic:

```php
<?php

namespace App\EventSubscriber;

use Masilia\ConsentBundle\Event\ConsentChangedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Psr\Log\LoggerInterface;

class CustomConsentSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private LoggerInterface $logger
    ) {
    }
    
    public static function getSubscribedEvents(): array
    {
        return [
            ConsentChangedEvent::class => [
                ['onConsentChanged', 10],      // High priority
                ['logCustomMetrics', 0],       // Normal priority
                ['cleanupOldData', -10],       // Low priority
            ],
        ];
    }
    
    public function onConsentChanged(ConsentChangedEvent $event): void
    {
        // Main handler
    }
    
    public function logCustomMetrics(ConsentChangedEvent $event): void
    {
        // Log custom metrics
    }
    
    public function cleanupOldData(ConsentChangedEvent $event): void
    {
        // Cleanup (runs last)
    }
}
```

Register in `services.yaml`:

```yaml
services:
    App\EventSubscriber\CustomConsentSubscriber:
        tags: ['kernel.event_subscriber']
```

---

## Event Priority

Events are processed in priority order (highest to lowest):

1. **Priority 10**: Built-in subscriber main handler
2. **Priority 0**: Built-in subscriber logging (default)
3. **Priority -10**: Low priority handlers (cleanup, etc.)

Set priority in your listener:

```php
#[AsEventListener(event: ConsentChangedEvent::class, priority: 20)]
```

Or in subscriber:

```php
public static function getSubscribedEvents(): array
{
    return [
        ConsentChangedEvent::class => ['onConsentChanged', 20],
    ];
}
```

---

## Common Use Cases

### 1. Enable/Disable Features

```php
public function __invoke(ConsentChangedEvent $event): void
{
    if ($event->isConsentGranted('personalization')) {
        $this->featureToggle->enable('recommendations');
        $this->featureToggle->enable('saved_preferences');
    } else {
        $this->featureToggle->disable('recommendations');
        $this->featureToggle->disable('saved_preferences');
    }
}
```

### 2. Send Notifications

```php
public function __invoke(ConsentChangedEvent $event): void
{
    $old = $event->getOldPreferences();
    $new = $event->getNewPreferences();
    
    if ($old && $new && $old->getPolicyVersion() !== $new->getPolicyVersion()) {
        $this->notificationService->send(
            'You have accepted our updated cookie policy'
        );
    }
}
```

### 3. Update External Services

```php
public function __invoke(ConsentChangedEvent $event): void
{
    if ($event->hasConsentChanged('analytics')) {
        $this->externalApi->updateConsentStatus(
            'analytics',
            $event->isConsentGranted('analytics')
        );
    }
}
```

### 4. Audit Trail

```php
public function __invoke(ConsentChangedEvent $event): void
{
    $this->auditLog->record([
        'event' => 'consent_changed',
        'user_id' => $this->security->getUser()?->getId(),
        'old_preferences' => $event->getOldPreferences()?->toArray(),
        'new_preferences' => $event->getNewPreferences()?->toArray(),
        'ip_address' => $this->requestStack->getCurrentRequest()?->getClientIp(),
        'user_agent' => $this->requestStack->getCurrentRequest()?->headers->get('User-Agent'),
        'timestamp' => time(),
    ]);
}
```

---

## Testing Events

### Unit Test Example

```php
<?php

namespace App\Tests\EventListener;

use App\EventListener\AnalyticsConsentListener;
use Masilia\ConsentBundle\Event\ConsentChangedEvent;
use Masilia\ConsentBundle\ValueObject\ConsentPreferences;
use PHPUnit\Framework\TestCase;

class AnalyticsConsentListenerTest extends TestCase
{
    public function testEnablesAnalyticsWhenConsentGranted(): void
    {
        $listener = new AnalyticsConsentListener();
        
        $event = new ConsentChangedEvent(
            null,
            new ConsentPreferences(['analytics' => true], '1.0.0')
        );
        
        $listener($event);
        
        // Assert analytics was enabled
    }
    
    public function testDisablesAnalyticsWhenConsentRevoked(): void
    {
        $listener = new AnalyticsConsentListener();
        
        $event = new ConsentChangedEvent(
            new ConsentPreferences(['analytics' => true], '1.0.0'),
            new ConsentPreferences(['analytics' => false], '1.0.0')
        );
        
        $listener($event);
        
        // Assert analytics was disabled
    }
}
```

---

## Best Practices

### 1. Keep Listeners Focused

Each listener should handle one specific concern:

```php
// ✓ Good - focused on one thing
class AnalyticsConsentListener { }
class MarketingConsentListener { }

// ✗ Bad - does too much
class AllConsentListener { }
```

### 2. Use Appropriate Priority

- High priority (10+): Critical operations that must run first
- Normal priority (0): Standard operations
- Low priority (-10): Cleanup, logging, non-critical tasks

### 3. Handle Errors Gracefully

```php
public function __invoke(ConsentChangedEvent $event): void
{
    try {
        $this->externalService->updateConsent();
    } catch (\Exception $e) {
        $this->logger->error('Failed to update external service', [
            'exception' => $e->getMessage()
        ]);
        // Don't throw - let other listeners run
    }
}
```

### 4. Check for Null Values

```php
public function __invoke(ConsentChangedEvent $event): void
{
    $preferences = $event->getNewPreferences();
    if (!$preferences) {
        // Handle consent revocation
        return;
    }
    
    // Process preferences
}
```

### 5. Use Dependency Injection

```php
public function __construct(
    private MyService $service,
    private LoggerInterface $logger
) {
}
```

---

## Debugging

### Enable Debug Logging

```yaml
# config/packages/monolog.yaml
monolog:
    handlers:
        consent:
            type: stream
            path: '%kernel.logs_dir%/consent.log'
            level: debug
            channels: ['consent']
```

### View Event Dispatch

```bash
# See all dispatched events
php bin/console debug:event-dispatcher ConsentChangedEvent

# Profile event listeners
php bin/console debug:event-dispatcher --dispatcher=event_dispatcher
```

---

## See Also

- [Twig Helpers](twig-helpers.md)
- [Configuration Reference](configuration.md)
- [API Documentation](api.md)
