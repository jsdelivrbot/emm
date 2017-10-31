module.exports = function (grunt) {
  grunt.config.merge({
    watch: {
        options: {
          files: ['<%= pkg.themePath %>/sass/**/*.scss'],
          tasks: ['gessoBuildStyles']
        },
        css: {
            files: ['<%= pkg.themePath %>/watchlearn/sass/**/*.scss',
                    '<%= pkg.themePath %>/watchlearn/**/*.twig',
                    '<%= pkg.themePath %>/watchlearn/**/*.js'],
            tasks: ['sass:dev'],
            options: {
                spawn: false
            }
        },
      gesso: {
        files: ['<%= pkg.themePath %>/sass/**/*.scss'],
        tasks: ['gessoBuildStyles'],
      },
      patternlab: {
        files: ['<%= pkg.themePath %>/pattern-lab/source/**/*'],
        tasks: ['shell:patternlab'],
        options: {
          livereload: true
        }
      },
      svgs: {
        files: ['<%= pkg.themePath %>/images/bg/*.svg'],
        tasks: ['gessoBuildImages', 'gessoBuildStyles'],
      },
    }
  });

  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-simple-watch');
}
