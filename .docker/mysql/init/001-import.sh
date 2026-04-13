#!/bin/sh
set -eu

mysql \
  --user="root" \
  --password="${MYSQL_ROOT_PASSWORD}" \
  --database="${MYSQL_DATABASE}" \
  < /docker-entrypoint-initdb.d/010-remote-db.sql
