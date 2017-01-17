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
     * bind extra events
     * 
     * @param {Object} view - instance of AbstractFormView
     */
    bindExtraEvents(view) {
        let delegateEventSplitter = /^(\S+)\s*(.*)$/;
        let events = this.getExtraEvents();
        for (let key in events) {
            let method = events[key];
            if (!_.isFunction(method)) method = this[method];
            if (!method) continue;
            let match = key.match(delegateEventSplitter);
            view.delegate(match[1], this.getSelector() + ' ' + match[2], _.bind(method, view));
        }
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
        let behavior = this;
        this.bindExtraEvents(view);
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
        let behavior = this;
        view.undelegateEvents();
        $(this.getSelector(), view.$el).each(function(){
            behavior.deactivate($(this));
        });
        view.delegateEvents();
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
