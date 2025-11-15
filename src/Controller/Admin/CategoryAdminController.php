<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Controller\Admin;

use Masilia\ConsentBundle\Entity\CookieCategory;
use Masilia\ConsentBundle\Entity\CookiePolicy;
use Masilia\ConsentBundle\Repository\CookieCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/policy/{policyId}/create', name: 'create', methods: ['POST'], requirements: ['policyId' => '\d+'])]
    public function create(Request $request, CookiePolicy $policy): Response
    {
        $data = $request->request->all('category');
        
        $category = new CookieCategory();
        $category->setPolicy($policy);
        $category->setIdentifier($data['identifier'] ?? '');
        $category->setName($data['name'] ?? '');
        $category->setDescription($data['description'] ?? '');
        $category->setPosition((int)($data['position'] ?? 0));
        $category->setRequired(isset($data['required']));
        $category->setDefaultEnabled(isset($data['defaultEnabled']));
        
        $this->categoryRepository->save($category, true);
        $this->addFlash('success', sprintf('Category "%s" has been created.', $category->getName()));

        return $this->redirectToRoute('masilia_consent_admin_policy_view', ['id' => $policy->getId()]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, CookieCategory $category): Response
    {
        $data = $request->request->all('category');
        
        $category->setIdentifier($data['identifier'] ?? $category->getIdentifier());
        $category->setName($data['name'] ?? $category->getName());
        $category->setDescription($data['description'] ?? $category->getDescription());
        $category->setPosition((int)($data['position'] ?? $category->getPosition()));
        $category->setRequired(isset($data['required']));
        $category->setDefaultEnabled(isset($data['defaultEnabled']));
        
        $this->categoryRepository->save($category, true);
        $this->addFlash('success', sprintf('Category "%s" has been updated.', $category->getName()));

        return $this->redirectToRoute('masilia_consent_admin_policy_view', ['id' => $category->getPolicy()->getId()]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, CookieCategory $category): Response
    {
        $policyId = $category->getPolicy()->getId();

        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $name = $category->getName();
            $this->categoryRepository->remove($category, true);
            $this->addFlash('success', sprintf('Category "%s" has been deleted.', $name));
        }

        return $this->redirectToRoute('masilia_consent_admin_policy_view', ['id' => $policyId]);
    }
}
