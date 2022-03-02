<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220302193515 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE logfile ADD year INT NOT NULL, ADD month INT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AE1106315E237E06 ON logfile (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE httprequest CHANGE host host VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE request_method request_method VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE request_url request_url VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE request_protocol request_protocol VARCHAR(10) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE request_protocol_version request_protocol_version VARCHAR(10) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE response_code response_code VARCHAR(3) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE logfile_name logfile_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('DROP INDEX UNIQ_AE1106315E237E06 ON logfile');
        $this->addSql('ALTER TABLE logfile DROP year, DROP month, CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE filename filename VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
