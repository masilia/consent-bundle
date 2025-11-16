<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Service;

/**
 * Service that provides predefined cookie templates for common third-party services
 */
class CookiePresetService
{
    /**
     * Get all available service presets
     *
     * @return array<string, array{
     *     name: string,
     *     description: string,
     *     category: string,
     *     privacy_policy_url: string,
     *     script_template: string,
     *     cookies: array<array{name: string, purpose: string, expiry: string}>
     * }>
     */
    public function getPresets(): array
    {
        return [
            'google_analytics' => [
                'name' => 'Google Analytics',
                'description' => 'Web analytics service that tracks and reports website traffic',
                'category' => 'analytics',
                'privacy_policy_url' => 'https://policies.google.com/privacy',
                'script_template' => <<<'JS'
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{SERVICE_ID}}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{{SERVICE_ID}}');
</script>
JS,
                'cookies' => [
                    [
                        'name' => '_ga',
                        'purpose' => 'Registers a unique ID used to generate statistical data on how you use the website',
                        'expiry' => '2 years',
                    ],
                    [
                        'name' => '_gid',
                        'purpose' => 'Registers a unique ID used to generate statistical data on how you use the website',
                        'expiry' => '24 hours',
                    ],
                    [
                        'name' => '_gat',
                        'purpose' => 'Used by Google Analytics to throttle request rate',
                        'expiry' => '1 minute',
                    ],
                ],
            ],
            'google_tag_manager' => [
                'name' => 'Google Tag Manager',
                'description' => 'Tag management system that allows you to quickly update tags and code snippets',
                'category' => 'analytics',
                'privacy_policy_url' => 'https://policies.google.com/privacy',
                'script_template' => <<<'JS'
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{{SERVICE_ID}}');</script>
<!-- End Google Tag Manager -->
JS,
                'cookies' => [
                    [
                        'name' => '_ga',
                        'purpose' => 'Registers a unique ID used to generate statistical data',
                        'expiry' => '2 years',
                    ],
                    [
                        'name' => '_gid',
                        'purpose' => 'Registers a unique ID used to generate statistical data',
                        'expiry' => '24 hours',
                    ],
                ],
            ],
            'facebook_pixel' => [
                'name' => 'Facebook Pixel',
                'description' => 'Analytics tool that helps measure the effectiveness of advertising',
                'category' => 'marketing',
                'privacy_policy_url' => 'https://www.facebook.com/privacy/policy/',
                'script_template' => <<<'JS'
<!-- Facebook Pixel -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '{{SERVICE_ID}}');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id={{SERVICE_ID}}&ev=PageView&noscript=1"
/></noscript>
<!-- End Facebook Pixel -->
JS,
                'cookies' => [
                    [
                        'name' => '_fbp',
                        'purpose' => 'Used by Facebook to deliver advertising and measure and improve the relevance of ads',
                        'expiry' => '3 months',
                    ],
                    [
                        'name' => 'fr',
                        'purpose' => 'Contains browser and user unique ID combination for targeted advertising',
                        'expiry' => '3 months',
                    ],
                ],
            ],
            'hotjar' => [
                'name' => 'Hotjar',
                'description' => 'Analytics and feedback tool that reveals user behavior',
                'category' => 'analytics',
                'privacy_policy_url' => 'https://www.hotjar.com/legal/policies/privacy/',
                'script_template' => <<<'JS'
<!-- Hotjar -->
<script>
    (function(h,o,t,j,a,r){
        h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
        h._hjSettings={hjid:{{SERVICE_ID}},hjsv:6};
        a=o.getElementsByTagName('head')[0];
        r=o.createElement('script');r.async=1;
        r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
        a.appendChild(r);
    })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
</script>
<!-- End Hotjar -->
JS,
                'cookies' => [
                    [
                        'name' => '_hjSessionUser_*',
                        'purpose' => 'Set when a user first lands on a page. Persists the Hotjar User ID',
                        'expiry' => '1 year',
                    ],
                    [
                        'name' => '_hjSession_*',
                        'purpose' => 'Holds current session data',
                        'expiry' => '30 minutes',
                    ],
                ],
            ],
            'linkedin_insight' => [
                'name' => 'LinkedIn Insight Tag',
                'description' => 'Analytics tool for LinkedIn advertising campaigns',
                'category' => 'marketing',
                'privacy_policy_url' => 'https://www.linkedin.com/legal/privacy-policy',
                'script_template' => <<<'JS'
<!-- LinkedIn Insight Tag -->
<script type="text/javascript">
_linkedin_partner_id = "{{SERVICE_ID}}";
window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || [];
window._linkedin_data_partner_ids.push(_linkedin_partner_id);
</script><script type="text/javascript">
(function(l) {
if (!l){window.lintrk = function(a,b){window.lintrk.q.push([a,b])};
window.lintrk.q=[]}
var s = document.getElementsByTagName("script")[0];
var b = document.createElement("script");
b.type = "text/javascript";b.async = true;
b.src = "https://snap.licdn.com/li.lms-analytics/insight.min.js";
s.parentNode.insertBefore(b, s);})(window.lintrk);
</script>
<noscript>
<img height="1" width="1" style="display:none;" alt="" src="https://px.ads.linkedin.com/collect/?pid={{SERVICE_ID}}&fmt=gif" />
</noscript>
<!-- End LinkedIn Insight Tag -->
JS,
                'cookies' => [
                    [
                        'name' => 'li_sugr',
                        'purpose' => 'Used to make a probabilistic match of a user\'s identity',
                        'expiry' => '90 days',
                    ],
                    [
                        'name' => 'UserMatchHistory',
                        'purpose' => 'LinkedIn Ads ID syncing',
                        'expiry' => '30 days',
                    ],
                ],
            ],
            'youtube' => [
                'name' => 'YouTube',
                'description' => 'Video hosting and sharing platform',
                'category' => 'marketing',
                'privacy_policy_url' => 'https://policies.google.com/privacy',
                'script_template' => '',
                'cookies' => [
                    [
                        'name' => 'VISITOR_INFO1_LIVE',
                        'purpose' => 'Tries to estimate users\' bandwidth on pages with integrated YouTube videos',
                        'expiry' => '179 days',
                    ],
                    [
                        'name' => 'YSC',
                        'purpose' => 'Registers a unique ID to keep statistics of what videos from YouTube the user has seen',
                        'expiry' => 'Session',
                    ],
                    [
                        'name' => 'yt-remote-device-id',
                        'purpose' => 'Stores the user\'s video player preferences using embedded YouTube video',
                        'expiry' => 'Persistent',
                    ],
                ],
            ],
        ];
    }

    /**
     * Get preset by identifier
     */
    public function getPreset(string $identifier): ?array
    {
        $presets = $this->getPresets();
        return $presets[$identifier] ?? null;
    }

    /**
     * Get all available preset identifiers
     *
     * @return array<string>
     */
    public function getPresetIdentifiers(): array
    {
        return array_keys($this->getPresets());
    }

    /**
     * Get cookies for a specific preset
     *
     * @return array<array{name: string, purpose: string, expiry: string}>
     */
    public function getCookiesForPreset(string $identifier): array
    {
        $preset = $this->getPreset($identifier);
        return $preset['cookies'] ?? [];
    }

    /**
     * Get script template for a preset with service ID replaced
     */
    public function getScriptForPreset(string $identifier, string $serviceId): string
    {
        $preset = $this->getPreset($identifier);
        if (!$preset) {
            return '';
        }

        return str_replace('{{SERVICE_ID}}', $serviceId, $preset['script_template']);
    }
}
