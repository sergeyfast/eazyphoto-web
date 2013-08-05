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
	ORDER BY `level` ASC, `title` ASC;