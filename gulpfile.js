var gulp    = require('gulp');
var watch   = require('gulp-watch');
var sass    = require('gulp-ruby-sass');
var uglify  = require('gulp-uglifyjs');

var cssDir  = 'assets/scss';
var jsDir   = 'js';

var jsListUser = 
[
	''
];
  
var jsListAdmin = 
[
    '',
    ''
];

gulp.task('watch', function () 
{
	gulp.watch(cssDir + '/**/*.scss', ['css']);
	gulp.watch(jsDir + '/**/*.js', ['assets/js']);
}); 

gulp.task('css', function () 
{
  	return sass(cssDir, {style: 'compressed'})
    .pipe(gulp.dest('./public/css'));    
});

gulp.task('js', function () 
{
  	gulp.src(jsListUser)
    .pipe(uglify('mtr-user.js'))
    .pipe(gulp.dest('./public/js'));

    gulp.src(jsListAdmin)
    .pipe(uglify('mtr-admin.js'))
    .pipe(gulp.dest('./public/js'));
});


gulp.task('default', ['css', 'js']); 