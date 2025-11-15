<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Controller\Api;

use Masilia\ConsentBundle\Service\ConsentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/consent', name: 'masilia_consent_api_')]
class ConsentApiController extends AbstractController
{
    public function __construct(
        private readonly ConsentManager $consentManager
    ) {
    }

    #[Route('/status', name: 'status', methods: ['GET'])]
    public function status(): JsonResponse
    {
        $preferences = $this->consentManager->getConsentPreferences();
        $policy = $this->consentManager->getActivePolicy();

        if (!$preferences || !$policy) {
            return new JsonResponse([
                'hasConsent' => false,
                'preferences' => null,
                'policyVersion' => $policy?->getVersion(),
            ]);
        }

        return new JsonResponse([
            'hasConsent' => true,
            'preferences' => $preferences->toArray(),
            'policyVersion' => $policy->getVersion(),
            'needsUpdate' => $preferences->getVersion() !== $policy->getVersion(),
        ]);
    }

    #[Route('/accept', name: 'accept', methods: ['POST'])]
    public function acceptAll(): JsonResponse
    {
        $response = new JsonResponse(['success' => true, 'message' => 'All cookies accepted']);
        
        try {
            $this->consentManager->acceptAll($response);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        return $response;
    }

    #[Route('/reject', name: 'reject', methods: ['POST'])]
    public function rejectNonEssential(): JsonResponse
    {
        $response = new JsonResponse(['success' => true, 'message' => 'Non-essential cookies rejected']);
        
        try {
            $this->consentManager->rejectNonEssential($response);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        return $response;
    }

    #[Route('/preferences', name: 'preferences', methods: ['POST'])]
    public function savePreferences(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['categories']) || !is_array($data['categories'])) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid request: categories array required',
            ], Response::HTTP_BAD_REQUEST);
        }

        $response = new JsonResponse(['success' => true, 'message' => 'Preferences saved']);
        
        try {
            $this->consentManager->updatePreferences($data['categories'], $response);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        return $response;
    }

    #[Route('/revoke', name: 'revoke', methods: ['DELETE', 'POST'])]
    public function revokeConsent(): JsonResponse
    {
        $response = new JsonResponse(['success' => true, 'message' => 'Consent revoked']);
        
        try {
            $this->consentManager->revokeConsent($response);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        return $response;
    }

    #[Route('/check/{category}', name: 'check', methods: ['GET'])]
    public function checkCategory(string $category): JsonResponse
    {
        return new JsonResponse([
            'category' => $category,
            'hasConsent' => $this->consentManager->hasConsent($category),
        ]);
    }
}
