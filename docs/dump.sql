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


CREATE OR REPLACE VIEW `getStatuses` AS
SELECT `statuses`.`statusId`
	, `statuses`.`title`
	, `statuses`.`alias`
 FROM `statuses`
ORDER BY `statusId`;

CREATE OR REPLACE VIEW `getDaemonLocks` AS
SELECT `daemonLockId`
	, `title`
	, `packageName`
	, `methodName`
	, `runAt`
	, `maxExecutionTime`
	, ( CURRENT_TIMESTAMP - `runAt` < `maxExecutionTime` ) as `isActive`
 FROM `daemonLocks`;

CREATE OR REPLACE VIEW `getUsers` AS
SELECT `users`.`userId`
	, `users`.`login`
	, `users`.`password`
	, `users`.`statusId`
 FROM `users`
	WHERE `users`.`statusId` != 3
ORDER BY `userId`;
CREATE OR REPLACE VIEW `getVfsFiles` AS
SELECT `vfsFiles`.`fileId`
	, `vfsFiles`.`folderId`
	, `vfsFiles`.`title`
	, `vfsFiles`.`path`
	, `vfsFiles`.`params`
	, `vfsFiles`.`isFavorite`
	, `vfsFiles`.`mimeType`
	, `vfsFiles`.`fileSize`
	, `vfsFiles`.`fileExists`
	, `vfsFiles`.`statusId`
	, `vfsFiles`.`createdAt`
	, `folder`.`folderId` AS `folder.folderId`
	, `folder`.`parentFolderId` AS `folder.parentFolderId`
	, `folder`.`title` AS `folder.title`
	, `folder`.`isFavorite` AS `folder.isFavorite`
	, `folder`.`createdAt` AS `folder.createdAt`
	, `folder`.`statusId` AS `folder.statusId`
 FROM `vfsFiles`
	INNER JOIN `vfsFolders` `folder` ON
		`folder`.`folderId` = `vfsFiles`.`folderId`
	WHERE `vfsFiles`.`statusId` != 3
ORDER BY `createdAt` DESC;
	
CREATE OR REPLACE VIEW `getVfsFolders` AS
SELECT ft.`objectId`
    , ft.`parentId`
    , ft.`path`
    , ft.`rKey`
    , ft.`lKey`   
    , ft.`level`
	,`vfsFolders`.`folderId`
	, `vfsFolders`.`parentFolderId`
	, `vfsFolders`.`title`
	, `vfsFolders`.`isFavorite`
	, `vfsFolders`.`createdAt`
	, `vfsFolders`.`statusId`
	, `parentFolder`.`folderId` AS `parentFolder.folderId`
	, `parentFolder`.`parentFolderId` AS `parentFolder.parentFolderId`
	, `parentFolder`.`title` AS `parentFolder.title`
	, `parentFolder`.`isFavorite` AS `parentFolder.isFavorite`
	, `parentFolder`.`createdAt` AS `parentFolder.createdAt`
	, `parentFolder`.`statusId` AS `parentFolder.statusId`
 FROM `vfsFolders`
	LEFT JOIN `vfsFoldersTree` ft ON
        `vfsFolders`.`folderId` = ft.`objectId`
	LEFT JOIN `vfsFolders` `parentFolder` ON
		`parentFolder`.`folderId` = `vfsFolders`.`parentFolderId`
	WHERE `vfsFolders`.`statusId` != 3
	ORDER BY `level` ASC, `title` ASC;CREATE OR REPLACE VIEW `getAlbums` AS
    SELECT `albums`.`albumId`
        , `albums`.`title`
        , `albums`.`description`
        , `albums`.`alias`
        , `albums`.`isPrivate`
        , `albums`.`startDate`
        , `albums`.`endDate`
        , `albums`.`orderNumber`
        , `albums`.`folderPath`
        , `albums`.`roSecret`
        , `albums`.`roSecretHd`
        , `albums`.`deleteOriginalsAfter`
        , `albums`.`isDescSort`
        , `albums`.`createdAt`
        , `albums`.`modifiedAt`
        , `albums`.`metaInfo`
        , `albums`.`userId`
        , `albums`.`statusId`
        , `user`.`userId` AS `user.userId`
        , `user`.`login` AS `user.login`
        , `user`.`statusId` AS `user.statusId`
    FROM `albums`
        INNER JOIN `users` `user` ON `user`.`userId` = `albums`.`userId`
    WHERE `albums`.`statusId` IN (1,2,4)
    ORDER BY ISNULL( `orderNumber`), `startDate` DESC;


CREATE OR REPLACE VIEW `getPhotos` AS
    SELECT `photos`.`photoId`
        , `photos`.`albumId`
        , `photos`.`originalName`
        , `photos`.`filename`
        , `photos`.`fileSize`
        , `photos`.`fileSizeHd`
        , `photos`.`orderNumber`
        , `photos`.`afterText`
        , `photos`.`title`
        , `photos`.`exif`
        , `photos`.`createdAt`
        , `photos`.`photoDate`
        , `photos`.`statusId`
        , `album`.`albumId` AS `album.albumId`
        , `album`.`title` AS `album.title`
        , `album`.`alias` AS `album.alias`
        , `album`.`folderPath` AS `album.folderPath`
        , `album`.`startDate` AS `album.startDate`
        , `album`.`roSecret` AS `album.roSecret`
        , `album`.`roSecretHd` AS `album.roSecretHd`
    FROM `photos`
        INNER JOIN `albums` `album` ON `album`.`albumId` = `photos`.`albumId`
    WHERE `photos`.`statusId` IN ( 1, 2 )
    ORDER BY ISNULL( `photos`.`orderNumber` ), `photoId` DESC;CREATE OR REPLACE VIEW `getSiteParams` AS
