import AbstractFormView from '../../../Service/Form/View/AbstractFormView'

/**
 * @class AbstractNewBlockFormView
 */
class AbstractNewBlockFormView extends AbstractFormView
{
    /**
     * Pre initialize
     * @param {Object} options
     */
    preinitialize(options) {
        super.preinitialize(options);
        this.events['click button.submit-continue-form'] = '_submit';
    }

    /**
     * Initialize
     * @param {Form}   form
     * @param {string} name
     */
    initialize({form, name}) {
        super.initialize({form : form});
        this._name = name;
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('Block/newBlockView', {
            name: this._name,
            labelButtonBack: this._getLabelButtonBack(),
            urlButtonBack: this._getUrlButtonBack()
        });
        this.$el.html(template);
        this._$formRegion = $('.form-new', this.$el);
        super.render();

        return this;
    }

    /**
     * @private
     *
     * @return string
     */
    _getLabelButtonBack() {
        throw new TypeError("Please implement abstract method _getLabelButtonBack.");
    }

    /**
     * @private
     *
     * @return string
     */
    _getUrlButtonBack() {
        throw new TypeError("Please implement abstract method _getUrlButtonBack.");
    }

    /**
     * @inheritdoc
     */
    getStatusCodeForm(event) {
        throw new TypeError("Please implement abstract method _getStatusCodeForm.");
    }
}

export default AbstractNewBlockFormView;
