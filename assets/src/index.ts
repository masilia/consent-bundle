// Components
export { ConsentBanner } from './components/ConsentBanner';
export { PreferencesModal } from './components/PreferencesModal';
export type { ConsentBannerProps } from './components/ConsentBanner';
export type { PreferencesModalProps } from './components/PreferencesModal';

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
