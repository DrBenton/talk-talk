;<?php die('Unauthorized access'); __halt_compiler(); //PHP security: dont remove this line!

[general]

[debug]
debug = true
livereload = false
livereload.port = 35729
perfs.tracking.enabled = true
perfs.tracking.session_content.max_length = 250
perfs.tracking.sql_queries.enabled = true
perfs.tracking.sql_queries.max_length = 20

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
