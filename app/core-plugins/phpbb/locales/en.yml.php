#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

core-plugins:
  phpbb:
    import:
      start:
        form:
          intro: >
            Please fill the following form to allow access to your phpBB forum database.
          driver: Database type
          driver.help: Most of the time, your phpBB installation will use MySQL.
          host: Database host name or IP address
          host.help: If you don't know the host of your phpBB installation, use "localhost".
          username: Database login
          password: Database password
          database: Database name
          prefix: phpBB tables prefix
          port: Database port
          charset: Database characters set
          charset.help: If you don't know what to put here, just leave the default value.
          collation: Database characters collation
          collation.help: If you don't know what to put here, just leave the default value.
        db-error: >
          Humm... It seems that the phpBB database access you provided
          are not correct.<br>
          The database responded with the following message:<br>
          <i>%pdo_message%</i>
        db-success: >
          PhpBB database access is successful!
      importing:
        intro: >
          We are importing your phpBB data, please wait...
      alerts:
        import-error: There has been a error while loading '%importUrl%' through AJAX! Please try again.
