module.exports = function(grunt) {
	grunt.loadNpmTasks('grunt-mocha');
	
	grunt.initConfig({
		mocha: {
			test: {
				src: [
				    '*/Tests/_mocha/*.htm'
				],
				options: {
					run: true,
				}
			}
		}
	});
	
	grunt.registerTask('default', function() {
		grunt.log.writeln('Nothing to do by default.');
	});
	
	grunt.registerTask('js-test', ['mocha']);
};
