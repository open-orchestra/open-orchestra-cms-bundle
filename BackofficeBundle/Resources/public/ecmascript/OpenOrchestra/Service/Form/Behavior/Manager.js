/**
 * @class Manager
 */
class Manager
{
    /**
     * Constructor
     */
    constructor() {
        this._behaviors = [];
        Backbone.Events.on('form:activate', this._activate, this);
        Backbone.Events.on('form:deactivate', this._deactivate, this);
    }

    /**
     * @param {Object} $behavior
     */
    add($behavior) {
        this._behaviors.push($behavior);
    }

    /**
     * activate behavior
     *
     * @param {Object} view - instance of AbstractFormView
     */
    _activate(view) {
        for (let behavior of this._behaviors) {
            behavior.activateBehavior(view);
        }
    }

    /**
     * deactivate behavior
     *
     * @param {Object} view - instance of AbstractFormView
     */
    _deactivate(view) {
        for (let behavior of this._behaviors) {
            behavior.deactivateBehavior(view);
        }
    }
}

// unique instance of Manager
export default (new Manager);
