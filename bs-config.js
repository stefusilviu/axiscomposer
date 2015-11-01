/**
 * Browser-sync config file
 * @see http://www.browsersync.io/docs/options/
 */
module.exports = {
	proxy: {
		target: 'localhost/axiscomposer',
		proxyRes: [
			function ( res ) {
				res.headers['Expires'] = '0';
				res.headers['Cache-Control'] = 'no-cache, no-store, must-revalidate';
				res.headers["Pragma"] = "no-cache";
			}
		]
	},
	files: [
		'wp-content/themes/**/*.{css|js}',
		'wp-content/plugins/**/*.{css|js}'
	],
	logPrefix: 'AC'
};
