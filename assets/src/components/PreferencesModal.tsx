import React, { useState, useEffect } from 'react';
import { useConsent } from '../hooks/useConsent';
import { useConsentPolicy } from '../hooks/useConsentPolicy';
import type { CookieCategory } from '../types/consent.types';
import '../styles/PreferencesModal.css';

export interface PreferencesModalProps {
  isOpen: boolean;
  onClose: () => void;
  onSave?: () => void;
  theme?: 'light' | 'dark';
  primaryColor?: string;
}

export const PreferencesModal: React.FC<PreferencesModalProps> = ({
  isOpen,
  onClose,
  onSave,
  theme = 'light',
  primaryColor = '#007bff',
}) => {
  const { updatePreferences, getPreferences } = useConsent();
  const { policy, loading } = useConsentPolicy();
  const [selectedCategories, setSelectedCategories] = useState<Record<string, boolean>>({});
  const [activeTab, setActiveTab] = useState<string>('overview');

  useEffect(() => {
    if (policy) {
      // Initialize with current preferences or defaults
      const currentPrefs = getPreferences();
      const initial: Record<string, boolean> = {};
      
      policy.categories.forEach((category) => {
        if (currentPrefs) {
          initial[category.identifier] = currentPrefs.categories[category.identifier] ?? category.defaultEnabled;
        } else {
          initial[category.identifier] = category.required || category.defaultEnabled;
        }
      });
      
      setSelectedCategories(initial);
      
      // Set first category as active tab if not overview
      if (policy.categories.length > 0 && activeTab === 'overview') {
        setActiveTab('overview');
      }
    }
  }, [policy, getPreferences]);

  const handleToggle = (categoryId: string, required: boolean) => {
    if (required) return; // Can't toggle required categories
    
    setSelectedCategories(prev => ({
      ...prev,
      [categoryId]: !prev[categoryId],
    }));
  };

  const handleSave = async () => {
    await updatePreferences(selectedCategories);
    onSave?.();
    onClose();
  };

  const handleAcceptAll = async () => {
    if (!policy) return;
    
    const allAccepted: Record<string, boolean> = {};
    policy.categories.forEach(cat => {
      allAccepted[cat.identifier] = true;
    });
    
    setSelectedCategories(allAccepted);
    await updatePreferences(allAccepted);
    onSave?.();
    onClose();
  };

  const handleRejectAll = async () => {
    if (!policy) return;
    
    const onlyRequired: Record<string, boolean> = {};
    policy.categories.forEach(cat => {
      onlyRequired[cat.identifier] = cat.required;
    });
    
    setSelectedCategories(onlyRequired);
    await updatePreferences(onlyRequired);
    onSave?.();
    onClose();
  };

  if (!isOpen || loading || !policy) {
    return null;
  }

  const modalClasses = [
    'masilia-consent-modal',
    `masilia-consent-modal--${theme}`,
  ].join(' ');

  return (
    <div 
      className="masilia-consent-modal-overlay"
      onClick={onClose}
      role="dialog"
      aria-modal="true"
      aria-labelledby="preferences-modal-title"
    >
      <div 
        className={modalClasses}
        style={{ '--primary-color': primaryColor } as React.CSSProperties}
        onClick={(e) => e.stopPropagation()}
      >
        {/* Header */}
        <div className="masilia-consent-modal__header">
          <h2 id="preferences-modal-title" className="masilia-consent-modal__title">
            Cookie Preferences
          </h2>
          <button
            type="button"
            className="masilia-consent-modal__close"
            onClick={onClose}
            aria-label="Close preferences modal"
          >
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
              <path d="M18 6L6 18M6 6l12 12" />
            </svg>
          </button>
        </div>

        {/* Tabs */}
        <div className="masilia-consent-modal__tabs" role="tablist">
          <button
            type="button"
            role="tab"
            aria-selected={activeTab === 'overview'}
            aria-controls="overview-panel"
            className={`masilia-consent-modal__tab ${activeTab === 'overview' ? 'masilia-consent-modal__tab--active' : ''}`}
            onClick={() => setActiveTab('overview')}
          >
            Overview
          </button>
          {policy.categories.map((category) => (
            <button
              key={category.id}
              type="button"
              role="tab"
              aria-selected={activeTab === category.identifier}
              aria-controls={`${category.identifier}-panel`}
              className={`masilia-consent-modal__tab ${activeTab === category.identifier ? 'masilia-consent-modal__tab--active' : ''}`}
              onClick={() => setActiveTab(category.identifier)}
            >
              {category.name}
            </button>
          ))}
        </div>

        {/* Content */}
        <div className="masilia-consent-modal__content">
          {activeTab === 'overview' ? (
            <div role="tabpanel" id="overview-panel" className="masilia-consent-modal__panel">
              <p className="masilia-consent-modal__description">
                We use cookies to improve your experience on our website. You can choose which 
                categories of cookies you want to allow. Required cookies cannot be disabled as 
                they are essential for the website to function properly.
              </p>

              <div className="masilia-consent-modal__categories">
                {policy.categories.map((category) => (
                  <CategoryToggle
                    key={category.id}
                    category={category}
                    checked={selectedCategories[category.identifier] ?? false}
                    onToggle={() => handleToggle(category.identifier, category.required)}
                  />
                ))}
              </div>
            </div>
          ) : (
            policy.categories
              .filter(cat => cat.identifier === activeTab)
              .map((category) => (
                <div 
                  key={category.id}
                  role="tabpanel" 
                  id={`${category.identifier}-panel`}
                  className="masilia-consent-modal__panel"
                >
                  <CategoryDetails category={category} />
                </div>
              ))
          )}
        </div>

        {/* Footer */}
        <div className="masilia-consent-modal__footer">
          <button
            type="button"
            className="masilia-consent-modal__button masilia-consent-modal__button--secondary"
            onClick={handleRejectAll}
          >
            Reject All
          </button>
          
          <button
            type="button"
            className="masilia-consent-modal__button masilia-consent-modal__button--tertiary"
            onClick={handleAcceptAll}
          >
            Accept All
          </button>
          
          <button
            type="button"
            className="masilia-consent-modal__button masilia-consent-modal__button--primary"
            onClick={handleSave}
          >
            Save Preferences
          </button>
        </div>
      </div>
    </div>
  );
};

