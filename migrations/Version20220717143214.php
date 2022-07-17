<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220717143214 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE aircrafts (id INT AUTO_INCREMENT NOT NULL, aircraft_name VARCHAR(100) NOT NULL, aircraft_type ENUM("AIRLINER","PRIVATE") DEFAULT "AIRLINER" NOT NULL, aircraft_capacity INT DEFAULT NULL, aircraft_call_sign VARCHAR(20) NOT NULL, aircraft_state ENUM("PARKED","TAKE-OFF","AIRBORNE","APPROACHED","LANDED") DEFAULT "PARKED" NOT NULL, UNIQUE INDEX UNIQ_59AF8E008A91C3FF (aircraft_name), UNIQUE INDEX UNIQ_59AF8E00214D78AC (aircraft_call_sign), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE api_token (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, token VARCHAR(255) NOT NULL, INDEX IDX_7BA2F5EBA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE intents (id INT AUTO_INCREMENT NOT NULL, aircraft_id INT NOT NULL, state ENUM("TAKE-OFF","APPROACH","AIRBORNE") NOT NULL, status ENUM("NEW","ACCEPTED","REJECTED") DEFAULT "NEW" NOT NULL, created DATETIME NOT NULL, updated DATETIME DEFAULT NULL, INDEX IDX_71008302BBF16652 (aircraft_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE locations (id INT AUTO_INCREMENT NOT NULL, aircraft_id INT NOT NULL, type ENUM("AIRLINER","PRIVATE") NOT NULL, latitude VARCHAR(20) NOT NULL, longitude VARCHAR(20) NOT NULL, altitude INT NOT NULL, heading INT NOT NULL, created DATETIME NOT NULL, INDEX IDX_17E64ABABBF16652 (aircraft_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, callsign VARCHAR(20) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D6498B4924EF (callsign), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE weather (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, temperature DOUBLE PRECISION NOT NULL, visibility DOUBLE PRECISION NOT NULL, wind JSON DEFAULT NULL, last_update DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE api_token ADD CONSTRAINT FK_7BA2F5EBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE intents ADD CONSTRAINT FK_71008302BBF16652 FOREIGN KEY (aircraft_id) REFERENCES aircrafts (id)');
        $this->addSql('ALTER TABLE locations ADD CONSTRAINT FK_17E64ABABBF16652 FOREIGN KEY (aircraft_id) REFERENCES aircrafts (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE intents DROP FOREIGN KEY FK_71008302BBF16652');
        $this->addSql('ALTER TABLE locations DROP FOREIGN KEY FK_17E64ABABBF16652');
        $this->addSql('ALTER TABLE api_token DROP FOREIGN KEY FK_7BA2F5EBA76ED395');
        $this->addSql('DROP TABLE aircrafts');
        $this->addSql('DROP TABLE api_token');
        $this->addSql('DROP TABLE intents');
        $this->addSql('DROP TABLE locations');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE weather');
    }
}
