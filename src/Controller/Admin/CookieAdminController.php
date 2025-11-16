<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Controller\Admin;

use Masilia\ConsentBundle\Entity\Cookie;
use Masilia\ConsentBundle\Entity\CookieCategory;
use Masilia\ConsentBundle\Form\Type\CookieType;
use Masilia\ConsentBundle\Repository\CookieRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/consent/cookie', name: 'masilia_consent_admin_cookie_')]
class CookieAdminController extends AbstractController
{
    public function __construct(
        private readonly CookieRepository $cookieRepository
    ) {
    }

    #[Route('/category/{categoryId}/create', name: 'create', requirements: ['categoryId' => '\d+'], methods: ['POST'])]
    #[ParamConverter('category', options: ['id' => 'categoryId'])]
    public function create(Request $request, CookieCategory $category): Response
    {
        $cookie = new Cookie();
        $cookie->setCategory($category);
        
        $form = $this->createForm(CookieType::class, $cookie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->cookieRepository->save($cookie, true);
            $this->addFlash('success', sprintf('Cookie "%s" has been created.', $cookie->getName()));
        } else {
            foreach ($form->getErrors(true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }
        }

        return $this->redirectToRoute('masilia_consent_admin_policy_view', [
            'id' => $category->getPolicy()->getId()
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function edit(Request $request, Cookie $cookie): Response
    {
        $form = $this->createForm(CookieType::class, $cookie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->cookieRepository->save($cookie, true);
            $this->addFlash('success', sprintf('Cookie "%s" has been updated.', $cookie->getName()));
        } else {
            foreach ($form->getErrors(true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }
        }

        return $this->redirectToRoute('masilia_consent_admin_policy_view', [
            'id' => $cookie->getCategory()->getPolicy()->getId()
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Cookie $cookie): Response
    {
        $policyId = $cookie->getCategory()->getPolicy()->getId();

        if ($this->isCsrfTokenValid('delete' . $cookie->getId(), $request->request->get('_token'))) {
            $name = $cookie->getName();
            $this->cookieRepository->remove($cookie, true);
            $this->addFlash('success', sprintf('Cookie "%s" has been deleted.', $name));
        }

        return $this->redirectToRoute('masilia_consent_admin_policy_view', ['id' => $policyId]);
    }
}
