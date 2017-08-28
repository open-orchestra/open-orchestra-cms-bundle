module.exports = function(grunt) {

    grunt.registerTask('browserify:config', 'Open Orchestra task to config browserify', function () {
        var config = grunt.config('browserify.config');
        // browserify
        var browserifyFile = grunt.file.expand(config.browserify.pattern);

        var browserifyConfig = {
            dist: {
                src: browserifyFile,
                dest: config.browserify.dest + 'oo_application.js',
                options: {
                    transform: [
                        ["aliasify", {
                            global: true,
                            replacements: {
                                "OpenOrchestra/(\\w+)": "./"+ config.browserify.dest + "openorchestra/js/OpenOrchestra/$1"
                            }
                        }]
                    ]
                }
            }
        };

        grunt.config('browserify', browserifyConfig);
    });
};
