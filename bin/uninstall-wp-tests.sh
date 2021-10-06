#!/usr/bin/env bash

if [ $# -lt 3 ]; then
	echo "usage: $0 <db-name> <db-user> <db-pass> [db-host] [wp-version]"
	exit 1
fi

DB_NAME=$1
DB_USER=$2
DB_PASS=$3
DB_HOST=${4-localhost}
WP_VERSION=${5-latest}

TMPDIR=${TMPDIR-/tmp}
TMPDIR=$(echo $TMPDIR | sed -e "s/\/$//")
WP_TESTS_DIR=${WP_TESTS_DIR-$TMPDIR/wordpress-tests-lib}
WP_CORE_DIR=${WP_CORE_DIR-$TMPDIR/wordpress/}

set -ex

delete_wp() {
	if [[ $WP_VERSION == 'nightly' || $WP_VERSION == 'trunk' ]]; then
		if [ -f $TMPDIR/wordpress-nightly/wordpress-nightly.zip ]; then
			rm $TMPDIR/wordpress-nightly/wordpress-nightly.zip
		fi
	else
		if [ -f $TMPDIR/wordpress.tar.gz ]; then
			rm $TMPDIR/wordpress.tar.gz
		fi
	fi

	if [ -d $WP_CORE_DIR ]; then
		rm -rf $WP_CORE_DIR
	fi
}

delete_test_suite() {
	if [ -d $WP_TESTS_DIR ]; then
		rm -rf $WP_TESTS_DIR
	fi
}

drop_db() {
	# parse DB_HOST for port or socket references
	local PARTS=(${DB_HOST//\:/ })
	local DB_HOSTNAME=${PARTS[0]};
	local DB_SOCK_OR_PORT=${PARTS[1]};
	local EXTRA=""

	if ! [ -z $DB_HOSTNAME ] ; then
		if [ $(echo $DB_SOCK_OR_PORT | grep -e '^[0-9]\{1,\}$') ]; then
			EXTRA=" --host=$DB_HOSTNAME --port=$DB_SOCK_OR_PORT --protocol=tcp"
		elif ! [ -z $DB_SOCK_OR_PORT ] ; then
			EXTRA=" --socket=$DB_SOCK_OR_PORT"
		elif ! [ -z $DB_HOSTNAME ] ; then
			EXTRA=" --host=$DB_HOSTNAME --protocol=tcp"
		fi
	fi

	# delete database
	mysqladmin drop $DB_NAME --user="$DB_USER" --password="$DB_PASS"$EXTRA --force
}

delete_wp
delete_test_suite
drop_db
