/**
 * @class AbstractBehavior
 */
class AbstractBehavior
{
    /**
     * get extra events
     * 
     * @return {Object}
     */
    getExtraEvents() {
        return {};
    }
    
    /**
     * return selector
     * 
     * @return {String}
     */
    getSelector() {
        throw new TypeError("Please implement abstract method getSelector.");
    }

    /**
     * activate global behavior
     * 
     * @param {Object} view - instance of AbstractFormView
     */
    activateBehavior(view) {
        var delegateEventSplitter = /^(\S+)\s*(.*)$/;
        var events = this.getExtraEvents();
        var behavior = this;
        for(var key in events) {
            var method = events[key];
            if (!_.isFunction(method)) method = this[method];
            if (!method) continue;
            var match = key.match(delegateEventSplitter);
            view.delegate(match[1], this.getSelector() + ' ' + match[2], _.bind(method, view));
        }
        $(this.getSelector(), view.$el).each(function(){
            behavior.activate($(this));
        });
    }

    /**
     * activate behavior on each instance
     * 
     * @param {Object} element - JQuery object
     */
    activate(element) {
    }

    /**
     * deactivate global behavior
     * 
     * @param {Object} view - instance of AbstractFormView
     */
    deactivateBehavior(view) {
        view.undelegateEvents();
        $(this.getSelector(), view.$el).each(function(){
            this.deactivate($(this));
        });
    }

    /**
     * deactivate behavior on each instance
     * 
     * @param {Object} element - JQuery object
     */
    deactivate(element) {
    }
}

export default AbstractBehavior;
