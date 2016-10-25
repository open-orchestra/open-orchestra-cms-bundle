import OrchestraView from '../OrchestraView'

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
        this.className = 'oo-application-loader'
    }
}

export default LoaderView;
