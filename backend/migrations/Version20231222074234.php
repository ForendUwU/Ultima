<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231222074234 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game (id INT NOT NULL AUTO_INCREMENT, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, published_at TIMESTAMP(0) NOT NULL, PRIMARY KEY(id))');
        $this->addSql("ALTER TABLE `game` MODIFY COLUMN `published_at` DATETIME COMMENT '(DC2Type:datetime_immutable)'");
        $this->addSql('CREATE TABLE purchased_game (id INT NOT NULL AUTO_INCREMENT, user_id INT NOT NULL, game_id INT NOT NULL, hours_of_playing DOUBLE PRECISION NOT NULL, bought_at TIMESTAMP(0) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C47835D3A76ED395 ON purchased_game (user_id)');
        $this->addSql('CREATE INDEX IDX_C47835D3E48FD905 ON purchased_game (game_id)');
        $this->addSql("ALTER TABLE `purchased_game` MODIFY COLUMN `bought_at` DATETIME COMMENT '(DC2Type:datetime_immutable)'");
        $this->addSql('CREATE TABLE `user` (
                           id INT NOT NULL AUTO_INCREMENT,
                           login VARCHAR(180) NOT NULL,
                           roles JSON NOT NULL,
                           password VARCHAR(255) NOT NULL,
                           nickname VARCHAR(255) NOT NULL,
                           balance DOUBLE NOT NULL,
                           first_name VARCHAR(255) DEFAULT NULL,
                           last_name VARCHAR(255) DEFAULT NULL,
                           created_at TIMESTAMP NOT NULL,
                           email VARCHAR(255) NOT NULL,
                           PRIMARY KEY (id)
                           );
                     ');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649AA08CB10 ON `user` (login);');
        $this->addSql("ALTER TABLE `user` MODIFY COLUMN `created_at` DATETIME COMMENT '(DC2Type:datetime_immutable)'");
        $this->addSql('ALTER TABLE purchased_game ADD CONSTRAINT FK_C47835D3A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id);');
        $this->addSql('ALTER TABLE purchased_game ADD CONSTRAINT FK_C47835D3E48FD905 FOREIGN KEY (game_id) REFERENCES `game` (id);');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE game_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE purchased_game_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('ALTER TABLE purchased_game DROP CONSTRAINT FK_C47835D3A76ED395');
        $this->addSql('ALTER TABLE purchased_game DROP CONSTRAINT FK_C47835D3E48FD905');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE purchased_game');
        $this->addSql('DROP TABLE "user"');
    }
}
