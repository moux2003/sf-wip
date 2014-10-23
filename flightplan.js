/**
 * Fichier de deploiement via Flightplan
 *
 * @see https://coderwall.com/p/l1dzfw
 */

var config = require('./flightplan/config'),
    archivePrefix = 'sources_',
    moment = require('moment'),
    Flightplan = require('flightplan'),
    argv = require('minimist')(process.argv.slice(2)),
    path = require('path'),
    plan = new Flightplan(),
    now = new Date(),
    deployDir = moment().format('YYYYMMDDHHmmss'),
    gitTag = argv.tag,
    tmpDir = path.join('build', 'tmp'),
    archiveName,
    sha1 = 'unknown',
    currentReleaseDir,
    releaseDir = path.join(config.destinationDirectory, 'release'),
    newReleaseDir = path.join(releaseDir, deployDir),
    currentReleaseLink = path.join(config.destinationDirectory, 'current');

// configuration
plan.briefing(config.briefing);

// run commands on localhost
plan.local(['deploy', 'package'], function (local) {
  if (gitTag === undefined) {
    gitTag = local.prompt('Enter the version number or branch git to deploy? [ex: 1.2.3]');
  }
  archiveName = archivePrefix + gitTag + '.tar.gz';
  sha1 = local.git('show-ref --hash --heads --tags ' + gitTag).stdout.replace(/[\n\t\r]/g,"");
  // Remove tmp directory
  local.rm('-rf ' + tmpDir);
  // Create tmp directory
  local.mkdir('-p ' + path.join(tmpDir, 'sources'));
  // Checkout tag and run build
  local.with('cd ' + path.join(tmpDir, 'sources'), function () {
    // Extract code from git
    local.exec('git archive --remote=' + config.gitRepository + ' ' + gitTag + ' | tar -x');
    // Add Version
    local.echo('"' + gitTag + '" > VERSION');
    local.echo('"' + sha1 + '" >> VERSION');
    // Get dependancies to deploy with source code
    local.exec('composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader');
    // Remove vagrant stuffs
    local.rm('-rf provisioning vagrant ' + path.join('app', 'config', 'parameters.yml'));
    // Remove non production controllers
    local.find('web/ -maxdepth 1 -name "app_*.php"  | grep -v "prod" | while read app_file; do rm $app_file; done;');
    // Compress archive
    local.tar('-zcf ../../' + archiveName + ' * .??*');
  });
});

// Create remote directories
plan.remote(['deploy', 'upload'], function (remote) {
  remote.log('Pre-deploy on ' + plan.target.destination);
  remote.log('Create release folder :' + releaseDir);
  remote.mkdir('-p ' + releaseDir);
  // Directories shared between releases
  remote.mkdir('-p ' + path.join(config.destinationDirectory, "shared", "app", "log"));
  remote.mkdir('-p ' + path.join(config.destinationDirectory, "shared", "app", "config"));
  remote.mkdir('-p ' + path.join(config.destinationDirectory, "shared", "web", "media"));
});

// Upload files on remote
plan.local(['deploy', 'upload'], function (local) {
  // confirm deployment, as we don't want to do this accidentally
  var input = local.prompt('Ready for deploying to ' + plan.target.destination + '? [yes]');
  if (input.indexOf('yes') === -1) {
    local.abort('user canceled flight'); // this will stop the flightplan right away.
  }

  // Rsync files to all the destination's hosts
  local.log('Copy files to remote hosts');
  local.with('cd build', function () {
    local.transfer(archiveName, releaseDir);
  });
});

