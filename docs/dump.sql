/*
Created		16.08.2008
Modified		20.02.2015
Project		
Model			
Company		
Author		
Version		
Database		PostgreSQL 8.1 
*/


/* Create Domains */


/* Create Sequences */


/* Create Tables */


Create table "users"
(
	"userId" Serial NOT NULL,
	"login" Varchar(64) NOT NULL,
	"password" Varchar(64) NOT NULL,
	"authKey" Varchar(32),
	"lastActivityAt" Timestamp Default now(),
	"statusId" Integer NOT NULL,
 primary key ("userId")
) Without Oids;


Create table "statuses"
(
	"statusId" Serial NOT NULL,
	"title" Varchar(255) NOT NULL,
	"alias" Varchar(64) NOT NULL UNIQUE,
 primary key ("statusId")
) Without Oids;


Create table "daemonLocks"
(
	"daemonLockId" Serial NOT NULL,
	"title" Varchar(255) NOT NULL,
	"packageName" Varchar(255) NOT NULL,
	"methodName" Varchar(255) NOT NULL,
	"runAt" Timestamp NOT NULL Default now(),
	"maxExecutionTime" Interval NOT NULL Default '00:03:00',
 primary key ("daemonLockId")
) Without Oids;


Create table "vfsFiles"
(
	"fileId" Serial NOT NULL,
	"folderId" Integer NOT NULL,
	"title" Varchar(255) NOT NULL,
	"path" Varchar(255) NOT NULL,
	"params" Text,
	"isFavorite" Boolean Default false,
	"mimeType" Varchar(255) NOT NULL,
	"fileSize" Integer Default 0,
	"fileExists" Boolean NOT NULL Default true,
	"createdAt" Timestamp NOT NULL Default now(),
	"statusId" Integer NOT NULL,
 primary key ("fileId")
) Without Oids;


Create table "vfsFoldersTree"
(
	"objectId" Integer NOT NULL,
	"parentId" Integer,
	"path"  "ltree",
	"rKey" Integer,
	"lKey" Integer,
 primary key ("objectId")
) Without Oids;


Create table "vfsFolders"
(
	"folderId" Serial NOT NULL,
	"parentFolderId" Integer,
	"title" Varchar(255) NOT NULL,
	"isFavorite" Boolean Default false,
	"createdAt" Timestamp NOT NULL Default now(),
	"statusId" Integer NOT NULL,
 primary key ("folderId")
) Without Oids;


Create table "siteParams"
(
	"siteParamId" Serial NOT NULL,
	"alias" Varchar(32) NOT NULL,
	"value" Varchar(255) NOT NULL,
	"description" Varchar(255),
	"statusId" Integer NOT NULL,
 primary key ("siteParamId")
) Without Oids;


Create table "staticPages"
(
	"staticPageId" Serial NOT NULL,
	"title" Varchar(255) NOT NULL,
	"url" Varchar(255) NOT NULL,
	"content" Text,
	"orderNumber" Integer,
	"parentStaticPageId" Integer,
	"statusId" Integer NOT NULL,
 primary key ("staticPageId")
) Without Oids;


Create table "navigations"
(
	"navigationId" Serial NOT NULL,
	"navigationTypeId" Integer NOT NULL,
	"title" Varchar(255),
	"orderNumber" Integer NOT NULL Default 1,
	"staticPageId" Integer,
	"url" Varchar(255),
	"params" Text,
	"statusId" Integer NOT NULL,
 primary key ("navigationId")
) Without Oids;


Create table "navigationTypes"
(
	"navigationTypeId" Serial NOT NULL,
	"title" Varchar(255) NOT NULL,
	"alias" Varchar(32) NOT NULL,
	"statusId" Integer NOT NULL,
 primary key ("navigationTypeId")
) Without Oids;


Create table "objectImages"
(
	"objectImageId" Serial NOT NULL,
	"objectClass" Varchar(32) NOT NULL,
	"objectId" Integer NOT NULL,
	"title" Varchar(255),
	"orderNumber" Integer NOT NULL,
	"smallImageId" Integer NOT NULL,
	"bigImageId" Integer NOT NULL,
	"statusId" Integer NOT NULL,
 primary key ("objectImageId")
) Without Oids;


