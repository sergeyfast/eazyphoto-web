/*
Created		16.08.2008
Modified		19.02.2015
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


