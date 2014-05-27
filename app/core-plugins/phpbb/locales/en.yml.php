#<?php die('Unauthorized access');__halt_compiler(); //PHP security: don't remove this line!

core-plugins:
  phpbb:
    import:
      start:
        form:
          intro: >
            Please fill the following form to allow access to your phpBb forum database.
          driver: Database type
          driver.help: Most of the time, your phpBb installation will use MySQL.
          host: Database host name or IP address
          host.help: If you don't know the host of your phpBb installation, use "localhost".
          username: Database login
          password: Database password
          database: Database name
          prefix: phpBb tables prefix
          port: Database port
          charset: Database characters set
          charset.help: If you don't know what to put here, just leave the default value.
          collation: Database characters collation
          collation.help: If you don't know what to put here, just leave the default value.
        db-error: >
          Humm... It seems that the phpBb database access you provided
          are not correct.<br>
          The database responded with the following message:<br>
          <i>%pdo_message%</i>
        db-success: >
          PhpBB database access is successful!
      importing:
        intro: |
          Be aware that *ALL PREVIOUSLY IMPORTED PHPBB ENTITIES WILL BE DEFINITELY REMOVED* from your Forum.
          Of course, your phpBb database will remain untouched.
        please-wait: >
          We are importing your phpBb data, please wait...
        item-type-import-preparation: Preparing items import...
        item-type-import-in-progress: Items import in progress...
      alerts:
        import-error: There has been a error while loading '%importUrl%' through AJAX! Please try again.
