<?php

namespace main\forms\control;

use Yii;
use main\forms\core\Form;
use main\models\File;
use yii\web\UploadedFile;

class FileMultiAttachment extends BaseControl
{
    protected $title = 'Прикрепить файл';
    protected $msgFileError = 'Файл не загружен, возможно указан неправильный путь.';

    public function getAttributesString()
    {
        $p = parent::getAttributesString();
        $p .= ' title="' . $this->title . '"';
        return $p;
    }

    public function getHtmlControl($renderMode)
    {
        return $renderMode == Form::MODE_WRITE ?
            $this->getHtmlControlReadWrite() :
            $this->getHtmlControlReadOnly();
    }

    public function getHtmlControlReadWrite()
    {
        $p = '';
        foreach ($this->value as $fileId) {
            $file = File::findInfo($fileId);
            if (!$this->ds->isNew() && $file->deleted_at) {
                $this->approveFile($file);
            }
            $p .= sprintf('<li style="padding-bottom: 3px;"><input type="hidden" name="%s" value="%s"><a href="%s" class="btn btn-info margin-r-5"><i class="fa fa-download"></i> %s</a>
               <button type="submit" id="%s" class="btn btn-danger" title="Удалить файл?"><i class="fa fa-trash"></i></button>
               <script type="text/javascript">
                  $(function () {
                     var that=$(\'#%s\');
                     that.confirmation({
                        btnOkLabel: "Удалить",
                        btnCancelLabel: "Нет",
                        onConfirm: function (event) {
                           var input = $("<input>").attr("type", "hidden").attr("name", "%s").val("%s");
                           that.closest(\'form\').append($(input));
                           that.closest(\'form\').submit();
                           event.preventDefault();
                        }
                     });
                  });
               </script></li>',
                $this->htmlControlName . '[]',
                $fileId,
                $this->getDownloadLink($fileId),
                $file->name . ' (' . Yii::$app->formatter->asShortSize($file->size) . ')',
                $this->htmlControlName . '_' . $fileId,
                str_replace(':', '\\\\:', $this->htmlControlName . '_' . $fileId),
                $this->htmlControlName . '_delete',
                $fileId
            );
        }
        // кнопка загрузить
        for ($i = 0; $i < 5; $i++) {
            $p .= sprintf('<li%s><input type="file" id="%s" name="%s" %s"/></li>
               <script type="text/javascript">
                  $(function () {
                     var that=$(\'#%s\');
                     that.change(function () {
                         $(\'#%s\').closest(\'li\').show();
                     });
                  });
               </script></li>',
                $i > 0 ? ' style="display:none;"' : '',
                $this->htmlControlName . '_n' . $i,
                $this->htmlControlName . '[]',
                $this->getAttributesString(),
                str_replace(':', '\\\\:', $this->htmlControlName . '_n' . $i),
                str_replace(':', '\\\\:', $this->htmlControlName . '_n' . ($i + 1))
            );
        }
        return '<ul class="list-unstyled">' . $p . '</ul>';
    }

    public function getHtmlControlReadOnly()
    {
        if (!count($this->value)) {
            return '<p class="form-control-static">- нет файлов -</p>';
        }
        $p = '';
        foreach ($this->value as $fileId) {
            $file = File::findInfo($fileId);
            if (!$this->ds->isNew() && $file->deleted_at) {
                $this->approveFile($file);
            }
            $p .= sprintf('<li style="padding-bottom: 3px;"><a href="%s" class="btn btn-info margin-r-5"><i class="fa fa-download"></i> %s</a></li>',
                $this->getDownloadLink($fileId),
                $file->name . ' (' . Yii::$app->formatter->asShortSize($file->size) . ')'
            );
        }
        return '<ul class="list-unstyled">' . $p . '</ul>';
    }

    protected function getHtmlStaticControl()
    {
        return $this->getHtmlControlReadOnly();
    }

    /**
     * @param bool $GET
     * @return bool
     */
    public function loadPost($GET = false)
    {
        $name = $this->htmlControlName;
        if (isset($_POST[$name . '_delete'])) { // удалить файл
            $fileId = $_POST[$name . '_delete'];
            File::markDeleted($fileId);
            $this->value = array_values(array_filter($_POST[$name], function ($v) use ($fileId) {
                return $fileId != $v;
            }));
            return true;
        } else {
            $this->value = isset($_POST[$name]) ? $_POST[$name] : [];
            $uploaded = false;
            $files = UploadedFile::getInstancesByName($name);
            foreach ($files as $file) {
                if ($file->error == 0 && file_exists($file->tempName)) {
                    $fileId = (string)File::create([
                        'name' => $file->name,
                        'size' => $file->size,
                        'type' => $file->type,
                        'object_type' => $this->ds->getObjType(),
                        'object_id' => $this->ds->getObjId(),
                        'content' => $this->handleUpload($file->tempName)
                    ]);
                    if ($this->ds->isNew()) { // form is not saved yet - mark file as deleted
                        File::markDeleted($fileId);
                    }
                    $this->value[] = $fileId;
                    $uploaded = true;
                }
            }
            if ($uploaded) {
                return true;
            }
        }
        return false;
    }

    protected function serializeValue($val)
    {
        return implode(',', $val);
    }

    protected function unserializeValue($val)
    {
        return $val ? explode(',', $val) : [];
    }

    protected function getDownloadLink($fileId)
    {
        return \yii\helpers\Url::to(['site/download', 'id' => $fileId]);
    }

    public function validate($force = false)
    {
        return $this->doValidate();
    }

    protected function handleUpload($tempName)
    {
        return fopen($tempName, 'rb');
    }

    /**
     * @param File $file
     */
    protected function approveFile($file)
    {
        File::restore($file->id);
        $object = $this->ds->getObj();
        File::linkTo($file->id, $object->object_type, $object->id);
    }
}
