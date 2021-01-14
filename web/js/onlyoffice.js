!function ($) {
    "use strict";

    var OnlyOffice = function (element) {
        this.$element = $(element);
        this.initMarkup();
        return this;
    };

    OnlyOffice.prototype = {
        url: null,
        container: null,
        modal: null,
        fileId: null,
        status: null,
        editStartTime: null,
        initMarkup: function () {
            var self = this;
            this.container = this.$element.parent('div');
            this.url = this.$element.data('onlyoffice-url');
            this.fileId = this.$element.val();
            $('<button class="btn btn-primary margin-r-5 btn-onlyoffice-edit"><i class="fa fa-pencil-square-o"/> Редактировать онлайн</button>\
           <div class="modal modal-onlyoffice" role="dialog" data-backdrop="static" data-keyboard="false">\
               <div class="modal-dialog">\
                   <div class="modal-content">\
                       <div class="modal-header">\
                           <button type="button" class="close" data-dismiss="modal">&times;</button>\
                           <h4 class="modal-title">Редактирование документа онлайн</h4>\
                       </div>\
                       <div class="modal-body">\
                       </div>\
                   </div>\
               </div>\
           </div>').insertAfter(this.$element.next());

            this.modal = $('div.modal-onlyoffice div.modal-body', self.container);

            $('button.btn-onlyoffice-edit', this.container).on('click', function (e) {
                e.preventDefault();
                self.open();
            });
        },
        open: function () {
            var self = this;
            this.modal.html('Ожидание открытия документа...');
            $('div.modal-onlyoffice', this.container).modal('show');
            $.when(this.query('open')).done(function (data) {
                if (!data.success) {
                    self.modal.html('<strong>Ошибка</strong>: ' + data.error);
                    return;
                }
                setTimeout(function () { // start lock query
                    self.editStartTime = Date.now();
                    self.waitClose();
                }, 3000);

                window.open(data.result, '_blank'); // open office link
            });
        },
        waitClose: function () {
            var self = this;
            $.when(this.query('close')).done(function (data) {
                if (!data.success) {
                    self.modal.html('<strong>Ошибка</strong>: ' + data.error);
                    return;
                }
                var age = Math.round((Date.now() - self.editStartTime) / 1000);
                if (data.result !== null) { // document unlocked
                    // присваем новое id файла, перегружаем форму
                    self.$element.val(data.result);
                    window.document.getElementById('action').value=null;
                    self.$element.closest('form').submit();

                    // закрываем модал
                    $('div.modal-onlyoffice', self.container).modal('hide');
                    self.modal.html('');
                } else if ($('div.modal-onlyoffice', self.container).css('display') === 'none') {
                    self.cancelOpen();
                } else {
                    self.modal.html('Ожидание завершения редактирования документа, прошло: ' + age + ' сек.');
                    setTimeout(function () {
                        self.waitClose();
                    }, 3000);
                }
            });
        },
        cancelOpen: function () {
            var self = this;
            $.when(this.query('delete')).done(function (data) {
                if (!data.success) {
                    self.modal.html('<strong>Ошибка</strong>: ' + data.error);
                    return;
                }
                // закрываем модал
                $('div.modal-onlyoffice', self.container).modal('hide');
                self.modal.html('');
            });
        },
        query: function (method) {
            var dfd = new $.Deferred();
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: this.url + '/' + this.fileId + '/' + method
            }).always(function (data) {
                dfd.resolve(data);
            });
            return dfd.promise();
        },
    };

    $.fn.onlyoffice = function () {
        this.each(function () {
            var $this = $(this);
            var data = $this.data('onlyoffice');
            if (!data) {
                $this.data('onlyoffice', (data = new OnlyOffice(this)));
            }
        });
    };

    $.fn.onlyoffice.defaults = {};

}(window.jQuery);
