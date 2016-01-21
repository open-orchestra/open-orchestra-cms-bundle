/// <reference path="../built_sources/js/backboneRouter.js" />
describe("OrchestraBORouter", function() {
    it("Test generateUrl", function() {
        var router = new OrchestraBORouter();
        router.routes['some/path/with/:parameter'] = '';
        router.routePatterns['testRoute'] = 'some/path/with/:parameter';

        var routeUrl = router.generateUrl('testRoute', {parameter: 'test-value'});
        assert.equal('some/path/with/test-value', routeUrl);
    });
    it("Add route pattern", function() {
        var router = new OrchestraBORouter();
        var routeName = 'fakeRoute';
        var routePattern = 'fake/route';

        router.addRoutePattern(routeName, routePattern);
        assert.equal(router.routePatterns[routeName], routePattern);
    });
});
/*

describe('Array', function(){
    describe('#indexOf()', function(){
        it('should return -1 when the value is not present', function(){
            chai.assert.equal(-1, [1,2,3].indexOf(5));
            chai.assert.equal(-1, [1,2,3].indexOf(0));
        });
    });
});*/
