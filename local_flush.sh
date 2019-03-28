bin/passport.php mopi:cache:clear
bin/passport.php orm:clear-cache:metadata
bin/passport.php orm:clear-cache:query
bin/passport.php orm:clear-cache:result
bin/passport.php orm:schema-tool:update --dump-sql --force