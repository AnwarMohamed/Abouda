'use strict';

angular.module('AboudaApp.version', [
  'AboudaApp.version.interpolate-filter',
  'AboudaApp.version.version-directive'
])

.value('version', '0.1');
