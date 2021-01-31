<?php

namespace main\forms\activities;

use Yii;
use main\eav\object\Activities;
use main\forms\auth\Acl as form_auth_Acl;
use main\forms\core\Form;
use main\forms\datasource\DbObject as form_datasource_Object;
use main\forms\core\Renderer as form_render_Flight;

abstract class ActivitiesEdit extends \main\forms\ObjEdit
{
    protected $activitiesType;
    protected $timestamp;

    /**
     * ActivitiesEdit constructor.
     * @param $obj \main\eav\object\Base
     * @param $url string
     * @param $aclName string
     * @param $tmplName string
     * @param $activitiesType string
     * @throws \main\forms\core\FormException
     */
    public function __construct($obj, $url, $aclName, $tmplName, $activitiesType)
    {
        $objDS = new form_datasource_Object('', $obj);
        $objAuth = new form_auth_Acl($aclName);
        parent::__construct('f', 'Информация о мероприятии', $objDS, $objAuth);
        $this->setRenderer(new form_render_Flight($tmplName));
        $this->setUrl($url);
        $this->activitiesType = $activitiesType;
        $this->addField('form_control_Select', 'type', 'Тип мероприятия', [
            'list' => \main\eav\object\Activities::TYPE_EVENTS,
            'required' => '1'
        ]);
        $this->addField('form_control_Smartselect', 'author', 'Автор записи', ['type' => 'employees', 'cssSize' => 'sm', 'submit' => 1, 'required' => 1]);
        $this->addField('form_control_Smartselect', 'signer', 'Подписант', ['type' => 'employees', 'cssSize' => 'sm', 'submit' => 1, 'required' => 1]);

        $this->addField('form_control_Text', 'name', 'Название', ['required' => 1, 'hint' => 'Введите официальное название мероприятия, которое указано в положении. Например: «X Международный фестиваль «Ипполитовская хоровая весна». В случае проведения самостоятельного мероприятия вместе с более крупным, укажите название более крупного мероприятия, используя связку «в рамках», например: Мастер-класс по лепке из глины в рамках Большого фестиваля детских школ искусств. Название указывается в кавычках. Если мероприятие посвящено какому-либо событию и (или) памятной дате, вводится пояснение с указанием основной цели мероприятия. Например: Концерт «Симфония весны», посвящённый Международному женскому дню 8 Марта.']);
        $this->addField('form_control_DateTime2', 'time_in', 'Дата и время начала', ['required' => 1, 'hint' => 'Выберите запланированную дату и укажите время проведения мероприятия. Если на момент введения Вы не обладаете информацией о точном времени проведения мероприятия, указывается приблизительное время.']);
        $this->addField('form_control_DateTime2', 'time_out', 'Дата и время окончания', ['required' => 1]);
        $this->addField('form_control_TextFilter', 'places', 'Место проведения', ['required' => 1, 'hint' => 'Укажите место проведения в соответствии с фактическим местом, где проводится мероприятие (в случае, если мероприятие будет проводиться на разных площадках, указывается основное место его проведения. Данные вводятся в формате полного названия места. Например: Парк культуры и отдыха имени Горького). Если мероприятие проводится дистанционно, то местом проведения указывается «сеть интернет».']);
        $this->addField('form_control_Select2', 'departments', 'Отдел', [
            'list' => \RefBook::find('department')->getList(), 'required' => 1]);
        $this->addField('form_control_Select2', 'applicant_teachers', 'Ответственные', [
            'list' => \RefBook::find('teachers')->getList(), 'required' => 0]);

         $this->addField('form_control_Select', 'category', 'Категория', [
             'refbook' => 'activ_category', 'required' => 1]);
         // связанные списки
         $this->addField('form_control_Select', 'subcategory', 'Подкатегория', [
            'list' => \RefBook::find('activ_subcategory', $obj->getval('category'))->getList(), 'required' => 1]);

        $this->addField('form_control_Radio', 'form_partic', 'Форма участия', [
            'list' => \main\eav\object\Activities::FORM_PARTIC, 'required' => 1, 'inline' => false, 'defaultValue' => 1]);
        $this->addField('form_control_TextMoney', 'partic_price', 'Стоимость участия', ['required' => 0, 'hint' => 'Укажите стоимость участия одного человека/организации в рублях.']);

        $this->addField('form_control_Radio', 'visit_poss', 'Возможность посещения', [
            'list' => \main\eav\object\Activities::VISIT_POSS, 'required' => 1, 'inline' => false, 'defaultValue' => 1]);
        $this->addField('form_control_Text', 'poss_content', 'Комментарий по посещению', ['required' => 0, 'hint' => 'Укажите, является запланированное мероприятие открытым или закрытым. Открытое мероприятие - вход возможен для всех желающих (в независимости от того, платный он или нет). Закрытое мероприятие - вход возможен для ограниченного круга лиц, например: «Приглашаются выпускники и их родители».']);

        $this->addField('form_control_Text', 'region_partners', 'Зарубежные и региональные партнеры', ['required' => 0, 'hint' => 'Только для подкатегорий 3.1 и 3.2']);
        $this->addField('form_control_TextUrl', 'site_url', 'Ссылка на мероприятие (сайт/соцсети)', ['required' => 0, 'hint' => '']);
        $this->addField('form_control_TextUrl', 'site_media', 'Ссылка на медиаресурс', ['required' => 0, 'hint' => '']);
        $this->addField('form_control_FileAttachment', 'afisha_file', 'Образ афиши', ['required' => 0, 'hint' => '']);
        $this->addField('form_control_FileAttachment', 'program_file', 'Программа', ['required' => 0, 'hint' => '']);

        $this->addField('form_control_Textarea', 'description', 'Описание мероприятия', ['required' => 0, 'lengthMin' => 1000,'lengthMax' => 4000, 'hint' => 'Введите полное описание мероприятия, включающее важную и существенную информацию. Оно может содержать программу мероприятия, историю возникновения, значимость мероприятия для учреждения и участников, поименное перечисление участников, выступающих, организаторов, направленность мероприятия в форме развернутого ответа. Объем текста - не менее 1000 знаков и не более 4000 знаков.']);
        $this->addField('form_control_Textarea', 'rider', 'Технические требования', ['required' => 0, 'hint' => 'свет, микрофоны, хоровые станки и т.п.']);
        $this->addField('form_control_Textarea', 'result', 'Итоги мероприятия', ['required' => 0 , 'hint' => 'Введите данные о результатах мероприятия с указанием фамилии и имени учащихся, ФИО преподавателей и концертмейстеров в формате: Иванов Иван (преп. Петров П.П., конц. Сидоров С.С.) – лауреат I степени. В случае, если учащийся не получил награды по итогам мероприятия, он вносится как участник. Если участие в мероприятии не состоялось, укажите причину, по которой оно было отменено.']);
        $this->addField('form_control_TextNumber', 'num_users', 'Количество участников', ['required' => 0, 'hint' => 'Укажите, какое количество человек предположительно будет принимать участие в мероприятии. В случае, если Вы сами являетесь организатором, указывается точное количество участников, включая организаторов и преподавателей. Если вы не являетесь организатором указанного мероприятия, то в критерии учитываются только участники непосредственно от учреждения.']);
        $this->addField('form_control_TextNumber', 'num_winners', 'Количество победителей', ['required' => 0]);
        $this->addField('form_control_TextNumber', 'num_visitors', 'Количество зрителей', ['required' => 0]);

        $fApplicant = $this->addFieldset('form_core_Dynamic', 'applicant', 'Преподаватель', $this->getDataSource()->inherit('applicant'), new form_auth_Acl('public'));
        $fApplicant->setRequireOneElement(true);
        $fApplicant->addField('form_control_Smartselect', 'applicant_id', 'Преподаватель', ['type' => 'employees', 'cssSize' => 'sm', 'submit' => 1, 'required' => 0]);

        $fBonus = $fApplicant->addFieldset('form_core_Dynamic', 'bonus', 'Бонус', $this->getDataSource()->inherit('bonus'), new form_auth_Acl('public'));
        $fBonus->setRequireOneElement(false);
        $fBonus->addField('form_control_Month', 'period', 'Период', ['required' => 1]);
        $fBonus->addField('form_control_Text', 'bonus', 'Надбавка', ['required' => 1]);

        // блок подписи
        $this->addField('form_control_Text', 'sign_status', 'Статус подписи');

        $this->addField('form_control_Text', 'content_signer', 'Сообщение подписанта');
        $this->addField('form_control_Text', 'content_author', 'Сообщение автора');


        if ($obj instanceof \main\eav\object\Snapshot) { // режим отображения на прошлую дату
            $this->timestamp = $obj->getTimestamp();
        }

        $a = $this->addActionControl('sign-send', 'Отправить', 'actionSignSend');
        $a->cssClass = 'btn-success';
        $a->iconClass = 'fa fa-send-o';
        $a = $this->addActionControl('sign-by', 'Подписать', 'actionSignBy');
        $a->cssClass = 'btn-success';
        $a->iconClass = 'fa fa-check';
        $a = $this->addActionControl('sign-return', 'Отправить', 'actionSignReturn');
        $a->cssClass = 'btn-info';
        $a->iconClass = 'fa fa-send-o';
        $a = $this->addActionControl('make-changes', 'Внести изменения', 'actionMakeChanges');
        $a->iconClass = 'fa fa-check';
        $a->msgConfirm = 'Вы уверены?';
        $a = $this->addActionControl('sign-send-modal', 'Отправить на подпись', '','main\forms\control\ModalButton');
        $a->href = '#signSendModal';
        $a->cssClass = 'btn-success';
        $a->iconClass = 'fa fa-send-o';
        $a = $this->addActionControl('sign-return-modal', 'Отправить на доработку', '','main\forms\control\ModalButton');
        $a->href = '#signReturnModal';
        $a->cssClass = 'btn-info';
        $a->iconClass = 'fa fa-send-o';
    }

