module.exports = function(grunt) {

    grunt.registerTask('babel:config', 'Open Orchestra task to find and compile es6 scripts', function () {
        var config = grunt.config('babel.config');

        // Babel
        var patternsEs6 = [];
        grunt.util._.each(config.babel.bundles, function (value) {
            patternsEs6.push(value + '/ecmascript/**/*.js')
        });

        var mappingFileEs6 = grunt.file.expandMapping(
            patternsEs6,
            config.babel.dest,
            {
                cwd: 'web/bundles/',
                rename: function (dest, matchedSrcPath) {
                    var path = matchedSrcPath.substring(matchedSrcPath.indexOf('ecmascript'));
                    return dest + path.replace(/ecmascript/g, 'js');
                }
            }
        );

        var filesEs6 = {};
        grunt.util._.each(mappingFileEs6, function (value) {
            filesEs6[value.dest] = value.src[0];
        });

        var babelConfig = {es6: {options: {presets: ['es2015']}, files: filesEs6}};
        grunt.config('babel', babelConfig);
    });
};
