<?php

namespace main;

use main\models\User;
use main\models\UserSearchQueryTemplates;

class SearchTemplate
{
    protected $user;
    protected $searchSectionAction;
    protected $searchQueryTemplate;

    private function __construct($user, $searchSectionAction)
    {
        $this->user = $user;
        $this->searchSectionAction = $searchSectionAction;
    }

    /**
     * @param User $user
     * @param string $searchSectionAction
     * @return SearchTemplate
     */
    public static function get($user, $searchSectionAction)
    {
        return new self($user, $searchSectionAction);
    }

    /**
     * @param string $name
     * @param array $searchRequest
     * @return $this
     */
    public function setSearchQueryTemplate($name, $searchRequest)
    {
        $this->searchQueryTemplate = new UserSearchQueryTemplates();
        $this->searchQueryTemplate->user_id = $this->user->id;
        $this->searchQueryTemplate->name = $name;
        $this->searchQueryTemplate->search_section_action= $this->searchSectionAction;
        $this->searchQueryTemplate->search_request= json_encode($searchRequest);

        return $this;
    }

    /**
     * @param string $name
     * @param array $searchRequest
     * @return bool
     */
    public function save($name, $searchRequest)
    {
        $existingSearchQueryTemplate = UserSearchQueryTemplates::find()
            ->where(['user_id' => $this->user->id, 'name' => $name, 'search_section_action' => $this->searchSectionAction])
            ->one();

        if ($existingSearchQueryTemplate !== null) {
            \main\ui\Notice::registerError('Шаблон с таким именем уже существует.');
            return false;
        }

        if (empty($searchRequest)) {
            \main\ui\Notice::registerError('Пожалуйста, укажите условия поиска.');
            return false;
        }

        $this->setSearchQueryTemplate($name, $searchRequest);

        if (!$this->searchQueryTemplate->save()) {
            \main\ui\Notice::registerError('Не удалось сохранить шаблон поиска.');
            return false;
        }

        \main\ui\Notice::registerSuccess('Шаблон для поиска был успешно сохранен.');
        return true;
    }

    /**
     * @return array
     */
    public function getList() {
        return UserSearchQueryTemplates::getList($this->user, $this->searchSectionAction);
    }

    /**
     * @param $id
     * @return array
     */
    public function load($id)
    {
        $this->searchQueryTemplate = UserSearchQueryTemplates::findOne($id);

        if ($this->searchQueryTemplate === null) {
            \main\ui\Notice::registerError('Не удалось загрузить шаблон поиска.');
            return [];
        }

        \main\ui\Notice::registerSuccess('Шаблон поиска '.$this->searchQueryTemplate->name.' был успешно загружен.');

        return json_decode($this->searchQueryTemplate->search_request, true);
    }

    /**
     * @param $id
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function delete($id)
    {
        $this->searchQueryTemplate = UserSearchQueryTemplates::findOne($id);

        if(!$this->searchQueryTemplate->delete()){
            \main\ui\Notice::registerError('Не удалось удалить шаблон '.$this->searchQueryTemplate->name.' поиска.');
            return false;
        }

        \main\ui\Notice::registerSuccess('Шаблон для поиска '.$this->searchQueryTemplate->name.' был успешно удален.');
        return true;
    }


}
