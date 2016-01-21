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

    grunt.loadNpmTasks('grunt-mocha');
    grunt.config('mocha', {
        test: {
            src: [
                '*/Tests/_mocha/**/*.html'
            ],
            options: {
                run: true
            }
        }
    });

    grunt.registerTask('js-test', ['coffee', 'mocha'] );
    grunt.registerTask('default', 'js-test' );
};
