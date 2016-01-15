module.exports = function(grunt) {
  grunt.registerTask('coffee:discovering', 'Open Orchestra task to find and compile coffee scripts', function() {
    var mappingFileCoffee = grunt.file.expandMapping(
      ['*/coffee/*.coffee', '*/coffee/*/*.coffee', '*/coffee/*/*/*.coffee', '*/coffee/*/*/*/*.coffee'],
      'web/built/',
      {
        cwd: 'web/bundles/',
        rename: function(dest, matchedSrcPath, options) {
          return dest + matchedSrcPath.replace(/coffee/g, 'js');
        }
      }
    );

    var filesCoffee = {};
    grunt.util._.each(mappingFileCoffee, function(value) {
      filesCoffee[value.dest] = value.src[0];
    });

    var coffeeConfig = {
      compile: {
        options: {
          bare: true
        },
        files: filesCoffee
      }
    };

    grunt.config('coffee', coffeeConfig);
  });
};
