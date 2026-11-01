import js from '@eslint/js';
import prettier from 'eslint-config-prettier';
import globals from 'globals';

export default [
	// Ignored files (replaces .eslintignore)
	{
		ignores: [
			'node_modules/**',
			'vendor/**',
			'assets/js/dist/**',
			'**/*.min.js',
			'**/rev-manifest*.json',
		],
	},

	// Rekommenderade grundregler
	js.configs.recommended,

	// Theme JS — runs in the browser
	{
		files: ['assets/js/**/*.js'],
		languageOptions: {
			ecmaVersion: 2022,
			sourceType: 'module',
			globals: {
				...globals.browser,
				...globals.jquery,
				wp: 'readonly',
				ajaxurl: 'readonly',
			},
		},
		rules: {
			'no-console': 'warn',
			'no-debugger': 'warn',
			'no-unused-vars': ['warn', { argsIgnorePattern: '^_', varsIgnorePattern: '^_' }],
			eqeqeq: ['error', 'smart'],
			curly: ['error', 'all'],
			'no-var': 'error',
			'prefer-const': ['error', { destructuring: 'all' }],
			'prefer-template': 'warn',
			'no-implicit-globals': 'error',
			'no-undef-init': 'error',
			'no-useless-escape': 'warn',
			'no-shadow': 'warn',
			'no-param-reassign': ['warn', { props: false }],
			radix: ['error', 'as-needed'],
			'consistent-return': 'warn',
		},
	},

	// Node environment — gulpfile and config files
	{
		files: ['gulpfile.mjs', '*.config.mjs', '*.config.js'],
		languageOptions: {
			globals: {
				...globals.node,
			},
		},
		rules: {
			'no-console': 'off',
			'no-unused-vars': ['warn', { argsIgnorePattern: '^_', varsIgnorePattern: '^_' }],
		},
	},

	// Prettier — must be last (disables formatting rules)
	prettier,
];
