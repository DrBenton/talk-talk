;<?php die('Unauthorized access'); __halt_compiler(); //PHP security: dont remove this line!

[general]

[debug]
debug = true
use_whoops_for_errors = false
livereload = false
livereload.port = 35729
perfs.tracking.enabled = true
perfs.tracking.session_content.max_length = 250
perfs.tracking.sql_queries.enabled = true
perfs.tracking.sql_queries.max_length = 20

[packing]
use_app_packing = true
use_vendors_packing = true
always_repack_profiles = false
always_repack_plugins = false
; When we compile Plugins in a "non HTTP" context (i.e. CLI),
; we have to know the base_url in order to inject their URLs in packed Plugins code
; NO TRAILING SLASH! Leave blank if the site lies at its domain root.
base_url =
; Caution: when this option is set to 'true', all the code in PHP packs will
; be smallest and easier to load for your server, but... it won't be readable by you any more!
; Only use in production.
strip_white_spaces = true

[data-cache]
enabled = false

[security]
csrf.token_name = _token

[db]
driver = mysql
host = localhost
database = talk-talk
username = talk-talk
password = talk-talk
