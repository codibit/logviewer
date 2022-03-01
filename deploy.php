<?php
namespace Deployer;

require 'recipe/symfony.php';
require 'contrib/webpack_encore.php';

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

after('deploy:update_code', 'npm:install');

after('deploy:failed', 'deploy:unlock');
