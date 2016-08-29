<?php
date_default_timezone_set('Europe/Stockholm');

include_once 'vendor/ekandreas/valet-deploy/recipe.php';

set('domain','michelinkliniken.app');

server( 'production', 'orasolv.se', 22 )
	->env('deploy_path','/mnt/persist/www/michelinkliniken.se')
	->user( 'root' )
	->env('branch', 'master')
	->stage('production')
	->env('database','michelinkliniken')
	->env('domain','www.michelinkliniken.se')
	->identityFile();

set('repository', 'git@github.com:ekandreas/michelinkliniken.git');

// Symlink the .env file for Bedrock
set('env', 'prod');
set('keep_releases', 10);
set('shared_dirs', ['web/app/uploads']);
set('shared_files', ['.env', 'web/.htaccess', 'web/robots.txt']);
set('env_vars', '/usr/bin/env');
set('writable_dirs', ['web/app/uploads']);

task('deploy:restart', function () {
	run("service apache2 reload");
})->desc('Refresh cache');

task( 'deploy', [
	'deploy:prepare',
	'deploy:release',
	'deploy:update_code',
	'deploy:vendors',
	'deploy:shared',
	'deploy:writable',
	'deploy:symlink',
	'cleanup',
	'deploy:restart',
	'success'
] )->desc( 'Deploy your Bedrock project, eg dep deploy production' );