Create table "metaDetails"
(
	"metaDetailId" Serial NOT NULL,
	"url" Varchar(255),
	"objectClass" Varchar(32),
	"objectId" Integer,
	"pageTitle" Varchar(255),
	"metaKeywords" Varchar(1024),
	"metaDescription" Varchar(1024),
	"alt" Varchar(255),
	"canonicalUrl" Varchar(1024),
	"statusId" Integer NOT NULL,
 primary key ("metaDetailId")
) Without Oids;


Create table "albums"
(
	"albumId" Serial NOT NULL,
	"title" Varchar(255) NOT NULL,
	"description" Varchar(4096),
	"alias" Varchar(255) NOT NULL,
	"isPrivate" Boolean NOT NULL Default false,
	"startDate" Date NOT NULL,
	"endDate" Date,
	"orderNumber" Integer,
	"folderPath" Varchar(255) NOT NULL,
	"roSecret" Varchar(1024) NOT NULL,
	"roSecretHd" Varchar(1024),
	"deleteOriginalsAfter" Integer,
	"isDescSort" Boolean NOT NULL Default false,
	"createdAt" Timestamp NOT NULL Default now(),
	"modifiedAt" Timestamp,
	"metaInfo" Text NOT NULL,
	"tagIds" Integer[],
	"userId" Integer NOT NULL,
	"statusId" Integer NOT NULL,
 primary key ("albumId")
) Without Oids;


Create table "photos"
(
	"photoId" Serial NOT NULL,
	"albumId" Integer NOT NULL,
	"originalName" Varchar(255) NOT NULL,
	"filename" Varchar(255) NOT NULL,
	"fileSize" Integer NOT NULL,
	"fileSizeHd" Integer NOT NULL,
	"orderNumber" Integer,
	"afterText" Text,
	"title" Varchar(255),
	"exif" Text,
	"createdAt" Timestamp NOT NULL Default now(),
	"photoDate" Timestamp,
	"statusId" Integer NOT NULL,
 primary key ("photoId")
) Without Oids;


Create table "tags"
(
	"tagId" Serial NOT NULL,
	"title" Varchar(255) NOT NULL,
	"alias" Varchar(255) NOT NULL,
	"description" Text,
	"orderNumber" Integer,
	"photoPath" Text,
	"photoId" Integer,
	"parentTagId" Integer,
	"statusId" Integer NOT NULL,
 primary key ("tagId")
) Without Oids;


/* Create Tab 'Others' for Selected Tables */


/* Create Alternate Keys */


/* Create Indexes */
Create unique index "IX_daemonLock" on "daemonLocks" using btree ("title","packageName","methodName");
Create index "IX_vfsFoldersTreeTreePath" on "vfsFoldersTree" using gist ("path");
Create index "IX_vfsFoldersTreeTreeRKey" on "vfsFoldersTree" using btree ("rKey");
Create index "IX_vfsFoldersTreeTreeLKey" on "vfsFoldersTree" using btree ("lKey");


