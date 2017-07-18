import AbstractFormView from 'OpenOrchestra/Service/Form/View/AbstractFormView'
import Statuses         from 'OpenOrchestra/Application/Collection/Status/Statuses'
import DrawGraphicMixin from 'OpenOrchestra/Application/View/Transition/Mixin/DrawGraphicMixin'

/**
 * @class TransitionsFormView
 */
class TransitionsFormView extends mix(AbstractFormView).with(DrawGraphicMixin)
{
    /**
     * Pre initialize
     * @param {Object} options
     */
    preinitialize(options) {
        super.preinitialize(options);
        this.events['change .workflow-transition input[type="checkbox"]'] = '_updateGraphic';
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('Transition/transitionsFormView');
        this.$el.html(template);
        this._$formRegion = $('.form-edit', this.$el);
        this._displayLoader(this._$formRegion);

        this._statuses = new Statuses();
        this._statuses.fetch({
            apiContext : "nodes",
            success : () => {
                super.render();
                this._updateGraphic();
            }
        });

        return this;
    }

    /**
     * @return {Array}
     */
    _transformTransitions() {
        let transitions = [];
        $('input[type="checkbox"]:checked', this._$formRegion).each( (index, checkbox) => {
            let statusFrom = this._statuses.findWhere({'id' : $(checkbox).attr('data-status-from')});
            let statusTo = this._statuses.findWhere({'id' : $(checkbox).attr('data-status-to')});
            if (typeof statusFrom !== "undefined" && typeof statusTo !== "undefined") {
                transitions.push({
                    'statusFrom': statusFrom,
                    'statusTo': statusTo,
                    'label': $(checkbox).closest('tr').children('td').first().text().trim()
                });
            }
        });


        return transitions;
    }

    /**
     * Update graphic workflow
     *
     * @private
     */
    _updateGraphic()
    {
        let transitions = this._transformTransitions();
        this._drawGraphic(transitions, '.workflow-preview svg');
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

export default TransitionsFormView;
