<?php
namespace frontend\modules\api\v1\controllers;

use common\models\Event;
use frontend\models\search\EventSearch;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\HttpException;

/**
 * Class EventController
 * @author Eugene Terentev <eugene@terentev.net>
 */
class EventController extends ActiveController
{
    public $modelClass = '\common\models\Article';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBearerAuth::className(),
                QueryParamAuth::className()
            ],
        ];
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => 'yii\rest\IndexAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'prepareDataProvider' => function() {
                    $searchModel = new EventSearch();
                    return $searchModel->search();
                }
            ],
            'view' => [
                'class' => 'yii\rest\ViewAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'findModel' => [$this, 'findModel']
            ]
        ];
    }

    public function findModel($id)
    {
        $model = Event::findOne(['id' => (int) $id]);
        if (!$model) {
            throw new HttpException(404);
        }
        return $model;
    }
}
