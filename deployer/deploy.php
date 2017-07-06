<?php
namespace Deployer;

require 'recipe/laravel.php';

// Configuration
inventory('config/deployer/servers.yml');

set('default_stage', 'dev');
set('repository', 'git@bitbucket.org:username/app_repo.git');
set('keep_releases', 4);
set('writable_use_sudo', true);
set('bin/npm', function () {
    return run('which npm')->toString();
});
set('bin/gulp', function () {
    return run('which gulp')->toString();
});

//Tasks
desc('Run npm install');
task(
    'deploy:npm:install',
    function () {
        run('cd {{release_path}} && {{bin/npm}} install --save');
    }
);

desc('Run gulp assets');
task(
    'deploy:gulp',
    function () {
        $stage = get('default_stage');
        if (input()->hasArgument('stage')) {
            $stage = input()->getArgument('stage');
        }

        $flags = '';
        if ($stage == 'production') {
            $flags .= ' --production';
        }

        run('cd {{release_path}} && {{bin/gulp}} '.$flags);
    }
);

before('deploy:gulp', 'deploy:npm:install');
after('deploy:symlink', 'deploy:gulp');
