<?php
namespace Deployer;

require 'recipe/symfony.php';

// Config

set('repository', 'git@github.com:codibit/logviewer.git');

add('shared_files', ['var/storage', 'var/log']);
add('shared_dirs', ['var/storage', 'var/log', 'var/cache']);
add('writable_dirs', ['var/storage', 'var/log', 'var/cache']);

// Hosts

host('linode1.vanherpt.net')
    ->set('remote_user', 'deployer')
    ->set('deploy_path', '~/logviewer');

// Tasks

task('build', function () {
    cd('{{release_path}}');
    run('npm run build');

    run('mkdir -p var/storage var/cache var/log');
});
task('encore', function(){
    cd('{{release_path}}');
    run('yarn install');
    run('yarn build');
});

before('deploy:success', 'database:migrate');

after('deploy:symlink', 'encore');

after('deploy:failed', 'deploy:unlock');
