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
                            ['label' => 'Контрагенты', 'icon' => 'fa fa-user', 'url' => ['client/index']],
                            ['label' => 'Преподаватели и cотрудники', 'icon' => 'fa fa-user', 'url' => ['employees/index']],
                            ['label' => 'Ученики', 'icon' => 'fa fa-graduation-cap', 'url' => ['student/index']],
                            ['label' => 'Родители', 'icon' => 'fa fa-female', 'url' => ['parents/index']],
                        ],
                    ],
                    [
                        'label' => 'Аналитика',
                        'icon' => 'fa fa-bar-chart',
                        'url' => '#',
                        'options' => ['class' => 'treeview'],
                        'items' => [
                            ['label' => 'Визуализация', 'icon' => 'fa fa-globe', 'url' => ['visual/index']],
                        ],
                    ],
                    [
                        'label' => 'Справочники',
                        'icon' => 'fa fa-briefcase',
                        'url' => '#',
                        'options' => ['class' => 'treeview'],
                        'items' => [
                            ['label' => 'Аудитории школы', 'icon' => 'fa fa-home', 'url' => ['oksm/index']],
                            ['label' => 'Организации и площадки', 'icon' => 'fa fa-sitemap', 'url' => ['oksm/index']],
                            ['label' => 'Сведения об организации', 'icon' => 'fa fa-flag', 'url' => ['oksm/index']],
//                            ['label' => 'Справочник ОКСМ', 'icon' => 'fa fa-flag', 'url' => ['oksm/index']],
                        ],
                    ],
                    [
                        'label' => 'Администрирование',
                        'icon' => 'fa fa-cogs',
                        'url' => '#',
                        'options' => ['class' => 'treeview'],
                        'items' => [
                            ['label' => 'Сеансы пользователей', 'icon' => 'fa fa-laptop', 'url' => ['admin/sessions']],
                            ['label' => 'Инструменты админа', 'icon' => 'fa fa-bug', 'url' => ['admin/tools']],
                            ['label' => 'Пользователи', 'icon' => 'fa fa-users', 'url' => ['user/index'],],
                            ['label' => 'Роли', 'icon' => 'fa fa-user-secret', 'url' => ['role/index']],
                            ['label' => 'Календарь', 'icon' => 'fa fa-calendar', 'url' => ['calendar/index']],
                            ['label' => 'Статус', 'icon' => 'fa fa-line-chart', 'url' => ['admin/status']],
                        ],
                    ],
                    [
                        'label' => 'Помощь',
                        'icon' => 'fa fa-question-circle',
                        'url' => '#',
                        'options' => ['class' => 'treeview'],
                        'items' => [
                            ['label' => 'Техническая поддержка', 'icon' => 'fa fa-support', 'url' => ['support/index']],
                            ['label' => 'Руководства пользователя', 'icon' => 'fa fa-book', 'url' => ['site/help']],
                            ['label' => 'О системе', 'icon' => 'fa fa-info-circle', 'url' => ['site/about']],
                        ],
                    ],
                    ['label' => 'Debug', 'icon' => 'fa fa-dashboard', 'url' => ['/debug'], 'visible' => isset(Yii::$app->modules['debug'])],
                    ['label' => 'Gii', 'icon' => 'fa fa-file-code-o', 'url' => ['/gii'], 'visible' => Yii::$app->getModule('gii') !== null],
                ],
            ]
        ) ?>

    </section>

</aside>
