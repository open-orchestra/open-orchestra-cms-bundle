/**
 * Override Backbone history to add method for generate url
 */
_.extend(Backbone.history, {
    routePatterns : {},

    /**
     * Add route to route pattern
     * @param {string} routeName
     * @param {string} routePattern
     */
    addRoutePattern: function(routeName, routePattern) {
        this.routePatterns[routeName] = routePattern;
    },

    /**
     *
     * @param {string} name
     * @param {object} parameter
     *
     * @returns {string}
     */
    generateUrl: function(name, parameter = {}) {
        let optionalParam = /\(([^)]*):([^)]*)\)/g;
        let namedParam = /():([^/]*)/g;
        let route = this.routePatterns[name];
        var replaceFunction = (match, previousKey, key) => {
            if (parameter[key]) {
                return previousKey + parameter[key];
            }

            return '';
        };

        if ('undefined' !== typeof route) {
            route = route.replace(optionalParam, replaceFunction);
            route = route.replace(namedParam, replaceFunction);
            return route
        }

        throw new Error('route name is unknown');
    }
});
