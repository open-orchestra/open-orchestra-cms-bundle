module.exports = function(grunt) {

    grunt.registerTask('navigation:compile', 'Open Orchestra task to find template underscore and compile it', function () {
        var config = grunt.config('application.config');

        var navigationConfigs = [];
        grunt.util._.each(config.application.bundles, function (value) {
            navigationConfigs.push('web/bundles/' + value + '/config/navigation.json')
        });
        navigationConfigs = grunt.file.expand(navigationConfigs);

        var json = {};
        // merge config
        navigationConfigs.forEach(function (navigationFile) {
            if (!grunt.file.exists(navigationFile))
                throw "JSON source file \"" + navigationFile + "\" not found.";
            else {
                try { var fragment = grunt.file.readJSON(navigationFile); }
                catch (e) { grunt.fail.warn(e); }
                json = grunt.util._.merge(json, fragment, {});
            }
        });

        // sort navigation
        grunt.util._.forEach(json, function(value, key) {
            value = grunt.util._.sortBy(value, function(o) { return o.rank; });
            value = grunt.util._.filter(value, function(o) {return true !== o.disabled});
            json[key] = value;
        });


        // json to javascript
        var contentJavascript = '';
            contentJavascript += 'this["Orchestra"] = this["Orchestra"] || {};\n';
            contentJavascript += 'this["Orchestra"]["Config"] = this["Orchestra"]["Config"] || {};\n';
            contentJavascript += 'this["Orchestra"]["Config"]["Navigation"] = this["Orchestra"]["Config"]["Navigation"] || {};\n';
            contentJavascript += 'this["Orchestra"]["Config"]["Navigation"] =' + JSON.stringify(json) + ';';

        var destFile = config.application.dest.navigation + 'navigation/navigation.js';
        grunt.file.write(destFile, contentJavascript);
        grunt.log.write('File "' + destFile + '" created.');

    });
};
