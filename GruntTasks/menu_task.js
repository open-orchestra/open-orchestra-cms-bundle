module.exports = function(grunt) {

    grunt.registerTask('menu:compile', 'Open Orchestra task to find template underscore and compile it', function () {
        var config = grunt.config('application.config');

        var menuConfigsFile = [];
        grunt.util._.each(config.application.bundles, function (value) {
            menuConfigsFile.push('web/bundles/' + value + '/config/menu.json')
        });
        menuConfigsFile = grunt.file.expand(menuConfigsFile);

        var menuConfigs = mergeMenuConfiguration(menuConfigsFile);
        menuConfigs = sortMenu(menuConfigs);

        var menuConfigsJavascript = convertConfigToJavascript(menuConfigs);

        var destFile = config.application.dest.menu + 'menu/menu.js';
        grunt.file.write(destFile, menuConfigsJavascript);
        grunt.log.write('File "' + destFile + '" created.');

    });

    /**
     * @param {Array} menuConfigsFile
     *
     * @returns {Object}
     */
    var mergeMenuConfiguration = function(menuConfigsFile) {
        var json = {};
        menuConfigsFile.forEach(function (menuFile) {
            if (!grunt.file.exists(menuFile))
                throw "JSON source file \"" + menuFile + "\" not found.";
            else {
                try { var fragment = grunt.file.readJSON(menuFile); }
                catch (e) { grunt.fail.warn(e); }
                json = grunt.util._.merge(json, fragment, {});
            }
        });

        return json;
    };

    /**
     * @param {Object} menuConfigs
     *
     * @returns {Object}
     */
    var sortMenu = function(menuConfigs) {
        grunt.util._.forEach(menuConfigs, function(value, key) {
            value = grunt.util._.sortBy(value, function(o) { return o.rank; });
            value = grunt.util._.filter(value, function(o) {return true !== o.disabled});
            menuConfigs[key] = value;
        });

        return menuConfigs;
    };

    /**
     * @param {Object} menuConfigs
     *
     * @returns {String}
     */
    var convertConfigToJavascript = function(menuConfigs) {
        var contentJavascript = '';
            contentJavascript += 'this["Orchestra"] = this["Orchestra"] || {};\n';
            contentJavascript += 'this["Orchestra"]["Config"] = this["Orchestra"]["Config"] || {};\n';
            contentJavascript += 'this["Orchestra"]["Config"]["Menu"] = this["Orchestra"]["Config"]["Menu"] || {};\n';
            contentJavascript += 'this["Orchestra"]["Config"]["Menu"] =' + JSON.stringify(menuConfigs) + ';';

        return contentJavascript;
    }
};


