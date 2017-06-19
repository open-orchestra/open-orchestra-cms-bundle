import AbstractFormView from '../../../Service/Form/View/AbstractFormView'
import Statuses         from '../../Collection/Status/Statuses'

/**
 * @class TransitionsFormView
 */
class TransitionsFormView extends AbstractFormView
{
    /**
     * Pre initialize
     * @param {Object} options
     */
    preinitialize(options) {
        super.preinitialize(options);
        this.events['change .workflow-transition input[type="checkbox"]'] = '_drawGraphic';
    }

    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('Transition/transitionsFormView');
        this.$el.html(template);
        this._$formRegion = $('.form-edit', this.$el);
        this._displayLoader(this._$formRegion);
        $('.workflow-preview',  this.$el).hide();

        this._statuses = new Statuses();
        this._statuses.fetch({
            apiContext : "nodes",
            success : () => {
                super.render();
                this._drawGraphic();
                $('.workflow-preview',  this.$el).show();
            }
        });

        return this;
    }

    /***
     * Draw graphic
     * @private
     */
    _drawGraphic() {
        this._displayLoader($('.workflow-preview svg', this.$el));
        let transitions = [];
        $('input[type="checkbox"]:checked', this._$formRegion).each( (index, checkbox) => {
            transitions.push({
                'statusFrom': $(checkbox).attr('data-status-from'),
                'statusTo':   $(checkbox).attr('data-status-to'),
                'label': $(checkbox).closest('tr').children('td').first().text().trim()
            });
        });

        var g = new dagreD3.graphlib.Graph().setGraph({});
        g.graph().rankdir = 'LR';

        for (let transition of transitions) {
            let statusFrom = this._statuses.findWhere( {'id' : transition.statusFrom });
            let statusTo = this._statuses.findWhere( {'id' : transition.statusTo });
            if (typeof statusFrom !== "undefined" && typeof statusTo !== "undefined")
            {
                g.setNode(statusFrom.get('id'),  { label: statusFrom.get('label'), shape: "circle" , class:"node-workflow-"+statusFrom.get('code_color')} );
                g.setNode(statusTo.get('id'),  { label: statusTo.get('label'), shape: "circle" , class:"node-workflow-"+statusTo.get('code_color')} );
                let label = transition.label;
                let edge = g.edge(statusFrom.get('id'), statusTo.get('id'));
                if (typeof edge !== "undefined") {
                    label = edge.label + ', ' + label
                }
                g.setEdge(statusFrom.get('id'), statusTo.get('id'), { label: label });
            }
        }

        var render = dagreD3.render();

        $(this.$el).initialize('.workflow-preview', () => {
            let $svg = $('.workflow-preview svg', this.$el);
            $svg.empty();
            let svg = d3.select("svg"),
                inner = svg.append("g");
            let zoom = d3.behavior.zoom().on("zoom", function() {
                inner.attr("transform", "translate(" + d3.event.translate + ")" +
                    "scale(" + d3.event.scale + ")");
            });

            render(inner, g);

            let xCenterOffset = ($svg.width() - g.graph().width) / 2;
            inner.attr("transform", "translate(" + xCenterOffset + ", 20)");
            svg.attr("height", g.graph().height + 40);
        });

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
