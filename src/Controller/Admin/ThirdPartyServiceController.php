<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Controller\Admin;

use Ibexa\Contracts\AdminUi\Notification\NotificationHandlerInterface;
use Masilia\ConsentBundle\Entity\CookiePolicy;
use Masilia\ConsentBundle\Entity\ThirdPartyService;
use Masilia\ConsentBundle\Form\Type\ThirdPartyServiceType;
use Masilia\ConsentBundle\Repository\ThirdPartyServiceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/consent/service', name: 'masilia_consent_admin_service_')]
class ThirdPartyServiceController extends AbstractController
{
    public function __construct(
        private readonly ThirdPartyServiceRepository $serviceRepository,
        private readonly NotificationHandlerInterface $notificationHandler
    ) {
    }

    #[Route('/policy/{policyId}', name: 'list', requirements: ['policyId' => '\d+'], methods: ['GET'])]
    #[ParamConverter('policy', options: ['id' => 'policyId'])]
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

    #[Route('/{id}', name: 'view', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function view(ThirdPartyService $service): Response
    {
        return $this->render('@MasiliaConsent/admin/service/view.html.twig', [
            'service' => $service,
        ]);
    }

    #[Route('/policy/{policyId}/create', name: 'create', requirements: ['policyId' => '\d+'], methods: ['POST'])]
    #[ParamConverter('policy', options: ['id' => 'policyId'])]
    public function create(Request $request, CookiePolicy $policy): Response
    {
        $service = new ThirdPartyService();
        $service->setPolicy($policy);
        
        $form = $this->createForm(ThirdPartyServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->serviceRepository->save($service, true);
            $this->notificationHandler->success(
                /** @Desc("Service '%name%' has been created.") */
                'service.create.success',
                ['%name%' => $service->getName()],
                'masilia_consent'
            );
        } else {
            foreach ($form->getErrors(true) as $error) {
                $this->notificationHandler->error(
                    $error->getMessage(),
                    [],
                    'masilia_consent'
                );
            }
        }

        return $this->redirectToRoute('masilia_consent_admin_policy_view', ['id' => $policy->getId()]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function edit(Request $request, ThirdPartyService $service): Response
    {
        $form = $this->createForm(ThirdPartyServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->serviceRepository->save($service, true);
            $this->notificationHandler->success(
                /** @Desc("Service '%name%' has been updated.") */
                'service.edit.success',
                ['%name%' => $service->getName()],
                'masilia_consent'
            );
        } else {
            foreach ($form->getErrors(true) as $error) {
                $this->notificationHandler->error(
                    $error->getMessage(),
                    [],
                    'masilia_consent'
                );
            }
        }

        return $this->redirectToRoute('masilia_consent_admin_policy_view', ['id' => $service->getPolicy()->getId()]);
    }

    #[Route('/{id}/delete', name: 'delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, ThirdPartyService $service): Response
    {
        $policyId = $service->getPolicy()->getId();

        if ($this->isCsrfTokenValid('delete' . $service->getId(), $request->request->get('_token'))) {
            $name = $service->getName();
            $this->serviceRepository->remove($service, true);
            $this->notificationHandler->success(
                /** @Desc("Service '%name%' has been deleted.") */
                'service.delete.success',
                ['%name%' => $name],
                'masilia_consent'
            );
        }

        return $this->redirectToRoute('masilia_consent_admin_policy_view', ['id' => $policyId]);
    }

    #[Route('/{id}/toggle', name: 'toggle', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function toggle(Request $request, ThirdPartyService $service): Response
    {
        if ($this->isCsrfTokenValid('toggle' . $service->getId(), $request->request->get('_token'))) {
            $service->setEnabled(!$service->isEnabled());
            $this->serviceRepository->save($service, true);
            
            $status = $service->isEnabled() ? 'enabled' : 'disabled';
            $this->notificationHandler->success(
                /** @Desc("Service '%name%' has been %status%.") */
                'service.toggle.success',
                ['%name%' => $service->getName(), '%status%' => $status],
                'masilia_consent'
            );
        }

        return $this->redirectToRoute('masilia_consent_admin_policy_view', ['id' => $service->getPolicy()->getId()]);
    }
}
