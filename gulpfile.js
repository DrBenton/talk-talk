var fs = require('fs'),
    ini = require('ini');

var appConfig = ini.parse(fs.readFileSync('./app/config/main.ini.php', 'utf-8'));

var gulp = require('gulp'),
    livereload = require('gulp-livereload');

gulp.task('watch', function() {
  var livereloadPort = parseInt(appConfig.debug['livereload.port']);
  var server = livereload(livereloadPort);
  gulp.watch([
    'app/**',
    '!**/*.log'
  ]).on('change', function(file) {
      console.log(file.path + ' changed.');
      server.changed(file.path);
  });
});

gulp.task('default', ['watch']);

