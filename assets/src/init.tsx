import { createRoot } from 'react-dom/client';
import { ConsentBanner, ConsentBannerProps } from './components/ConsentBanner';

// Store root instance to prevent multiple createRoot calls
let rootInstance: ReturnType<typeof createRoot> | null = null;
let containerInstance: HTMLElement | null = null;

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

  // Reuse existing root or create new one
  if (containerInstance === container && rootInstance) {
    // Update existing root
    rootInstance.render(<ConsentBanner {...options} />);
  } else {
    // Create new root
    rootInstance = createRoot(container);
    containerInstance = container;
    rootInstance.render(<ConsentBanner {...options} />);
  }

  // Return cleanup function
  return () => {
    if (rootInstance) {
      rootInstance.unmount();
      rootInstance = null;
    }
    if (containerInstance && containerInstance.parentNode) {
      containerInstance.parentNode.removeChild(containerInstance);
      containerInstance = null;
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
