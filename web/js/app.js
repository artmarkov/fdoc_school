var app = {
    timeDiff: 0,
    init: function () {
        //var self = this;
        this.enqueueUpdateTime();
        this.initObjManager();
        this.activateFormFields();
        this.bindAjaxModal();
    },
    enqueueUpdateTime: function () {
        var self = this;
        var time = Date.now();
        this.timeDiff = parseInt($('div.datetime').data('time')) - time;
        setTimeout(function () {
            self.updateTime();
        }, 61000 - (time + self.timeDiff) % 60000);
    },
    updateTime: function () {
        var self = this;
        var serverDate = new Date(Date.now() + self.timeDiff);
        $('span.time').html(serverDate.toTimeString().split(' ')[0].substr(0, 5));
        setTimeout(function () {
            self.updateTime();
        }, 60000);
    },
    initObjManager: function () {
        $('div.objmanager').each(function () {
            // setup column filter popovers
            $(document).ready(function () {
                $('[data-toggle="search-popover"]', this).popover({
                    html: true,
                    content: function () {
                        $('div.objmanager div.quick-search input[name="column"]').attr('value', $(this).data('column')); // set selected column to form
                        return $("div.objmanager div.quick-search").html();
                    }
                });
                $('[data-toggle="search-popover"]').on('click', function () {
                    $('[data-toggle="search-popover"]').not(this).popover('hide');
                });
            });

            // setup column dialog
            $('ul.obj-column-list', this).sortable({
                placeholder: "sort-highlight",
                handle: ".handle",
                forcePlaceholderSize: true,
                zIndex: 999999
            });
            if ($('#column-row-template').length) {
                var template = Handlebars.compile($('#column-row-template').html());
                $('a.add-column', this).click(function () { // setup add column button
                    $('ul.obj-column-list').append(template({name: $(this).data('column'), label: $(this).html()}));
                });
                $('ul.obj-column-list', this).click(function (e) { // setup remove column button
                    if ($(e.target).hasClass("remove-column")) {
                        e.preventDefault();
                        $(e.target).parent().parent().remove(); // remove <li>
                    }
                });

                $('#removeall').click(function () {
                    $('ul.obj-column-list').children().each(function (i, val) {
                        val.remove();
                    });
                });

                $('#addall').click(function () {
                    $('ul.obj-column-list').children().each(function (i, val) {
                        val.remove();
                    });
                    $('a.add-column').each(function (i, val) {
                        $('ul.obj-column-list').append(template({name: $(val).data('column'), label: $(val).html()}));
                    });
                });
            }

            // setup advsearch dialog
            var typeMap = {
                'boolean': 'boolean',
                'date': 'date',
                'datetime': 'datetime',
                'time': 'time',
                'double': 'double',
                'email': 'string',
                'integer': 'integer',
                'number': 'double',
                'string': 'string',
                'url': 'string',
                'ip': 'string'
            };
            var operationDefaults = {
                'string': ['contains', 'not_contains', 'equal', 'not_equal', 'begins_with', 'not_begins_with', 'ends_with', 'not_ends_with', 'is_empty', 'is_not_empty'],
                'integer': ['equal', 'not_equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'is_null', 'is_not_null'],
                'double': ['equal', 'not_equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'is_null', 'is_not_null'],
                'date': ['equal', 'not_equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'is_null', 'is_not_null'],
                'time': ['equal', 'not_equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'is_null', 'is_not_null'],
                'datetime': ['equal', 'not_equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'is_null', 'is_not_null'],
                'boolean': ['equal', 'not_equal', 'is_null', 'is_not_null   ']
            };

            var searchAttrs = $('#builder').data('filters');
            var rules = $('#builder').data('rules');
            var filters = [];
            $.each(searchAttrs, function (key, val) {
                if ('string'==typeof(val)) { // eav
                    filters.push({
                       id: key,
                       label: val,
                       operators: ['contains','not_contains','equal','not_equal','less','less_or_equal','greater','greater_or_equal'],
                       type: 'string'
                    });
                    return;
                }
                console.log(val);
                var filter = {
                    id: key,
                    label: val.label,
                    operators: operationDefaults[typeMap[val.type]],
                    type: typeMap[val.type],
                    value_separator: '|'
                };
                if (typeof val.input != 'undefined') {
                    filter['input'] = val.input
                }

                if (typeof val.values != 'undefined') {
                    filter['values'] = val.values
                }

                if (typeof val.operators != 'undefined') {
                    filter['operators'] = val.operators
                }


                if (filter.type === 'date') {
                    filter['input'] = function (rule, input_name) {
                        return '<div class="input-group date"  data-provide="datepicker" id="' + input_name + ':wrapper" data-date-format="dd.mm.yyyy" data-date-clear-btn="true" data-date-language="ru" data-date-today-highlight="true" data-date-autoclose="true" style="width: 174px;">' +
                            '            <input type="text" class="form-control" id="' + input_name + '" name="' + input_name + '"/>' +
                            '            <span class="input-group-addon"><i class="fa fa-calendar"></i></span></div>';
                    }
                }

                filters.push(filter);
            });

            $('#builder').queryBuilder({
                plugins: {
                    'bt-tooltip-errors': {delay: 100},
                    'sortable': null
                },
                filters: filters,
                allow_empty: true
            });

            if (typeof rules === 'object') {
                $('#builder').queryBuilder('setRules', rules);
            }

            $('#btn-reset', this).on('click', function () {
                $('#builder').queryBuilder('reset');
                return false;
            });

            $('#btn-save', this).on('click', function () {
                var result = $('#builder').queryBuilder('getRules');
                if (!$.isEmptyObject(result)) {
                    $('#search_query').val(JSON.stringify(result, null, 2));
                    $('#builder').closest('form').submit();
                }
                return false;
            });

        });
    },
    activateFormFields: function (div) {
        if (div === undefined) {
            $('[data-provide="datepicker"]').each(function (i, el) {
                if ($(el).data('datepicker')) { // already initialized
                    return;
                }
                $(el).datepicker(); // поле: datepicker
            });
            $('select.select2').select2(); // поле: select2
            $('input[type=file]').each(function (i, el) { // поле: bootstrapFileInput
                if (!$(el).hasClass('fileinput')) { // not initialized
                    $(el).bootstrapFileInput();
                    $(el).addClass('fileinput');
                }
            });
            $('input.icheck').each(function (i, el) {
                if (!$(el).next().hasClass('iCheck-helper')) { // not initialized
                    $(el).iCheck({
                        checkboxClass: 'icheckbox_square-blue',
                        radioClass: 'iradio_square-blue',
                        increaseArea: '20%'
                    });
                }
            });
            $('div.checkbox-tree').checkboxTree();
            $('div.address-fias').addressFias();
            $('input.webdav-document').webdav();
            $('input.onlyoffice-document').onlyoffice();
            // workaround - fix box-widget wrong behavior when collapse inner box
            $(document).ready(function () {
                setTimeout(function () {
                    $('.box.disable-box-widget').off('click');
                }, 500)
            });
        } else {
            $('[data-provide="datepicker"]', div).datepicker(); // поле: datepicker
            $('select.select2', div).select2(); // поле: select2
            $('input[type=file]', div).each(function (i, el) { // поле: bootstrapFileInput
                 if (!$(el).hasClass('fileinput')) { // not initialized
                    $(el).bootstrapFileInput();
                     $(el).addClass('fileinput');
                 }
            });
            $('input.icheck', div).each(function (i, el) {
                if (!$(el).next().hasClass('iCheck-helper')) { // not initialized
                    $(el).iCheck({
                        checkboxClass: 'icheckbox_square-blue',
                        radioClass: 'iradio_square-blue',
                        increaseArea: '20%'
                    });
                }
            });
            $('div.checkbox-tree', div).checkboxTree();
            $('div.address-fias',div).addressFias();
            $('input.webdav-document',div).webdav();
            $('input.onlyoffice-document',div).onlyoffice();

        }
    },
    formInitContainer: function (div, htmlid, tmplObject, tmplParams, initSectionCallback) {
        var self = this;
        $('#' + htmlid + '\\:add', div).click(function () { // активировать кнопку добавить
            var id = div.data('lastid');
            div.children('div.box-body').append(tmplObject(Object.assign({id: id}, tmplParams))); // вставить шаблон
            self.formInitSection($('div.box[data-id=' + id + ']', div), htmlid, initSectionCallback);
            self.activateFormFields($('div.box[data-id=' + id + ']', div));
            div.data('lastid', id + 1);
            return false;
        });
        // настройка секций
        div.children('div.box-body').children('div.box').each(function () {
            self.formInitSection($(this), htmlid, initSectionCallback);
        });
    },
    formInitSection: function (div, htmlid, initSectionCallback) {
        var self = this;
        var id = div.data('id');
        $('#' + htmlid + '\\:' + id + '\\:delete', div).click(self.deleteSectionCallback); // активировать кнопку удалить
        if (undefined !== initSectionCallback) {
            initSectionCallback(id, div, htmlid);
        }
        self.activateFormFields(div);
    },
    deleteSectionCallback: function () {
        var b = $(this).closest('div.box');
        b.find('div.box-body').remove();
        b.find('div.box-header div.box-tools').html('<span class="label label-danger">К удалению</span>');
        return false;
    },
    smartselect: function (type, auth, cval, field, form, submit, urlstr, baseurl) {
        L = (screen.width - 1000) / 2;
        T = (screen.height - 600) / 2;
        if (undefined === baseurl) {
            baseurl = '/' + window.location.pathname.split('/')[1];
        }
        openwin = window.open(baseurl + '/smartselect/' + type + '?form=' + form + '&auth=' + auth + '&field=' + field + '&cval=' + cval + '&submit=' + submit + urlstr, 'select', 'top=' + T + ',left=' + L + ',width=1000,height=600,status=no,menubar=no,resizable=yes,toolbar=no,scrollbars=yes,location=no');
    },
    bopenwindow: function (url, name, width, height) {
        L = (screen.width - width) / 2;
        T = (screen.height - height) / 2;
        openwin = window.open(url, name, 'top=' + T + ',left=' + L + ',width=' + width + ',height=' + height + ',status=no,menubar=no,resizable=yes,toolbar=no,scrollbars=yes,location=no');
    },
    bindAjaxModal: function () {
        $('[data-toggle="ajaxModal"]').on('click', function (e) {
            e.preventDefault();
            var m = $('#ajaxModal');
            $('.modal-title', m).text($(this).data('title'));
            $('.modal-body', m).load($(this).attr('href'), function () {
                m.modal({show: true});
            });
        });
    }
};

$(function () {
    "use strict";

    app.init();
});