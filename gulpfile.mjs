// Disable OS popups from gulp-notify
process.env.DISABLE_NOTIFIER = 'true';

import gulp from 'gulp';
import plumber from 'gulp-plumber';
import autoprefixer from 'gulp-autoprefixer';
import eslint from 'gulp-eslint-new';
import terser from 'gulp-terser';
import rename from 'gulp-rename';
import notify from 'gulp-notify';
import gulpSass from 'gulp-sass';
import * as dartSass from 'sass';
import browserSyncLib from 'browser-sync';
import zip from 'gulp-zip';

import path from 'path';
import { exec as _exec, spawn } from 'child_process';
import { fileURLToPath } from 'url';
import { promisify } from 'util';

const exec = promisify(_exec);

const sass = gulpSass(dartSass);
const browserSync = browserSyncLib.create();

const isProduction = process.env.NODE_ENV === 'production';

const config = {
  jsFiles: ['./assets/js/src/**/*.js'],
  cssFiles: ['./assets/scss/**/*.scss', './inc/**/*.scss'],
  browserSyncWatchFiles: ['./assets/css/*.min.css', './assets/js/dist/*.min.js', './**/*.php'],
  proxyUrl: process.env.LOCAL_URL || 'http://wp-starter-theme.local/',
};

// ==== Helpers ==================================================
function onError(err) {
  console.error('An error occurred:', err.message);
  this.emit('end');
}

function bsNotify(title, message, timeout = 5000) {
  if (isProduction) {
    return;
  }
  try {
    browserSync.notify(`<strong>${title}</strong><br>${message}`, timeout);
  } catch {
    /* noop */
  }
}

let phpTimer;
const debounce = (fn, delay = 150) => (...args) => {
  clearTimeout(phpTimer);
  phpTimer = setTimeout(() => fn(...args), delay);
};

const scssBounce = new Map();
function debounceScss(filePath, fn, delay = 150) {
  const prev = scssBounce.get(filePath);
  if (prev) {
    clearTimeout(prev);
  }
  const t = setTimeout(() => {
    scssBounce.delete(filePath);
    fn();
  }, delay);
  scssBounce.set(filePath, t);
}

// ==== JavaScript ===============================================
export function lintJS() {
  return gulp
    .src('./assets/js/src/**/*.js', { allowEmpty: true })
    .pipe(eslint())
    .pipe(eslint.format())
    .pipe(eslint.failAfterError());
}

export function scripts() {
  let pipeline = gulp
    .src('./assets/js/src/**/*.js', { allowEmpty: true })
    .pipe(rename({ basename: 'scripts' }));

  if (isProduction) {
    pipeline = pipeline.pipe(terser());
  }

  pipeline = pipeline.pipe(rename({ suffix: '.min' }));
  pipeline = pipeline.pipe(gulp.dest('./assets/js/dist'));

  return pipeline.pipe(browserSync.stream()).on('end', () => bsNotify('Scripts', 'Klar'));
}

// ==== Sass/CSS =================================================
export function lintSCSSAll() {
  return new Promise((resolve, reject) => {
    const args = ['stylelint', 'assets/scss/**/*.scss', 'inc/**/*.scss', '--custom-syntax', 'postcss-scss'];
    const child = spawn('npx', args, { stdio: 'inherit', shell: false });
    child.on('close', (code) => {
      if (code === 0) {
        bsNotify('Stylelint', 'Allt ser bra ut! ✅', 2500);
        resolve();
      } else {
        bsNotify('Stylelint', 'Lint-fel hittades – se terminalen.');
        reject(new Error(`stylelint exited ${code}`));
      }
    });
  });
}

function lintScssFile(filePath) {
  return new Promise((resolve) => {
    const args = ['stylelint', filePath, '--custom-syntax', 'postcss-scss'];
    const child = spawn('npx', args, { stdio: 'inherit', shell: false });
    child.on('close', (code) => {
      if (code === 0) {
        bsNotify('Stylelint', `OK: ${path.basename(filePath)} ✅`, 2000);
      } else {
        bsNotify('Stylelint', `Fel i ${path.basename(filePath)} – se terminalen.`);
      }
      resolve();
    });
  });
}

export function compileSass() {
  let pipeline = gulp
    .src('./assets/scss/style.scss', { allowEmpty: true })
    .pipe(plumber({ errorHandler: onError }));

  pipeline = pipeline
    .pipe(
      sass({
        outputStyle: isProduction ? 'compressed' : 'expanded',
      }).on('error', sass.logError)
    )
    .pipe(autoprefixer())
    .pipe(gulp.dest('./assets/css/'))
    .pipe(rename({ suffix: '.min' }));

  pipeline = pipeline.pipe(gulp.dest('./assets/css/'));

  return pipeline.pipe(browserSync.stream()).on('end', () => bsNotify('Sass', 'Klar'));
}

