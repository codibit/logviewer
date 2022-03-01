<?php
namespace Deployer;

require 'recipe/symfony.php';
require 'contrib/yarn.php';

// Config

set('repository', 'git@github.com:codibit/logviewer.git');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host('linode1.vanherpt.net')
    ->set('remote_user', 'deployer')
    ->set('deploy_path', '~/logviewer');

// Tasks

task('build', function () {
    cd('{{release_path}}');
    run('npm run build');
});
task('yarn:build', function () {
    cd('{{release_path}}');
    run('npm run build');
});

after('deploy:failed', 'deploy:unlock');
