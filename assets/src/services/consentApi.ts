import type {
    CookiePolicy,
    ConsentStatus,
    ConsentApiResponse,
} from '../types/consent.types';

export class ConsentApi {
    private baseUrl: string;

    constructor(baseUrl: string = '/api/consent') {
        this.baseUrl = baseUrl;
    }

    async getPolicy(): Promise<CookiePolicy> {
        const response = await fetch(`${this.baseUrl}/policy`);
        if (!response.ok) {
            throw new Error('Failed to fetch policy');
        }
        return response.json();
    }

    async getStatus(): Promise<ConsentStatus> {
        const response = await fetch(`${this.baseUrl}/status`);
        if (!response.ok) {
            throw new Error('Failed to fetch consent status');
        }
        return response.json();
    }

    async acceptAll(): Promise<ConsentApiResponse> {
        const response = await fetch(`${this.baseUrl}/accept`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
        });
        if (!response.ok) {
            throw new Error('Failed to accept all cookies');
        }
        return response.json();
    }

    async rejectNonEssential(): Promise<ConsentApiResponse> {
        const response = await fetch(`${this.baseUrl}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
        });
        if (!response.ok) {
            throw new Error('Failed to reject non-essential cookies');
        }
        return response.json();
    }

    async savePreferences(categories: Record<string, boolean>): Promise<ConsentApiResponse> {
        const response = await fetch(`${this.baseUrl}/preferences`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({categories}),
        });
        if (!response.ok) {
            throw new Error('Failed to save preferences');
        }
        return response.json();
    }

    async revokeConsent(): Promise<ConsentApiResponse> {
        const response = await fetch(`${this.baseUrl}/revoke`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
        });
        if (!response.ok) {
            throw new Error('Failed to revoke consent');
        }
        return response.json();
    }

    async checkCategory(category: string): Promise<{ category: string; hasConsent: boolean }> {
        const response = await fetch(`${this.baseUrl}/check/${category}`);
        if (!response.ok) {
            throw new Error('Failed to check category consent');
        }
        return response.json();
    }
}

export const consentApi = new ConsentApi();