export function compileEditorSass() {
  let pipeline = gulp
    .src('./assets/scss/editor-style.scss', { allowEmpty: true })
    .pipe(plumber({ errorHandler: onError }));

  pipeline = pipeline
    .pipe(
      sass({
        outputStyle: isProduction ? 'compressed' : 'expanded',
      }).on('error', sass.logError)
    )
    .pipe(autoprefixer())
    .pipe(rename({ basename: 'editor-style' }))
    .pipe(gulp.dest('./assets/css/'));

  return pipeline.pipe(browserSync.stream()).on('end', () => bsNotify('Editor Sass', 'Klar'));
}

// ==== Zip ======================================================
export function zipFiles() {
  return gulp
    .src(
      [
        '**/*',
        '!node_modules/**',
        '!*.zip',
        '!gulpfile.mjs',
        '!package.json',
        '!package-lock.json',
        '!scripts/**',
        '!.npmrc',
        '!.nvmrc',
        '!.editorconfig',
        '!eslint.config.mjs',
        '!.prettierrc.json',
        '!.stylelintrc.json',
        '!assets/js/dist/**/*.map',
      ],
      { base: '.' }
    )
    .pipe(zip('wp-starter-theme.zip'))
    .pipe(gulp.dest('.'));
}

// ==== PHP Lint & Fix ===========================================
const __filename = fileURLToPath(import.meta.url);
const THEME_DIR = path.dirname(__filename);
const ROOT_DIR = path.resolve(THEME_DIR, '../../../..');

const PHPCS =
  process.platform === 'win32'
    ? path.join(ROOT_DIR, 'vendor/bin/phpcs.bat')
    : path.join(ROOT_DIR, 'vendor/bin/phpcs');
const PHPCBF =
  process.platform === 'win32'
    ? path.join(ROOT_DIR, 'vendor/bin/phpcbf.bat')
    : path.join(ROOT_DIR, 'vendor/bin/phpcbf');

export async function lintPHPAll() {
  try {
    const { stdout, stderr } = await exec(`"${PHPCS}" -p -s web/app/themes/wp-starter-theme`, { cwd: ROOT_DIR });
    process.stdout.write(stdout || '');
    process.stderr.write(stderr || '');
    bsNotify('PHPCS', 'Allt ser bra ut! ✅');
  } catch (e) {
    process.stdout.write(e.stdout || '');
    process.stderr.write(e.stderr || e.message || '');
    bsNotify('PHPCS', 'Lint-fel hittades – se terminalen.');
  }
}

async function lintPHPFile(filePath) {
  const rel = path.relative(ROOT_DIR, filePath);
  try {
    const { stdout, stderr } = await exec(`"${PHPCS}" -p -s "${rel}"`, { cwd: ROOT_DIR });
    console.log(`\n▶ PHPCS OK: ${rel}\n${stdout}${stderr}`);
    bsNotify('PHPCS', `OK: ${rel} ✅`, 2500);
  } catch (e) {
    console.log(`\n▶ PHPCS ERR: ${rel}\n${e.stdout || ''}${e.stderr || e.message || ''}`);
    bsNotify('PHPCS', `Fel i ${rel} – se terminalen.`);
  }
}

async function fixPHPFile(filePath) {
  const rel = path.relative(ROOT_DIR, filePath);
  try {
    await exec(`"${PHPCBF}" "${rel}"`, { cwd: ROOT_DIR });
  } catch {
    /* auto-fix may fail — that is OK */
  }
  await lintPHPFile(filePath);
}

export async function fixPHPAll() {
  try {
    await exec(`"${PHPCBF}" web/app/themes/wp-starter-theme`, { cwd: ROOT_DIR });
  } catch {
    /* ignore */
  }
  await lintPHPAll();
}

// ==== Watchers =================================================
export function watchFiles() {
  gulp.watch(config.jsFiles, gulp.series(lintJS, scripts));

  const scssWatcher = gulp.watch(config.cssFiles);
  scssWatcher.on('change', (fp) =>
    debounceScss(fp, async () => {
      await lintScssFile(path.resolve(fp));
      compileSass();
      if (fp.includes('gutenberg') || fp.includes('editor-style')) {
        compileEditorSass();
      }
    })
  );
  scssWatcher.on('add', (fp) =>
    debounceScss(fp, async () => {
      await lintScssFile(path.resolve(fp));
      compileSass();
      if (fp.includes('gutenberg') || fp.includes('editor-style')) {
        compileEditorSass();
      }
    })
  );

  const phpGlob = ['./**/*.php', '!./node_modules/**', '!./vendor/**', '!./**/dist/**'];
  const w = gulp.watch(phpGlob);
  w.on('change', (fp) => debounce(() => fixPHPFile(path.resolve(fp)))());
  w.on('add', (fp) => debounce(() => fixPHPFile(path.resolve(fp)))());
}

// ==== BrowserSync ==============================================
export function startBrowserSync() {
  browserSync.init({
    proxy: config.proxyUrl,
    files: config.browserSyncWatchFiles,
  });
}

// ==== Build & Default ==========================================
export const build = gulp.series(gulp.parallel(compileSass, compileEditorSass), scripts);
export default gulp.parallel(watchFiles, startBrowserSync);
