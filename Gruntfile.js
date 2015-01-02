/* jshint node:true */
module.exports = function( grunt ){
	'use strict';

	grunt.initConfig({

		// Setting folder templates.
		dirs: {
			fonts: 'assets/fonts',
			images: 'assets/images',
			styles: 'assets/styles',
			scripts: 'assets/scripts'
		},

		// JavaScript linting with JSHint.
		jshint: {
			options: {
				jshintrc: '.jshintrc'
			},
			all: [
				'Gruntfile.js',
				'<%= dirs.scripts %>/admin/*.js',
				'!<%= dirs.scripts %>/admin/*.min.js',
				'<%= dirs.scripts %>/modal/*.js',
				'!<%= dirs.scripts %>/modal/*.min.js',
				'<%= dirs.scripts %>/frontend/*.js',
				'!<%= dirs.scripts %>/frontend/*.min.js'
			]
		},

		// Minify .js files.
		uglify: {
			options: {
				preserveComments: 'some'
			},
			admin: {
				files: [{
					expand: true,
					cwd: '<%= dirs.scripts %>/admin/',
					src: [
						'*.js',
						'!*.min.js'
					],
					dest: '<%= dirs.scripts %>/admin/',
					ext: '.min.js'
				}]
			},
			modal: {
				files: [{
					expand: true,
					cwd: '<%= dirs.scripts %>/modal/',
					src: [
						'*.js',
						'!*.min.js'
					],
					dest: '<%= dirs.scripts %>/modal/',
					ext: '.min.js'
				}]
			},
			frontend: {
				files: [{
					expand: true,
					cwd: '<%= dirs.scripts %>/frontend/',
					src: [
						'*.js',
						'!*.min.js'
					],
					dest: '<%= dirs.scripts %>/frontend/',
					ext: '.min.js'
				}]
			}
		},

		// Compile all .scss files.
		sass: {
			options: {
				sourcemap: 'none',
				loadPath: require( 'node-bourbon' ).includePaths
			},
			compile: {
				files: [{
					expand: true,
					cwd: '<%= dirs.styles %>/',
					src: ['*.scss'],
					dest: '<%= dirs.styles %>/',
					ext: '.css'
				}]
			}
		},

		// Minify all .css files.
		cssmin: {
			minify: {
				expand: true,
				cwd: '<%= dirs.styles %>/',
				src: ['*.css'],
				dest: '<%= dirs.styles %>/',
				ext: '.css'
			}
		},

		// Watch changes for assets.
		watch: {
			styles: {
				files: [
					'<%= dirs.styles %>/*.scss'
				],
				tasks: ['sass', 'cssmin']
			},
			scripts: {
				files: [
					'<%= dirs.scripts %>/admin/*.js',
					'<%= dirs.scripts %>/modal/*.js',
					'<%= dirs.scripts %>/frontend/*.js',
					'!<%= dirs.scripts %>/admin/*.min.js',
					'!<%= dirs.scripts %>/modal/*.min.js',
					'!<%= dirs.scripts %>/frontend/*.min.js'
				],
				tasks: ['uglify']
			}
		},

		// Generate POT files.
		makepot: {
			options: {
				type: 'wp-plugin',
				domainPath: 'i18n/languages',
				potHeaders: {
					'report-msgid-bugs-to': 'https://github.com/axisthemes/axis-builder/issues',
					'language-team': 'LANGUAGE <EMAIL@ADDRESS>'
				}
			},
			frontend: {
				options: {
					potFilename: 'axisbuilder.pot',
					exclude: [
						'api/.*',
						'tmp/.*',
						'tests/.*',
						'includes/admin/.*'
					],
					processPot: function ( pot ) {
						pot.headers['project-id-version'] += ' Frontend';
						return pot;
					}
				}
			},
			admin: {
				options: {
					potFilename: 'axisbuilder-admin.pot',
					include: [
						'includes/admin/.*'
					],
					processPot: function ( pot ) {
						pot.headers['project-id-version'] += ' Admin';
						return pot;
					}
				}
			}
		},

		// Check textdomain errors.
		checktextdomain: {
			options:{
				text_domain: 'axisbuilder',
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				]
			},
			files: {
				src: [
					'**/*.php',
					'!api/**',
					'!tmp/**',
					'!tests/**',
					'!node_modules/**'
				],
				expand: true
			}
		}
	});

	// Load NPM tasks to be used here
	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-checktextdomain' );
	grunt.loadNpmTasks( 'grunt-contrib-jshint' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-sass' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );

	// Register tasks
	grunt.registerTask( 'default', [
		'css',
		'uglify'
	]);

	grunt.registerTask( 'css', [
		'sass',
		'cssmin'
	]);

	grunt.registerTask( 'dev', [
		'default',
		'makepot'
	]);
};
