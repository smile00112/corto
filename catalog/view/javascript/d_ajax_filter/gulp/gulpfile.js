/*jslint node: true */
"use strict";

var gulp = require("gulp");
var sass = require("gulp-sass");
var sourcemaps = require("gulp-sourcemaps");
var autoprefixer = require("gulp-autoprefixer");
var browserSync = require("browser-sync");
var path = require("path");

var sassDest = "../../../theme/default/stylesheet/d_ajax_filter/";

var baseDir = path.resolve(__dirname, "../../../../");


gulp.task("sass", function () {
    return gulp.src(sassDest + "*.scss")
        .pipe(sourcemaps.init())
        .pipe(sass({outputStyle: "compressed"}).on("error", sass.logError))
        .pipe(autoprefixer({
            browsers: ["last 15 versions"]
        }))
        .pipe(sourcemaps.write("./"))
        .pipe(gulp.dest(sassDest))
        .pipe(browserSync.reload({stream: true}));
});

gulp.task("sass:themes", function () {
    return gulp.src(sassDest + "themes/*.scss")
        .pipe(sourcemaps.init())
        .pipe(sass({outputStyle: "compressed"}).on("error", sass.logError))
        .pipe(autoprefixer({
            browsers: ["last 15 versions"]
        }))
        .pipe(sourcemaps.write("./themes/"))
        .pipe(gulp.dest(sassDest+'themes/'))
        .pipe(browserSync.reload({stream: true}));
});

gulp.task("sass:watch", function () {
    gulp.watch([sassDest + "*.scss", sassDest + "themes/*.scss"], ["sass", "sass:themes"]);
});

gulp.task("browser_sync_init", function () {
    if (typeof process.env.HOST !== "undefined") {
        browserSync({
            proxy: process.env.HOST
        });
    }
})

gulp.task("build_sass", ["browser_sync_init"], function () {
    if (typeof process.env.HOST !== "undefined") {
        gulp.watch([
            baseDir + "/controller/extension/d_ajax_filter/**/*.php",
            baseDir + "/view/theme/default/template/extension/d_ajax_filter/**/*.tag"
        ], browserSync.reload);
    }
    gulp.start(["sass", "sass:themes", "sass:watch"]);
})