CREATE OR REPLACE VIEW "getStatuses" AS
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
ORDER BY "orderNumber";