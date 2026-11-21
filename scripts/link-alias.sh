#!/bin/sh
# Makes other domains on the same hosting account serve this same folder.
#
#   sh scripts/link-alias.sh /home/u1/domains/first.example/public_html second.example [third.example ...]
#
# Each listed domain has its own public_html beside this one (the host creates
# it with a placeholder page). That folder is set aside, not deleted, and a
# link to the deploy folder takes its place, so both addresses serve one
# install: one database, one .env, one deploy. Which address is the real one
# is decided by PRIMARY_URL (app/Http/Middleware/PrimaryHost.php).
#
# Safe on every deploy: a domain that is already linked, or that has no folder
# on this account, is reported and left alone.
#
# To undo:  rm <domain>/public_html && mv <domain>/public_html.before-alias <domain>/public_html
set -eu

deploy_path="${1:?deploy path}"
shift
domains_dir="$(dirname "$(dirname "$deploy_path")")"

for domain in "$@"; do
    case "$domain" in
        '' | .* | */* | *[!A-Za-z0-9.-]*)
            echo "alias $domain: not a domain name, skipped"
            continue
            ;;
    esac

    target="$domains_dir/$domain/public_html"

    if [ "$target" = "$deploy_path" ]; then
        echo "alias $domain: this is the deploy folder itself, skipped"
    elif [ -L "$target" ]; then
        echo "alias $domain: already linked to $(readlink "$target")"
    elif [ -d "$target" ]; then
        aside="$target.before-alias"
        if [ -e "$aside" ]; then
            aside="$aside-$(date +%s)"
        fi
        mv "$target" "$aside"
        ln -s "$deploy_path" "$target"
        echo "alias $domain: linked to $deploy_path (its own folder is kept as $aside)"
    else
        echo "alias $domain: no folder at $target on this account, skipped"
    fi
done
