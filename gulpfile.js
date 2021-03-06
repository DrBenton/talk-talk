var fs = require("fs");
var ini = require("ini");
var exec = require("child_process").exec;
var path = require("path");
// Gulp stuff
var gulp = require("gulp");
var livereload = require("gulp-livereload");
var jshint = require("gulp-jshint");
var less = require("gulp-less");
var flatten = require('gulp-flatten');

var appConfig = ini.parse(fs.readFileSync("./app/config/main.ini.php", "utf-8"));

gulp.task("jshint", function() {
  return gulp.src("./app/**/assets/js/**/*.js")
    .pipe(jshint())
    .pipe(jshint.reporter("default"));
});

gulp.task("compile-php-packs", function (cb) {
  exec('php app/bin/compile-packs.php', function (err, stdout, stderr) {
    console.log(stdout);
    console.log(stderr);
    cb(err);
  });
});

gulp.task("less", function () {
  return gulp.src("app/core-plugins/*/assets-src/less/**/*.less")
    .pipe(less())
    .pipe(gulp.dest(function(file){
      var destPath = file.path.replace('/assets-src/less/', '/assets/css/');
      file.path = 'css/' + path.basename(destPath);
      return destPath;
    }));
});

gulp.task("watch", ["less", "compile-php-packs"], function() {

  console.log("LESS files watching starts.");
  gulp.watch([
    "app/core-plugins/**/*.less"
  ],
    ["less"]
  );

  var liveReloadEnabled = !! appConfig["debug"]["livereload"];

  if (liveReloadEnabled) {
    var livereloadPort = parseInt(appConfig["debug"]["livereload.port"]);
    var server = livereload(livereloadPort);
    console.log("LiveReload started.");
  }

  console.log("Yes captain?");
  gulp.watch([
      "app/**",
      "plugins/**",
      "!**/var/**",
      "!**/components/**",
      "!**/*.pack.php"
    ],
    ["compile-php-packs"]
  ).on("change", function(file) {

      console.log(file.path + " changed.");

      if (liveReloadEnabled) {
        server.changed(file.path);
      }

  });
  console.log("Ready to serve!");

});

gulp.task("default", ["compile-php-packs"]);

