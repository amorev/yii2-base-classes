<?php

namespace Zvinger\BaseClasses\app\migrations;

use yii\data\ActiveDataProvider;
use yii\db\Migration;
use yii\helpers\Console;
use Zvinger\BaseClasses\app\components\data\dictionary\models\DBDictionaryInfo;

/**
 * Class M180820145703FilterSystem
 */
class M180907141411SlugToDictionary extends Migration
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

        $this->addColumn('{{%dictionary_info}}', 'slug', 'string(255)');
        $this->createIndex('dictionary_info_slug', '{{%dictionary_info}}', 'slug');
        $query = DBDictionaryInfo::find();
        $provider = new ActiveDataProvider();
        $provider->query = $query;
        $provider->pagination->page = 0;
        $provider->pagination->pageSize = 10;
        /** @var DBDictionaryInfo[] $models */
        Console::startProgress(0, $provider->getTotalCount());
        $done = 0;
        while ($models = $provider->getModels()) {
            foreach ($models as $model) {
                $model->save();
            }
            $done = $done + count($models);
            Console::startProgress($done, $provider->getTotalCount());
            $provider->pagination->page = $provider->pagination->page + 1;
            $provider->refresh();
        }
        Console::endProgress(false);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%dictionary_info}}', 'slug');

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
