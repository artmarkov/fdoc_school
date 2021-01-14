<?php

namespace main\forms\control;

class TextMgtsBlcl extends Text
{

    protected $msgDupeError = 'Уже существует карточка с указанным BLCL_ID (#%06d)';

    public function doValidate()
    {
        if (parent::doValidate()) {
            $mgtsId = $this->findDuplicate($this->value);
            if (is_null($mgtsId)) {
                return true;
            }
            $this->validationError = sprintf($this->msgDupeError, $mgtsId);
            return false;
        }
        return false;
    }

    protected function findDuplicate($blclid)
    {
        return $this->ds->findFirstBlclDuplicate($blclid);
    }

}
