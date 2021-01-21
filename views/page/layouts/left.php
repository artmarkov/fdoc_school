<aside class="main-sidebar">

    <section class="sidebar">

        <!-- search form -->
        <!--<form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>-->
        <!-- /.search form -->

        <?= /** @noinspection PhpUnhandledExceptionInspection */
        main\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu', 'data-widget' => 'tree'],
                'items' => [

                    [
                        'label' => 'Реестры',
                        'icon' => 'fa fa-list',
                        'url' => '#',
                        'options' => ['class' => 'treeview'],
                        'items' => [
                            ['label' => 'Контрагенты', 'icon' => 'fa fa-minus', 'url' => ['client/index']],
                            ['label' => 'Заявления', 'icon' => 'fa fa-minus', 'url' => ['order/index']],
                            ['label' => 'Преподаватели и cотрудники', 'icon' => 'fa fa-minus', 'url' => ['employees/index']],
                            ['label' => 'Ученики', 'icon' => 'fa fa-minus', 'url' => ['students/index']],
                            ['label' => 'Родители', 'icon' => 'fa fa-minus', 'url' => ['parents/index']],
                        ],
                    ],
                    [
                        'label' => 'Организационная работа',
                        'icon' => 'fa fa-university',
                        'url' => '#',
                        'options' => ['class' => 'treeview'],
                        'items' => [
                            ['label' => 'План работы школы', 'icon' => 'fa fa-minus', 'url' => ['attandlog/index']],
                            ['label' => 'Учебные планы', 'icon' => 'fa fa-minus', 'url' => ['attandlog/index']],
                            ['label' => 'Счета за обучение', 'icon' => 'fa fa-minus', 'url' => ['attandlog/index']],
                            ['label' => 'Табель учета педагогических часов', 'icon' => 'fa fa-minus', 'url' => ['attandlog/index']],
                            ['label' => 'Календарный график', 'icon' => 'fa fa-minus', 'url' => ['attandlog/index']],
                            ['label' => 'Методические и творческие работы, сертификаты', 'icon' => 'fa fa-minus', 'url' => ['attandlog/index']],
                        ],
                    ],
                    [
                        'label' => 'Учебная работа',
                        'icon' => 'fa fa-graduation-cap',
                        'url' => '#',
                        'options' => ['class' => 'treeview'],
                        'items' => [
                            ['label' => 'Вступительные экзамены', 'icon' => 'fa fa-minus', 'url' => ['attandlog/index']],
                            ['label' => 'Движение учеников', 'icon' => 'fa fa-minus', 'url' => ['attandlog/index']],
                            ['label' => 'Расписание занятий', 'icon' => 'fa fa-minus', 'url' => ['attandlog/index']],
                            ['label' => 'Календарь мероприятий', 'icon' => 'fa fa-minus', 'url' => ['attandlog/index']],
                            ['label' => 'Журнал успеваемости', 'icon' => 'fa fa-minus', 'url' => ['attandlog/index']],
                        ],
                    ],
                    [
                        'label' => 'Аналитика',
                        'icon' => 'fa fa-bar-chart',
                        'url' => '#',
                        'options' => ['class' => 'treeview'],
                        'items' => [
                            ['label' => 'Журнал посещений', 'icon' => 'fa fa-minus', 'url' => ['attandlog/index']],
                            ['label' => 'Показатели эфективности', 'icon' => 'fa fa-minus', 'url' => ['visual/index']],
                            ['label' => 'Портфолио преподавателей', 'icon' => 'fa fa-minus', 'url' => ['visual/index']],
                            ['label' => 'Контроль исполнения', 'icon' => 'fa fa-minus', 'url' => ['visual/index']],
//                            ['label' => 'Визуализация', 'icon' => 'fa fa-minus', 'url' => ['visual/index']],
                        ],
                    ],
                    [
                        'label' => 'Справочники',
                        'icon' => 'fa fa-briefcase',
                        'url' => '#',
                        'options' => ['class' => 'treeview'],
                        'items' => [
                            ['label' => 'Аудитории школы', 'icon' => 'fa fa-minus', 'url' => ['auditory/index']],
                            ['label' => 'Дисциплины', 'icon' => 'fa fa-minus', 'url' => ['subject/index']],
                            ['label' => 'Сведения об организации', 'icon' => 'fa fa-minus', 'url' => ['oksm/index']],
//                            ['label' => 'Справочник ОКСМ', 'icon' => 'fa fa-minus', 'url' => ['oksm/index']],
                        ],
                    ],
                    [
                        'label' => 'Администрирование',
                        'icon' => 'fa fa-cogs',
                        'url' => '#',
                        'options' => ['class' => 'treeview'],
                        'items' => [
                            ['label' => 'Сеансы пользователей', 'icon' => 'fa fa-minus', 'url' => ['admin/sessions']],
                            ['label' => 'Инструменты админа', 'icon' => 'fa fa-minus', 'url' => ['admin/tools']],
                            ['label' => 'Пользователи', 'icon' => 'fa fa-minus', 'url' => ['user/index'],],
                            ['label' => 'Роли', 'icon' => 'fa fa-minus', 'url' => ['role/index']],
                            ['label' => 'Календарь', 'icon' => 'fa fa-minus', 'url' => ['calendar/index']],
                            ['label' => 'Статус', 'icon' => 'fa fa-minus', 'url' => ['admin/status']],
                        ],
                    ],
                    [
                        'label' => 'Помощь',
                        'icon' => 'fa fa-question-circle',
                        'url' => '#',
                        'options' => ['class' => 'treeview'],
                        'items' => [
                            ['label' => 'Техническая поддержка', 'icon' => 'fa fa-minus', 'url' => ['support/index']],
                            ['label' => 'Руководства пользователя', 'icon' => 'fa fa-minus', 'url' => ['site/help']],
                            ['label' => 'О системе', 'icon' => 'fa fa-minus', 'url' => ['site/about']],
                        ],
                    ],
                    ['label' => 'Debug', 'icon' => 'fa fa-dashboard', 'url' => ['/debug'], 'visible' => isset(Yii::$app->modules['debug'])],
                    ['label' => 'Gii', 'icon' => 'fa fa-file-code-o', 'url' => ['/gii'], 'visible' => Yii::$app->getModule('gii') !== null],
                ],
            ]
        ) ?>

    </section>

</aside>