/* Create Foreign Keys */
Create index "IX_FK_albums_userId_albums" on "albums" ("userId");
Alter table "albums" add  foreign key ("userId") references "users" ("userId") on update restrict on delete restrict;
Create index "IX_FK_users_statusId_users" on "users" ("statusId");
Alter table "users" add  foreign key ("statusId") references "statuses" ("statusId") on update restrict on delete restrict;
Create index "IX_FK_siteParams_statusId_siteParams" on "siteParams" ("statusId");
Alter table "siteParams" add  foreign key ("statusId") references "statuses" ("statusId") on update restrict on delete restrict;
Create index "IX_FK_staticPages_statusId_staticPages" on "staticPages" ("statusId");
Alter table "staticPages" add  foreign key ("statusId") references "statuses" ("statusId") on update restrict on delete restrict;
Create index "IX_FK_navigationTypes_statusId_navigationTypes" on "navigationTypes" ("statusId");
Alter table "navigationTypes" add  foreign key ("statusId") references "statuses" ("statusId") on update restrict on delete restrict;
Create index "IX_FK_navigations_statusId_navigations" on "navigations" ("statusId");
Alter table "navigations" add  foreign key ("statusId") references "statuses" ("statusId") on update restrict on delete restrict;
Create index "IX_FK_vfsFolders_statusId_vfsFolders" on "vfsFolders" ("statusId");
Alter table "vfsFolders" add  foreign key ("statusId") references "statuses" ("statusId") on update restrict on delete restrict;
Create index "IX_FK_vfsFiles_statusId_vfsFiles" on "vfsFiles" ("statusId");
Alter table "vfsFiles" add  foreign key ("statusId") references "statuses" ("statusId") on update restrict on delete restrict;
Create index "IX_FK_objectImages_statusId_objectImages" on "objectImages" ("statusId");
Alter table "objectImages" add  foreign key ("statusId") references "statuses" ("statusId") on update restrict on delete restrict;
Create index "IX_FK_metaDetails_statusId_metaDetails" on "metaDetails" ("statusId");
Alter table "metaDetails" add  foreign key ("statusId") references "statuses" ("statusId") on update restrict on delete restrict;
Create index "IX_FK_albums_statusId_albums" on "albums" ("statusId");
Alter table "albums" add  foreign key ("statusId") references "statuses" ("statusId") on update restrict on delete restrict;
Create index "IX_FK_photos_statusId_photos" on "photos" ("statusId");
Alter table "photos" add  foreign key ("statusId") references "statuses" ("statusId") on update restrict on delete restrict;
Create index "IX_FK_tags_statusId_tags" on "tags" ("statusId");
Alter table "tags" add  foreign key ("statusId") references "statuses" ("statusId") on update restrict on delete restrict;
Create index "IX_FK_objectImages_smallImageId_objectImages" on "objectImages" ("smallImageId");
Alter table "objectImages" add  foreign key ("smallImageId") references "vfsFiles" ("fileId") on update restrict on delete restrict;
Create index "IX_FK_objectImages_bigImageId_objectImages" on "objectImages" ("bigImageId");
Alter table "objectImages" add  foreign key ("bigImageId") references "vfsFiles" ("fileId") on update restrict on delete restrict;
Create index "IX_FK_vfsFolders_folderId_vfsFolders" on "vfsFolders" ("parentFolderId");
Alter table "vfsFolders" add  foreign key ("parentFolderId") references "vfsFolders" ("folderId") on update restrict on delete restrict;
Create index "IX_FK_vfsFiles_folderId_vfsFiles" on "vfsFiles" ("folderId");
Alter table "vfsFiles" add  foreign key ("folderId") references "vfsFolders" ("folderId") on update restrict on delete restrict;
Create index "IX_FK_vfsFoldersTree_folderId_vfsFoldersTree" on "vfsFoldersTree" ("objectId");
Alter table "vfsFoldersTree" add  foreign key ("objectId") references "vfsFolders" ("folderId") on update restrict on delete restrict;
Create index "IX_FK_vfsFoldersTree_parentId_vfsFoldersTree" on "vfsFoldersTree" ("parentId");
Alter table "vfsFoldersTree" add  foreign key ("parentId") references "vfsFolders" ("folderId") on update restrict on delete restrict;
Create index "IX_FK_navigations_staticPageId_navigations" on "navigations" ("staticPageId");
Alter table "navigations" add  foreign key ("staticPageId") references "staticPages" ("staticPageId") on update restrict on delete restrict;
Create index "IX_FK_staticPages_parentStaticPageId_staticPages" on "staticPages" ("parentStaticPageId");
Alter table "staticPages" add  foreign key ("parentStaticPageId") references "staticPages" ("staticPageId") on update restrict on delete restrict;
Create index "IX_FK_navigations_navigationTypeId_navigations" on "navigations" ("navigationTypeId");
Alter table "navigations" add  foreign key ("navigationTypeId") references "navigationTypes" ("navigationTypeId") on update restrict on delete restrict;
Create index "IX_FK_photos_albumId_photos" on "photos" ("albumId");
Alter table "photos" add  foreign key ("albumId") references "albums" ("albumId") on update restrict on delete restrict;
Create index "IX_FK_tags_photoId_tags" on "tags" ("photoId");
Alter table "tags" add  foreign key ("photoId") references "photos" ("photoId") on update restrict on delete restrict;
Create index "IX_FK_tags_parentTagId_tags" on "tags" ("parentTagId");
Alter table "tags" add  foreign key ("parentTagId") references "tags" ("tagId") on update restrict on delete restrict;


/* Create Procedures */


/* Create Views */


/* Create Referential Integrity Triggers */


/* Create User-Defined Triggers */


/* Create Roles */


/* Add Roles To Roles */


/* Create Role Permissions */
/* Role permissions on tables */

/* Role permissions on views */

/* Role permissions on procedures */


