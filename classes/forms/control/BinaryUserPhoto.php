<?php

namespace main\forms\control;

use Yii;
use yii\imagine\Image;
use yii\helpers\Url;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

class BinaryUserPhoto extends Binary
{
    protected $title = 'Загрузить';
    protected $msgError = null;
    protected $tumbSize = 160;

    public function getHtmlControlReadWrite()
    {
        if ($this->value) { // кнопка скачать и удалить
            return sprintf('<input type="hidden" name="%s" value="%s">
               <img src="data:image/png;base64,'.base64_encode(stream_get_contents($this->value)).'" class="profile-user-img img-circle">
               <button type="submit" id="%s" class="btn btn-danger" title="Удалить фото?"><i class="fa fa-trash"></i></button>
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
               </script>', $this->htmlControlName, 'binary', $this->htmlControlName, str_replace(':', '\\\\:', $this->htmlControlName), $this->htmlControlName
            );
        } else { // кнопка загрузить
            return sprintf('<img src="'.Url::to('@web/img/nofoto.png').'" class="profile-user-img img-circle" style="margin-right: 5px">'
            . '<input type="file" id="%s" name="%s" %s"/>', $this->htmlControlName, $this->htmlControlName, $this->getAttributesString()
            );
        }
    }

    public function getHtmlControlReadOnly()
    {
        if ($this->value) { // кнопка скачать и удалить
            $p = '<img src="data:image/png;base64,'.base64_encode(stream_get_contents($this->value)).'" class="profile-user-img img-circle">';
        } else { // кнопка загрузить
            return '<p class="form-control-static">- нет контента -</p>';
        }
        return $p;
    }

    protected function handleUpload($tempName)
    {
        try {
            return Image::getImagine()->open($tempName)->thumbnail(new Box($this->tumbSize, $this->tumbSize), ImageInterface::THUMBNAIL_OUTBOUND)->get('png',['quality'=>0]);
        } catch (\Exception $ex) {
            $this->msgError ='Ошибка загрузки изображения';
            Yii::info('Ошибка загрузки изображения '.$ex->getMessage());
        }
        return null;
    }

    public function doValidate()
    {
        if ($this->msgError) {
            $this->validationError = $this->msgError;
            return false;
        }
        return true;
    }


}
