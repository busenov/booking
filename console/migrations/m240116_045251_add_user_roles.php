<?php

use booking\access\Rbac;
use yii\db\Migration;

/**
 * Class m240116_045251_add_user_roles
 */
class m240116_045251_add_user_roles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('{{%auth_items}}', ['type', 'name', 'description'], [
            [1, Rbac::ROLE_USER, Rbac::getRoleName(Rbac::ROLE_USER)],
            [1, Rbac::ROLE_ADMIN, Rbac::getRoleName(Rbac::ROLE_ADMIN)],
            [1, Rbac::ROLE_MANAGER, Rbac::getRoleName(Rbac::ROLE_MANAGER)],
            [1, Rbac::ROLE_SUPER_ADMIN, Rbac::getRoleName(Rbac::ROLE_SUPER_ADMIN)],
        ]);

        $this->batchInsert('{{%auth_item_children}}', ['parent', 'child'], [
            [Rbac::ROLE_MANAGER, Rbac::ROLE_USER],
        ]);

        $this->batchInsert('{{%auth_item_children}}', ['parent', 'child'], [
            [Rbac::ROLE_ADMIN, Rbac::ROLE_MANAGER],
        ]);

        $this->batchInsert('{{%auth_item_children}}', ['parent', 'child'], [
            [Rbac::ROLE_SUPER_ADMIN, Rbac::ROLE_ADMIN],
        ]);

        $this->execute('INSERT INTO {{%auth_assignments}} (item_name, user_id) SELECT \'user\', u.id FROM {{%users}} u ORDER BY u.id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%auth_items}}', ['name' => [
            Rbac::ROLE_USER,
            Rbac::ROLE_ADMIN,
            Rbac::ROLE_SUPER_ADMIN
        ]]);
    }
}
