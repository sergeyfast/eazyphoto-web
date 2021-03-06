CREATE OR REPLACE VIEW "getAlbums" AS
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
        , "photos"."isFavorite"
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
	ORDER BY "orderNumber", "title";