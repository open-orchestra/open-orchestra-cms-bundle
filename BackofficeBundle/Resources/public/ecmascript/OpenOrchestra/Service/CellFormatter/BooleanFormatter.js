import AbstractCellFormatter from './AbstractCellFormatter'
import BooleanDataFormatter  from '../DataFormatter/BooleanFormatter'

/**
 * @class BooleanFormatter
 */
class BooleanFormatter extends AbstractCellFormatter
{
    /**
     * @inheritdoc
     */
    getDataFormatter() {
        return BooleanDataFormatter;
    }
}

// unique instance of BooleanFormatter
export default (new BooleanFormatter);
