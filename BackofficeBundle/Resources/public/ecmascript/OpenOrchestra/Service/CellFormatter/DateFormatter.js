import AbstractCellFormatter from './AbstractCellFormatter'
import DateDataFormatter     from '../DataFormatter/DateFormatter'

/**
 * @class DateFormatter
 */
class DateFormatter extends AbstractCellFormatter
{
    /**
     * @inheritdoc
     */
    getDataFormatter() {
        return DateDataFormatter;
    }
}

// unique instance of DateFormatter
export default (new DateFormatter);
