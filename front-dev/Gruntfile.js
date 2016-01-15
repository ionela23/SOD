module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    jade: {
        compile: {
            options: {
                data: {
                    debug: false
                }
            },
            files: [{
                src: "*.jade",
                dest: "../public/",
                ext: ".html",
                expand: true,
                cwd: "jade/"
            }]
        }
    }
  });
  grunt.loadNpmTasks('grunt-contrib-jade');
 
  // Default task(s).
  grunt.registerTask('default', ['jade']);

};