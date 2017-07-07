import AbstractFormView from '../../../Service/Form/View/AbstractFormView'
import GraphicView      from '../../View/Transition/GraphicView'

/**
 * @class ParametersFormView
 */
class ParametersFormView extends AbstractFormView
{
    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('Parameter/parametersFormView');
        this.$el.html(template);
        this._$formRegion = $('.form-edit', this.$el);
        super.render();
        $('.graphic-workflow-preview', this.$el).html(new GraphicView().render().$el);

        return this;
    }

    /**
     * @return {Object}
     */
    getStatusCodeForm() {
        return {
            '200': $.proxy(this.refreshRender, this),
            '201': $.proxy(this.refreshRender, this),
            '422': $.proxy(this.refreshRender, this)
        }
    }
}

export default ParametersFormView;