    protected function applyAuth() {
        if ($this->timestamp) {
            $this->setDisplayMode(Form::MODE_READ);
        }
        parent::applyAuth();
    }

    protected function onAfterLoad()
    {
        parent::onAfterLoad();
        if($this->getDataSource()->isNew()) {
            $this->getField('author')->value = Yii::$app->user->id; //TODO Заменить ид из Employees по связи
        }

        if (!Yii::$app->user->identity->isAdmin()) {
            $this->getField('author')->setRenderMode(Form::MODE_READ);
        }
        $f = $this->getField('type');
        $f->value = $this->activitiesType;
        $f->setRenderMode(Form::MODE_READ);
//        $this->getField('name')->setRenderMode(form_dispMode::Read);
        if(2 == $this->getField('form_partic')->value) {
            $this->getField('partic_price')->required = 1;
        }
        if(2 == $this->getField('visit_poss')->value) {
            $this->getField('poss_content')->required = 1;
        }

        switch ($this->getField('sign_status')->value) {
            case 'current':
                $this->getActionControl('sign-by')->setRenderMode(Form::MODE_NONE);
                $this->getActionControl('sign-send-modal')->setRenderMode(Form::MODE_NONE);
                break;
            case 'waiting':
                $this->getActionControl('sign-send-modal')->setRenderMode(Form::MODE_NONE);
                break;
            case 'draft':
                $this->getActionControl('sign-return-modal')->setRenderMode(Form::MODE_NONE);
                $this->getActionControl('make-changes')->setRenderMode(Form::MODE_NONE);
                break;
            case 'expired':
            default:
                $this->getActionControl('sign-by')->setRenderMode(Form::MODE_NONE);
                $this->getActionControl('sign-send-modal')->setRenderMode(Form::MODE_NONE);
                $this->getActionControl('sign-return-modal')->setRenderMode(Form::MODE_NONE);
                $this->getActionControl('make-changes')->setRenderMode(Form::MODE_NONE);
                break;
        }
    }

