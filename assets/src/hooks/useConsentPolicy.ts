import {useState, useEffect} from 'react';
import {consentApi} from '../services/consentApi';
import type {CookiePolicy} from '../types/consent.types';

export function useConsentPolicy(apiBaseUrl?: string) {
    const [policy, setPolicy] = useState<CookiePolicy | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<Error | null>(null);

    const api = apiBaseUrl ? new (consentApi.constructor as any)(apiBaseUrl) : consentApi;

    useEffect(() => {
        const fetchPolicy = async () => {
            try {
                setLoading(true);
                const data = await api.getPolicy();
                setPolicy(data);
                setError(null);
            } catch (err) {
                setError(err as Error);
            } finally {
                setLoading(false);
            }
        };

        fetchPolicy();
    }, [api]);

    return {
        policy,
        categories: policy?.categories || [],
        services: policy?.thirdPartyServices || [],
        loading,
        error,
    };
}
