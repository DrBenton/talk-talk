var fs = require('fs');
var ini = require('ini');
// Gulp stuff
var gulp = require('gulp');
var livereload = require('gulp-livereload');
var jshint = require('gulp-jshint');

var appConfig = ini.parse(fs.readFileSync('./app/config/main.ini.php', 'utf-8'));

gulp.task('jshint', function() {
  return gulp.src('./app/**/assets/js/**/*.js')
    .pipe(jshint())
    .pipe(jshint.reporter('default'));
});

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

