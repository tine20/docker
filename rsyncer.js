#! /usr/bin/env node

// npm install -G lodash
// npm install -G node-watch

let syncTasks = [
  {name: 'tinetests', src:  __dirname + '/tine20/tests/', dst: 'rsync://localhost:4873/tinetests'},
  {name: 'tinesource', src:  __dirname + '/tine20/tine20/', dst: 'rsync://localhost:4873/tinesource'},
];

let _ = require('lodash');
const { execSync } = require('child_process');
var watch = require('node-watch');
// var throttle = require('lodash.debounce');

_.each(syncTasks, (task) => {
  if (! task.syncCmd) {
    task.syncCmd = `rsync -urlv --no-perms --chmod=a+r,D+x ${task.src}/ --delete ${task.dst}`;
  }

  console.log(`[${task.name}] initial sync...`);
  execSync(task.syncCmd);
  console.log(`[${task.name}] initial sync done`);
  console.log(``);

  task.syncThrottled = _.throttle(function() {
    console.log(`[${task.name}] syncing...`);
    execSync(task.syncCmd);
    console.log(`[${task.name}] done.`);
    console.log('');
  }, 100, {leading: false});

});

console.log(`listening for changes...`);
watch( __dirname + '/tine20/', { recursive: true, delay: 0 }, function(evt, name) {
  if (! name.match(/___jb|.idea|.git/)) {

    let task = _.find(syncTasks, (task) => {
      return name.match(new RegExp(`^${task.src}`));
    });
    if (task) {
      console.log(`[${task.name}] ${name.replace(task.src, '')} changed`);
      task.syncThrottled();
    } else {
      console.log(`[no tasks] ${name} - not syncing`)
    }
  }
});
