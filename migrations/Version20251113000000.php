<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251113000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Masilia Consent Bundle tables';
    }

    public function up(Schema $schema): void
    {
        // Cookie Policy table
        $this->addSql('CREATE TABLE masilia_cookie_policy (
            id INT AUTO_INCREMENT NOT NULL,
            version VARCHAR(20) NOT NULL,
            last_updated DATETIME NOT NULL,
            expiration_days INT NOT NULL,
            cookie_prefix VARCHAR(50) NOT NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY(id),
            INDEX idx_policy_active (is_active),
            INDEX idx_policy_version (version)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Cookie Category table
        $this->addSql('CREATE TABLE masilia_cookie_category (
            id INT AUTO_INCREMENT NOT NULL,
            policy_id INT NOT NULL,
            identifier VARCHAR(50) NOT NULL,
            name VARCHAR(100) NOT NULL,
            description TEXT NOT NULL,
            required TINYINT(1) NOT NULL DEFAULT 0,
            default_enabled TINYINT(1) NOT NULL DEFAULT 0,
            position INT NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY(id),
            INDEX IDX_CATEGORY_POLICY (policy_id),
            INDEX idx_category_identifier (identifier),
            UNIQUE INDEX unique_policy_identifier (policy_id, identifier),
            CONSTRAINT FK_CATEGORY_POLICY FOREIGN KEY (policy_id) 
                REFERENCES masilia_cookie_policy (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Cookie table
        $this->addSql('CREATE TABLE masilia_cookie (
            id INT AUTO_INCREMENT NOT NULL,
            category_id INT NOT NULL,
            name VARCHAR(100) NOT NULL,
            purpose TEXT NOT NULL,
            provider VARCHAR(100) NOT NULL,
            expiry VARCHAR(50) NOT NULL,
            script_src VARCHAR(500) DEFAULT NULL,
            script_async TINYINT(1) NOT NULL DEFAULT 0,
            init_code TEXT DEFAULT NULL,
            position INT NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY(id),
            INDEX IDX_COOKIE_CATEGORY (category_id),
            INDEX idx_cookie_name (name),
            CONSTRAINT FK_COOKIE_CATEGORY FOREIGN KEY (category_id) 
                REFERENCES masilia_cookie_category (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Third Party Service table
        $this->addSql('CREATE TABLE masilia_third_party_service (
            id INT AUTO_INCREMENT NOT NULL,
            policy_id INT NOT NULL,
            identifier VARCHAR(50) NOT NULL,
            name VARCHAR(100) NOT NULL,
            category VARCHAR(50) NOT NULL,
            description TEXT NOT NULL,
            privacy_policy_url VARCHAR(500) NOT NULL,
            config_key VARCHAR(100) NOT NULL,
            config_value VARCHAR(255) NOT NULL,
            enabled TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY(id),
            INDEX IDX_SERVICE_POLICY (policy_id),
            INDEX idx_service_category (category),
            UNIQUE INDEX unique_policy_identifier (policy_id, identifier),
            CONSTRAINT FK_SERVICE_POLICY FOREIGN KEY (policy_id) 
                REFERENCES masilia_cookie_policy (id) ON DELETE CASCADE
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Consent Log table
        $this->addSql('CREATE TABLE masilia_consent_log (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT DEFAULT NULL,
            session_id VARCHAR(255) NOT NULL,
            policy_version VARCHAR(20) NOT NULL,
            preferences JSON NOT NULL,
            ip_address VARCHAR(45) DEFAULT NULL,
            user_agent VARCHAR(500) DEFAULT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY(id),
            INDEX idx_session_id (session_id),
            INDEX idx_user_id (user_id),
            INDEX idx_created_at (created_at)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS masilia_consent_log');
        $this->addSql('DROP TABLE IF EXISTS masilia_third_party_service');
        $this->addSql('DROP TABLE IF EXISTS masilia_cookie');
        $this->addSql('DROP TABLE IF EXISTS masilia_cookie_category');
        $this->addSql('DROP TABLE IF EXISTS masilia_cookie_policy');
    }
}
