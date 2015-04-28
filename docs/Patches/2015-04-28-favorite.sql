ALTER TABLE "photos" ADD COLUMN "isFavorite" Boolean Default false;
Create index "IX_photos_isFavorite" on "photos" using btree ("isFavorite");

DROP VIEW "getPhotos";
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