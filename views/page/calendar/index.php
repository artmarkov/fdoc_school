<?php

/* @var $this \yii\web\View */
/* @var $content string */

\main\assets\BootstrapYearCalendarAsset::register($this);
?>

<div class="box box-solid box-primary">
   <div class="box-header with-border">
      <h3 class="box-title">Производственный календарь</h3>
   </div>
   <div class="box-body">
      <div id="calendar" style="overflow: hidden;"></div>
   </div>
   <div class="box-footer">
      <span style="background-color: indianred; color: rgb(255, 255, 255); border-radius: 5px; padding: 2px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> - нерабочий день
   </div>
</div>

<script type="text/javascript">
   $(function() {
      $.ajax({
         type: 'post',
         dataType: 'json',
         url: 'calendar/metadata'
      }).done(function(resp) {
         var c=$('#calendar').calendar({ language: 'ru' });
         c.setMaxDate(new Date(resp.max));
         c.setMinDate(new Date(resp.min));
         c.setCustomDayRenderer(function(element, date) {
            var flag = $(element).attr('data-holiday');
            if (undefined === flag) { // first load
               var day=date.getDay();
               var holiday_at_weekday=[1,2,3,4,5].indexOf(day) >= 0 && -1!==resp.weekday_exceptions.indexOf(date.getTime());
               var holiday_at_weekend=[0,6].indexOf(day) >= 0 && -1===resp.weekend_exceptions.indexOf(date.getTime());
               flag = holiday_at_weekday || holiday_at_weekend ? '1' : '0';
               $(element).attr('data-holiday', flag);
            }
            if ('1' === flag) {
               $(element).css('background-color', 'indianred');
               $(element).css('color', 'white');
               $(element).css('border-radius', '5px');
            }
            else {
               $(element).css('background-color', '');
               $(element).css('color', '');
               $(element).css('border-radius', '');
            }
         });
         $('#calendar').bind('clickDay', function(e) {
            var flag=e.element.children().attr('data-holiday')==='1';
            $.ajax({
               type: 'post',
               dataType: 'json',
               data: {day: e.date.getTime(), flag: flag?0:1},
               url: 'calendar/markday'
            }).done(function(resp) {
               if (resp.success) {
                  e.element.children().attr('data-holiday',flag?'0':'1');
                  c.getCustomDayRenderer()(e.element.children(), e.date);
               }
            });
         });
      });
   });
</script>
