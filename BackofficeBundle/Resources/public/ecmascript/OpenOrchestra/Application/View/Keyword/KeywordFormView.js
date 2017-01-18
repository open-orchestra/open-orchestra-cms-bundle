import AbstractFormView from '../../../Service/Form/View/AbstractFormView'

/**
 * @class KeywordFormView
 */
class KeywordFormView extends AbstractFormView
{
    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate(
            'Keyword/keywordFormView',
            {
                title: Translator.trans('open_orchestra_backoffice.keyword.title_edit')
            }
        );
        this.$el.html(template);
        this._$formRegion = $('.form', this.$el);
        super.render();

        return this;
    }

    /**
     * @return {Object}
     */
    getStatusCodeForm() {
        return {
            '200': $.proxy(this.refreshRender, this),
            '422': $.proxy(this.refreshRender, this)
        }
    }
}

export default KeywordFormView;
