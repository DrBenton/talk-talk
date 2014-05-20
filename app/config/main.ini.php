;<?php die('Unauthorized access'); __halt_compiler(); //PHP security: dont remove this line!

[general]
debug = true

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

[development]
livereload.port = 35729