CREATE OR REPLACE VIEW "getVfsFiles" AS
SELECT "public"."vfsFiles"."fileId"
	, "public"."vfsFiles"."folderId"
	, "public"."vfsFiles"."title"
	, "public"."vfsFiles"."path"
	, "public"."vfsFiles"."params"
	, "public"."vfsFiles"."isFavorite"
	, "public"."vfsFiles"."mimeType"
	, "public"."vfsFiles"."fileSize"
	, "public"."vfsFiles"."fileExists"
	, "public"."vfsFiles"."statusId"
	, "public"."vfsFiles"."createdAt"
	, "folder"."folderId" AS "folder.folderId"
	, "folder"."parentFolderId" AS "folder.parentFolderId"
	, "folder"."title" AS "folder.title"
	, "folder"."isFavorite" AS "folder.isFavorite"
	, "folder"."createdAt" AS "folder.createdAt"
	, "folder"."statusId" AS "folder.statusId"
 FROM "public"."vfsFiles"
	INNER JOIN "public"."vfsFolders" "folder" ON
		"folder"."folderId" = "public"."vfsFiles"."folderId"
	WHERE "public"."vfsFiles"."statusId" IN (1,2)
ORDER BY "createdAt" DESC;
	
CREATE OR REPLACE VIEW "getVfsFolders" AS
SELECT ft."objectId"
    , ft."parentId"
    , ft."path"
    , ft."rKey"
    , ft."lKey"   
    , COALESCE( nlevel(ft."path" ), 0 ) as "level"
	,"public"."vfsFolders"."folderId"
	, "public"."vfsFolders"."parentFolderId"
	, "public"."vfsFolders"."title"
	, "public"."vfsFolders"."isFavorite"
	, "public"."vfsFolders"."createdAt"
	, "public"."vfsFolders"."statusId"
	, "parentFolder"."folderId" AS "parentFolder.folderId"
	, "parentFolder"."parentFolderId" AS "parentFolder.parentFolderId"
	, "parentFolder"."title" AS "parentFolder.title"
	, "parentFolder"."isFavorite" AS "parentFolder.isFavorite"
	, "parentFolder"."createdAt" AS "parentFolder.createdAt"
	, "parentFolder"."statusId" AS "parentFolder.statusId"
 FROM "public"."vfsFolders"
	LEFT JOIN "vfsFoldersTree" ft ON
        "vfsFolders"."folderId" = ft."objectId"
	LEFT JOIN "public"."vfsFolders" "parentFolder" ON
		"parentFolder"."folderId" = "public"."vfsFolders"."parentFolderId"
	WHERE "public"."vfsFolders"."statusId" IN (1,2)
	ORDER BY "level" ASC, "title" ASC;CREATE OR REPLACE VIEW "getAlbums" AS
    SELECT "albums"."albumId"
        , "albums"."title"
        , "albums"."description"
        , "albums"."alias"
        , "albums"."isPrivate"
        , "albums"."startDate"
        , "albums"."endDate"
        , "albums"."orderNumber"
        , "albums"."folderPath"
        , "albums"."roSecret"
        , "albums"."roSecretHd"
        , "albums"."deleteOriginalsAfter"
        , "albums"."isDescSort"
        , "albums"."createdAt"
        , "albums"."modifiedAt"
        , "albums"."metaInfo"
        , "albums"."userId"
        , "albums"."tagIds"
        , "albums"."statusId"
        , "user"."userId" AS "user.userId"
        , "user"."login" AS "user.login"
        , "user"."statusId" AS "user.statusId"
    FROM "albums"
        INNER JOIN "users" "user" ON "user"."userId" = "albums"."userId"
    WHERE "albums"."statusId" IN (1,2,4)
    ORDER BY "orderNumber", "startDate" DESC;


CREATE OR REPLACE VIEW "getPhotos" AS
    SELECT "photos"."photoId"
        , "photos"."albumId"
        , "photos"."originalName"
        , "photos"."filename"
        , "photos"."fileSize"
        , "photos"."fileSizeHd"
        , "photos"."orderNumber"
        , "photos"."afterText"
        , "photos"."title"
        , "photos"."exif"
        , "photos"."createdAt"
        , "photos"."photoDate"
        , "photos"."statusId"
        , "album"."albumId" AS "album.albumId"
        , "album"."title" AS "album.title"
        , "album"."alias" AS "album.alias"
        , "album"."folderPath" AS "album.folderPath"
        , "album"."startDate" AS "album.startDate"
        , "album"."roSecret" AS "album.roSecret"
        , "album"."roSecretHd" AS "album.roSecretHd"
    FROM "photos"
        INNER JOIN "albums" "album" ON "album"."albumId" = "photos"."albumId"
    WHERE "photos"."statusId" IN ( 1, 2 )
    ORDER BY "photos"."orderNumber", "photoId" DESC;


