<?php

namespace main\models;

use PDOException;
use RuntimeException;
use Throwable;
use Yii;
use yii\behaviors\TimestampBehavior;
use main\helpers\CreatedByBehavior;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;

/**
 * This is the model class for table "files".
 *
 * @property int $id
 * @property string $name
 * @property int $size
 * @property resource $content
 * @property string $type
 * @property int $created_at
 * @property int $created_by
 * @property int $deleted_at
 * @property int $deleted_by
 * @property string $object_type
 * @property int $object_id
 *
 * @property User $createdBy
 * @property User $deletedBy
 */
class File extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'files';
    }

    public static function formatSize($value)
    {
        return Yii::$app->formatter->asShortSize($value);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'size', 'content'], 'required'],
            [['size', 'created_at', 'created_by', 'deleted_at', 'deleted_by'], 'integer'],
            [['name'], 'string', 'max' => 500],
            [['type'], 'string', 'max' => 100],
            [['object_type'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя файла',
            'size' => 'Размер',
            'content' => 'Содержимое',
            'type' => 'Тип',
            'object_type' => 'Тип связанного объекта',
            'object_id' => 'ID связанного объекта',
            'created_at' => 'Создан',
            'created_by' => 'Создал',
            'deleted_at' => 'Удален',
            'deleted_by' => 'Удалил',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => null,
            ],
            [
                'class' => CreatedByBehavior::class,
                'updatedByAttribute' => null,
            ]
        ];
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return is_resource($this->content) ? stream_get_contents($this->content) : $this->content;
    }

    /**
     * Записывает файл в бд
     * @param array $data
     * @return int id файла
     */
    public static function create($data)
    {
        /* @var $f File */
        $f = new static($data);
        if (!$f->save()) {
            throw new RuntimeException('Can\'t create file record: ' . implode(',', $f->getFirstErrors()));
        }
        return $f->id;
    }

    /**
     * Записывает файл в бд (для postgresql через large objects)
     * @param string $fileName путь до файла
     * @param string $name имя
     * @param string $type mime type
     * @param string $objectType тип связанного объекта
     * @param int $objectId id связанного объекта
     * @return int id файла
     * @deprecated экспериментальный метод
     */
    public static function createFromFile($fileName, $name, $type, $objectType = null, $objectId = null)
    {
        try {
            $db = Yii::$app->db->pdo;
            $db->beginTransaction();
            /** @noinspection PhpUndefinedMethodInspection */
            $oid = $db->pgsqlLOBCreate();
            /** @noinspection PhpUndefinedMethodInspection */
            $stream = $db->pgsqlLOBOpen($oid, 'w');
            $local = fopen($fileName, 'rb');
            stream_copy_to_stream($local, $stream);
            $local = null;
            $stream = null;

            $stmt = $db->prepare('
               insert into files (name, size, content, type, created_at, created_by, object_type, object_id)
               values (?, ?, lo_get(?), ?, ?, ?, ?, ?) returning id
            ');
            $stmt->execute([
                $name,
                filesize($fileName),
                $oid,
                $type,
                time(),
                !Yii::$app->user->isGuest ? Yii::$app->user->getId() : null,
                $objectType,
                $objectId
            ]);
            $result = $stmt->fetch();
            /** @noinspection PhpUndefinedMethodInspection */
            $db->pgsqlLOBUnlink($oid);
            $db->commit();
            return $result['id'];
        } catch (PDOException $e) {
            Yii::error('Can\'t create file record: ' . $e->getMessage());
            Yii::error($e);
            throw new RuntimeException('Can\'t create file record: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Перезаписывает файл в бд (с существующим id)
     * @param $data
     * @return int
     */
    public static function recreate($data)
    {
        /* @var $f File */
        $f = new static(array_merge([
            'deleted_at' => null,
            'deleted_by' => null
        ], $data));
        if (!$f->save()) {
            throw new RuntimeException('Can\'t create file record: ' . implode(',', $f->getFirstErrors()));
        }
        return $f->id;
    }

    /**
     * Удаляет файл
     * @param int $fileId
     */
    public static function remove($fileId)
    {
        File::deleteAll(['id' => $fileId]);
    }

    /**
     * Отмечает файл как удаленный
     * @param int $fileId
     */
    public static function markDeleted($fileId)
    {
        $f = File::findInfo($fileId);
        $f->deleted_at = time();
        $f->deleted_by = !Yii::$app->user->isGuest ? Yii::$app->user->getId() : null;
        try {
            $f->update(false, ['deleted_at', 'deleted_by']);
        } catch (StaleObjectException|Throwable $e) {
            throw new RuntimeException('Can\'t update: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Снимает отметку об удалении с файла
     * @param int $fileId
     */
    public static function restore($fileId)
    {
        $f = File::findInfo($fileId);
        $f->deleted_at = null;
        $f->deleted_by = null;
        try {
            $f->update(false, ['deleted_at', 'deleted_by']);
        } catch (StaleObjectException|Throwable $e) {
            throw new RuntimeException('Can\'t update: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Привязывает файл к объекту
     * @param int $fileId
     * @param string $objectType
     * @param int $objectId
     */
    public static function linkTo($fileId, $objectType, $objectId)
    {
        $f = File::findInfo($fileId);
        $f->object_type = $objectType;
        $f->object_id = $objectId;
        try {
            $f->update(false, ['object_type', 'object_id']);
        } catch (StaleObjectException|Throwable $e) {
            throw new RuntimeException('Can\'t update: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeletedBy()
    {
        return $this->hasOne(User::class, ['id' => 'deleted_by']);
    }

    /**
     * Возвращает список файлов прикрепленных к объекту
     * @param \main\eav\object\Base $object
     * @param bool $incDel флаг включать в список отмеченных как удаленные
     * @param bool $noContent флаг загружать контент файла
     * @return File[]
     */
    public static function getAttachedFileList($object, $incDel = false, $noContent = true)
    {
        return self::find($noContent)
            ->where(['object_type' => $object->object_type, 'object_id' => $object->id])
            ->andWhere($incDel ? [] : ['deleted_at' => null])
            ->indexBy('id')
            ->orderBy('id')
            ->all();
    }

    public static function getIcon($filename)
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        switch ($ext) {
            case 'zip':
                return 'fa-file-archive-o';
            case 'doc':
            case 'docx':
                return 'fa-file-word-o';
            case 'xls':
            case 'xlsx':
                return 'fa-file-excel-o';
            case 'tif':
            case 'tiff':
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                return ' fa-file-image-o';
            case 'avi':
            case 'mkv':
            case 'mov':
            case 'mp4':
                return 'fa-file-video-o';
            case 'wav':
            case 'mp3':
            case 'flac':
                return 'fa-file-audio-o';
            case 'pdf':
                return 'fa-file-pdf-o';
            case 'ppt':
            case 'pptx':
                return 'fa-file-powerpoint-o';
            case 'txt':
                return 'fa-file-text-o';
            case 'htm':
            case 'html':
            case 'xml':
                return 'fa-file-code-o';
            default:
                return 'fa-file-o';
        }
    }

    /**
     * @param int $id
     * @return static|null
     */
    public static function findInfo($id)
    {
        return static::find(true)->andWhere(['id' => $id])->one();
    }

    /**
     * @param bool $noContent
     * @return \yii\db\ActiveQuery
     */
    public static function find($noContent = false)
    {
        return $noContent ?
            parent::find()->select([
                'id',
                'name',
                'size',
                //'content' exclude content column
                'type',
                'created_at',
                'created_by',
                'deleted_at',
                'deleted_by',
                'object_type',
                'object_id',
            ]) :
            parent::find();
    }
}
