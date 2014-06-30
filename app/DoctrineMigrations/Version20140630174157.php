<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140630174157 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("CREATE TABLE content_news (id INT AUTO_INCREMENT NOT NULL, stream_id INT DEFAULT NULL, date DATETIME NOT NULL, title VARCHAR(255) NOT NULL, announce LONGTEXT DEFAULT NULL, body LONGTEXT DEFAULT NULL, INDEX IDX_D0B17495D0ED463E (stream_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE content_text (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, body LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sip_user_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT '(DC2Type:array)', UNIQUE INDEX UNIQ_6D5740125E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sip_user_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT '(DC2Type:array)', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, date_of_birth DATETIME DEFAULT NULL, firstname VARCHAR(64) DEFAULT NULL, lastname VARCHAR(64) DEFAULT NULL, website VARCHAR(64) DEFAULT NULL, biography VARCHAR(1000) DEFAULT NULL, gender VARCHAR(1) DEFAULT NULL, locale VARCHAR(8) DEFAULT NULL, timezone VARCHAR(64) DEFAULT NULL, phone VARCHAR(64) DEFAULT NULL, facebook_uid VARCHAR(255) DEFAULT NULL, facebook_name VARCHAR(255) DEFAULT NULL, facebook_data LONGTEXT DEFAULT NULL COMMENT '(DC2Type:json)', twitter_uid VARCHAR(255) DEFAULT NULL, twitter_name VARCHAR(255) DEFAULT NULL, twitter_data LONGTEXT DEFAULT NULL COMMENT '(DC2Type:json)', gplus_uid VARCHAR(255) DEFAULT NULL, gplus_name VARCHAR(255) DEFAULT NULL, gplus_data LONGTEXT DEFAULT NULL COMMENT '(DC2Type:json)', token VARCHAR(255) DEFAULT NULL, two_step_code VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_1A97014992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_1A970149A0D96FBF (email_canonical), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE sip_user_user_group (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_C3E925F6A76ED395 (user_id), INDEX IDX_C3E925F6FE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE cms_area (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, title VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE cms_page_type_container (id INT AUTO_INCREMENT NOT NULL, area_id INT DEFAULT NULL, settings LONGTEXT NOT NULL COMMENT '(DC2Type:array)', title VARCHAR(255) DEFAULT NULL, usageService VARCHAR(255) DEFAULT NULL, usageType VARCHAR(255) DEFAULT NULL, is_main TINYINT(1) DEFAULT NULL, INDEX IDX_51F5BC11BD0F409C (area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE cms_domain (id INT AUTO_INCREMENT NOT NULL, site_id INT DEFAULT NULL, pattern VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_955F0173F6BD1646 (site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE cms_page (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, page_type_id INT DEFAULT NULL, site_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, lft INT NOT NULL, rgt INT NOT NULL, root INT DEFAULT NULL, lvl INT NOT NULL, menuEnabled TINYINT(1) DEFAULT NULL, parameters LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)', toFirstChild TINYINT(1) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_D39C1B5DF47645AE (url), INDEX IDX_D39C1B5D727ACA70 (parent_id), INDEX IDX_D39C1B5D3F2C6706 (page_type_id), INDEX IDX_D39C1B5DF6BD1646 (site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE cms_page_container (id INT AUTO_INCREMENT NOT NULL, page_id INT DEFAULT NULL, container_id INT DEFAULT NULL, settings LONGTEXT NOT NULL COMMENT '(DC2Type:array)', usageService VARCHAR(255) DEFAULT NULL, usageType VARCHAR(255) DEFAULT NULL, INDEX IDX_A452434C4663E4 (page_id), INDEX IDX_A452434BC21F742 (container_id), UNIQUE INDEX page_container_unique_idx (page_id, container_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE cms_page_type (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, layout VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE cms_container_page_type (pagetype_id INT NOT NULL, container_id INT NOT NULL, INDEX IDX_371381B453A99D0E (pagetype_id), INDEX IDX_371381B4BC21F742 (container_id), PRIMARY KEY(pagetype_id, container_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE cms_site (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, parameters LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)', logo VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE content_log_entries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)', username VARCHAR(255) DEFAULT NULL, INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE cms_entity (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, class VARCHAR(255) NOT NULL, service VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE cms_stream (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, sys_name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE stream_entity (stream_id INT NOT NULL, entity_id INT NOT NULL, INDEX IDX_D407945DD0ED463E (stream_id), INDEX IDX_D407945D81257D5D (entity_id), PRIMARY KEY(stream_id, entity_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acl_classes (id INT UNSIGNED AUTO_INCREMENT NOT NULL, class_type VARCHAR(200) NOT NULL, UNIQUE INDEX UNIQ_69DD750638A36066 (class_type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acl_security_identities (id INT UNSIGNED AUTO_INCREMENT NOT NULL, identifier VARCHAR(200) NOT NULL, username TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8835EE78772E836AF85E0677 (identifier, username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acl_object_identities (id INT UNSIGNED AUTO_INCREMENT NOT NULL, parent_object_identity_id INT UNSIGNED DEFAULT NULL, class_id INT UNSIGNED NOT NULL, object_identifier VARCHAR(100) NOT NULL, entries_inheriting TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_9407E5494B12AD6EA000B10 (object_identifier, class_id), INDEX IDX_9407E54977FA751A (parent_object_identity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acl_object_identity_ancestors (object_identity_id INT UNSIGNED NOT NULL, ancestor_id INT UNSIGNED NOT NULL, INDEX IDX_825DE2993D9AB4A6 (object_identity_id), INDEX IDX_825DE299C671CEA1 (ancestor_id), PRIMARY KEY(object_identity_id, ancestor_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE acl_entries (id INT UNSIGNED AUTO_INCREMENT NOT NULL, class_id INT UNSIGNED NOT NULL, object_identity_id INT UNSIGNED DEFAULT NULL, security_identity_id INT UNSIGNED NOT NULL, field_name VARCHAR(50) DEFAULT NULL, ace_order SMALLINT UNSIGNED NOT NULL, mask INT NOT NULL, granting TINYINT(1) NOT NULL, granting_strategy VARCHAR(30) NOT NULL, audit_success TINYINT(1) NOT NULL, audit_failure TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_46C8B806EA000B103D9AB4A64DEF17BCE4289BF4 (class_id, object_identity_id, field_name, ace_order), INDEX IDX_46C8B806EA000B103D9AB4A6DF9183C9 (class_id, object_identity_id, security_identity_id), INDEX IDX_46C8B806EA000B10 (class_id), INDEX IDX_46C8B8063D9AB4A6 (object_identity_id), INDEX IDX_46C8B806DF9183C9 (security_identity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE content_news ADD CONSTRAINT FK_D0B17495D0ED463E FOREIGN KEY (stream_id) REFERENCES cms_stream (id)");
        $this->addSql("ALTER TABLE sip_user_user_group ADD CONSTRAINT FK_C3E925F6A76ED395 FOREIGN KEY (user_id) REFERENCES sip_user_user (id)");
        $this->addSql("ALTER TABLE sip_user_user_group ADD CONSTRAINT FK_C3E925F6FE54D947 FOREIGN KEY (group_id) REFERENCES sip_user_group (id)");
        $this->addSql("ALTER TABLE cms_page_type_container ADD CONSTRAINT FK_51F5BC11BD0F409C FOREIGN KEY (area_id) REFERENCES cms_area (id)");
        $this->addSql("ALTER TABLE cms_domain ADD CONSTRAINT FK_955F0173F6BD1646 FOREIGN KEY (site_id) REFERENCES cms_site (id)");
        $this->addSql("ALTER TABLE cms_page ADD CONSTRAINT FK_D39C1B5D727ACA70 FOREIGN KEY (parent_id) REFERENCES cms_page (id)");
        $this->addSql("ALTER TABLE cms_page ADD CONSTRAINT FK_D39C1B5D3F2C6706 FOREIGN KEY (page_type_id) REFERENCES cms_page_type (id)");
        $this->addSql("ALTER TABLE cms_page ADD CONSTRAINT FK_D39C1B5DF6BD1646 FOREIGN KEY (site_id) REFERENCES cms_site (id)");
        $this->addSql("ALTER TABLE cms_page_container ADD CONSTRAINT FK_A452434C4663E4 FOREIGN KEY (page_id) REFERENCES cms_page (id)");
        $this->addSql("ALTER TABLE cms_page_container ADD CONSTRAINT FK_A452434BC21F742 FOREIGN KEY (container_id) REFERENCES cms_page_type_container (id)");
        $this->addSql("ALTER TABLE cms_container_page_type ADD CONSTRAINT FK_371381B453A99D0E FOREIGN KEY (pagetype_id) REFERENCES cms_page_type (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE cms_container_page_type ADD CONSTRAINT FK_371381B4BC21F742 FOREIGN KEY (container_id) REFERENCES cms_page_type_container (id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE stream_entity ADD CONSTRAINT FK_D407945DD0ED463E FOREIGN KEY (stream_id) REFERENCES cms_stream (id)");
        $this->addSql("ALTER TABLE stream_entity ADD CONSTRAINT FK_D407945D81257D5D FOREIGN KEY (entity_id) REFERENCES cms_entity (id)");
        $this->addSql("ALTER TABLE acl_object_identities ADD CONSTRAINT FK_9407E54977FA751A FOREIGN KEY (parent_object_identity_id) REFERENCES acl_object_identities (id)");
        $this->addSql("ALTER TABLE acl_object_identity_ancestors ADD CONSTRAINT FK_825DE2993D9AB4A6 FOREIGN KEY (object_identity_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acl_object_identity_ancestors ADD CONSTRAINT FK_825DE299C671CEA1 FOREIGN KEY (ancestor_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B806EA000B10 FOREIGN KEY (class_id) REFERENCES acl_classes (id) ON UPDATE CASCADE ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B8063D9AB4A6 FOREIGN KEY (object_identity_id) REFERENCES acl_object_identities (id) ON UPDATE CASCADE ON DELETE CASCADE");
        $this->addSql("ALTER TABLE acl_entries ADD CONSTRAINT FK_46C8B806DF9183C9 FOREIGN KEY (security_identity_id) REFERENCES acl_security_identities (id) ON UPDATE CASCADE ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");
        
        $this->addSql("ALTER TABLE sip_user_user_group DROP FOREIGN KEY FK_C3E925F6FE54D947");
        $this->addSql("ALTER TABLE sip_user_user_group DROP FOREIGN KEY FK_C3E925F6A76ED395");
        $this->addSql("ALTER TABLE cms_page_type_container DROP FOREIGN KEY FK_51F5BC11BD0F409C");
        $this->addSql("ALTER TABLE cms_page_container DROP FOREIGN KEY FK_A452434BC21F742");
        $this->addSql("ALTER TABLE cms_container_page_type DROP FOREIGN KEY FK_371381B4BC21F742");
        $this->addSql("ALTER TABLE cms_page DROP FOREIGN KEY FK_D39C1B5D727ACA70");
        $this->addSql("ALTER TABLE cms_page_container DROP FOREIGN KEY FK_A452434C4663E4");
        $this->addSql("ALTER TABLE cms_page DROP FOREIGN KEY FK_D39C1B5D3F2C6706");
        $this->addSql("ALTER TABLE cms_container_page_type DROP FOREIGN KEY FK_371381B453A99D0E");
        $this->addSql("ALTER TABLE cms_domain DROP FOREIGN KEY FK_955F0173F6BD1646");
        $this->addSql("ALTER TABLE cms_page DROP FOREIGN KEY FK_D39C1B5DF6BD1646");
        $this->addSql("ALTER TABLE stream_entity DROP FOREIGN KEY FK_D407945D81257D5D");
        $this->addSql("ALTER TABLE content_news DROP FOREIGN KEY FK_D0B17495D0ED463E");
        $this->addSql("ALTER TABLE stream_entity DROP FOREIGN KEY FK_D407945DD0ED463E");
        $this->addSql("ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B806EA000B10");
        $this->addSql("ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B806DF9183C9");
        $this->addSql("ALTER TABLE acl_object_identities DROP FOREIGN KEY FK_9407E54977FA751A");
        $this->addSql("ALTER TABLE acl_object_identity_ancestors DROP FOREIGN KEY FK_825DE2993D9AB4A6");
        $this->addSql("ALTER TABLE acl_object_identity_ancestors DROP FOREIGN KEY FK_825DE299C671CEA1");
        $this->addSql("ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B8063D9AB4A6");
        $this->addSql("DROP TABLE content_news");
        $this->addSql("DROP TABLE content_text");
        $this->addSql("DROP TABLE sip_user_group");
        $this->addSql("DROP TABLE sip_user_user");
        $this->addSql("DROP TABLE sip_user_user_group");
        $this->addSql("DROP TABLE cms_area");
        $this->addSql("DROP TABLE cms_page_type_container");
        $this->addSql("DROP TABLE cms_domain");
        $this->addSql("DROP TABLE cms_page");
        $this->addSql("DROP TABLE cms_page_container");
        $this->addSql("DROP TABLE cms_page_type");
        $this->addSql("DROP TABLE cms_container_page_type");
        $this->addSql("DROP TABLE cms_site");
        $this->addSql("DROP TABLE content_log_entries");
        $this->addSql("DROP TABLE cms_entity");
        $this->addSql("DROP TABLE cms_stream");
        $this->addSql("DROP TABLE stream_entity");
        $this->addSql("DROP TABLE acl_classes");
        $this->addSql("DROP TABLE acl_security_identities");
        $this->addSql("DROP TABLE acl_object_identities");
        $this->addSql("DROP TABLE acl_object_identity_ancestors");
        $this->addSql("DROP TABLE acl_entries");
    }
}
