import OrchestraModel   from '../OrchestraModel'
import Status           from '../Status/Status'
import Area             from '../Area/Area'
import ApplicationError from '../../../Service/Error/ApplicationError'

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
                let attributes = response.areas[index];
                attributes.name = attributes.name || index;
                areas[index] =  new Area(attributes, {parse: true});
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

        throw new ApplicationError('Area '+areaId+' not found');
    }

    /**
     * Get changed area
     * @returns
     */
    getChangedArea() {
        let changedAreas = {};
        let areas = this.get('areas');
        for (let index in areas) {
            let area = areas[index];
            if (true === area.get('hasChanged')) {
                changedAreas[index] = area;
            }
        }

        return changedAreas;
    }

    /**
     * @inheritdoc
     */
    _getSyncUrl(method, options) {
        let urlParameter = options.urlParameter || {};
        switch (method) {
            case "read":
                return Routing.generate('open_orchestra_api_node_show', urlParameter);
            case "update":
                return this._getSyncUpdateUrl(options);
            case "patch":
                return Routing.generate('open_orchestra_node_copy_blocks_in_area', urlParameter);
        }
    }

    /**
     * @param {Object} options
     *
     * @returns {string}
     * @private
     */
    _getSyncUpdateUrl(options) {
        let apiContext = options.apiContext || null;
        let urlParameter = options.urlParameter || {};
        switch (apiContext) {
            case "update_status":
                return Routing.generate('open_orchestra_api_node_update_status');
            case "update_position_block":
                return Routing.generate('open_orchestra_node_update_block_position', urlParameter);
            case 'add_block':
                return Routing.generate('open_orchestra_node_add_block', urlParameter);
        }
    }
}

export default Node
