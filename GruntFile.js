module.exports = function(grunt) {
    grunt.loadNpmTasks('grunt-contrib-coffee');
    grunt.config('coffee', {
        options: {
            bare: true
        },
        files: {
            expand: true,
            cwd: './',
            src: [
                '*Bundle/Resources/public/coffee/!**!/!*.coffee'
            ],
            dest: './',
            ext: '.js',
            rename: function(dest, src) {
                return src.replace(
                    /^([^\/]*)\/Resources\/public\/coffee\//,
                    '$1/Tests/_mocha/built_sources/js/'
                );
            }
        }
    });

    grunt.loadNpmTasks('grunt-js-test');
    var patternPath = '.'+__dirname.replace(process.cwd(), '')+'/';
    grunt.initConfig({
        'js-test': {
            options: {
                referenceTags: true,
                coverage: false,
                pattern: patternPath+'BackofficeBundle/Tests/_mocha/OrchestraBORouter/OrchestraBORouter-test.js',
                deps: [
                    "vendor/bower_components/jquery/dist/jquery.js",
                    "vendor/bower_components/underscore/underscore.js",
                    "vendor/bower_components/backbone/backbone.js"
                ]
            }
        }
    });
    grunt.registerTask('js-test', ['coffee']);
    grunt.registerTask('default', ['js-test']);

};
