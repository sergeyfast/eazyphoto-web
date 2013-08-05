eazyphoto-web
=============

Web Albums Implementation

Install 
------
  * Create database from docs/dump.sql (utf8)
  * Change settings in web/etc/conf/sites.xml
  * Sample nginx config: docs/nginx-fpm.conf
  * Make sure you have btsync and eazyphotod installed and running


Admin Panel
------
Open http://eazyphoto/vt/ (admin:admin)
PHP Indexer (w/o eazyphotod): http://eazyphoto/int/daemons/sync-photos

Changelog
------
v0.1
  * Proof of Concept
