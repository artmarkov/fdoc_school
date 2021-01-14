<?php

namespace main\forms\control;

use Yii;
use yii\web\UploadedFile;
use yii\helpers\Url;
use main\models\File;

class FileAttachment extends BaseControl
{

    protected $title = 'Прикрепить файл';
    protected $msgFileError = 'Файл не загружен, возможно указан неправильный путь.';

    public function getAttributesString()
    { // необязательные аттрибуты
        $p = parent::getAttributesString();
        $p .= ' title="' . $this->title . '"';
        return $p;
    }

    public function getHtmlControl($renderMode)
    {
        // даем возможность приложить файл после первого сохранения формы
        return $renderMode == \main\forms\core\Form::MODE_WRITE && !$this->ds->isNew() ?
            $this->getHtmlControlReadWrite() :
            $this->getHtmlControlReadOnly();
    }

    public function getHtmlControlReadWrite()
    {
        if ($this->value) { // кнопка скачать и удалить
            $file = File::findInfo($this->value);
            if ($file->deleted_at) {
                File::restore($file->id);
            }
            return sprintf('<input type="hidden" name="%s" value="%s" %s><a href="%s" class="btn btn-info margin-r-5"><i class="fa fa-download"></i> %s</a>
               <button type="submit" id="%s" class="btn btn-danger" title="Удалить файл?"><i class="fa fa-trash"></i></button>
               <script type="text/javascript">
                  $(function () {
                     var that=$(\'#%s\');
                     that.confirmation({
                        btnOkLabel: "Удалить",
                        btnCancelLabel: "Нет",
                        onConfirm: function (event, element) {
                           var input = $("<input>").attr("type", "hidden").attr("name", "%s").val("delete");
                           window.document.getElementById(\'' . $this->objFieldset->createFieldName('action') . '\').value=null;
                           that.closest(\'form\').append($(input)).submit();
                           event.preventDefault();
                        },
                     });
                  });
               </script>',
                $this->htmlControlName,
                $file->id,
                $this->getAttributesString(),
                $this->getDownloadLink($file->id),
                $file->name . ' (' . Yii::$app->formatter->asShortSize($file->size) . ')',
                $this->htmlControlName, str_replace(':', '\\\\:', $this->htmlControlName), $this->htmlControlName
            );
        } else { // кнопка загрузить
            return sprintf(
                '<input type="file" id="%s" name="%s" %s/>',
                $this->htmlControlName,
                $this->htmlControlName,
                $this->getAttributesString()
            );
        }
    }

    public function getHtmlControlReadOnly()
    {
        if ($this->value) { // кнопка скачать и удалить
            $file = File::findInfo($this->value);
            if ($file->deleted_at) {
                File::restore($file->id);
            }
            $p = sprintf(
                '<a href="%s" class="btn btn-info margin-r-5"><i class="fa fa-download"></i> %s</a>',
                $this->getDownloadLink($file->id),
                $file->name . ' (' . Yii::$app->formatter->asShortSize($file->size) . ')'
            );
        } else { // кнопка загрузить
            return '<p class="form-control-static">- нет файла -</p>';
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
        $file = UploadedFile::getInstanceByName($name);
        if ($file && !$file->hasError && file_exists($file->tempName)) {
            $this->value = (string)File::create([
                'name' => $file->name,
                'size' => $file->size,
                'type' => $file->type,
                'object_type' => $this->ds->getObjType(),
                'object_id' => $this->ds->getObjId(),
                'content' => $this->handleUpload($file->tempName)
            ]);
            return true;
        } elseif (Yii::$app->request->post($name) == 'delete') { // удалить файл
            File::markDeleted($this->ds->getValue($this->name));
            return true;
        } elseif (isset($_POST[$name])) {
            $this->value = $_POST[$name];
            return true;
        }
        return false;
    }

    protected function handleUpload($tempName)
    {
        return fopen($tempName, 'rb');
    }

    protected function getDownloadLink($fileId)
    {
        return Url::to(['site/download', 'id' => $fileId]);
    }

    public function validate($force = false)
    {
        return $this->doValidate();
    }

}
