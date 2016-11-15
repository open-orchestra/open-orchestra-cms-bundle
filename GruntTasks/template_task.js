module.exports = function(grunt) {

    grunt.registerTask('template:compile', 'Open Orchestra task to find template underscore and compile it', function () {
        var config = grunt.config('application.config');

        var templates = {};
        grunt.util._.each(config.application.bundles, function (value) {
            var mappingFile = grunt.file.expand('web/bundles/' + value + '/template/**/*._tpl.html');
            mappingFile.forEach(function(file){
                var name = file.substring(file.indexOf('template'));
                templates[name] = file;
            });
        });

        //convert to array
        templates = Object.keys(templates).map(function (key) { return templates[key]; });
        var dest = config.application.dest.template + 'template/templates.js';

        var files = {};
        files[dest] = templates;

        var jstConfig =  {
            compile: {
                options: {
                    namespace: 'Orchestra.Template',
                    processName: function(filepath) {
                        return filepath.substring(filepath.indexOf('template'));
                    }
                },
                files: files
            }
        };

        grunt.config('jst', jstConfig);
    });
};
