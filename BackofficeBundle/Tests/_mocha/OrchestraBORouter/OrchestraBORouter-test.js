describe("OrchestraBORouter", function() {
    it("should be able to generate URL of a route", function() {
    	var router = new OrchestraBORouter();
    	router.route('some/path/with/:parameter', 'testRoute', function(parameter) {});
    	
    	var routeUrl = router.generateUrl('testRoute', {parameter: 'test-value'});
    	assert.equal('some/path/with/test-value', routeUrl);
    });
});
