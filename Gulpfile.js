var gulp = require('gulp');
var sass = require('gulp-sass');

gulp.task('sass', function () {
  return gulp
    // Find all `.scss` files from the `stylesheets/` folder
    .src('./assets/css/*.scss')
    // Run Sass on those files
    .pipe(sass({
        outputStyle: 'compressed'
    }))
    // Write the resulting CSS in the output folder
    .pipe(gulp.dest('./assets/css'));
});

gulp.task('watch', function() {
    return gulp
      // Watch the input folder for change,
      // and run `sass` task when something happens
      .watch('./assets/css/*.scss', ['sass'])
      // When there is a change,
      // log a message in the console
      .on('change', function(event) {
        console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
      });
  });