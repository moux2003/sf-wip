'use strict';

module.exports = {
  gitRepository: 'git@git.niji.fr:mri-mri/mri-web.git',
  destinationDirectory: '/home/mri/web',
  briefing: {
    destinations: {
      'preproduction': {
        host: '192.168.2.223',
        username: 'mri',
        agent: process.env.SSH_AUTH_SOCK
      },
      'production': {
        host: 'to.be.defined',
        username: 'mri',
        agent: process.env.SSH_AUTH_SOCK
      }
    }
  }
};