// Category Toggle Component
interface CategoryToggleProps {
  category: CookieCategory;
  checked: boolean;
  onToggle: () => void;
}

const CategoryToggle: React.FC<CategoryToggleProps> = ({ category, checked, onToggle }) => {
  return (
    <div className="masilia-consent-category">
      <div className="masilia-consent-category__header">
        <div className="masilia-consent-category__info">
          <h3 className="masilia-consent-category__name">
            {category.name}
            {category.required && (
              <span className="masilia-consent-category__badge">Required</span>
            )}
          </h3>
          <p className="masilia-consent-category__description">
            {category.description}
          </p>
        </div>
        
        <label className="masilia-consent-toggle">
          <input
            type="checkbox"
            checked={checked}
            onChange={onToggle}
            disabled={category.required}
            aria-label={`Toggle ${category.name} cookies`}
          />
          <span className="masilia-consent-toggle__slider"></span>
        </label>
      </div>
    </div>
  );
};

// Category Details Component
interface CategoryDetailsProps {
  category: CookieCategory;
}

const CategoryDetails: React.FC<CategoryDetailsProps> = ({ category }) => {
  return (
    <div className="masilia-consent-details">
      <h3 className="masilia-consent-details__title">{category.name}</h3>
      <p className="masilia-consent-details__description">{category.description}</p>
      
      {category.cookies && category.cookies.length > 0 && (
        <div className="masilia-consent-details__cookies">
          <h4 className="masilia-consent-details__subtitle">Cookies Used:</h4>
          <div className="masilia-consent-details__table">
            <table>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Provider</th>
                  <th>Purpose</th>
                  <th>Expiry</th>
                </tr>
              </thead>
              <tbody>
                {category.cookies.map((cookie) => (
                  <tr key={cookie.id}>
                    <td><code>{cookie.name}</code></td>
                    <td>{cookie.provider}</td>
                    <td>{cookie.purpose}</td>
                    <td>{cookie.expiry}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}
    </div>
  );
};
