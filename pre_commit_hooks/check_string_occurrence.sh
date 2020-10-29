#!/usr/bin/env sh

set -e

fail_if_found() {
  if [ "$?" == "0" ]; then
    return 1
  else
    return 0
  fi
}

grep "$@" /dev/null || fail_if_found
