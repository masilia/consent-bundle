<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Command;

use Masilia\ConsentBundle\Entity\Cookie;
use Masilia\ConsentBundle\Entity\CookieCategory;
use Masilia\ConsentBundle\Entity\CookiePolicy;
use Masilia\ConsentBundle\Entity\ThirdPartyService;
use Masilia\ConsentBundle\Repository\CookiePolicyRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'masilia:consent:import',
    description: 'Import cookie policy from JSON file'
)]
class ImportPolicyCommand extends Command
{
    public function __construct(
        private readonly CookiePolicyRepository $policyRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Path to JSON file')
            ->addOption('activate', 'a', InputOption::VALUE_NONE, 'Activate the imported policy')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrite existing policy with same version');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = $input->getArgument('file');

        if (!file_exists($filePath)) {
            $io->error(sprintf('File not found: %s', $filePath));
            return Command::FAILURE;
        }

        try {
            $jsonContent = file_get_contents($filePath);
            $data = json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $io->error(sprintf('Invalid JSON: %s', $e->getMessage()));
            return Command::FAILURE;
        }

        if (!isset($data['cookiePolicy'])) {
            $io->error('Invalid format: cookiePolicy key not found');
            return Command::FAILURE;
        }

        $policyData = $data['cookiePolicy'];

        // Check if policy version already exists
        $existingPolicy = $this->policyRepository->findByVersion($policyData['version']);
        if ($existingPolicy && !$input->getOption('force')) {
            $io->error(sprintf('Policy version %s already exists. Use --force to overwrite.', $policyData['version']));
            return Command::FAILURE;
        }

        if ($existingPolicy && $input->getOption('force')) {
            $io->warning(sprintf('Removing existing policy version %s', $policyData['version']));
            $this->policyRepository->remove($existingPolicy, true);
        }

        $io->section('Importing Cookie Policy');

        // Create policy
        $policy = new CookiePolicy();
        $policy->setVersion($policyData['version']);
        $policy->setLastUpdated(new \DateTime($policyData['lastUpdated']));
        $policy->setExpirationDays($policyData['expirationDays']);
        $policy->setCookiePrefix($policyData['cookiePrefix']);
        $policy->setIsActive($input->getOption('activate'));

        $io->text(sprintf('Version: %s', $policyData['version']));
        $io->text(sprintf('Last Updated: %s', $policyData['lastUpdated']));
        $io->text(sprintf('Cookie Prefix: %s', $policyData['cookiePrefix']));

        // Import categories
        $io->section('Importing Categories');
        $position = 0;
        foreach ($policyData['categories'] as $categoryData) {
            $category = new CookieCategory();
            $category->setPolicy($policy);
            $category->setIdentifier($categoryData['id']);
            $category->setName($categoryData['name']);
            $category->setDescription($categoryData['description']);
            $category->setRequired($categoryData['required']);
            $category->setDefaultEnabled($categoryData['defaultEnabled']);
            $category->setPosition($position++);

            $io->text(sprintf('  - %s (%s)', $categoryData['name'], $categoryData['id']));

            // Import cookies
            $cookiePosition = 0;
            foreach ($categoryData['cookies'] as $cookieData) {
                $cookie = new Cookie();
                $cookie->setCategory($category);
                $cookie->setName($cookieData['name']);
                $cookie->setPurpose($cookieData['purpose']);
                $cookie->setProvider($cookieData['provider']);
                $cookie->setExpiry($cookieData['expiry']);
                $cookie->setPosition($cookiePosition++);

                // Handle script data
                if (isset($cookieData['script'])) {
                    if (isset($cookieData['script']['src'])) {
                        $cookie->setScriptSrc($cookieData['script']['src']);
                    }
                    if (isset($cookieData['script']['async'])) {
                        $cookie->setScriptAsync($cookieData['script']['async']);
                    }
                    if (isset($cookieData['script']['initCode'])) {
                        $cookie->setInitCode($cookieData['script']['initCode']);
                    }
                }

                $category->addCookie($cookie);
                $io->text(sprintf('    + Cookie: %s', $cookieData['name']));
            }

            $policy->addCategory($category);
        }

        // Import third-party services
        if (isset($policyData['thirdPartyServices'])) {
            $io->section('Importing Third-Party Services');
            foreach ($policyData['thirdPartyServices'] as $serviceData) {
                $service = new ThirdPartyService();
                $service->setPolicy($policy);
                $service->setIdentifier($serviceData['id']);
                $service->setName($serviceData['name']);
                $service->setCategory($serviceData['category']);
                $service->setDescription($serviceData['description']);
                $service->setPrivacyPolicyUrl($serviceData['privacyPolicy']);
                $service->setConfigKey($serviceData['configKey']);
                $service->setConfigValue($serviceData['configValue']);
                $service->setEnabled(true);

                $policy->addThirdPartyService($service);
                $io->text(sprintf('  - %s (%s)', $serviceData['name'], $serviceData['id']));
            }
        }

        // Deactivate other policies if activating this one
        if ($input->getOption('activate')) {
            $this->policyRepository->deactivateAll();
            $io->info('Deactivated all other policies');
        }

        // Save policy
        $this->policyRepository->save($policy, true);

        $io->success(sprintf(
            'Policy version %s imported successfully%s',
            $policyData['version'],
            $input->getOption('activate') ? ' and activated' : ''
        ));

        return Command::SUCCESS;
    }
}
