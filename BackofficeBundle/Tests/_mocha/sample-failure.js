describe("Failure on test", function() {
    it("should lead to the travis build failure", function() {
        assert.equal(false, true);
    });
});
