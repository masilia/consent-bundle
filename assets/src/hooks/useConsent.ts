import { useState, useEffect, useCallback } from 'react';
import { consentApi } from '../services/consentApi';
import type { ConsentStatus } from '../types/consent.types';

export function useConsent(apiBaseUrl?: string) {
  const [status, setStatus] = useState<ConsentStatus | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<Error | null>(null);

  const api = apiBaseUrl ? new (consentApi.constructor as any)(apiBaseUrl) : consentApi;

  const fetchStatus = useCallback(async () => {
    try {
      setLoading(true);
      const data = await api.getStatus();
      setStatus(data);
      setError(null);
    } catch (err) {
      setError(err as Error);
    } finally {
      setLoading(false);
    }
  }, [api]);

  useEffect(() => {
    fetchStatus();
  }, [fetchStatus]);

  const acceptAll = useCallback(async () => {
    try {
      await api.acceptAll();
      await fetchStatus();
      // Reload page to apply scripts
      window.location.reload();
    } catch (err) {
      setError(err as Error);
      throw err;
    }
  }, [api, fetchStatus]);

  const rejectAll = useCallback(async () => {
    try {
      await api.rejectNonEssential();
      await fetchStatus();
      // Reload page to remove scripts
      window.location.reload();
    } catch (err) {
      setError(err as Error);
      throw err;
    }
  }, [api, fetchStatus]);

  const updatePreferences = useCallback(async (categories: Record<string, boolean>) => {
    try {
      await api.savePreferences(categories);
      await fetchStatus();
      // Reload page to apply/remove scripts
      window.location.reload();
    } catch (err) {
      setError(err as Error);
      throw err;
    }
  }, [api, fetchStatus]);

  const revokeConsent = useCallback(async () => {
    try {
      await api.revokeConsent();
      await fetchStatus();
      // Reload page to remove scripts
      window.location.reload();
    } catch (err) {
      setError(err as Error);
      throw err;
    }
  }, [api, fetchStatus]);

  const hasConsent = useCallback((category?: string) => {
    if (!status?.preferences) return false;
    if (!category) return status.hasConsent;
    return status.preferences.categories[category] === true;
  }, [status]);

  return {
    status,
    loading,
    error,
    hasConsent,
    acceptAll,
    rejectAll,
    updatePreferences,
    revokeConsent,
    refresh: fetchStatus,
  };
}
