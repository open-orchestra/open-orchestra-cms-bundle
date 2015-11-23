/* Object to test. */
var User = function (name) {
    this.name = name;
};

User.prototype = {
    getName: function() {
        return this.name;
    }
};

/* Test. */
describe("User", function() {
    it("should define its name in constructor", function() {
        var name = "John Smith";
        var user = new User(name);
        assert.equal(name, user.name);
        assert.equal(name, user.getName());
    });
});