    protected function asArray()
    {
        $data = parent::asArray();

        /* @var $d Activities */

        $data['stateInfo'] = null;
        if (!$this->getDataSource()->isNew()) {
            $signStatus = $this->getField('sign_status')->value;
            $data['stateInfo'] = [
                'statusName' => Activities::SIGNED_DESC[$signStatus],
                'cssClass' => $signStatus === 'draft' ? 'box-default' : ($signStatus == 'current' ? 'box-success' : ($signStatus == 'waiting' ? 'box-warning' : 'box-danger')),

            ];
            $data['hist'] = $this->getStatusHistory();
        }

        $data['timestamp'] = $this->timestamp;
        $data['versionList'] = $this->getDataSource()->getVersionList();
        $data['version'] = $this->getDataSource()->getVersion();
        $data['isNew'] = $this->getDataSource()->isNew();
        return $data;
    }

    /**
     * Возвращает историю измений статуса
     * @param bool $enrich
     * @return array
     */
    public function getStatusHistory()
    {
        $data = [];
        foreach ($this->getHistory() as $k => $v){
            if (preg_match('/(sign_status|content_signer|content_author)/is', $k)) {
                $data[] = $v;
            }
        }
       array_walk($data, function (&$v, $k)  {
            if($v['field'] == 'f:sign_status') {
                $v['value']  = array_key_exists($v['value'], \main\eav\object\Activities::SIGNED_DESC) ? \main\eav\object\Activities::SIGNED_DESC[$v['value']] : '';
            }
        });
        return $data;
    }

    protected function actionSignSend()
    {
        /** @var Activities $activities */
        if ($this->actionSaveGeneric()) {
            $activities = $this->getDataSource()->getObj();
            $content = \Yii::$app->request->post('author_content');
            $activities->setdata([
                'sign_status' => 'waiting',
                'content_author' => $content,
                ]);
            \main\ui\Notice::registerSuccess('Уведомление отправлено успешно');
            $this->resetForm();
        }
    }
    protected function actionSignBy()
    {
        /** @var Activities $activities */
        if ($this->actionSaveGeneric()) {
            $activities = $this->getDataSource()->getObj();
            $activities->setStatus('current');
            \main\ui\Notice::registerSuccess('Мероприятие переведено в статус "Подписано"');
            $this->resetForm();
        }
    }
    protected function actionSignReturn()
    {
        /** @var Activities $activities */
        if ($this->actionSaveGeneric()) {
            $activities = $this->getDataSource()->getObj();
            $content = \Yii::$app->request->post('signer_content');
            $activities->setdata([
                'sign_status' => 'draft',
                'content_signer' => $content,
            ]);
            \main\ui\Notice::registerInfo('Уведомление отправлено, мероприятие переведено в статус "Черновик"');
            $this->resetForm();
        }
    }
    protected function actionMakeChanges()
    {
        $activities = $this->getDataSource()->getObj();
        $activities->setStatus('draft');
        \main\ui\Notice::registerInfo('Мероприятие переведено в статус "Черновик"');
        $this->resetForm();
    }
}
