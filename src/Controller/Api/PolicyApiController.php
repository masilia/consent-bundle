<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Controller\Api;

use Masilia\ConsentBundle\Repository\CookiePolicyRepository;
use Masilia\ConsentBundle\Service\ScriptInjectionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/consent', name: 'masilia_consent_api_policy_')]
class PolicyApiController extends AbstractController
{
    public function __construct(
        private readonly CookiePolicyRepository $policyRepository,
        private readonly ScriptInjectionService $scriptInjectionService
    ) {
    }

    #[Route('/policy', name: 'get', methods: ['GET'])]
    public function getPolicy(): JsonResponse
    {
        $policy = $this->policyRepository->findActivePolicy();

        if (!$policy) {
            return new JsonResponse([
                'error' => 'No active policy found',
            ], Response::HTTP_NOT_FOUND);
        }

        $categories = [];
        foreach ($policy->getCategories() as $category) {
            $cookies = [];
            foreach ($category->getCookies() as $cookie) {
                $cookies[] = [
                    'name' => $cookie->getName(),
                    'purpose' => $cookie->getPurpose(),
                    'provider' => $cookie->getProvider(),
                    'expiry' => $cookie->getExpiry(),
                ];
            }

            $categories[] = [
                'id' => $category->getId(),
                'identifier' => $category->getIdentifier(),
                'name' => $category->getName(),
                'description' => $category->getDescription(),
                'required' => $category->isRequired(),
                'defaultEnabled' => $category->isDefaultEnabled(),
                'cookies' => $cookies,
            ];
        }

        $services = [];
        foreach ($policy->getThirdPartyServices() as $service) {
            if ($service->isEnabled()) {
                $services[] = [
                    'id' => $service->getIdentifier(),
                    'name' => $service->getName(),
                    'category' => $service->getCategory(),
                    'description' => $service->getDescription(),
                    'privacyPolicyUrl' => $service->getPrivacyPolicyUrl(),
                ];
            }
        }

        return new JsonResponse([
            'version' => $policy->getVersion(),
            'lastUpdated' => $policy->getLastUpdated()->format('Y-m-d'),
            'expirationDays' => $policy->getExpirationDays(),
            'cookiePrefix' => $policy->getCookiePrefix(),
            'categories' => $categories,
            'thirdPartyServices' => $services,
        ]);
    }

    #[Route('/categories', name: 'categories', methods: ['GET'])]
    public function getCategories(): JsonResponse
    {
        $policy = $this->policyRepository->findActivePolicy();

        if (!$policy) {
            return new JsonResponse([
                'error' => 'No active policy found',
            ], Response::HTTP_NOT_FOUND);
        }

        $categories = [];
        foreach ($policy->getCategories() as $category) {
            $categories[] = [
                'id' => $category->getIdentifier(),
                'name' => $category->getName(),
                'description' => $category->getDescription(),
                'required' => $category->isRequired(),
                'defaultEnabled' => $category->isDefaultEnabled(),
            ];
        }

        return new JsonResponse(['categories' => $categories]);
    }

    #[Route('/scripts/{category}', name: 'scripts', methods: ['GET'])]
    public function getScripts(string $category): JsonResponse
    {
        $policy = $this->policyRepository->findActivePolicy();

        if (!$policy) {
            return new JsonResponse([
                'error' => 'No active policy found',
            ], Response::HTTP_NOT_FOUND);
        }

        $categoryEntity = null;
        foreach ($policy->getCategories() as $cat) {
            if ($cat->getIdentifier() === $category) {
                $categoryEntity = $cat;
                break;
            }
        }

        if (!$categoryEntity) {
            return new JsonResponse([
                'error' => 'Category not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $scripts = $this->scriptInjectionService->getScriptsForCategory($categoryEntity);

        return new JsonResponse([
            'category' => $category,
            'scripts' => $scripts,
            'shouldInject' => $this->scriptInjectionService->shouldInjectScripts($category),
        ]);
    }
}
