import OrchestraView from 'OpenOrchestra/Application/View/OrchestraView'

/**
 * @class LoaderView
 */
class LoaderView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.tagName = 'div';
        this.className = 'spinner'
    }
}

export default LoaderView;
