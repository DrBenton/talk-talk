var path = require("path");
// Gulp stuff
var gulp = require("gulp");
var livereload = require("gulp-livereload");
var less = require("gulp-less");
var changed = require("gulp-changed");

gulp.task("less", function () {
  return gulp.src("assets-src/less/**/*.less")
    .pipe(changed("assets/css"))
    .pipe(less({
      paths: [ path.join(__dirname, "assets-src", "less") ]
    }))
    .pipe(gulp.dest("assets/css"));
});

gulp.task("watch", ["watch:less", "watch:livereload"]);

gulp.task("watch:less", ["less"], function () {
  gulp.watch("assets-src/less/**/*.less", ["less"]);
});

gulp.task("watch:livereload", function () {
  var server = livereload();
  gulp.watch([
    "**/*.php",
    "assets/**/*.js",
    "assets/**/*.css"
  ]).on("change", function(file) {
    console.log(file.path + " changed.");
    server.changed(file.path);
  });
});

gulp.task("default", ["less", "watch"]);


