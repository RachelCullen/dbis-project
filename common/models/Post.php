<?php

namespace common\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string|null $tags
 * @property int $status
 * @property int|null $create_time
 * @property int|null $update_time
 * @property int $author_id
 *
 * @property Comment[] $comments
 * @property Adminuser $author
 * @property Poststatus $status0
 */
class Post extends \yii\db\ActiveRecord
{
    private $_oldTags;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'post';
    }
    /**
       * @inheritdoc$primaryKey
       */
    public static function primaryKey()
    {
        return ['id'];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'content', 'status', 'author_id'], 'required'],
            [['content', 'tags'], 'string'],
            [['status', 'create_time', 'update_time', 'author_id'], 'integer'],
            [['title'], 'string', 'max' => 128],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Adminuser::className(), 'targetAttribute' => ['author_id' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => Poststatus::className(), 'targetAttribute' => ['status' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'content' => 'Content',
            'tags' => 'Tags',
            'status' => 'Status',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'author_id' => 'Author ID',
        ];
    }

    /**
     * Gets query for [[Comments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['post_id' => 'id']);
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Adminuser::className(), ['id' => 'author_id']);
    }

    /**
     * Gets query for [[Status0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0()
    {
        return $this->hasOne(Poststatus::className(), ['id' => 'status']);
    }


    public function beforeSave($insert)
    {
    	if(parent::beforeSave($insert))
    	{
    		if($insert)
    		{
    			$this->create_time = time();
    			$this->update_time = time();
    		}
    		else 
    		{
    			$this->update_time = time();
    		}
    		
    		return true;
    			
    	}
    	else 
    	{
    		return false;
    	}
    } 
    
    public function afterFind()
    {
    	parent::afterFind();
    	$this->_oldTags = $this->tags;
    }
    
    public function afterSave($insert, $changedAttributes)
    {
    	parent::afterSave($insert, $changedAttributes);
    	Tag::updateFrequency($this->_oldTags, $this->tags);
    }
    
    public function afterDelete()
    {
    	parent::afterDelete();
    	Tag::updateFrequency($this->tags, '');
    }
    
    public function getUrl()
    {
    	return Yii::$app->urlManager->createUrl(
    			['post/detail','id'=>$this->id,'title'=>$this->title]);
    }
    
    public function getBeginning($length=288)
    {
    	$tmpStr = strip_tags($this->content);
    	$tmpLen = mb_strlen($tmpStr);
    	 
    	$tmpStr = mb_substr($tmpStr,0,$length,'utf-8');
    	return $tmpStr.($tmpLen>$length?'...':'');
    }
    
    public function  getTagLinks()
    {
    	$links=array();
    	foreach(Tag::string2array($this->tags) as $tag)
    	{
    		$links[]=Html::a(Html::encode($tag),array('post/index','PostSearch[tags]'=>$tag));
    	}
    	return $links;
    }

    public function getCommentCount()
    {
    	return Comment::find()->where(['post_id'=>$this->id,'status'=>2])->count();
    }

}