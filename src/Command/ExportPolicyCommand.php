<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Command;

use Masilia\ConsentBundle\Repository\CookiePolicyRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'masilia:consent:export',
    description: 'Export cookie policy to JSON file'
)]
class ExportPolicyCommand extends Command
{
    public function __construct(
        private readonly CookiePolicyRepository $policyRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Output file path')
            ->addOption('version', null, InputOption::VALUE_REQUIRED, 'Policy version to export (default: active policy)')
            ->addOption('pretty', 'p', InputOption::VALUE_NONE, 'Pretty print JSON');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = $input->getArgument('file');
        $version = $input->getOption('version');

        // Find policy
        if ($version) {
            $policy = $this->policyRepository->findByVersion($version);
            if (!$policy) {
                $io->error(sprintf('Policy version %s not found', $version));
                return Command::FAILURE;
            }
        } else {
            $policy = $this->policyRepository->findActivePolicy();
            if (!$policy) {
                $io->error('No active policy found');
                return Command::FAILURE;
            }
        }

        $io->section(sprintf('Exporting Policy Version %s', $policy->getVersion()));

        // Build data structure
        $categories = [];
        foreach ($policy->getCategories() as $category) {
            $cookies = [];
            foreach ($category->getCookies() as $cookie) {
                $cookieData = [
                    'name' => $cookie->getName(),
                    'purpose' => $cookie->getPurpose(),
                    'provider' => $cookie->getProvider(),
                    'expiry' => $cookie->getExpiry(),
                ];

                if ($cookie->hasScript()) {
                    $cookieData['script'] = [];
                    if ($cookie->getScriptSrc()) {
                        $cookieData['script']['src'] = $cookie->getScriptSrc();
                        $cookieData['script']['async'] = $cookie->isScriptAsync();
                    }
                    if ($cookie->getInitCode()) {
                        $cookieData['script']['initCode'] = $cookie->getInitCode();
                    }
                }

                $cookies[] = $cookieData;
            }

            $categories[] = [
                'id' => $category->getIdentifier(),
                'name' => $category->getName(),
                'description' => $category->getDescription(),
                'required' => $category->isRequired(),
                'defaultEnabled' => $category->isDefaultEnabled(),
                'cookies' => $cookies,
            ];
        }

        $services = [];
        foreach ($policy->getThirdPartyServices() as $service) {
            $services[] = [
                'id' => $service->getIdentifier(),
                'name' => $service->getName(),
                'category' => $service->getCategory(),
                'description' => $service->getDescription(),
                'privacyPolicy' => $service->getPrivacyPolicyUrl(),
                'configKey' => $service->getConfigKey(),
                'configValue' => $service->getConfigValue(),
            ];
        }

        $data = [
            'cookiePolicy' => [
                'version' => $policy->getVersion(),
                'lastUpdated' => $policy->getLastUpdated()->format('Y-m-d'),
                'expirationDays' => $policy->getExpirationDays(),
                'cookiePrefix' => $policy->getCookiePrefix(),
                'categories' => $categories,
                'thirdPartyServices' => $services,
            ],
        ];

        // Write to file
        $flags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        if ($input->getOption('pretty')) {
            $flags |= JSON_PRETTY_PRINT;
        }

        $json = json_encode($data, $flags);
        file_put_contents($filePath, $json);

        $io->success(sprintf('Policy exported to %s', $filePath));

        return Command::SUCCESS;
    }
}
