<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Controller\Admin;

use Masilia\ConsentBundle\Repository\ConsentLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/consent/statistics', name: 'masilia_consent_admin_statistics_')]
class StatisticsController extends AbstractController
{
    public function __construct(
        private readonly ConsentLogRepository $logRepository
    ) {
    }

    #[Route('', name: 'dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        $from = new \DateTime('-30 days');
        $to = new \DateTime();

        $totalConsents = $this->logRepository->getTotalConsents($from, $to);
        $statistics = $this->logRepository->getConsentStatistics($from, $to);

        // Process statistics for display
        $categoryStats = [];
        foreach ($statistics as $stat) {
            $preferences = $stat['preferences'];
            foreach ($preferences as $category => $accepted) {
                if (!isset($categoryStats[$category])) {
                    $categoryStats[$category] = ['accepted' => 0, 'rejected' => 0];
                }
                if ($accepted) {
                    $categoryStats[$category]['accepted'] += $stat['count'];
                } else {
                    $categoryStats[$category]['rejected'] += $stat['count'];
                }
            }
        }

        return $this->render('@MasiliaConsent/admin/statistics/dashboard.html.twig', [
            'totalConsents' => $totalConsents,
            'categoryStats' => $categoryStats,
            'dateRange' => [
                'from' => $from,
                'to' => $to,
            ],
        ]);
    }
}