CREATE OR REPLACE VIEW "getTags" AS
SELECT "public"."tags"."tagId"
	, "public"."tags"."title"
	, "public"."tags"."alias"
	, "public"."tags"."description"
	, "public"."tags"."orderNumber"
	, "public"."tags"."photoPath"
	, "public"."tags"."photoId"
	, "public"."tags"."parentTagId"
	, "public"."tags"."statusId"
	, "parentTag"."tagId" AS "parentTag.tagId"
	, "parentTag"."title" AS "parentTag.title"
	, "parentTag"."alias" AS "parentTag.alias"
	, "parentTag"."parentTagId" AS "parentTag.parentTagId"
 FROM "public"."tags"
	LEFT JOIN "public"."tags" "parentTag" ON
		"parentTag"."tagId" = "public"."tags"."parentTagId"
	WHERE "public"."tags"."statusId" IN (1,2)
	ORDER BY "orderNumber", "title";CREATE OR REPLACE VIEW "getStatuses" AS
SELECT "public"."statuses"."statusId"
	, "public"."statuses"."title"
	, "public"."statuses"."alias"
 FROM "public"."statuses";

CREATE OR REPLACE VIEW "getDaemonLocks" AS
SELECT "public"."daemonLocks"."daemonLockId"
	, "public"."daemonLocks"."title"
	, "public"."daemonLocks"."packageName"
	, "public"."daemonLocks"."methodName"
	, "public"."daemonLocks"."runAt"
	, "public"."daemonLocks"."maxExecutionTime"
	, ( now() - "runAt" < "maxExecutionTime" ) as "isActive"
 FROM "public"."daemonLocks";

CREATE OR REPLACE VIEW "getUsers" AS
SELECT "public"."users"."userId"
	, "public"."users"."login"
	, "public"."users"."password"
	, "public"."users"."authKey"
	, "public"."users"."lastActivityAt"
	, "public"."users"."statusId"
 FROM "public"."users"
	WHERE "public"."users"."statusId" IN (1,2);

CREATE OR REPLACE VIEW "getSiteParams" AS
SELECT "public"."siteParams"."siteParamId"
	, "public"."siteParams"."alias"
	, "public"."siteParams"."value"
	, "public"."siteParams"."description"
	, "public"."siteParams"."statusId"
 FROM "public"."siteParams"
	WHERE "public"."siteParams"."statusId" IN (1,2);

CREATE OR REPLACE VIEW "getMetaDetails" AS
SELECT "public"."metaDetails"."metaDetailId"
	, "public"."metaDetails"."url"
	, "public"."metaDetails"."pageTitle"
	, "public"."metaDetails"."metaKeywords"
	, "public"."metaDetails"."metaDescription"
	, "public"."metaDetails"."alt"
	, "public"."metaDetails"."objectId"
	, "public"."metaDetails"."objectClass"
	, "public"."metaDetails"."canonicalUrl"
	, "public"."metaDetails"."statusId"
 FROM "public"."metaDetails"
	WHERE "public"."metaDetails"."statusId" IN (1,2)
ORDER BY "url";

CREATE OR REPLACE VIEW "getStaticPages" AS
SELECT "public"."staticPages"."staticPageId"
	, "public"."staticPages"."title"
	, "public"."staticPages"."url"
	, "public"."staticPages"."content"
	, "public"."staticPages"."orderNumber"
	, "public"."staticPages"."parentStaticPageId"
	, "public"."staticPages"."statusId"
	, "parentStaticPage"."staticPageId" AS "parentStaticPage.staticPageId"
	, "parentStaticPage"."title" AS "parentStaticPage.title"
	, "parentStaticPage"."url" AS "parentStaticPage.url"		
	, "parentStaticPage"."parentStaticPageId" AS "parentStaticPage.parentStaticPageId"
 FROM "public"."staticPages"
	LEFT JOIN "public"."staticPages" "parentStaticPage" ON
		"parentStaticPage"."staticPageId" = "public"."staticPages"."parentStaticPageId"
	WHERE "public"."staticPages"."statusId" IN (1,2)
ORDER BY "orderNumber", "url";

