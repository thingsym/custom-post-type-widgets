#!/usr/bin/env bash

if [ $# -lt 3 ]; then
	echo "usage: $0 <db-name> <db-user> <db-pass>"
	exit 1
fi

DB_NAME=$1
DB_USER=$2
DB_PASS=$3

TMPDIR=${TMPDIR-/tmp}
TMPDIR=$(echo $TMPDIR | sed -e "s/\/$//")
WP_TESTS_DIR=${WP_TESTS_DIR-$TMPDIR/wordpress-tests-lib}
WP_CORE_DIR=${WP_CORE_DIR-$TMPDIR/wordpress/}

set -ex

delete_wp() {
	if [ -f /tmp/wordpress.tar.gz ]; then
		rm -fr /tmp/wordpress.tar.gz
	fi

	if [ -d $WP_CORE_DIR ]; then
		rm -fr $WP_CORE_DIR
	fi
}

delete_test_suite() {
	if [ -d $WP_TESTS_DIR ]; then
		rm -fr $WP_TESTS_DIR
	fi
}

drop_db() {
	mysqladmin drop $DB_NAME --user="$DB_USER" --password="$DB_PASS"
}

delete_wp
delete_test_suite
drop_db
