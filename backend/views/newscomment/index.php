<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\NewscommentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Newscomments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="newscomment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Newscomment', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'content:ntext',
            'status',
            'create_time:datetime',
            'userid',
            //'email:email',
            //'url:url',
            //'post_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