CREATE OR REPLACE VIEW "getNavigationTypes" AS
SELECT "public"."navigationTypes"."navigationTypeId"
	, "public"."navigationTypes"."title"
	, "public"."navigationTypes"."alias"
	, "public"."navigationTypes"."statusId"
 FROM "public"."navigationTypes"
	WHERE "public"."navigationTypes"."statusId" IN (1,2)
ORDER BY "alias";

CREATE OR REPLACE VIEW "getNavigations" AS
SELECT "public"."navigations"."navigationId"
	, "public"."navigations"."navigationTypeId"
	, "public"."navigations"."title"
	, "public"."navigations"."orderNumber"
	, "public"."navigations"."staticPageId"
	, "public"."navigations"."url"
	, "public"."navigations"."params"
	, "public"."navigations"."statusId"
	, "navigationType"."navigationTypeId" AS "navigationType.navigationTypeId"
	, "navigationType"."title" AS "navigationType.title"
	, "navigationType"."alias" AS "navigationType.alias"
	, "staticPage"."staticPageId" AS "staticPage.staticPageId"
	, "staticPage"."title" AS "staticPage.title"
	, "staticPage"."url" AS "staticPage.url"
	, "staticPage"."parentStaticPageId" AS "staticPage.parentStaticPageId"
 FROM "public"."navigations"
	INNER JOIN "public"."navigationTypes" "navigationType" ON
		"navigationType"."navigationTypeId" = "public"."navigations"."navigationTypeId"
	LEFT JOIN "public"."staticPages" "staticPage" ON
		"staticPage"."staticPageId" = "public"."navigations"."staticPageId"
	WHERE "public"."navigations"."statusId" IN (1,2)
ORDER BY "navigationType"."alias", "orderNumber";

CREATE OR REPLACE VIEW "getObjectImages" AS
SELECT "public"."objectImages"."objectImageId"
	, "public"."objectImages"."objectClass"
	, "public"."objectImages"."objectId"
	, "public"."objectImages"."title"
	, "public"."objectImages"."orderNumber"
	, "public"."objectImages"."smallImageId"
	, "public"."objectImages"."bigImageId"
	, "public"."objectImages"."statusId"
	, "smallImage"."fileId" AS "smallImage.fileId"
	, "smallImage"."folderId" AS "smallImage.folderId"
	, "smallImage"."title" AS "smallImage.title"
	, "smallImage"."path" AS "smallImage.path"
	, "smallImage"."params" AS "smallImage.params"
	, "bigImage"."fileId" AS "bigImage.fileId"
	, "bigImage"."folderId" AS "bigImage.folderId"
	, "bigImage"."title" AS "bigImage.title"
	, "bigImage"."path" AS "bigImage.path"
	, "bigImage"."params" AS "bigImage.params"
 FROM "public"."objectImages"
	INNER JOIN "public"."vfsFiles" "smallImage" ON
		"smallImage"."fileId" = "public"."objectImages"."smallImageId"
	INNER JOIN "public"."vfsFiles" "bigImage" ON
		"bigImage"."fileId" = "public"."objectImages"."bigImageId"
	WHERE "public"."objectImages"."statusId" IN (1,2)
ORDER BY "orderNumber";INSERT INTO "statuses" ( "statusId", "title", "alias" ) VALUES ( 1, 'Опубликован', 'enabled' );
INSERT INTO "statuses" ( "statusId", "title", "alias" ) VALUES ( 2, 'Не опубликован', 'disabled' );
INSERT INTO "statuses" ( "statusId", "title", "alias" ) VALUES ( 3, 'Удален', 'deleted' );
INSERT INTO "statuses" ( "statusId", "title", "alias" ) VALUES ( 4, 'Новый', 'queue' );

INSERT INTO "siteParams" VALUES (1,'BigImage.Size','1920','Максимальный размер фото в px',1)
    ,(2,'BigImage.Quality','70','Качество JPEG большой фотографии',1)
    ,(3,'SmallImage.Quality','90','Качество JPEG превью',1)
    ,(4,'Site.Header','My EazyPhoto','Название сайта',1),(5,'Site.Footer','&copy; 2015 Unknown Author','Копирайт',1)
;

INSERT INTO "users" ( "login", "password", "statusId" ) VALUES ( 'admin', md5( 'saltedp@$$-' ||  md5( 'saltedp@$$-' ||  'admin' )), 1 );

INSERT INTO "vfsFolders" ("title", "statusId") 			VALUES ( 'root', 1 ); 
INSERT INTO "vfsFoldersTree" 							VALUES ( 1, NULL, '1', NULL, NULL );