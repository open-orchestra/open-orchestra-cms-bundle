module.exports = function(grunt) {

    grunt.registerTask('navigation:compile', 'Open Orchestra task to find template underscore and compile it', function () {
        var config = grunt.config('application.config');

        var navigationConfigsFile = [];
        grunt.util._.each(config.application.bundles, function (value) {
            navigationConfigsFile.push('web/bundles/' + value + '/config/navigation.json')
        });
        navigationConfigsFile = grunt.file.expand(navigationConfigsFile);

        var navigationConfigs = mergeNavigationConfiguration(navigationConfigsFile);
        navigationConfigs = sortNavigation(navigationConfigs);

        var navigationConfigsJavascript = convertConfigToJavascript(navigationConfigs);

        var destFile = config.application.dest.navigation + 'navigation/navigation.js';
        grunt.file.write(destFile, navigationConfigsJavascript);
        grunt.log.write('File "' + destFile + '" created.');

    });

    /**
     * @param {Array} navigationConfigsFile
     *
     * @returns {Object}
     */
    var mergeNavigationConfiguration = function(navigationConfigsFile) {
        var json = {};
        navigationConfigsFile.forEach(function (navigationFile) {
            if (!grunt.file.exists(navigationFile))
                throw "JSON source file \"" + navigationFile + "\" not found.";
            else {
                try { var fragment = grunt.file.readJSON(navigationFile); }
                catch (e) { grunt.fail.warn(e); }
                json = grunt.util._.merge(json, fragment, {});
            }
        });

        return json;
    };

    /**
     * @param {Object} navigationConfigs
     *
     * @returns {Object}
     */
    var sortNavigation = function(navigationConfigs) {
        grunt.util._.forEach(navigationConfigs, function(value, key) {
            value = grunt.util._.sortBy(value, function(o) { return o.rank; });
            value = grunt.util._.filter(value, function(o) {return true !== o.disabled});
            navigationConfigs[key] = value;
        });

        return navigationConfigs;
    };

    /**
     * @param {Object} navigationConfigs
     *
     * @returns {String}
     */
    var convertConfigToJavascript = function(navigationConfigs) {
        var contentJavascript = '';
            contentJavascript += 'this["Orchestra"] = this["Orchestra"] || {};\n';
            contentJavascript += 'this["Orchestra"]["Config"] = this["Orchestra"]["Config"] || {};\n';
            contentJavascript += 'this["Orchestra"]["Config"]["Navigation"] = this["Orchestra"]["Config"]["Navigation"] || {};\n';
            contentJavascript += 'this["Orchestra"]["Config"]["Navigation"] =' + JSON.stringify(navigationConfigs) + ';';

        return contentJavascript;
    }
};


