import OrchestraView from '../OrchestraView'
import ModalView     from '../../../Service/Modal/View/ModalView'

/**
 * @class LogOutModalView
 */
class LogOutModalView extends ModalView
{
    /**
     * Render Site selector
     */
    render() {
        let template = this._renderTemplate('Header/logOutModalView');
        this.$el.html(template);

        return this;
    }
}

export default LogOutModalView;
