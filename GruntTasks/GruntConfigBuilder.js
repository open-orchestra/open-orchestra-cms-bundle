module.exports = {
  merge: null,
  glob: null,

  init: function(grunt, appConfig) {
    this.merge = require('merge');
    this.glob = require('glob');
    require('load-grunt-tasks')(grunt);

    var config = {
      pkg: grunt.file.readJSON('package.json'),
      env: process.env
    };

    for (var i= 0; i < appConfig.tasksDir.length; i++) {
      grunt.loadTasks(appConfig.tasksDir[i]);
    }

    merge = this.merge;
    for (var i= 0; i < appConfig.targetsDir.length; i++) {
      config = merge.recursive(true, config, this.loadDir(appConfig.targetsDir[i]));
    }

    grunt.initConfig(config);
  },

  loadDir: function(path) {
    var dirConfig = {};
    var that = this;

    this.glob.sync('*', {cwd: path}).forEach(function(filename) {
      var fileConfig = that.loadFile(path, filename);
      dirConfig = this.merge.recursive(true, dirConfig, fileConfig);
    });

    return dirConfig;
  },

  loadFile: function(path, filename) {
    var keys =  filename.replace(/\.js$/, '').split('.');

    return this.buildFromFile(keys, path + '/' + filename);
  },

  buildFromFile: function(keys, filepath) {
    if (keys.length == 0) {

      return require(filepath);
    } else {
      var subArray = {};
      var index = keys[0];
      keys.shift();
      subArray[index] = this.buildFromFile(keys, filepath);

      return subArray;
    }
  }
};
