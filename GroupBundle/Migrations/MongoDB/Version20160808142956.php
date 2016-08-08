<?php

namespace OpenOrchestra\ModelBundle\Migrations\MongoDB;

use AntiMattr\MongoDB\Migrations\AbstractMigration;
use Doctrine\MongoDB\Database;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160808142956 extends AbstractMigration
{
    /**
     * Get custom migration description
     *
     * @return string
     */
    public function getDescription()
    {
        return 'Create an id for modelGroupRoles based on id, type and role';
    }

    /**
     * Upgrade db
     *
     * @param Database $db
     */
    public function up(Database $db)
    {
        $db->execute('
            db.users_group.find().forEach(function(item) {
                var modelRoles = {};
                for (var i in item.modelRoles) {
                    var modelRole = item.modelRoles[i];
                    var key = hex_md5(modelRole.id+modelRole.type+modelRole.role);
                    modelRoles[key] = modelRole;
                };
                item.modelRoles = modelRoles;
                db.users_group.update({_id: item._id}, item);
            })
        ');
    }

    /**
     * Downgrade db
     *
     * @param Database $db
     */
    public function down(Database $db)
    {
        $db->execute('
            db.users_group.find().forEach(function(item) {
                var modelRoles = [];
                for (var i in item.modelRoles) {
                    var modelRole = item.modelRoles[i];
                    modelRoles.push(modelRole);
                };
                item.modelRoles = modelRoles;
                db.users_group.update({_id: item._id}, item);
            })
        ');
    }
}
