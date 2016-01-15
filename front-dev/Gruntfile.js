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
    },
    watch: {
      scripts: {
        files: ['jade/*.jade', 'jade/**/*.jade', 'jade/**/**/*.jade'],
        tasks: ['jade'],
        options: {
          spawn: false,
        },
      },
    },
  });
  grunt.loadNpmTasks('grunt-contrib-jade');
  grunt.loadNpmTasks('grunt-contrib-watch');
  // Default task(s).
  grunt.registerTask('default', ['jade','watch']);

};