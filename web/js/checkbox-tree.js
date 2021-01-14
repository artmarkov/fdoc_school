   !function ($) {
   "use strict";

   var CheckboxTree = function (element) {
      this.$element = $(element);
      this.initMarkup();
      this.loadValue();
      return this;
   };

   CheckboxTree.prototype = {
      initMarkup: function () {
         var id=this.$element.attr('id').split(':').join('_');
         this.$element.append('<ul class="root"><br /></ul>');
         if (true) { //!this.options.readonly
            this.$element.append('<button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#'+id+'_modal">Выбрать</button>');
            this.$element.append('<div id="'+id+'_modal" class="modal fade" role="dialog"><div class="modal-dialog"><div class="modal-content">'+
            '<div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Выбор значений</h4></div>'+
            '<div class="modal-body"></div>'+
            '<div class="modal-footer"><button type="button" class="btn btn-default save-choice">Сохранить</button></div></div></div>');
            this.$element.find('div.modal').on('shown.bs.modal', $.proxy(this.loadModal, this));
            this.$element.find('button.save-choice').on('click', $.proxy(this.saveChoice, this));
         }
      },
      getName: function() {
         return this.$element.attr('id')
      },
      getUrl: function(method,id) {
         return this.$element.data('url')+'/'+method+'?id='+id;
      },
      getValue: function() {
         return this.$element.data('value');
      },
      setValue: function(value) {
         this.$element.data('value',value);
      },
      loadValue: function() {
         var self=this;
         this.$element.prepend('<div class="loading"><i class="fa fa-refresh fa-spin"></i> Загрузка...</div>');
         var ul=this.$element.children('ul.root');
         ul.hide();
         $.when(this.getTree(this.getValue())) // получаем список всех видимых узлов
         .done(function(items){
            ul.html(''); // clear contents
            self.drawValuesTree(items,ul);
            self.$element.children('div.loading').remove();
            ul.show();
            app.activateFormFields(ul);
         });
      },
      drawValuesTree: function(items,ul) {
         var self=this;
         $.each(items, function(i, item){
            var checked = self.getValue().indexOf(item[0]) !== -1;
            var li=$('<li><label class="radio"><input type="checkbox" class="icheck" disabled'+(checked ? ' checked': '')+'> '+item[1]+'</label></li>');
            ul.append(li);
            if (checked) {
               ul.append('<input type="hidden" name="'+self.getName()+'[]" value="'+item[0]+'">');
            }
            if (item[2].length > 0) {
               var child_ul=$('<ul class="group-'+item[0]+'"></ul>');
               li.append(child_ul);
               return self.drawValuesTree(item[2],child_ul);
            }
         });
      },
      getParents: function(id) {
         var self=this;
         return $.Deferred(function(dfd) {
            $.ajax(self.getUrl('parents',id)).done(function(resp) {
               dfd.resolve(resp.data);
            });
            /*var result=[];
            var currId=id;
            while(true) {
               var item = self.options.data.filter(function(el) {
                  return el[0] === currId;
               });
               currId=item[0][2];
               if ("0" === currId) {
                  break;
               }
               result.push(currId);
            }
            dfd.resolve(result);*/
         }).promise();
      },
      getChilds: function(id) {
         var self=this;
         id = undefined === id ? "" : id;
         return $.Deferred(function(dfd) {
            $.ajax(self.getUrl('childs',id)).done(function(resp) {
               dfd.resolve(resp.data);
            });
            //console.log('getChilds '+id);
//            var items = self.options.data.filter(function(el) {
//               return el[2] === id;
//            });
//            return dfd.resolve(items);
         }).promise();
      },
      getTree: function(ids) {
         var self=this;
         ids = undefined === ids ? [] : ids;
         return $.Deferred(function(dfd) {
            $.ajax(self.getUrl('tree',ids.join(","))).done(function(resp) {
               dfd.resolve(resp.data);
            });
         }).promise();
      },
      getUnfoldedItems: function(extra) {
         var self=this;
         return $.Deferred(function(dfd) {
            var pids = undefined !== extra ? self.getValue() : [];
            var promises = $.map(self.getValue(), function(id) {
               return self.getParents(id).then(function(result){
                  pids = pids.concat(result);
               });
            });
            $.when.apply(this, promises)
            .then(function(){
               var items = pids.filter(function(elem, pos) {
                  return pids.indexOf(elem) === pos;
               });
               dfd.resolve(items);
            });
         }).promise();
      },
      loadModal: function() {
         var self=this;
         var mb=this.$element.find('div.modal-body');
         if (mb.children('ul.root').length === 0) {
            var ul=$('<ul class="checkbox-tree root"></ul>');
            mb.append(ul);
            self.unfold(ul,undefined,false);
         }
      },
      drawTree: function(items,ul) {
         var self=this;
         $.each(items, function(i, item){
            var li=$('<li><i class="fold fa fa-plus-square-o"></i><label class="radio"><input type="checkbox" class="icheck" value="'+item[0]+'">'+item[1]+'</label></li>');
            ul.append(li);
            $('i.fold',li).on('click', $.proxy(self.unfoldHandler, self));
         });
      },
      unfold: function(ul,id,checked) {
         var self=this;
         $.when(this.getChilds(id)) // получаем список корневых узлов
         .then(function(items) {
            if (items.length>0) {
               ul.find('div').remove();
               self.drawTree(items,ul);
               app.activateFormFields(ul);
               $('input.icheck',ul).on('ifToggled', function () {
                  $(this).closest('li').children('ul').find('input.icheck').iCheck(this.checked?'check':'uncheck');
               });
               $('input.icheck',ul).iCheck(checked?'check':'uncheck');
            }
            else {
               ul.parent().find('i').remove();
               ul.remove();
            }
         });
      },
      unfoldHandler: function(ev) {
         var self=this;
         var el=$(ev.target);
         var expanded=el.hasClass('expanded');
         if (expanded) { // do collapse
            el.removeClass('fa-minus-square-o expanded');
            el.addClass('fa-plus-square-o');
            el.parent().find('ul:first').hide();
         }
         else { // do expand
            el.removeClass('fa-plus-square-o');
            el.addClass('fa-minus-square-o expanded');

            var li=el.parent();
            var ul=li.find('ul:first');
            if (ul.length > 0) {
               ul.show();
            }
            else {
               var checked=li.find('input.icheck:first').prop('checked');
               var id=li.find('input').val();
               ul=$('<ul class="group-'+id+'"><div class="loading"><i class="fa fa-refresh fa-spin"></i> Загрузка...</div></ul>');
               li.append(ul);
               self.unfold(ul,id,checked);
            }
         }
      },
      saveChoice: function() {
         var self=this;
         var ul=this.$element.find('div.modal-body').children('ul.root');
         var values=[];
         $.each(ul.find('label > div > input.icheck:checked'), function(k, el ) {
            values.push($(el).val());
         });
         this.setValue(values);
         this.$element.find('div.modal').modal("hide");
         this.loadValue();
      }
   };

   $.fn.checkboxTree = function () {
      this.each(function () {
         var $this = $(this);
         var data = $this.data('checkboxtree');
         if (!data) {
            $this.data('checkboxtree', (data = new CheckboxTree(this)));
         }
      });
   };

   $.fn.checkboxTree.defaults = {
      readonly: false,
      checkChildren: true, // When checking a box, all children are checked
      uncheckChildren: true, // When unchecking a box, all children are unchecked
      openBranches: null // Array to specify selectors of default expanded branches
   };

}(window.jQuery);
