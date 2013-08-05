CREATE OR REPLACE VIEW `getSiteParams` AS
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
ORDER BY `navigationType`.`alias`, `orderNumber`;