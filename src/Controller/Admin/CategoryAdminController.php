<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Controller\Admin;

use Masilia\ConsentBundle\Entity\CookieCategory;
use Masilia\ConsentBundle\Entity\CookiePolicy;
use Masilia\ConsentBundle\Repository\CookieCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/consent/category', name: 'masilia_consent_admin_category_')]
class CategoryAdminController extends AbstractController
{
    public function __construct(
        private readonly CookieCategoryRepository $categoryRepository
    ) {
    }

    #[Route('/policy/{policyId}', name: 'list', methods: ['GET'], requirements: ['policyId' => '\d+'])]
    public function list(CookiePolicy $policy): Response
    {
        $categories = $this->categoryRepository->findByPolicy($policy);

        return $this->render('@MasiliaConsent/admin/category/list.html.twig', [
            'policy' => $policy,
            'categories' => $categories,
        ]);
    }

    #[Route('/{id}', name: 'view', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function view(CookieCategory $category): Response
    {
        return $this->render('@MasiliaConsent/admin/category/view.html.twig', [
            'category' => $category,
        ]);
    }
}
