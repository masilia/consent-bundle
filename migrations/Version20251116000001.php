<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add preset_type field to third_party_service table
 */
final class Version20251116000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add preset_type field to masilia_third_party_service table for cookie presets';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE masilia_third_party_service ADD preset_type VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE masilia_third_party_service DROP preset_type');
    }
}
