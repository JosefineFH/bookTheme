const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const postcss = require('gulp-postcss');
const autoprefixer = require('autoprefixer');
const cleanCSS = require('gulp-clean-css');
const sourcemaps = require('gulp-sourcemaps');
const browserSync = require('browser-sync').create();

// Paths
const paths = {
    styles: {
        src: 'assets/scss/**/*.scss',
        dest: 'styles'
    }
};

// Compile SCSS
function styles() {
    return gulp.src(paths.styles.src)
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(postcss([autoprefixer()])) // ✅ Use PostCSS with Autoprefixer
        .pipe(cleanCSS())
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(paths.styles.dest))
        .pipe(browserSync.stream());
}

// Watch files
function watch() {
    browserSync.init({
        proxy: "http://test.local/" // Replace with your local WP URL
    });
    gulp.watch(paths.styles.src, styles);
    gulp.watch("**/*.php").on('change', browserSync.reload);
}

// Tasks
exports.styles = styles;
exports.watch = watch;
exports.default = gulp.series(styles, watch);
