const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const postcss = require('gulp-postcss');
const autoprefixer = require('autoprefixer');
const cleanCSS = require('gulp-clean-css');
const sourcemaps = require('gulp-sourcemaps');
const browserSync = require('browser-sync').create();
const uglify = require('gulp-uglify');
const concat = require('gulp-concat');
const rename = require('gulp-rename');
const { createGulpEsbuild } = require('gulp-esbuild');
const gulpEsbuild = createGulpEsbuild({ incremental: false });
// const esbuild = require('gulp-esbuild');

// Paths
const paths = {
  styles: {
    src: 'assets/scss/**/*.scss',
    dest: 'public/style'
  },
  script: {
    entry: 'assets/js/main.js',
    watch: 'assets/js/**/*.js',   // <— watch all JS under assets/js
    dest: 'public/script'
  },
  adminscript: {
    entry: 'assets/js/admin.js',
    watch: 'assets/js/**/*.js',   // <— or 'assets/js/admin/**' if you prefer
    dest: 'public/script'
  }
};


function styles() {
  return gulp.src(paths.styles.src)
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(postcss([autoprefixer()]))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(paths.styles.dest))
    .pipe(browserSync.stream());
}

function scripts() {
  return gulp.src(paths.script.entry)
    .pipe(gulpEsbuild({
      bundle: true,
      format: 'iife',
      outfile: 'main.js',
      sourcemap: true,
      target: ['es2018'],
      minify: true
    }))
    .pipe(gulp.dest(paths.script.dest))
    .pipe(browserSync.stream());
}

function admin_scripts() {
  return gulp.src(paths.adminscript.entry)
    .pipe(gulpEsbuild({
      bundle: true,
      format: 'iife',
      outfile: 'admin.js',
      sourcemap: true,
      target: ['es2018'],
      minify: true
    }))
    .pipe(gulp.dest(paths.adminscript.dest))
    .pipe(browserSync.stream());
}



function watch() {
  browserSync.init({ proxy: 'http://booktheme.local/' });

  gulp.watch(paths.styles.src, styles);

  // Avoid watching the build output to prevent loops:
  gulp.watch([paths.script.watch, '!public/script/**'], scripts);
  gulp.watch([paths.adminscript.watch, '!public/script/**'], admin_scripts);

  gulp.watch('**/*.php').on('change', browserSync.reload);
}

exports.dev = gulp.series(gulp.parallel(styles, scripts, admin_scripts), watch);
exports.default = exports.dev;
