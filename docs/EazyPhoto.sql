/*
Created		05.05.2009
Modified		13.05.2013
Project		
Model		
Company		
Author		
Version		
Database		mySQL 5 
*/


Create table `vfsFiles` (
	`fileId` Int NOT NULL AUTO_INCREMENT,
	`folderId` Int NOT NULL,
	`title` Varchar(255) NOT NULL,
	`path` Varchar(255) NOT NULL,
	`params` Text,
	`isFavorite` Bool DEFAULT false,
	`mimeType` Varchar(255) NOT NULL,
	`fileSize` Int DEFAULT 0,
	`fileExists` Bool NOT NULL DEFAULT true,
	`statusId` Int NOT NULL,
	`createdAt` Timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 Primary Key (`fileId`)) ENGINE = InnoDB;

Create table `vfsFolders` (
	`folderId` Int NOT NULL AUTO_INCREMENT,
	`parentFolderId` Int,
	`title` Varchar(255) NOT NULL,
	`isFavorite` Bool DEFAULT false,
	`createdAt` Timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`statusId` Int NOT NULL,
	Index `AI_folderId` (`folderId`),
 Primary Key (`folderId`)) ENGINE = InnoDB;

Create table `vfsFoldersTree` (
	`objectId` Int NOT NULL,
	`parentId` Int,
	`path` Char(255),
	`rKey` Int,
	`lKey` Int,
	`level` Int,
 Primary Key (`objectId`)) ENGINE = InnoDB;

Create table `metaDetails` (
	`metaDetailId` Int NOT NULL AUTO_INCREMENT,
	`url` Varchar(255) NOT NULL,
	`pageTitle` Varchar(255),
	`metaKeywords` Varchar(1024),
	`metaDescription` Varchar(1024),
	`alt` Varchar(255),
	`isInheritable` Bool NOT NULL DEFAULT false,
	`statusId` Int NOT NULL,
	Index `AI_metaDetailId` (`metaDetailId`),
 Primary Key (`metaDetailId`)) ENGINE = InnoDB;

Create table `users` (
	`userId` Int NOT NULL AUTO_INCREMENT,
	`login` Varchar(64) NOT NULL,
	`password` Varchar(64) NOT NULL,
	`statusId` Int NOT NULL,
	Index `AI_userId` (`userId`),
 Primary Key (`userId`)) ENGINE = InnoDB;

Create table `siteParams` (
	`siteParamId` Int NOT NULL AUTO_INCREMENT,
	`alias` Varchar(32) NOT NULL,
	`value` Varchar(255) NOT NULL,
	`description` Varchar(255),
	`statusId` Int NOT NULL,
	Index `AI_siteParamId` (`siteParamId`),
 Primary Key (`siteParamId`)) ENGINE = InnoDB;

Create table `statuses` (
	`statusId` Int NOT NULL AUTO_INCREMENT,
	`title` Varchar(255) NOT NULL,
	`alias` Varchar(64) NOT NULL,
	UNIQUE (`alias`),
	Index `AI_statusId` (`statusId`),
 Primary Key (`statusId`)) ENGINE = InnoDB;

Create table `daemonLocks` (
	`daemonLockId` Int NOT NULL AUTO_INCREMENT,
	`title` Varchar(255) NOT NULL,
	`packageName` Varchar(255) NOT NULL,
	`methodName` Varchar(255) NOT NULL,
	`runAt` Timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`maxExecutionTime` Time NOT NULL DEFAULT '00:03:00',
	Index `AI_daemonLockId` (`daemonLockId`),
 Primary Key (`daemonLockId`)) ENGINE = InnoDB;

Create table `staticPages` (
	`staticPageId` Int NOT NULL AUTO_INCREMENT,
	`title` Varchar(255) NOT NULL,
	`url` Varchar(255) NOT NULL,
	`content` Text,
	`pageTitle` Varchar(255),
	`metaKeywords` Varchar(2048),
	`metaDescription` Varchar(2048),
	`orderNumber` Int,
	`parentStaticPageId` Int,
	`statusId` Int NOT NULL,
	Index `AI_staticPageId` (`staticPageId`),
 Primary Key (`staticPageId`)) ENGINE = InnoDB;

Create table `navigationTypes` (
	`navigationTypeId` Int NOT NULL AUTO_INCREMENT,
	`title` Varchar(255) NOT NULL,
	`alias` Varchar(32) NOT NULL,
	`statusId` Int NOT NULL,
	Index `AI_navigationTypeId` (`navigationTypeId`),
 Primary Key (`navigationTypeId`)) ENGINE = InnoDB;

Create table `navigations` (
	`navigationId` Int NOT NULL AUTO_INCREMENT,
	`navigationTypeId` Int NOT NULL,
	`title` Varchar(255),
	`orderNumber` Int NOT NULL DEFAULT 1,
	`staticPageId` Int,
	`url` Varchar(255),
	`statusId` Int NOT NULL,
	Index `AI_navigationId` (`navigationId`),
 Primary Key (`navigationId`)) ENGINE = InnoDB;

