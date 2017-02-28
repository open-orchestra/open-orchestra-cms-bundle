module.exports = function(grunt) {
    grunt.loadNpmTasks('grunt-js-test');

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
    grunt.registerTask('test', ['js-test']);
    grunt.registerTask('default', ['test']);
};
