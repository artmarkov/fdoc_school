!function ($) {
    "use strict";

    var Webdav = function (element) {
        this.$element = $(element);
        this.initMarkup();
        return this;
    };

    Webdav.prototype = {
        url: null,
        container: null,
        modal: null,
        fileId: null,
        status: null,
        editStartTime: null,
        initMarkup: function () {
            var self = this;
            this.container = this.$element.parent('div');
            this.url = this.$element.data('webdav-url');
            this.fileId = this.$element.val();
            $('<button class="btn btn-primary margin-r-5 btn-webdav-edit"><i class="fa fa-file-word-o"/> Редактировать в MS Word</button>\
           <div class="modal modal-webdav" role="dialog" data-backdrop="static" data-keyboard="false">\
               <div class="modal-dialog">\
                   <div class="modal-content">\
                       <div class="modal-header">\
                           <button type="button" class="close" data-dismiss="modal">&times;</button>\
                           <h4 class="modal-title">Редактирование документа в MS Word</h4>\
                       </div>\
                       <div class="modal-body">\
                       </div>\
                   </div>\
               </div>\
           </div>').insertAfter(this.$element.next());

            this.modal = $('div.modal-webdav div.modal-body', self.container);

            $('button.btn-webdav-edit', this.container).on('click', function (e) {
                e.preventDefault();
                self.open();
            });
        },
        open: function () {
            var self = this;
            this.modal.html('Ожидание открытия документа...');
            $('div.modal-webdav', this.container).modal('show');
            $.when(this.query('open')).done(function (data) {
                if (!data.success) {
                    self.modal.html('<strong>Ошибка</strong>: ' + data.error);
                    return;
                }
                setTimeout(function () { // start lock query
                    self.waitOpen();
                }, 1000);
                location.href = data.result; // open office link
            });
        },
        waitOpen: function () {
            var self = this;
            $.when(this.query('status')).done(function (data) {
                if (!data.success) {
                    self.modal.html('<strong>Ошибка</strong>: ' + data.error);
                    return;
                }
                var age = Math.round((Date.now() - Date.parse(data.result["{DAV:}creationdate"])) / 1000);
                if (age > 60) { // document not opened after 1 min
                    self.cancelOpen();
                } else if ($('div.modal-webdav', self.container).css('display') === 'none') {
                    self.cancelOpen();
                } else if (data.result["{DAV:}lockdiscovery"] !== null) { // document locked
                    setTimeout(function () {
                        self.waitClose();
                    }, 3000);
                    self.editStartTime = Date.now();
                } else {
                    self.modal.html('Ожидание открытия документа, осталось: ' + (120 - age) + ' сек.');
                    setTimeout(function () {
                        self.waitOpen();
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
                $('div.modal-webdav', self.container).modal('hide');
                self.modal.html('');
            });
        },
        waitClose: function () {
            var self = this;
            $.when(this.query('status')).done(function (data) {
                if (!data.success) {
                    self.modal.html('<strong>Ошибка</strong>: ' + data.error);
                    return;
                }
                var age = Math.round((Date.now() - self.editStartTime) / 1000);
                if (data.result["{DAV:}lockdiscovery"] === null) { // document unlocked
                    self.saveClose();
                } else if ($('div.modal-webdav', self.container).css('display') === 'none') {
                    self.cancelOpen();
                } else {
                    self.modal.html('Ожидание завершения редактирования документа, прошло: ' + age + ' сек.');
                    setTimeout(function () {
                        self.waitClose();
                    }, 3000);
                }
            });
        },
        saveClose: function () {
            var self = this;
            $.when(this.query('close')).done(function (data) {
                if (!data.success) {
                    self.modal.html('<strong>Ошибка</strong>: ' + data.error);
                    return;
                }

                // присваем новое id файла, перегружаем форму
                self.$element.val(data.result);
                window.document.getElementById('action').value=null;
                self.$element.closest('form').submit();

                // закрываем модал
                $('div.modal-webdav', self.container).modal('hide');
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

    $.fn.webdav = function () {
        this.each(function () {
            var $this = $(this);
            var data = $this.data('webdav');
            if (!data) {
                $this.data('webdav', (data = new Webdav(this)));
            }
        });
    };

    $.fn.webdav.defaults = {};

}(window.jQuery);
