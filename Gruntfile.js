/* jshint node:true */
module.exports = function( grunt ){
	'use strict';

	grunt.initConfig({

		// Setting folder templates.
		dirs: {
			css: 'assets/css',
			fonts: 'assets/fonts',
			images: 'assets/images',
			js: 'assets/js'
		},

		// JavaScript linting with JSHint.
		jshint: {
			options: {
				jshintrc: '.jshintrc'
			},
			all: [
				'Gruntfile.js',
				'<%= dirs.js %>/admin/*.js',
				'!<%= dirs.js %>/admin/*.min.js',
				'<%= dirs.js %>/frontend/*.js',
				'!<%= dirs.js %>/frontend/*.min.js'
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
					cwd: '<%= dirs.js %>/admin/',
					src: [
						'*.js',
						'!*.min.js'
					],
					dest: '<%= dirs.js %>/admin/',
					ext: '.min.js'
				}]
			},
			frontend: {
				files: [{
					expand: true,
					cwd: '<%= dirs.js %>/frontend/',
					src: [
						'*.js',
						'!*.min.js'
					],
					dest: '<%= dirs.js %>/frontend/',
					ext: '.min.js'
				}]
			},
			vendor: {
				files: {
					'<%= dirs.js %>/jquery-blockui/jquery.blockUI.min.js': ['<%= dirs.js %>/jquery-blockui/jquery.blockUI.js'],
					'<%= dirs.js %>/jquery-serializejson/jquery.serializejson.min.js': ['<%= dirs.js %>/jquery-serializejson/jquery.serializejson.js'],
					'<%= dirs.js %>/jquery-tiptip/jquery.tipTip.min.js': ['<%= dirs.js %>/jquery-tiptip/jquery.tipTip.js'],
					'<%= dirs.js %>/select2/select2.min.js': ['<%= dirs.js %>/select2/select2.js'],
					'<%= dirs.js %>/gist-embed/gist-embed.min.js': ['<%= dirs.js %>/gist-embed/gist-embed.js'],
					'<%= dirs.js %>/stupidtable/stupidtable.min.js': ['<%= dirs.js %>/stupidtable/stupidtable.js'],
					'<%= dirs.js %>/zeroclipboard/jquery.zeroclipboard.min.js': ['<%= dirs.js %>/zeroclipboard/jquery.zeroclipboard.js']
				}
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
					cwd: '<%= dirs.css %>/',
					src: ['*.scss'],
					dest: '<%= dirs.css %>/',
					ext: '.css'
				}]
			}
		},

		// Minify all .css files.
		cssmin: {
			minify: {
				expand: true,
				cwd: '<%= dirs.css %>/',
				src: ['*.css'],
				dest: '<%= dirs.css %>/',
				ext: '.css'
			}
		},

		// Watch changes for assets.
		watch: {
			css: {
				files: [
					'<%= dirs.css %>/*.scss'
				],
				tasks: ['sass', 'cssmin']
			},
			js: {
				files: [
					'<%= dirs.js %>/admin/*.js',
					'<%= dirs.js %>/frontend/*.js',
					'!<%= dirs.js %>/admin/*.min.js',
					'!<%= dirs.js %>/frontend/*.min.js'
				],
				tasks: ['jshint', 'uglify']
			}
		},

		// Generate POT files.
		makepot: {
			options: {
				type: 'wp-plugin',
				domainPath: 'i18n/languages',
				potHeaders: {
					'report-msgid-bugs-to': 'https://github.com/axisthemes/axiscomposer/issues',
					'language-team': 'LANGUAGE <EMAIL@ADDRESS>'
				}
			},
			frontend: {
				options: {
					potFilename: 'axiscomposer.pot',
					exclude: [
						'tmp/.*',
						'tests/.*',
						'apigen/.*',
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
					potFilename: 'axiscomposer-admin.pot',
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
			options: {
				text_domain: 'axiscomposer',
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
					'!tmp/**',
					'!tests/**',
					'!apigen/**',
					'!node_modules/**'
				],
				expand: true
			}
		},

		// Exec shell commands.
		shell: {
			options: {
				stdout: true,
				stderr: true
			},
			apigen: {
				command: [
					'apigen generate',
					'cd apigen',
					'php hook-docs.php'
				].join( '&&' )
			}
		},

		// Clean the directory.
		clean: {
			apigen: {
				src: [ 'ac-apidocs' ]
			}
		}
	});

	// Load NPM tasks to be used here
	grunt.loadNpmTasks( 'grunt-shell' );
	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-checktextdomain' );
	grunt.loadNpmTasks( 'grunt-contrib-jshint' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-sass' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );
	grunt.loadNpmTasks( 'grunt-contrib-clean' );

	// Register tasks
	grunt.registerTask( 'default', [
		'jshint',
		'uglify',
		'css'
	]);

	grunt.registerTask( 'css', [
		'sass',
		'cssmin'
	]);

	grunt.registerTask( 'docs', [
		'clean:apigen',
		'shell:apigen'
	]);

	grunt.registerTask( 'dev', [
		'default',
		'makepot'
	]);
};
