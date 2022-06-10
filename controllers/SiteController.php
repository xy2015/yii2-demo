<?php

namespace app\controllers;

use app\models\Supplier;
use m35\thecsv\theCsv;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionSupplier()
    {
        $model = new Supplier();
        $queryParams = \Yii::$app->request->getQueryParams();
        $dataProvider = $model->search($queryParams);
        return $this->render('supplier',[
            'searchModel' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionExportSupplier()
    {
        $ids = Yii::$app->request->post('ids');
        if ($ids){
            $filePath = 'supplier-'.date('YmdHi').'.csv';
            $fp = fopen($filePath,'a');
            $header = [['Id','id'],['Name','name'],['Code','code'],['Status','t_status']];
            $head = [];
            foreach ($header as $item){
                $head[] = $item[0];
            }
            fputcsv($fp,$head);
            $list = Supplier::find()->where(['in','id',explode(',',$ids)])->asArray()->all();
            foreach ($list as $row) {
                $data = [];
                foreach ($header as $v) {
                    $data[] = $row[$v[1]];
                }
                fputcsv($fp, $data);
                unset($data);
            }
            fclose($fp);
            $filePath = \Yii::$app->request->hostInfo.'/'.$filePath;
            return $this->asJson(['status'=>'11','msg'=>'导出成功','data'=>$filePath]);
        }
        return $this->asJson(['status'=>'00','msg'=>'请选择要导出的项目']);
    }
}
