<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Controller\Admin;

use Masilia\ConsentBundle\Entity\CookieCategory;
use Masilia\ConsentBundle\Entity\CookiePolicy;
use Masilia\ConsentBundle\Entity\ThirdPartyService;
use Masilia\ConsentBundle\Form\Type\CategoryType;
use Masilia\ConsentBundle\Form\Type\PolicyType;
use Masilia\ConsentBundle\Form\Type\ThirdPartyServiceType;
use Masilia\ConsentBundle\Repository\CookiePolicyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/consent/policy', name: 'masilia_consent_admin_policy_')]
class PolicyAdminController extends AbstractController
{
    public function __construct(
        private readonly CookiePolicyRepository $policyRepository
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): Response
    {
        $policies = $this->policyRepository->findAll();

        return $this->render('@MasiliaConsent/admin/policy/list.html.twig', [
            'policies' => $policies,
        ]);
    }

    #[Route('/{id}', name: 'view', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function view(CookiePolicy $policy): Response
    {
        // Create forms for add category
        $newCategory = new CookieCategory();
        $newCategory->setPolicy($policy);
        $addCategoryForm = $this->createForm(CategoryType::class, $newCategory, [
            'action' => $this->generateUrl('masilia_consent_admin_category_create', ['policyId' => $policy->getId()]),
        ]);

        // Create edit forms for each category
        $editForms = [];
        foreach ($policy->getCategories() as $category) {
            $editForms[$category->getId()] = $this->createForm(CategoryType::class, $category, [
                'action' => $this->generateUrl('masilia_consent_admin_category_edit', ['id' => $category->getId()]),
            ])->createView();
        }

        // Create form for add service
        $newService = new ThirdPartyService();
        $newService->setPolicy($policy);
        $addServiceForm = $this->createForm(ThirdPartyServiceType::class, $newService, [
            'action' => $this->generateUrl('masilia_consent_admin_service_create', ['policyId' => $policy->getId()]),
        ]);

        // Create edit forms for each service
        $editServiceForms = [];
        foreach ($policy->getThirdPartyServices() as $service) {
            $editServiceForms[$service->getId()] = $this->createForm(ThirdPartyServiceType::class, $service, [
                'action' => $this->generateUrl('masilia_consent_admin_service_edit', ['id' => $service->getId()]),
            ])->createView();
        }

        return $this->render('@MasiliaConsent/admin/policy/view_refactored.html.twig', [
            'policy' => $policy,
            'addCategoryForm' => $addCategoryForm->createView(),
            'editForms' => $editForms,
            'addServiceForm' => $addServiceForm->createView(),
            'editServiceForms' => $editServiceForms,
        ]);
    }

    #[Route('/{id}/activate', name: 'activate', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function activate(CookiePolicy $policy): Response
    {
        // Deactivate all other policies
        $this->policyRepository->deactivateAll();
        
        // Activate this policy
        $policy->setIsActive(true);
        $this->policyRepository->save($policy, true);

        $this->addFlash('success', sprintf('Policy version %s has been activated.', $policy->getVersion()));

        return $this->redirectToRoute('masilia_consent_admin_policy_list');
    }

    #[Route('/{id}/deactivate', name: 'deactivate', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deactivate(CookiePolicy $policy): Response
    {
        $policy->setIsActive(false);
        $this->policyRepository->save($policy, true);

        $this->addFlash('success', sprintf('Policy version %s has been deactivated.', $policy->getVersion()));

        return $this->redirectToRoute('masilia_consent_admin_policy_list');
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $policy = new CookiePolicy();
        $form = $this->createForm(PolicyType::class, $policy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // If this policy is set as active, deactivate all others
            if ($policy->isActive()) {
                $this->policyRepository->deactivateAll();
            }

            $this->policyRepository->save($policy, true);
            $this->addFlash('success', sprintf('Policy version %s has been created.', $policy->getVersion()));

            return $this->redirectToRoute('masilia_consent_admin_policy_view', ['id' => $policy->getId()]);
        }

        return $this->render('@MasiliaConsent/admin/policy/create.html.twig', [
            'form' => $form->createView(),
            'policy' => $policy,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, CookiePolicy $policy): Response
    {
        $form = $this->createForm(PolicyType::class, $policy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // If this policy is set as active, deactivate all others
            if ($policy->isActive()) {
                $this->policyRepository->deactivateAll();
                $policy->setIsActive(true);
            }

            $this->policyRepository->save($policy, true);
            $this->addFlash('success', sprintf('Policy version %s has been updated.', $policy->getVersion()));

            return $this->redirectToRoute('masilia_consent_admin_policy_view', ['id' => $policy->getId()]);
        }

        return $this->render('@MasiliaConsent/admin/policy/edit.html.twig', [
            'form' => $form->createView(),
            'policy' => $policy,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, CookiePolicy $policy): Response
    {
        if ($policy->isActive()) {
            $this->addFlash('error', 'Cannot delete an active policy. Deactivate it first.');
            return $this->redirectToRoute('masilia_consent_admin_policy_list');
        }

        if ($this->isCsrfTokenValid('delete' . $policy->getId(), $request->request->get('_token'))) {
            $version = $policy->getVersion();
            $this->policyRepository->remove($policy, true);
            $this->addFlash('success', sprintf('Policy version %s has been deleted.', $version));
        }

        return $this->redirectToRoute('masilia_consent_admin_policy_list');
    }
}
