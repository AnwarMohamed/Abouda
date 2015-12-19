'use strict';

describe('AboudaApp.version module', function() {
  beforeEach(module('AboudaApp.version'));

  describe('version service', function() {
    it('should return current version', inject(function(version) {
      expect(version).toEqual('0.1');
    }));
  });
});
