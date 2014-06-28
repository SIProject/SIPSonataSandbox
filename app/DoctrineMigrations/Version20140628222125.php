<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140628222125 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE cms_area (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, title VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE cms_page_type_container (id INT AUTO_INCREMENT NOT NULL, area_id INT DEFAULT NULL, settings LONGTEXT NOT NULL COMMENT '(DC2Type:array)', title VARCHAR(255) DEFAULT NULL, usageService VARCHAR(255) DEFAULT NULL, usageType VARCHAR(255) DEFAULT NULL, is_main TINYINT(1) DEFAULT NULL, INDEX IDX_51F5BC11BD0F409C (area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE cms_domain (id INT AUTO_INCREMENT NOT NULL, site_id INT DEFAULT NULL, pattern VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_955F0173F6BD1646 (site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE cms_page (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, page_type_id INT DEFAULT NULL, site_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, lft INT NOT NULL, rgt INT NOT NULL, root INT DEFAULT NULL, lvl INT NOT NULL, menuEnabled TINYINT(1) DEFAULT NULL, parameters LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)', toFirstChild TINYINT(1) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_D39C1B5DF47645AE (url), INDEX IDX_D39C1B5D727ACA70 (parent_id), INDEX IDX_D39C1B5D3F2C6706 (page_type_id), INDEX IDX_D39C1B5DF6BD1646 (site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE cms_page_container (id INT AUTO_INCREMENT NOT NULL, page_id INT DEFAULT NULL, container_id INT DEFAULT NULL, settings LONGTEXT NOT NULL COMMENT '(DC2Type:array)', usageService VARCHAR(255) DEFAULT NULL, usageType VARCHAR(255) DEFAULT NULL, INDEX IDX_A452434C4663E4 (page_id), INDEX IDX_A452434BC21F742 (container_id), UNIQUE INDEX page_container_unique_idx (page_id, container_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE cms_page_type (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, layout VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE cms_container_page_type (pagetype_id INT NOT NULL, container_id INT NOT NULL, INDEX IDX_371381B453A99D0E (pagetype_id), INDEX IDX_371381B4BC21F742 (container_id), PRIMARY KEY(pagetype_id, container_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE cms_site (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, parameters LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)', logo VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE cms_favorites (id INT AUTO_INCREMENT NOT NULL, serviceId VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE cms_statistic (id INT AUTO_INCREMENT NOT NULL, counterId VARCHAR(255) NOT NULL, gaCounterId VARCHAR(255) DEFAULT NULL, providerClass VARCHAR(255) NOT NULL, appId VARCHAR(255) NOT NULL, appSecret VARCHAR(255) NOT NULL, userLogin VARCHAR(255) NOT NULL, userPassword VARCHAR(255) NOT NULL, isActive TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE cms_page_type_container ADD CONSTRAINT FK_51F5BC11BD0F409C FOREIGN KEY (area_id) REFERENCES cms_area (id)");
        $this->addSql("ALTER TABLE cms_domain ADD CONSTRAINT FK_955F0173F6BD1646 FOREIGN KEY (site_id) REFERENCES cms_site (id)");
        $this->addSql("ALTER TABLE cms_page ADD CONSTRAINT FK_D39C1B5D727ACA70 FOREIGN KEY (parent_id) REFERENCES cms_page (id)");
        $this->addSql("ALTER TABLE cms_page ADD CONSTRAINT FK_D39C1B5D3F2C6706 FOREIGN KEY (page_type_id) REFERENCES cms_page_type (id)");
        $this->addSql("ALTER TABLE cms_page ADD CONSTRAINT FK_D39C1B5DF6BD1646 FOREIGN KEY (site_id) REFERENCES cms_site (id)");
        $this->addSql("ALTER TABLE cms_page_container ADD CONSTRAINT FK_A452434C4663E4 FOREIGN KEY (page_id) REFERENCES cms_page (id)");
        $this->addSql("ALTER TABLE cms_page_container ADD CONSTRAINT FK_A452434BC21F742 FOREIGN KEY (container_id) REFERENCES cms_page_type_container (id)");
        $this->addSql("ALTER TABLE cms_container_page_type ADD CONSTRAINT FK_371381B453A99D0E FOREIGN KEY (pagetype_id) REFERENCES cms_page_type (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE cms_container_page_type ADD CONSTRAINT FK_371381B4BC21F742 FOREIGN KEY (container_id) REFERENCES cms_page_type_container (id) ON DELETE CASCADE");
        $this->addSql("DROP TABLE sip_real_estate");
        $this->addSql("ALTER TABLE sip_user_user CHANGE biography biography VARCHAR(1000) DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE cms_page_type_container DROP FOREIGN KEY FK_51F5BC11BD0F409C");
        $this->addSql("ALTER TABLE cms_page_container DROP FOREIGN KEY FK_A452434BC21F742");
        $this->addSql("ALTER TABLE cms_container_page_type DROP FOREIGN KEY FK_371381B4BC21F742");
        $this->addSql("ALTER TABLE cms_page DROP FOREIGN KEY FK_D39C1B5D727ACA70");
        $this->addSql("ALTER TABLE cms_page_container DROP FOREIGN KEY FK_A452434C4663E4");
        $this->addSql("ALTER TABLE cms_page DROP FOREIGN KEY FK_D39C1B5D3F2C6706");
        $this->addSql("ALTER TABLE cms_container_page_type DROP FOREIGN KEY FK_371381B453A99D0E");
        $this->addSql("ALTER TABLE cms_domain DROP FOREIGN KEY FK_955F0173F6BD1646");
        $this->addSql("ALTER TABLE cms_page DROP FOREIGN KEY FK_D39C1B5DF6BD1646");
        $this->addSql("CREATE TABLE sip_real_estate (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, price VARCHAR(255) NOT NULL, currency INT DEFAULT NULL, home_area VARCHAR(255) DEFAULT NULL, piece_area VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, distance VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, link VARCHAR(255) NOT NULL, date_upload DATETIME NOT NULL, date_update DATETIME DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("DROP TABLE cms_area");
        $this->addSql("DROP TABLE cms_page_type_container");
        $this->addSql("DROP TABLE cms_domain");
        $this->addSql("DROP TABLE cms_page");
        $this->addSql("DROP TABLE cms_page_container");
        $this->addSql("DROP TABLE cms_page_type");
        $this->addSql("DROP TABLE cms_container_page_type");
        $this->addSql("DROP TABLE cms_site");
        $this->addSql("DROP TABLE cms_favorites");
        $this->addSql("DROP TABLE cms_statistic");
        $this->addSql("ALTER TABLE sip_user_user CHANGE biography biography VARCHAR(255) DEFAULT NULL");
    }
}
