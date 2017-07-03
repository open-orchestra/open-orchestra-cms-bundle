import OrchestraView    from '../../../Application/View/OrchestraView'
import WorkflowProfiles from '../../Collection/WorkflowProfiles/WorkflowProfiles'

/**
 * @class GraphicView
 */
class GraphicView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    render() {
        let workflowProfiles = new WorkflowProfiles().fetch({
            success: (workflowProfiles) => {
                console.log(workflowProfiles);
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
                g.setNode(statusFrom.get('id'),  { label: statusFrom.get('label'), shape: "circle" ,class:"node-workflow-"+statusFrom.get('code_color')} );

                g.setNode(statusTo.get('id'),  { label: statusTo.get('label'), shape: "circle" , class:"node-workflow-"+statusTo.get('code_color')} );
                let label = transition.label;
                let edge = g.edge(statusFrom.get('id'), statusTo.get('id'));
                if (typeof edge !== "undefined") {
                    label = edge.label + ', ' + label
                }
                g.setEdge(statusFrom.get('id'), statusTo.get('id'), { label: label });
            }
        }

        let maxWidthLabel = 0;
        let paddingLabel = 10;
        g.nodes().forEach(function(v) {
            var node = g.node(v);
            let widthLabel = node.label.length*5+paddingLabel;
            if (widthLabel  > maxWidthLabel) {
                maxWidthLabel = widthLabel;
            }
        });
        g.nodes().forEach(function(v) {
            var node = g.node(v);
            node.width = maxWidthLabel;
            node.height = maxWidthLabel;
        });


        var render = dagreD3.render();

        $(this.$el).initialize('.workflow-preview', () => {
            $('.workflow-preview',  this.$el).show();
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

}

export default GraphicView;
