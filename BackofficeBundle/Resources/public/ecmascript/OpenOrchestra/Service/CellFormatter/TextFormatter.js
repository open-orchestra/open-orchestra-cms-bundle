import AbstractCellFormatter from './AbstractCellFormatter'
import TextDataFormatter     from '../DataFormatter/TextFormatter'

/**
 * @class TextFormatter
 */
class TextFormatter extends AbstractCellFormatter
{
    /**
     * @inheritdoc
     */
    getDataFormatter() {
        return TextDataFormatter;
    }
}

// unique instance of TextFormatter
export default (new TextFormatter);
