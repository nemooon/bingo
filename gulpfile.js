var gulp = require('gulp');
var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

gulp.task('bower', function () {
  gulp.src('bower_components/bootstrap/dist/js/**/*').pipe(gulp.dest('public/js'));
  gulp.src('bower_components/jquery/dist/**/*').pipe(gulp.dest('public/js'));
  gulp.src('bower_components/font-awesome/fonts/**/*').pipe(gulp.dest('public/fonts'));
  gulp.src('bower_components/font-awesome/css/**/*').pipe(gulp.dest('public/css'));
});

elixir(function(mix) {
    mix.sass('app.scss');
    mix.task('bower');
    mix.copy('resources/assets/sound', 'public/sound');
});


