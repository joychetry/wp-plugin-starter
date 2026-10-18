/**
 * wp-scripts config with explicit entries. Script modules (front end) go in
 * `modules`; classic scripts (wp-admin) go in `scripts`.
 */
const config = require( '@wordpress/scripts/config/webpack.config' );
const [ , modules ] = config;

module.exports = [
	{ ...modules, entry: { example: './assets-src/frontend/example.js' } },
];
