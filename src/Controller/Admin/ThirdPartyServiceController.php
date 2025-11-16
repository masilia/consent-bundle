<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Controller\Admin;

use Masilia\ConsentBundle\Entity\CookiePolicy;
use Masilia\ConsentBundle\Entity\ThirdPartyService;
use Masilia\ConsentBundle\Form\Type\ThirdPartyServiceType;
use Masilia\ConsentBundle\Repository\ThirdPartyServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/consent/service', name: 'masilia_consent_admin_service_')]
class ThirdPartyServiceController extends AbstractController
{
    public function __construct(
        private readonly ThirdPartyServiceRepository $serviceRepository
    ) {
    }

    #[Route('/policy/{policyId}', name: 'list', methods: ['GET'], requirements: ['policyId' => '\d+'])]
    public function list(CookiePolicy $policy): Response
    {
        $services = $this->serviceRepository->findBy(
            ['policy' => $policy],
            ['createdAt' => 'DESC']
        );

        return $this->render('@MasiliaConsent/admin/service/list.html.twig', [
            'policy' => $policy,
            'services' => $services,
        ]);
    }

    #[Route('/{id}', name: 'view', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function view(ThirdPartyService $service): Response
    {
        return $this->render('@MasiliaConsent/admin/service/view.html.twig', [
            'service' => $service,
        ]);
    }

    #[Route('/policy/{policyId}/create', name: 'create', methods: ['GET', 'POST'], requirements: ['policyId' => '\d+'])]
    public function create(Request $request, CookiePolicy $policy): Response
    {
        $service = new ThirdPartyService();
        $service->setPolicy($policy);
        
        $form = $this->createForm(ThirdPartyServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->serviceRepository->save($service, true);
            $this->addFlash('success', sprintf('Service "%s" has been created.', $service->getName()));
            
            return $this->redirectToRoute('masilia_consent_admin_policy_view', ['id' => $policy->getId()]);
        }

        // If GET request or form has errors, show the form
        return $this->render('@MasiliaConsent/admin/service/create.html.twig', [
            'policy' => $policy,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, ThirdPartyService $service): Response
    {
        $form = $this->createForm(ThirdPartyServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->serviceRepository->save($service, true);
            $this->addFlash('success', sprintf('Service "%s" has been updated.', $service->getName()));
            
            return $this->redirectToRoute('masilia_consent_admin_policy_view', ['id' => $service->getPolicy()->getId()]);
        }

        return $this->render('@MasiliaConsent/admin/service/edit.html.twig', [
            'service' => $service,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, ThirdPartyService $service): Response
    {
        $policyId = $service->getPolicy()->getId();

        if ($this->isCsrfTokenValid('delete' . $service->getId(), $request->request->get('_token'))) {
            $name = $service->getName();
            $this->serviceRepository->remove($service, true);
            $this->addFlash('success', sprintf('Service "%s" has been deleted.', $name));
        }

        return $this->redirectToRoute('masilia_consent_admin_policy_view', ['id' => $policyId]);
    }

    #[Route('/{id}/toggle', name: 'toggle', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function toggle(Request $request, ThirdPartyService $service): Response
    {
        if ($this->isCsrfTokenValid('toggle' . $service->getId(), $request->request->get('_token'))) {
            $service->setEnabled(!$service->isEnabled());
            $this->serviceRepository->save($service, true);
            
            $status = $service->isEnabled() ? 'enabled' : 'disabled';
            $this->addFlash('success', sprintf('Service "%s" has been %s.', $service->getName(), $status));
        }

        return $this->redirectToRoute('masilia_consent_admin_policy_view', ['id' => $service->getPolicy()->getId()]);
    }
}
