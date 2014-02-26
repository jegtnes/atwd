var sassFiles     = ['./bower_components/foundation/scss/normalize.scss'
                    ,'./bower_components/foundation/scss/foundation.scss'
                    ,'./scss/*.scss'],
    sassCompile   = './assets/css',
    jsFiles       = ['./bower_components/jquery/dist/jquery.js'
                    ,'./bower_components/foundation/js/foundation.js'
                    ,'./bower_components/Chart.js/Chart.js'
                    ,'./js/**/*.js'],
    jsHeadFiles   = './bower_components/modernizr/modernizr.js',
    jsCompile     = './assets/js',
    phpFiles      = './*.php';

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

gulp.task('php', function(){
    gulp.src(phpFiles)
    .pipe(livereload(server));
});

gulp.task('styles', function () {
    gulp.src(sassFiles)
        .pipe(concat('app.css'))
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
        gulp.watch(phpFiles, ['php']);
        gulp.watch(sassFiles, ['styles']);
        gulp.watch(jsFiles, ['scripts']);
        gulp.watch(jsHeadFiles, ['headScripts']);
    });
});

gulp.task('default', ['styles', 'scripts', 'headScripts']);
