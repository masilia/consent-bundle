<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Event;

final class ConsentEvents
{
    /**
     * Dispatched when user consent preferences are changed
     */
    public const CONSENT_CHANGED = 'masilia_consent.changed';

    /**
     * Dispatched when user revokes all consent
     */
    public const CONSENT_REVOKED = 'masilia_consent.revoked';

    /**
     * Dispatched when a new cookie policy is activated
     */
    public const POLICY_ACTIVATED = 'masilia_consent.policy_activated';
}
