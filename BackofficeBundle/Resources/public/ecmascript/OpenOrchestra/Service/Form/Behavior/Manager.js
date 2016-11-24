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
     * deactivate behavior
     * 
     * @param {Object} $form - jQuery element containing form
     */
    _activate($form) {
        for (let behavior of this._behaviors) {
            behavior.activate($(behavior.getSelector(), $form));
        }
    }

    /**
     * activate behavior
     * 
     * @param {Object} $form - jQuery element containing form
     */
    _deactivate($form) {
        for (let behavior of this._behaviors) {
            behavior.deactivate($(behavior.getSelector(), $form));
        }
    }
}

// unique instance of Manager
export default (new Manager);