Create table `albums` (
	`albumId` Serial NOT NULL,
	`title` Varchar(255) NOT NULL,
	`description` Varchar(4096),
	`alias` Varchar(255) NOT NULL,
	`isPrivate` Bool NOT NULL DEFAULT false,
	`startDate` Date NOT NULL,
	`endDate` Date,
	`orderNumber` Int,
	`folderPath` Varchar(255) NOT NULL,
	`roSecret` Varchar(1024) NOT NULL,
	`roSecretHd` Varchar(1024),
	`deleteOriginalsAfter` Int,
	`isDescSort` Bool NOT NULL DEFAULT false,
	`createdAt` Timestamp NOT NULL DEFAULT now(),
	`modifiedAt` Timestamp,
	`metaInfo` Text NOT NULL,
	`userId` Int NOT NULL,
	`statusId` Int NOT NULL,
 Primary Key (`albumId`)) ENGINE = InnoDB;

Create table `photos` (
	`photoId` Serial NOT NULL,
	`albumId` Bigint UNSIGNED NOT NULL,
	`originalName` Varchar(255) NOT NULL,
	`filename` Varchar(255) NOT NULL,
	`fileSize` Int NOT NULL,
	`fileSizeHd` Int NOT NULL,
	`orderNumber` Int,
	`afterText` Text,
	`title` Varchar(255),
	`exif` Text,
	`createdAt` Timestamp NOT NULL DEFAULT now(),
	`photoDate` Timestamp,
	`statusId` Int NOT NULL,
 Primary Key (`photoId`)) ENGINE = InnoDB;


Create Index `IX_vfsFoldersTreeTreePath` ON `vfsFoldersTree` (`path`);
Create Index `IX_vfsFoldersTreeTreeRKey` ON `vfsFoldersTree` (`rKey`);
Create Index `IX_vfsFoldersTreeTreeLKey` ON `vfsFoldersTree` (`lKey`);
Create UNIQUE Index `IX_daemonLock` Using BTREE ON `daemonLocks` (`title`,`packageName`,`methodName`);


Alter table `vfsFolders` add Constraint `FK_vfsFoldersFolderId` Foreign Key (`parentFolderId`) references `vfsFolders` (`folderId`) on delete  restrict on update  restrict;
Alter table `vfsFiles` add Constraint `FK_vfsFilesFolderId` Foreign Key (`folderId`) references `vfsFolders` (`folderId`) on delete  restrict on update  restrict;
Alter table `vfsFoldersTree` add Constraint `FK_vfsFoldersTreeFolderId` Foreign Key (`objectId`) references `vfsFolders` (`folderId`) on delete  restrict on update  restrict;
Alter table `vfsFoldersTree` add Constraint `FK_vfsFoldersTreeParentId` Foreign Key (`parentId`) references `vfsFolders` (`folderId`) on delete  restrict on update  restrict;
Alter table `albums` add Constraint `FK_albums_userId` Foreign Key (`userId`) references `users` (`userId`) on delete  restrict on update  restrict;
Alter table `users` add Constraint `FK_usersStatusId` Foreign Key (`statusId`) references `statuses` (`statusId`) on delete  restrict on update  restrict;
Alter table `metaDetails` add Constraint `FK_metaDetailsStatusId` Foreign Key (`statusId`) references `statuses` (`statusId`) on delete  restrict on update  restrict;
Alter table `siteParams` add Constraint `FK_siteParamsStatusId` Foreign Key (`statusId`) references `statuses` (`statusId`) on delete  restrict on update  restrict;
Alter table `vfsFiles` add Constraint `FK_vfsFoldersStatusId` Foreign Key (`statusId`) references `statuses` (`statusId`) on delete  restrict on update  restrict;
Alter table `vfsFolders` add Constraint `FK_vfsFilesStatusId` Foreign Key (`statusId`) references `statuses` (`statusId`) on delete  restrict on update  restrict;
Alter table `staticPages` add Constraint `FK_staticPagesStatusId` Foreign Key (`statusId`) references `statuses` (`statusId`) on delete  restrict on update  restrict;
Alter table `navigationTypes` add Constraint `FK_navigationTypesStatusId` Foreign Key (`statusId`) references `statuses` (`statusId`) on delete  restrict on update  restrict;
Alter table `navigations` add Constraint `FK_navigationsStatusId` Foreign Key (`statusId`) references `statuses` (`statusId`) on delete  restrict on update  restrict;
Alter table `albums` add Constraint `FK_albums_statusId` Foreign Key (`statusId`) references `statuses` (`statusId`) on delete  restrict on update  restrict;
Alter table `photos` add Constraint `FK_photos_statusId` Foreign Key (`statusId`) references `statuses` (`statusId`) on delete  restrict on update  restrict;
Alter table `staticPages` add Constraint `FK_staticPagesParentStaticPageId` Foreign Key (`parentStaticPageId`) references `staticPages` (`staticPageId`) on delete  restrict on update  restrict;
Alter table `navigations` add Constraint `FK_navigationsStaticPageId` Foreign Key (`staticPageId`) references `staticPages` (`staticPageId`) on delete  restrict on update  restrict;
Alter table `navigations` add Constraint `FK_navigationsNavigationTypeId` Foreign Key (`navigationTypeId`) references `navigationTypes` (`navigationTypeId`) on delete  restrict on update  restrict;
Alter table `photos` add Constraint `FK_photos_albumId` Foreign Key (`albumId`) references `albums` (`albumId`) on delete  restrict on update  restrict;


/* Users permissions */


