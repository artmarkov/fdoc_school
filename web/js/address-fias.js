!function ($) {
   "use strict";

   var AddressFias = function (element) {
      this.$element = $(element);
      this.initMarkup();
      return this;
   };

   AddressFias.prototype = {
      delim: ' fias_id=',
      url: null,
      popoverContainer: null,
      initMarkup: function () {
         var self=this;
         this.url = this.$element.data('url');
         var value=$(this.$element).children('input[type=hidden]')[0].value;
         var el=$($(this.$element).children('input[type=text]')[0]);
         var p=value.split(this.delim);
         var id=p.length ===2 ? p[1] : null;
         el.val(p.length ===2 ? p[0] : value); // set address
         el.data('id',id);

         el.typeahead({
            displayText: function(item) {
               return item;
            },
            updater: function(item) {
               return item.name;
            },
            sorter: function(items) {
               return items;
            },
            source: function (query, process) {
               return $.get(self.url+'/find', { query: query }, function (data) {
                  return process(data.result);
               });
            },
            highlighter: function (item) {
               return item.match;
            },
            matcher: function (item) {
               return true;
            }
         });
         el.change(function() {
            var current = el.typeahead("getActive");
            if (current && current.name === el.val()) {
               self.updateValue(current.name,current.id);
            }
            else {
               self.updateValue(el.val(),null);
            }
         });

         this.popoverContainer=$('[data-toggle="popover"]',$(this.$element));
         this.popoverContainer.on('click',function(e){
            e.preventDefault();
         }).popover({
            placement: 'left',
            html: true
         });

         this.updateStatus(id);
      },
      updateStatus: function(id) {
         var self=this;
         if (id) {
            var old_id=this.popoverContainer.attr('data-fias-id');
            if (old_id === id) {
               return;
            }
            this.popoverContainer.children('i').removeClass('fa-spinner fa-pulse fa-check-square fa-exclamation-triangle').addClass('fa-spinner fa-pulse');
            this.popoverContainer.attr('data-content','Запрос информации ...');
            this.popoverContainer.attr('data-fias-id',id);

            $.get(self.url+'/details/'+id, function (data) {
               if (0 == data.result.length) {
                  self.popoverContainer.children('i').removeClass('fa-spinner fa-pulse fa-check-square fa-exclamation-triangle').addClass('fa-exclamation-triangle');
                  self.popoverContainer.attr('data-content','Адрес отсутствует в справочнике ФИАС');
                  return;
               }
               var content='<dl class="dl-horizontal"><dt>Индекс</dt><dd>'+data.result[0].postalcode+'</dd>';
               $.each(data.result, function (k, v) {
                  content+='<dt>'+v.type+'</dt><dd>'+v.name+'</dd>';
               });
               self.popoverContainer.children('i').removeClass('fa-spinner fa-pulse fa-check-square fa-exclamation-triangle').addClass('fa-check-square');
               self.popoverContainer.attr('data-content',content);
            });
         }
         else {
            this.popoverContainer.children('i').removeClass('fa-spinner fa-pulse fa-check-square fa-exclamation-triangle').addClass('fa-exclamation-triangle');
            this.popoverContainer.attr('data-content','Адрес отсутствует в справочнике ФИАС');
         }
      },
      updateValue: function(address,id) {
         $(this.$element).children('input[type=hidden]')[0].value=id ? [address,id].join(this.delim) : address;
         this.updateStatus(id);
      },
   };

   $.fn.addressFias = function () {
      this.each(function () {
         var $this = $(this);
         var data = $this.data('addressfias');
         if (!data) {
            $this.data('addressfias', (data = new AddressFias(this)));
         }
      });
   };

   $.fn.addressFias.defaults = {
   };

}(window.jQuery);
