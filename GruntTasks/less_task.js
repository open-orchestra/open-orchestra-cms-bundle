module.exports = function(grunt) {
  grunt.registerTask('less:discovering', 'Open Orchestra task to find and compile less files', function() {
    var mappingFileLess = grunt.file.expandMapping(
      ['*/less/*.less', '*/less/*/*.less'],
      'web/built/',
      {
        cwd: 'web/bundles/',
        rename: function(dest, matchedSrcPath, options) {
          return dest + matchedSrcPath.replace(/less/g, 'css');
        }
      }
    );

    var filesLess = {};
    grunt.util._.each(mappingFileLess, function(value) {
      filesLess[value.dest] = value.src[0];
    });

    var lessConfig = {
      compile: {
        files: filesLess
      }
    };

    grunt.config('less', lessConfig);
  });
};
