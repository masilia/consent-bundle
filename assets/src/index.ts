// Services
export { ConsentApi, consentApi } from './services/consentApi';

// Hooks
export { useConsent } from './hooks/useConsent';
export { useConsentPolicy } from './hooks/useConsentPolicy';

// Types
export type {
  CookiePolicy,
  CookieCategory,
  Cookie,
  ThirdPartyService,
  ConsentPreferences,
  ConsentStatus,
  ConsentApiResponse,
} from './types/consent.types';
