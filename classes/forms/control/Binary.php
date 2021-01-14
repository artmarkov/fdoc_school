<?php

namespace main\forms\control;

use Yii;
use yii\web\UploadedFile;
use yii\helpers\Url;

class Binary extends BaseControl
{

    protected $title = 'Прикрепить файл';

    public function getAttributesString()
    { // необязательные аттрибуты
        $p = parent::getAttributesString();
        $p.=' title="' . $this->title . '"';
        return $p;
    }

    public function getHtmlControl($renderMode)
    {
        // даем возможность приложить файл после первого сохранения формы
        return $renderMode == \main\forms\core\Form::MODE_WRITE ?
        $this->getHtmlControlReadWrite() :
        $this->getHtmlControlReadOnly();
    }

    public function getHtmlControlReadWrite()
    {
        if (is_resource($this->value)) { // кнопка скачать и удалить
            return sprintf('<input type="hidden" name="%s" value="%s"><a href="%s" class="btn btn-info margin-r-5"><i class="fa fa-download"></i> %s</a>
               <button type="submit" id="%s" class="btn btn-danger" title="Удалить контент?"><i class="fa fa-trash"></i></button>
               <script type="text/javascript">
                  $(function () {
                     var that=$(\'#%s\');
                     that.confirmation({
                        btnOkLabel: "Удалить",
                        btnCancelLabel: "Нет",
                        placement: "bottom",
                        onConfirm: function (event, element) {
                           var input = $("<input>").attr("type", "hidden").attr("name", "%s").val("delete");
                           that.closest(\'form\').append($(input));
                        },
                     });
                  });
               </script>', $this->htmlControlName, 'binary', $this->getDownloadLink($this->name), '(' .  Yii::$app->formatter->asShortSize(strlen(stream_get_contents($this->value))) . ')', $this->htmlControlName, str_replace(':', '\\\\:', $this->htmlControlName), $this->htmlControlName
            );
        } else { // кнопка загрузить
            return sprintf('<input type="file" id="%s" name="%s" %s"/>', $this->htmlControlName, $this->htmlControlName, $this->getAttributesString()
            );
        }
    }

    public function getHtmlControlReadOnly()
    {
        if ($this->value) { // кнопка скачать и удалить
            $p = sprintf('<a href="%s" class="btn btn-info margin-r-5"><i class="fa fa-download"></i> %s</a>', $this->getDownloadLink($this->name), '(' . Yii::$app->formatter->asShortSize(strlen(stream_get_contents($this->value))) . ')');
        } else { // кнопка загрузить
            return '<p class="form-control-static">- нет контента -</p>';
        }
        return $p;
    }

    protected function getHtmlStaticControl()
    {
        return $this->getHtmlControlReadOnly();
    }

    public function loadPost($GET = false)
    {
        $name = $this->htmlControlName;
        $file=UploadedFile::getInstanceByName($name);
        if ($file && !$file->hasError && file_exists($file->tempName)) {
            $this->value = $this->handleUpload($file->tempName);
            return true;
        }
        elseif (Yii::$app->request->post($name)=='delete') { // удалить файл
            $this->value=null;
            return true;
        }
        return false;
    }

    protected function handleUpload($tempName)
    {
        return file_get_contents($tempName);
    }

    protected function getDownloadLink($name)
    {
        /* @var $model \yii\db\ActiveRecord */
        $model=$this->ds->getModel();
        $meta=[
            'class' => $model::className(),
            'id'   => $model->primaryKey,
            'name' => $name
        ];
        return Url::to(['site/download-object', 'object' => base64_encode(json_encode($meta))]);
    }

}
