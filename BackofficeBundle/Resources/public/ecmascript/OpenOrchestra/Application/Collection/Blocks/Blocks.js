import OrchestraCollection from '../OrchestraCollection'
import Block               from '../../Model/Block/Block'

/**
 * @class Blocks
 */
class Blocks extends OrchestraCollection
{
    /**
     * Pre initialize
     */
    preinitialize() {
        this.model = Block;
    }
}

export default Blocks
