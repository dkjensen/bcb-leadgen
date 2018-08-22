var gulp         = require('gulp');
var sass         = require('gulp-sass');
var concat       = require('gulp-concat'); // Concatenates JS files
var uglify       = require('gulp-uglify'); // Minifies JS files

gulp.task('sass', function () {
  return gulp
    // Find all `.scss` files from the `stylesheets/` folder
    .src('./assets/css/*.scss')
    // Run Sass on those files
    .pipe(sass({
        outputStyle: 'compressed'
    }))
    // Write the resulting CSS in the output folder
    .pipe(gulp.dest('./dist'));
});

// JavaScript
gulp.task('js', function() {
  return gulp.src([
      './node_modules/jquery-tabledit/jquery.tabledit.min.js',
      './assets/js/bcb-leadgen-admin.js'
    ])
    .pipe(concat('bcb-leadgen-admin.js'))
    .pipe(uglify())
    .pipe(gulp.dest('./dist'));
});

gulp.task('watch', function() {
    gulp.watch('./assets/css/*.scss', ['sass']);
    gulp.watch('./assets/js/*.js', ['js']);
});