SELECT `siteParams`.`siteParamId`
	, `siteParams`.`alias`
	, `siteParams`.`value`
	, `siteParams`.`description`
	, `siteParams`.`statusId`
 FROM `siteParams`
	WHERE `siteParams`.`statusId` IN (1,2);

CREATE OR REPLACE VIEW `getMetaDetails` AS
SELECT `metaDetails`.`metaDetailId`
	, `metaDetails`.`url`
	, `metaDetails`.`pageTitle`
	, `metaDetails`.`metaKeywords`
	, `metaDetails`.`metaDescription`
	, `metaDetails`.`alt`
	, `metaDetails`.`isInheritable`
	, `metaDetails`.`statusId`
 FROM `metaDetails`
	WHERE `metaDetails`.`statusId` IN (1,2)
ORDER BY `url`;

CREATE OR REPLACE VIEW `getStaticPages` AS
SELECT `staticPages`.`staticPageId`
	, `staticPages`.`title`
	, `staticPages`.`url`
	, `staticPages`.`content`
	, `staticPages`.`pageTitle`
	, `staticPages`.`metaKeywords`
	, `staticPages`.`metaDescription`
	, `staticPages`.`orderNumber`
	, `staticPages`.`parentStaticPageId`
	, `staticPages`.`statusId`
	, `parentStaticPage`.`staticPageId` AS `parentStaticPage.staticPageId`
	, `parentStaticPage`.`title` AS `parentStaticPage.title`
	, `parentStaticPage`.`url` AS `parentStaticPage.url`		
	, `parentStaticPage`.`parentStaticPageId` AS `parentStaticPage.parentStaticPageId`
 FROM `staticPages`
	LEFT JOIN `staticPages` `parentStaticPage` ON
		`parentStaticPage`.`staticPageId` = `staticPages`.`parentStaticPageId`
	WHERE `staticPages`.`statusId` IN (1,2)
ORDER BY `orderNumber`, `url`;

CREATE OR REPLACE VIEW `getNavigationTypes` AS
SELECT `navigationTypes`.`navigationTypeId`
	, `navigationTypes`.`title`
	, `navigationTypes`.`alias`
	, `navigationTypes`.`statusId`
 FROM `navigationTypes`
	WHERE `navigationTypes`.`statusId` IN (1,2)
ORDER BY `alias`;

CREATE OR REPLACE VIEW `getNavigations` AS
SELECT `navigations`.`navigationId`
	, `navigations`.`navigationTypeId`
	, `navigations`.`title`
	, `navigations`.`orderNumber`
	, `navigations`.`staticPageId`
	, `navigations`.`url`
	, `navigations`.`statusId`
	, `navigationType`.`navigationTypeId` AS `navigationType.navigationTypeId`
	, `navigationType`.`title` AS `navigationType.title`
	, `navigationType`.`alias` AS `navigationType.alias`
	, `staticPage`.`staticPageId` AS `staticPage.staticPageId`
	, `staticPage`.`title` AS `staticPage.title`
	, `staticPage`.`url` AS `staticPage.url`
	, `staticPage`.`parentStaticPageId` AS `staticPage.parentStaticPageId`
 FROM `navigations`
	INNER JOIN `navigationTypes` `navigationType` ON
		`navigationType`.`navigationTypeId` = `navigations`.`navigationTypeId`
	LEFT JOIN `staticPages` `staticPage` ON
		`staticPage`.`staticPageId` = `navigations`.`staticPageId`
	WHERE `navigations`.`statusId` IN (1,2)
ORDER BY `navigationType`.`alias`, `orderNumber`;INSERT INTO `statuses` (`statusId`, `title`, `alias`) VALUES ( 1, 'Опубликован', 'enabled' );
INSERT INTO `statuses` (`statusId`, `title`, `alias`) VALUES ( 2, 'Не опубликован', 'disabled' );
INSERT INTO `statuses` (`statusId`, `title`, `alias`) VALUES ( 3, 'Удален', 'deleted' );
INSERT INTO `statuses` (`statusId`, `title`, `alias`) VALUES ( 4, 'Новый', 'queue' );

INSERT INTO `siteParams` VALUES (1,'BigImage.Size','1920','Максимальный размер фото в px',1)
    ,(2,'BigImage.Quality','70','Качество JPEG большой фотографии',1)
    ,(3,'SmallImage.Quality','90','Качество JPEG превью',1)
    ,(4,'Site.Header','My EazyPhoto','Название сайта',1),(5,'Site.Footer','&copy; 2013 Unknown Author','Копирайт',1)
;

INSERT INTO `users` ( `login`, `password`, `statusId` ) VALUES ( 'admin', md5( CONCAT( '321p@$$-' , md5( '321p@$$-admin' ))), 1 );

INSERT INTO `vfsFolders` (`title`, `statusId`) VALUES ( 'root', 1 );
INSERT INTO `vfsFoldersTree` VALUES ( 1, NULL, '1', NULL, NULL, 1 );