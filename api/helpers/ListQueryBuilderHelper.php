<?php


namespace Zvinger\BaseClasses\api\helpers;


use Carbon\Carbon;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\debug\models\timeline\DataProvider;
use yii\helpers\Inflector;
use Zvinger\BaseClasses\api\request\BaseListRequest;

class ListQueryBuilderHelper
{

    static public function getDataProvider(ActiveQuery $activeQuery, BaseListRequest $request): ActiveDataProvider
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $request->pageSize,
            ],
        ]);

        if ($request->page) {
            $dataProvider->pagination->setPage($request->page - 1);
        }

        return $dataProvider;
    }

    static public function setQueryConditions(ActiveQuery &$activeQuery, BaseListRequest $request)
    {
        foreach ($request->where as $fieldName => $value) {
            $activeQuery->andWhere([Inflector::underscore($fieldName) => $value]);
        }

        foreach ($request->like as $fieldName => $value) {
            $activeQuery->andWhere(['like', Inflector::underscore($fieldName), $value]);
        }

        if ($request->orLike) {
            $like = ['or'];
            foreach ($request->orLike as $fieldName => $value) {
                $like[] = ['like', Inflector::underscore($fieldName), $value];
            }
            $activeQuery->andWhere($like);
        }

        foreach ($request->between as $fieldName => $value) {
            if (is_array($value)) {
                $query->andWhere(
                    Inflector::underscore($fieldName) . ' between :min and :max',
                    [
                        ':min' => $value[0],
                        ':max' => $value[1],
                    ]
                );
            }
        }

        if ($request->sort) {
            $sort = [];
            foreach ($request->sort as $fieldName => $value) {
                $sort[Inflector::underscore($fieldName)] = $value;
            }
            $query->orderBy($sort);
        }

    }


    static public function getMongoDataProvider(\yii\mongodb\ActiveQuery $activeQuery, BaseListRequest $request): ActiveDataProvider
    {

    }

}
