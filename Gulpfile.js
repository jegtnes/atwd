var sassFiles     = ['.bower_components/foundation/scss/normalize.scss'
                    ,'.bower_components/foundation/scss/foundation.scss'
                    ,'./scss/*.scss'
                    ,'.bower_components/foundation/scss'],
    sassCompile   = './assets/css',
    jsFiles       = ['./bower_components/jquery/dist/jquery.js'
                    ,'./bower_components/foundation/js/foundation.js'
                    ,'./js/**/*.js'],
    jsHeadFiles = './bower_components/modernizr/modernizr.js',
    jsCompile     = './assets/js';

var gulp          = require('gulp'),
    gutil         = require('gulp-util'),

    sass          = require('gulp-sass'),
    autoprefixer  = require('gulp-autoprefixer'),
    minifycss     = require('gulp-minify-css'),

    rename        = require('gulp-rename'),
    concat        = require('gulp-concat'),
    uglify        = require('gulp-uglify'),

    livereload    = require('gulp-livereload'),
    lr            = require('tiny-lr'),
    server        = lr();

gulp.task('styles', function () {
    gulp.src(sassFiles)
        .pipe(sass({ style: 'expanded' }))
        .pipe(autoprefixer('last 2 version'))
        .pipe(gulp.dest(sassCompile))
        .pipe(rename('app.min.css'))
        .pipe(minifycss())
        .pipe(gulp.dest(sassCompile))
        .pipe(livereload(server));
});

gulp.task('scripts', function() {
   gulp.src(jsFiles)
    .pipe(concat('app.js'))
    .pipe(gulp.dest(jsCompile))
    .pipe(rename('app.min.js'))
    .pipe(uglify())
    .pipe(gulp.dest(jsCompile))
    .pipe(livereload(server));
});

gulp.task('headScripts', function() {
   gulp.src(jsHeadFiles)
    .pipe(concat('app-head.min.js'))
    .pipe(gulp.dest(jsCompile))
    .pipe(uglify())
    .pipe(gulp.dest(jsCompile))
    .pipe(livereload(server));
});

gulp.task('watch', function() {
    server.listen(35729, function (err) {
        if (err) {
          return console.log(err);
        }
        gulp.watch(sassFiles, ['styles']);
        gulp.watch(jsFiles, ['scripts']);
        gulp.watch(jsHeadFiles, ['headScripts']);
    });
});

gulp.task('default', ['styles', 'scripts', 'headScripts']);
