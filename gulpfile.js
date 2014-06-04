var fs = require("fs");
var ini = require("ini");
var exec = require("child_process").exec;
// Gulp stuff
var gulp = require("gulp");
var livereload = require("gulp-livereload");
var jshint = require("gulp-jshint");

var appConfig = ini.parse(fs.readFileSync("./app/config/main.ini.php", "utf-8"));

gulp.task("jshint", function() {
  return gulp.src("./app/**/assets/js/**/*.js")
    .pipe(jshint())
    .pipe(jshint.reporter("default"));
});

gulp.task("compilePhpPacks", function (cb) {
  exec('php app/bin/compile-packs.php', function (err, stdout, stderr) {
    console.log(stdout);
    console.log(stderr);
    cb(err);
  });
});

gulp.task("watch", ["compilePhpPacks"], function() {

  // PHP packings
  gulp.watch(
    [
      "app/boot/**/*.php",
      "app/core-plugins/**/*.php",
      "app/plugins/**/*.*"
    ],
    ["compilePhpPacks"]
  );

  // Live reload
  var livereloadPort = parseInt(appConfig.debug["livereload.port"]);
  var server = livereload(livereloadPort);
  gulp.watch([
    "app/**",
    "!**/cache/**",
    "!**/*.pack.php",
    "!**/*.log"
  ]).on("change", function(file) {
      console.log(file.path + " changed.");
      server.changed(file.path);
  });

});

gulp.task("default", ["watch"]);

