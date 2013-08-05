CREATE OR REPLACE VIEW `getAlbums` AS
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
    ORDER BY ISNULL( `photos`.`orderNumber` ), `photoId` DESC;