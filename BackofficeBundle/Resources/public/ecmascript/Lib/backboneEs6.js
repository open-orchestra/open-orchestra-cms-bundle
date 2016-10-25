/**
 * Override default constructor of Backbone component (View, Model, Collection, Router)
 * to support ES6
 * Call preinitialize method before call basic constructor of backbone view
 * Remove this when backbone 1.4 is out
 */

/**
 * Override default constructor Backbone View to support ES6
 * Call preinitialize method before call basic constructor of backbone view
 * Remove this when backbone 1.4 is out
 */
Backbone.View = (function(View) {
    _.extend(View.prototype, {
        preinitialize: function() {}
    });
    Backbone.View = function(options) {
        this.preinitialize.apply(this, arguments);
        View.apply(this, arguments);
    };

    _.extend(Backbone.View, View);

    Backbone.View.prototype = (function(Prototype) {
        Prototype.prototype = View.prototype;
        return new Prototype;
    })(function() {});

    Backbone.View.prototype.constructor = Backbone.View;
    return Backbone.View;

})(Backbone.View);

/**
 * Override default constructor Backbone Router to support ES6
 * Call preinitialize method before call basic constructor of Backbone Router
 * Remove this when backbone 1.4 is out
 */
Backbone.Router = (function(Router) {
    _.extend(Router.prototype, {
        preinitialize: function() {}
    });
    Backbone.Router = function(options) {
        this.preinitialize.apply(this, arguments);
        Router.apply(this, arguments);
    };

    _.extend(Backbone.Router, Router);

    Backbone.Router.prototype = (function(Prototype) {
        Prototype.prototype = Router.prototype;
        return new Prototype;
    })(function() {});

    Backbone.Router.prototype.constructor = Backbone.Router;
    return Backbone.Router;

})(Backbone.Router);

/**
 * Override default constructor Backbone Model to support ES6
 * Call preinitialize method before call basic constructor of Backbone Model
 * Remove this when backbone 1.4 is out
 */
Backbone.Model = (function(Model) {
    _.extend(Model.prototype, {
        preinitialize: function() {}
    });
    Backbone.Model = function(attributes, options) {
        this.preinitialize.apply(this, arguments);
        Model.apply(this, arguments);
    };

    _.extend(Backbone.Model, Model);

    Backbone.Model.prototype = (function(Prototype) {
        Prototype.prototype = Model.prototype;
        return new Prototype;
    })(function() {});

    Backbone.Model.prototype.constructor = Backbone.Model;
    return Backbone.Model;

})(Backbone.Model);

/**
 * Override default constructor Backbone Collection to support ES6
 * Call preinitialize method before call basic constructor of Backbone Collection
 * Remove this when backbone 1.4 is out
 */
Backbone.Collection = (function(Collection) {
    _.extend(Collection.prototype, {
        preinitialize: function() {}
    });
    Backbone.Collection = function(models, options) {
        this.preinitialize.apply(this, arguments);
        Collection.apply(this, arguments);
    };

    _.extend(Backbone.Collection, Collection);

    Backbone.Collection.prototype = (function(Prototype) {
        Prototype.prototype = Collection.prototype;
        return new Prototype;
    })(function() {});

    Backbone.Collection.prototype.constructor = Backbone.Collection;
    return Backbone.Collection;

})(Backbone.Collection);
