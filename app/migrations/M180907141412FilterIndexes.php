<?php

namespace Zvinger\BaseClasses\app\migrations;

use yii\data\ActiveDataProvider;
use yii\db\Migration;
use yii\helpers\Console;
use Zvinger\BaseClasses\app\components\data\dictionary\models\DBDictionaryInfo;

/**
 * Class M180820145703FilterSystem
 */
class M180907141412FilterIndexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci';
        }

        $this->execute(
            'create index filter_misc_info_object_id_object_type_key_index
  on filter_misc_info (object_id, object_type, `key`);

create index filter_misc_info_object_type_key_index
  on filter_misc_info (object_type, `key`);

create index filter_misc_info_value_data_index
  on filter_misc_info (value_data);
'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M180820145703FilterSystem cannot be reverted.\n";

        return false;
    }
    */
}
