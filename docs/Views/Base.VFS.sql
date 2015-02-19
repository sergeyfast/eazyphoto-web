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
	ORDER BY "level" ASC, "title" ASC;