#!/bin/sh
# Moves the install into another domain's folder on the same hosting account
# and leaves a link behind, so the first domain still reaches it.
#
#   sh relocate.sh /home/u1/domains/first.example/public_html /home/u1/domains/second.example/public_html
#
# Before:  first/public_html  = the install        second/public_html -> first/public_html
# After:   first/public_html -> second/public_html  second/public_html = the install
#
# The move is a rename on one disk, so it takes no time and copies nothing:
# the database, the .env and the uploaded files stay exactly as they are.
# Safe on every deploy: once the install is in the second folder it reports
# that and changes nothing. Anything it does not recognise, it leaves alone
# and stops the deploy.
set -eu

from="${1:?the folder the install is in}"
to="${2:?the folder it should be in}"

if [ "$from" = "$to" ]; then
    echo "relocate: nothing to do"
    exit 0
fi

# Already moved.
if [ -f "$to/artisan" ] && [ ! -L "$to" ]; then
    if [ ! -e "$from" ] && [ ! -L "$from" ] && [ -d "$(dirname "$from")" ]; then
        ln -s "$to" "$from"
        echo "relocate: the install is in place; linked the first folder to it"
    else
        echo "relocate: the install is already in place"
    fi
    exit 0
fi

# The expected starting point: the install in the first folder, the second a link to it.
if [ -f "$from/artisan" ] && [ ! -L "$from" ] && [ -L "$to" ] && [ "$(readlink "$to")" = "$from" ]; then
    rm "$to"
    if ! mv "$from" "$to"; then
        ln -s "$from" "$to"
        echo "relocate: could not move the folder; the link was put back, nothing changed"
        exit 1
    fi
    ln -s "$to" "$from"

    # Made by `artisan storage:link` with the old absolute path: the deploy
    # makes it again with the new one.
    if [ -L "$to/public/storage" ]; then
        rm "$to/public/storage"
    fi

    echo "relocate: moved the install; the first folder now links to it"
    exit 0
fi

echo "relocate: the folders are not laid out as expected, nothing changed"
exit 1
