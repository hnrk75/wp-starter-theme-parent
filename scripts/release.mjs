/**
 * Release a new theme version: build, bump the version and move Unreleased in CHANGELOG.md.
 *
 * Usage: npm run release -- patch | minor | major | 1.2.3
 */

import { execSync } from 'node:child_process';
import { readFileSync, writeFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const file = (name) => path.join(root, name);
const fail = (message) => {
  console.error(`\n✗ ${message}\n`);
  process.exit(1);
};

// --- Work out the new version ---------------------------------
const pkg = JSON.parse(readFileSync(file('package.json'), 'utf8'));
const current = pkg.version;
const bump = process.argv[2];

if (!/^\d+\.\d+\.\d+$/.test(current)) {
  fail(`package.json has an invalid version: ${current}`);
}

const [major, minor, patch] = current.split('.').map(Number);
const next = {
  major: `${major + 1}.0.0`,
  minor: `${major}.${minor + 1}.0`,
  patch: `${major}.${minor}.${patch + 1}`,
}[bump] ?? (/^\d+\.\d+\.\d+$/.test(bump ?? '') ? bump : null);

if (!next) {
  fail('Usage: npm run release -- patch | minor | major | 1.2.3');
}

const isNewer = next.split('.').map(Number).reduce(
  (result, part, i) => result ?? (part === [major, minor, patch][i] ? null : part > [major, minor, patch][i]),
  null
);

if (!isNewer) {
  fail(`${next} is not newer than the current version ${current}.`);
}

// --- CHANGELOG.md ---------------------------------------------
const changelog = readFileSync(file('CHANGELOG.md'), 'utf8');
const unreleased = changelog.match(/## \[Unreleased\]\n([\s\S]*?)(?=\n## \[)/);

if (!unreleased || !unreleased[1].trim()) {
  fail('The "Unreleased" section in CHANGELOG.md is empty. Describe the changes first.');
}

const today = new Date().toISOString().slice(0, 10);

// --- Production build -----------------------------------------
console.log('\nBuilding assets (npm run build)…\n');
try {
  execSync('npm run build', { cwd: root, stdio: 'inherit' });
} catch {
  fail('The build failed — nothing was changed. Fix the errors and run the release again.');
}

// --- Write all files ------------------------------------------
writeFileSync(
  file('CHANGELOG.md'),
  changelog.replace('## [Unreleased]\n', `## [Unreleased]\n\n## [${next}] - ${today}\n`)
);

const style = readFileSync(file('style.css'), 'utf8');
if (!/^Version:\s*.+$/m.test(style)) {
  fail('No "Version:" line found in style.css.');
}
writeFileSync(file('style.css'), style.replace(/^(Version:\s*).+$/m, `$1${next}`));

pkg.version = next;
writeFileSync(file('package.json'), `${JSON.stringify(pkg, null, 2)}\n`);

try {
  const lock = JSON.parse(readFileSync(file('package-lock.json'), 'utf8'));
  lock.version = next;
  if (lock.packages?.['']) {
    lock.packages[''].version = next;
  }
  writeFileSync(file('package-lock.json'), `${JSON.stringify(lock, null, 2)}\n`);
} catch {
  // No package-lock.json — nothing to update.
}

console.log(`
✓ Version ${current} → ${next}
  Built:   assets/css, assets/js/dist
  Updated: style.css, package.json, package-lock.json, CHANGELOG.md

Next steps:
  1. Check the changes:   git status && git diff
  2. Commit on dev:       git add -A && git commit -m "release: v${next}" && git push
  3. On GitHub:           PR dev → main, merge with "Create a merge commit" (not squash)
  4. Automatic:           the "Tag release" action tags v${next} on main — do not tag by hand
  5. Check:               v${next} appears under Tags on GitHub
`);
