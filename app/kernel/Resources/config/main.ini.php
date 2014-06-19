;<?php die('Unauthorized access'); __halt_compiler(); //PHP security: dont remove this line!

[general]

[debug]
debug = true

[optimization]
use_compiled_js_if_available = true

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