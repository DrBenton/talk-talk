module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({

    // Less stuff
    less: {
      development: {
        options: {
          paths: ["assets/css"]
        },
        files: {
          "assets/css/theme-twbootstrap.css": "assets/css/theme-twbootstrap.less"
        }
      }
    },
    // End Less
  
    // Watch stuff
    watch: {
      options: {
        atBegin: true
      },
      "css": {
        files: ["assets/css/**/*.less"],
        tasks: ['less:development']
      }
    }
    // End Watch
    
});

  // Load the tasks plugins.
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // Default task(s).
  grunt.registerTask('default', ['less']);

};
