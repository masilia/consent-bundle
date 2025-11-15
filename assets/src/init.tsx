import { createRoot } from 'react-dom/client';
import { ConsentBanner, ConsentBannerProps } from './components/ConsentBanner';

/**
 * Initialize the Masilia Consent Banner
 * 
 * @param options - Configuration options for the consent banner
 * @returns Cleanup function to unmount the banner
 */
export function initConsentBanner(options: Partial<ConsentBannerProps> = {}): () => void {
  // Find or create the container
  let container = document.getElementById('masilia-consent-banner');
  
  if (!container) {
    container = document.createElement('div');
    container.id = 'masilia-consent-banner';
    document.body.appendChild(container);
  }

  // Create root and render
  const root = createRoot(container);
  root.render(<ConsentBanner {...options} />);

  // Return cleanup function
  return () => {
    root.unmount();
    if (container && container.parentNode) {
      container.parentNode.removeChild(container);
    }
  };
}

/**
 * Auto-initialize on DOM ready if container exists
 */
if (typeof window !== 'undefined') {
  const autoInit = () => {
    const container = document.getElementById('masilia-consent-banner');
    if (container && container.dataset.autoInit !== 'false') {
      const options: Partial<ConsentBannerProps> = {
        position: (container.dataset.position as 'top' | 'bottom') || 'bottom',
        theme: (container.dataset.theme as 'light' | 'dark') || 'light',
        primaryColor: container.dataset.primaryColor,
      };
      
      initConsentBanner(options);
    }
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', autoInit);
  } else {
    autoInit();
  }
}

export default initConsentBanner;
