import React, {useState, useEffect} from 'react';
import {useConsent} from '../hooks/useConsent';
import {useConsentPolicy} from '../hooks/useConsentPolicy';
import {PreferencesModal} from './PreferencesModal';

export interface ConsentBannerProps {
    position?: 'top' | 'bottom';
    theme?: 'light' | 'dark';
    primaryColor?: string;
    onAcceptAll?: () => void;
    onRejectAll?: () => void;
    onSavePreferences?: () => void;
}

export const ConsentBanner: React.FC<ConsentBannerProps> = ({
    position = 'bottom',
    theme = 'light',
    primaryColor = '#007bff',
    onAcceptAll,
    onRejectAll,
    onSavePreferences,
}) => {
    const {acceptAll, rejectNonEssential, hasConsent} = useConsent();
    const {policy, loading, error} = useConsentPolicy();
    const [showBanner, setShowBanner] = useState(false);
    const [showModal, setShowModal] = useState(false);

    useEffect(() => {
        // Show banner if no consent exists and policy is loaded
        if (!loading && policy && !hasConsent()) {
            setShowBanner(true);
        }
    }, [loading, policy, hasConsent]);

    const handleAcceptAll = async () => {
        await acceptAll();
        setShowBanner(false);
        onAcceptAll?.();
    };

    const handleRejectAll = async () => {
        await rejectNonEssential();
        setShowBanner(false);
        onRejectAll?.();
    };

    const handleCustomize = () => {
        setShowModal(true);
    };

    const handleSavePreferences = () => {
        setShowBanner(false);
        setShowModal(false);
        onSavePreferences?.();
    };

    if (loading || error || !policy || !showBanner) {
        return null;
    }

    const bannerClasses = [
        'masilia-consent-banner',
        `masilia-consent-banner--${position}`,
        `masilia-consent-banner--${theme}`,
    ].join(' ');

    return (
        <>
            <div
                className={bannerClasses}
                style={{'--primary-color': primaryColor} as React.CSSProperties}
                role="dialog"
                aria-labelledby="consent-banner-title"
                aria-describedby="consent-banner-description"
            >
                <div className="masilia-consent-banner__container">
                    <div className="masilia-consent-banner__content">
                        <div className="masilia-consent-banner__icon">
                            <svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                strokeWidth="2"
                                aria-hidden="true"
                            >
                                <path
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                            </svg>
                        </div>

                        <div className="masilia-consent-banner__text">
                            <h2 id="consent-banner-title" className="masilia-consent-banner__title">
                                We value your privacy
                            </h2>
                            <p id="consent-banner-description" className="masilia-consent-banner__description">
                                We use cookies to enhance your browsing experience, serve personalized content,
                                and analyze our traffic. By clicking "Accept All", you consent to our use of cookies.
                            </p>
                        </div>
                    </div>

                    <div className="masilia-consent-banner__actions">
                        <button
                            type="button"
                            className="masilia-consent-banner__button masilia-consent-banner__button--secondary"
                            onClick={handleRejectAll}
                            aria-label="Reject non-essential cookies"
                        >
                            Reject All
                        </button>

                        <button
                            type="button"
                            className="masilia-consent-banner__button masilia-consent-banner__button--tertiary"
                            onClick={handleCustomize}
                            aria-label="Customize cookie preferences"
                        >
                            Customize
                        </button>

                        <button
                            type="button"
                            className="masilia-consent-banner__button masilia-consent-banner__button--primary"
                            onClick={handleAcceptAll}
                            aria-label="Accept all cookies"
                        >
                            Accept All
                        </button>
                    </div>
                </div>
            </div>

            {showModal && (
                <PreferencesModal
                    isOpen={showModal}
                    onClose={() => setShowModal(false)}
                    onSave={handleSavePreferences}
                    theme={theme}
                    primaryColor={primaryColor}
                />
            )}
        </>
    );
};
