<?php
use function Deployer\{host, task, run, set, get, upload, add, before, after, locateBinaryPath};

require 'recipe/common.php';

//set('repository', 'https://webdev:RppR84EyvDhBcNfX8xkD@devhub.ditgt.ru:8082/gost/siu_rst_2020.git');
set('ssh_multiplexing', false);
set('http_user', 'apache');

// Set configurations
set('shared_files', []);
set('shared_dirs', ['runtime','modules/cryptoproEds/tmpFiles']);
set('writable_use_sudo', false);
set('writable_dirs', ['runtime','web/assets','modules/cryptoproEds/tmpFiles']);

set('timezone', 'Europe/Moscow');

set('bin/cp', function () {
    return locateBinaryPath('cp');
});

// Configure servers
//host('stage')
//    ->hostname('dev.ditgt.ru')
//    ->port(2225)
//    ->user('webdev')
//    ->set('deploy_path', '/var/www/app')
//    ->set('env-config', '.env-stage');

//host('prod')
//    ->hostname('10.11.100.105')
//    ->user('webdev')
//    ->set('deploy_path', '/var/www/app')
//    ->set('env-config', '.env-prod');

/**
 * Run migrations
 */
task('deploy:run_migrations', function () {
    run('{{bin/php}} {{release_path}}/yii migrate up --interactive=0');
})->desc('Run migrations');
task('deploy:copy_config', function () {
    run('{{bin/cp}} {{release_path}}/'.get('env-config').' {{release_path}}/.env');
})->desc('Run migrations');

/**
 * Main task
 */
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:copy_config',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'deploy:run_migrations',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
])->desc('Deploy your project');

after('deploy', 'success');
