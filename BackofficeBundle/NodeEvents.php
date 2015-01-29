<?php

namespace PHPOrchestra\BackofficeBundle;

/**
 * Class NodeEvents
 */
final class NodeEvents
{
    const PATH_UPDATED = 'node.path_updated';

    const NODE_UPDATE = 'node.update';
    const NODE_UPDATE_BLOCK = 'node.update_block';
    const NODE_UPDATE_BLOCK_POSITION = 'node.update_block_position';
    const NODE_CREATION = 'node.creation';
    const NODE_DELETE = 'node.delete';
    const NODE_DUPLICATE = 'node.duplicate';
    const NODE_ADD_LANGUAGE = 'node.add_language';
    const NODE_DELETE_BLOCK = 'node.delete_block';
}
