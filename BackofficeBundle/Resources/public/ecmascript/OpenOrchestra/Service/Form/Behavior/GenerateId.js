import AbstractBehavior from './AbstractBehavior'

/**
 * @class GenerateId
 */
class GenerateId extends AbstractBehavior
{
    /**
     * get extra events
     *
     * @return {Object}
     */
    getExtraEvents() {
        return {
            'focusout': '_generateId'
        }
    }

    /**
     * activate global behavior
     *
     * @param {Object} view - instance of AbstractFormView
     */
    activateBehavior(view) {
        this.bindExtraEvents(view);
    }

    /**
     * {Object} event
     * @private
     */
    _generateId(event) {
        let value = $(event.currentTarget).val();
        let $dest = $('.generate-id-dest', this.$el);
        if (0 !== $dest.length && '' === $dest.val() && '' !== value) {
            $dest.val(value.latinise().replace(/[^a-z0-9]/gi,'_'));
        }
    }

    /**
     * return selector
     *
     * @return {String}
     */
    getSelector() {
        return '.generate-id-source';
    }
}

export default (new GenerateId);
