#!/bin/bash
DATABASE_NAME=eazyphoto
DATABASE_USER=sergeyfast
DATABASE_ROOT=/usr/local/bin

${DATABASE_ROOT}/dropdb -U ${DATABASE_USER} ${DATABASE_NAME}
${DATABASE_ROOT}/createdb -U ${DATABASE_USER} -E UTF8 ${DATABASE_NAME}
${DATABASE_ROOT}/psql -c "CREATE EXTENSION \"ltree\"; CREATE EXTENSION \"hstore\"" ${DATABASE_NAME}


${DATABASE_ROOT}/psql -U ${DATABASE_USER} -f EazyPhoto.sql ${DATABASE_NAME}
for i in `ls Views/`; do ${DATABASE_ROOT}/psql -U ${DATABASE_USER} -f "Views/${i}" ${DATABASE_NAME}; done
${DATABASE_ROOT}/psql -U ${DATABASE_USER} -f init.sql ${DATABASE_NAME}