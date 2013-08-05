CREATE OR REPLACE VIEW `getStatuses` AS
SELECT `statuses`.`statusId`
	, `statuses`.`title`
	, `statuses`.`alias`
 FROM `statuses`
ORDER BY `statusId`;

CREATE OR REPLACE VIEW `getDaemonLocks` AS
SELECT `daemonLockId`
	, `title`
	, `packageName`
	, `methodName`
	, `runAt`
	, `maxExecutionTime`
	, ( CURRENT_TIMESTAMP - `runAt` < `maxExecutionTime` ) as `isActive`
 FROM `daemonLocks`;

CREATE OR REPLACE VIEW `getUsers` AS
SELECT `users`.`userId`
	, `users`.`login`
	, `users`.`password`
	, `users`.`statusId`
 FROM `users`
	WHERE `users`.`statusId` != 3
ORDER BY `userId`;
