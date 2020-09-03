<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191216131100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates `user` table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE `user` (
                id CHAR(36) NOT NULL PRIMARY KEY,
                name VARCHAR(50) NOT NULL,
                email VARCHAR(50) NOT NULL,
                password VARCHAR(100) NOT NULL,
                roles VARCHAR(100) NOT NULL,
                token VARCHAR(100) DEFAULT NULL,
                active TINYINT(1) UNSIGNED DEFAULT 0,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX IDX_user_email (email),
                UNIQUE U_user_email (email)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE = InnoDB'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE `user`');
    }
}
