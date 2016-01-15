module.exports = function(grunt) {
  grunt.registerTask(
    'javascriptProd',
    'Main Open Orchestra task to minify javascripts',
    ['uglify:all_js']
  );
};