// Extract sources on remote hosts
plan.remote(['deploy', 'extract'], function(remote) {
  remote.log('Deploy on '+ plan.target.destination);
  // Only used if there is a disaster deploy
  currentReleaseDir = remote.exec('readlink current || echo ' + newReleaseDir, {
    failsafe: true,
    exec : { cwd : config.destinationDirectory}
  }).stdout;

  // Create release folder and extract archive
  remote.with('cd ' + releaseDir, function () {
    remote.mkdir('-p ' + newReleaseDir);
    remote.tar('-xzf ' + archiveName + ' -C ' + newReleaseDir);
    remote.rm('-f ' + archiveName);
  });

  remote.with('cd ' + newReleaseDir, function(){
    // Link shared folders
    remote.rm('-rf app/logs');
    remote.ln('-s ' + path.join(config.destinationDirectory, 'shared', 'app', 'log') + ' ' + path.join('app', 'log'));
    remote.rm('-rf web/media');
    remote.ln('-s ' + path.join(config.destinationDirectory, 'shared', 'web', 'media') + ' ' + path.join('web', 'media'));

    var sharedParametersFile = path.join(config.destinationDirectory, 'shared', 'app', 'config', 'parameters.yml');
    remote.exec('ls ' + sharedParametersFile + ' || touch ' + sharedParametersFile);
    remote.ln('-s ' + sharedParametersFile + ' ' + path.join('app', 'config', 'parameters.yml'));

    // Symfony jobs
    remote.log('Run symfony tools');
    remote.exec('composer run-script build-parameters');
    // remote.exec('php app/console doctrine:migrations:migrate --no-interaction --env=prod');
    remote.exec('php app/console assets:install --no-interaction --symlink --env=prod');
    // remote.exec('php app/console fos:js-routing:dump --no-interaction  --env=prod');
    remote.exec('php app/console assetic:dump --no-interaction  --env=prod');
    remote.exec('php app/console cache:warmup --no-interaction  --env=prod');
  });

  // Link current folder on the new release
  remote.log('link folder to web root');
  remote.rm('-f ' + currentReleaseLink);
  remote.ln('-s ' + newReleaseDir + ' ' + currentReleaseLink);
});

// Remote provisioning
plan.remote(['provision'], function(remote) {
  //Load default fixtures
  remote.with('cd ' + currentReleaseLink, function(){
    remote.exec('php app/console doctrine:fixtures:load --env=prod');
    remote.exec('php app/console elasticsearch:create-index --env=prod');
  });
});

// Rollback management
plan.remote(['rollback'], function(remote) {

  currentReleaseDir = remote.exec('readlink ' + currentReleaseLink + ' || echo', {
    failsafe: true,
    exec : { cwd : config.destinationDirectory}
  }).stdout;

  // List available releases
  var listRelease = remote.ls('-1 ' + releaseDir, {silent: true}).stdout.split("\n");
  remote.log('List of releases:');
  for(var i=0; i < listRelease.length; i++){
    if(listRelease[i] != ''){
      remote.log('- ' + listRelease[i]);
    }
  }

  // Let choose a release and restore it
  var release = remote.prompt('Select the release to restore? [ex: 20141007092521]');
  remote.log('link folder to web root');
  remote.rm('-f ' + currentReleaseLink);
  remote.ln('-s ' + path.join(releaseDir, release) + ' ' + currentReleaseLink);

  if(currentReleaseDir != ''){
    // We can delete the release that failed
    var input = remote.prompt('Do you want to delete the broken release (' + currentReleaseDir + ')? [yes/no]');
    if (input === 'yes') {
      remote.rm('-rf ' + currentReleaseDir);
    }
  }

  // Actions after sources rollback
  remote.with('cd '+ path.join(releaseDir, release), function(){
    var input = remote.prompt('Do you want to migrate down database ? [yes/no]');
    if (input === 'yes') {
      var listVersion = remote.exec('php app/console doctrine:migrations:status --env=prod --show-versions', {silent: true}).stdout;
      remote.log(listVersion);
      var version = remote.prompt('Enter the migration version you want to downgrade to ? [ex: 20141002120011]');
      remote.exec('php app/console doctrine:migrations:execute --down --no-interaction --env=prod ' + version);
    }
  });
});

// If something went wrong...
plan.disaster(function () {
  if(currentReleaseDir !== undefined){
    plan.logger.info('The previous release was:' + currentReleaseDir)
  }
  plan.logger.error('To rollback use this command:'.error + ' "node ./node_modules/flightplan/bin/fly.js rollback:' + plan.target.destination + '"');
});