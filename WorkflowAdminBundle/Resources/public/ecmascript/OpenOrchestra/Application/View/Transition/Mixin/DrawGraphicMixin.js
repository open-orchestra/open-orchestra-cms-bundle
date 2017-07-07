let DrawGraphicMixin = (superclass) => class extends superclass {

    /***
     * Draw graphic
     * @param {Array}  transitions
     * @param {String} selector
     * @private
     */
    _drawGraphic(transitions , selector) {
        var g = new dagreD3.graphlib.Graph().setGraph({});
        g.graph().rankdir = 'LR';

        for (let transition of transitions) {

            g.setNode(transition.statusFrom.get('id'),{
                label: transition.statusFrom.get('label'),
                shape: "circle" ,
                class:"node-workflow-"+transition.statusFrom.get('code_color')
            });
            g.setNode(transition.statusTo.get('id'),{
                label: transition.statusTo.get('label'),
                shape: "circle" ,
                class:"node-workflow-"+transition.statusTo.get('code_color')
            });
            let label = transition.label;
            let edge = g.edge(transition.statusFrom.get('id'), transition.statusTo.get('id'));
            if (typeof edge !== "undefined") {
                label = edge.label + ', ' + label
            }
            g.setEdge(transition.statusFrom.get('id'), transition.statusTo.get('id'), { label: label });
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

        $(this.$el).initialize(selector, () => {
            $(selector, this.$el).show();
            let $svg = $(selector, this.$el);
            $svg.empty();
            let svg = d3.select("svg"),
                inner = svg.append("g");

            render(inner, g);

            let initialScale = 1;
            if ( g.graph().width > $svg.width()) {
                initialScale = $svg.width()/g.graph().width;
            }
            console.log( $svg.width());
            console.log( g.graph().width);
            console.log( g.graph().width/$svg.width());
            console.log( $svg.width()/g.graph().width);
            let xCenterOffset = ($svg.width() - g.graph().width * initialScale) / 2;
            inner.attr("transform", "translate(" + xCenterOffset + ", 20)scale(" + initialScale + ")");
            svg.attr("height", g.graph().height + 40);
        });

    }

};

export default DrawGraphicMixin;
