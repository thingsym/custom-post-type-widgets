#!/usr/bin/env bash

if [ $# -lt 3 ]; then
	echo "usage: $0 <db-name> <db-user> <db-pass> [db-host] [wp-version] [skip-database-creation]"
	exit 1
fi

DB_NAME=$1
DB_USER=$2
DB_PASS=$3
DB_HOST=${4-localhost}
WP_VERSION=${5-latest}
SKIP_DB_CREATE=${6-false}

script_dir=$(cd $(dirname $BASH_SOURCE); pwd)

$script_dir/uninstall-wp-tests.sh $DB_NAME $DB_USER $DB_PASS $DB_HOST $WP_VERSION
$script_dir/install-wp-tests.sh $DB_NAME $DB_USER $DB_PASS $DB_HOST $WP_VERSION $SKIP_DB_CREATE
