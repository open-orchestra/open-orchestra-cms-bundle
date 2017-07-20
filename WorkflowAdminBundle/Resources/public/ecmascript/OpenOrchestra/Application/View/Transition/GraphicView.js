import OrchestraView    from '../../../Application/View/OrchestraView'
import DrawGraphicMixin from './Mixin/DrawGraphicMixin'
import WorkflowProfiles from '../../Collection/WorkflowProfiles/WorkflowProfiles'

/**
 * @class GraphicView
 */
class GraphicView extends mix(OrchestraView).with(DrawGraphicMixin)
{
    /**
     * @inheritdoc
     */
    render() {
        let template = this._renderTemplate('Transition/graphicView');
        this.$el.html(template);
        new WorkflowProfiles().fetch({
            success: (workflowProfiles) => {
                let transitions = this._transformTransitions(workflowProfiles.models);
                this._drawGraphic(transitions, '.workflow-preview svg');
            }
        });

        return this;
    }

    /**
     * @param {WorkflowProfiles} workflowProfiles
     *
     * @return {Array}
     */
    _transformTransitions(workflowProfiles) {
        let transitions = [];
        for (let workflowProfile of workflowProfiles) {
            if (workflowProfile.has('transitions')) {
                for (let transition of workflowProfile.get('transitions').models) {
                    transitions.push({
                        'statusFrom': transition.get('status_from'),
                        'statusTo': transition.get('status_to'),
                        'label': workflowProfile.get('label')
                    });
                }
            }
        }

        return transitions;
    }
}

export default GraphicView;
