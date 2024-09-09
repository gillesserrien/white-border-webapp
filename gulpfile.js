const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const cleanCSS = require('gulp-clean-css');
const imagemin = require('gulp-imagemin');
const terser = require('gulp-terser');
const concat = require('gulp-concat');
const sourcemaps = require('gulp-sourcemaps');
const autoprefixer = require('gulp-autoprefixer');
const browserSync = require('browser-sync').create();

const paths = {
  scss: 'theme/main.scss', // Point to main.scss
  js: 'theme/**/*.js',
  images: 'theme/**/*.{png,jpg,jpeg,gif,svg}',
  html: 'public/**/*.html'
};

gulp.task('styles', function() {
  return gulp.src(paths.scss)
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(autoprefixer())
    .pipe(cleanCSS())
    .pipe(gulp.dest('public/resources/css')) // Save to public/resources/css
    .pipe(browserSync.stream());
});

gulp.task('scripts', function() {
  return gulp.src(paths.js)
    .pipe(sourcemaps.init())
    .pipe(concat('index.js')) // Concatenate all JS into one file
    .pipe(terser())
    .pipe(gulp.dest('public/resources/js')) // Save to public/resources/js
    .pipe(browserSync.stream());
});

gulp.task('images', function() {
  return gulp.src(paths.images)
    .pipe(imagemin())
    .pipe(gulp.dest('public/resources/')); // Save to public/resources/images
});

gulp.task('html', function() {
  return gulp.src(paths.html)
    .pipe(browserSync.stream());
});

gulp.task('serve', function() {
  browserSync.init({
    server: {
      baseDir: 'public'
    }
  });

  gulp.watch('theme/**/*.scss', gulp.series('styles'));
  gulp.watch(paths.js, gulp.series('scripts'));
  gulp.watch(paths.images, gulp.series('images'));
  gulp.watch(paths.html).on('change', browserSync.reload);
});

gulp.task('dev', gulp.series('styles', 'scripts', 'images', 'serve'));

gulp.task('build', gulp.series('styles', 'scripts', 'images'));




