export interface CookiePolicy {
  version: string;
  lastUpdated: string;
  expirationDays: number;
  cookiePrefix: string;
  categories: CookieCategory[];
  thirdPartyServices: ThirdPartyService[];
}

export interface CookieCategory {
  id: number;
  identifier: string;
  name: string;
  description: string;
  required: boolean;
  defaultEnabled: boolean;
  position: number;
  cookies: Cookie[];
}

export interface Cookie {
  id: number;
  name: string;
  purpose: string;
  provider: string;
  expiry: string;
}

export interface ThirdPartyService {
  id: string;
  name: string;
  category: string;
  description: string;
  privacyPolicyUrl: string;
}

export interface ConsentPreferences {
  categories: Record<string, boolean>;
  version: string;
  timestamp: string;
}

export interface ConsentStatus {
  hasConsent: boolean;
  preferences: ConsentPreferences | null;
  policyVersion: string;
  needsUpdate?: boolean;
}

export interface ConsentApiResponse {
  success: boolean;
  message?: string;
}
