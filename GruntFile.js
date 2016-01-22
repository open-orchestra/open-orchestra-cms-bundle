module.exports = function(grunt) {
    grunt.loadNpmTasks('grunt-contrib-coffee');
    grunt.loadNpmTasks('grunt-js-test');

    grunt.config('coffee', {
        options: {
            bare: true
        },
        files: {
            expand: true,
            cwd: './',
            src: [
                '*Bundle/Resources/public/coffee/**/*.coffee'
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


    var patternPath = '.'+__dirname.replace(process.cwd(), '')+'/';
    grunt.config('js-test', {
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
    );
    grunt.registerTask('test', ['coffee', 'js-test']);
    grunt.registerTask('default', ['test']);

};
