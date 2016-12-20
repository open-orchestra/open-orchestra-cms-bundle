import OrchestraModel from '../OrchestraModel'
import Status         from '../Status/Status'
import Area           from '../Area/Area'

/**
 * @class Node
 */
class Node extends OrchestraModel
{
    /**
     * Parse server response to create nested object
     * @param response
     *
     * @returns {Object}
     */
    parse(response) {
        if (response.hasOwnProperty('status')) {
            response.status = new Status(response.status);
        }
        if (response.hasOwnProperty('areas')) {
            let areas = {};
            for(let index in response.areas) {
                areas[index] =  new Area(response.areas[index], {parse: true});
            }
            response.areas = areas;
        }

        return response;
    }

    /**
     * @param {string} areaId
     */
    getArea(areaId) {
        let areas = this.get('areas');
        if (areas.hasOwnProperty(areaId)) {
            return areas[areaId];
        }

        return new Area();
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
        switch (method) {
            case "read":
                return Routing.generate('open_orchestra_api_node_show', urlParameter);
        }
    }
}

export default Node
