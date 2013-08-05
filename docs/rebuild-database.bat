@ECHO OFF
SET MYSQL_ROOT="D:\usr\mysql\bin"
SET DATABASE_NAME="eazyphoto"
SET DATABASE_USER="root"
SET DATABASE_PASS="root"

REM BYDLOCODE WITHOUT FOR
net stop Apache2.2
%MYSQL_ROOT%\mysqladmin.exe -h localhost -u %DATABASE_USER% -p%DATABASE_PASS% drop %DATABASE_NAME%
%MYSQL_ROOT%\mysqladmin.exe -h localhost -u %DATABASE_USER% -p%DATABASE_PASS% create %DATABASE_NAME%

%MYSQL_ROOT%\mysql.exe -h localhost -u %DATABASE_USER% -p%DATABASE_PASS% %DATABASE_NAME% < EazyPhoto.sql

%MYSQL_ROOT%\mysql.exe -h localhost -u %DATABASE_USER% -p%DATABASE_PASS% %DATABASE_NAME% < Views\Base.VFS.sql
%MYSQL_ROOT%\mysql.exe -h localhost -u %DATABASE_USER% -p%DATABASE_PASS% %DATABASE_NAME% < Views\Base.Common.sql
%MYSQL_ROOT%\mysql.exe -h localhost -u %DATABASE_USER% -p%DATABASE_PASS% %DATABASE_NAME% < Views\EazyPhoto.Common.sql
%MYSQL_ROOT%\mysql.exe -h localhost -u %DATABASE_USER% -p%DATABASE_PASS% %DATABASE_NAME% < Views\EazyPhoto.Albums.sql
%MYSQL_ROOT%\mysql.exe -h localhost -u %DATABASE_USER% -p%DATABASE_PASS% %DATABASE_NAME% < init.sql

net START Apache2.2
pause