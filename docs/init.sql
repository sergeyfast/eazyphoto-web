INSERT INTO `statuses` (`statusId`, `title`, `alias`) VALUES ( 1, 'Опубликован', 'enabled' );
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