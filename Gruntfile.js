module.exports = function (grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        composer: grunt.file.readJSON('composer.json'),
        phpcs: {
            application: {
                src: ['src/**/*.php', 'tests/**/*.php']
            },
            options: {
                bin: 'vendor/bin/phpcs',
                standard: 'PSR2'
            }
        },
        phpunit: {
            classes: {
                dir: 'tests'
            },
            options: {
                bin: 'vendor/bin/phpunit',
                // testSuffix: "BasicRetryHandlerTest.php",
                staticBackup: false,
                colors: true,
                followOutput: true,
                noGlobalsBackup: false
            }
        }
    });

    grunt.loadNpmTasks('grunt-phpunit');
    grunt.loadNpmTasks('grunt-phpcs');

    grunt.registerTask('default', ['phpcs', 'phpunit']);
};