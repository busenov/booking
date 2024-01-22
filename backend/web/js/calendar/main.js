(function($) {

	"use strict";

	document.addEventListener('DOMContentLoaded', function(){
        var today = new Date(),
            year = today.getFullYear(),
            month = today.getMonth(),
            monthTag =["Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь"],
            day = today.getDate(),
            days = document.getElementsByTagName('td'),
            selectedDay,
            setDate,
            daysLen = days.length;
    // options should like '2014-01-01'
        function Calendar(selector, options) {
            this.options = options;
            this.draw();
        }

        Calendar.prototype.draw  = function() {
            this.getCookie('selected_day');
            this.getOptions();
            this.drawDays();
            var that = this,
                reset = document.getElementById('reset'),
                pre = document.getElementsByClassName('pre-button'),
                next = document.getElementsByClassName('next-button'),
                generateSlots = document.getElementById('generate-btn'),
                clearSlots = document.getElementById('clear-btn');

                pre[0].addEventListener('click', function(){that.preMonth(); });
                next[0].addEventListener('click', function(){that.nextMonth(); });
                reset.addEventListener('click', function(){that.reset(); });
                generateSlots.addEventListener('click', function(){that.generateSlots(this); });
                clearSlots.addEventListener('click', function(){that.clearSlots(this); });
            while(daysLen--) {
                days[daysLen].addEventListener('click', function(){that.clickDay(this); });
            }

        };

        Calendar.prototype.drawHeader = function(e) {
            var headDay = document.getElementsByClassName('head-day'),
                headMonth = document.getElementsByClassName('head-month'),
                headSlots = document.getElementsByClassName('head-slots')
            ;

                e?headDay[0].innerHTML = e : headDay[0].innerHTML = day;
                headMonth[0].innerHTML = monthTag[month] +" - " + year;
                headSlots[0].innerHTML ='slots';
         };

        Calendar.prototype.drawDays = function() {
            var startDay = new Date(year, month, 1).getDay(),
    //      Ниже указано общее количество дней в этом месяце.
                nDays = new Date(year, month + 1, 0).getDate(),
                n = startDay-1;
    //      Очистить оригинальный стиль и дату
            for(var k = 0; k <42; k++) {
                days[k].innerHTML = '';
                days[k].id = '';
                days[k].className = '';
            }

            for(var i  = 1; i <= nDays ; i++) {
                let $free=0;
                if ($ykv_calendar[year]) {
                    if ($ykv_calendar[year][i]) {
                        $free=$ykv_calendar[year][i]['qty'];
                    }
                }

                days[n].innerHTML = i + "<br>детс<br>свободно: " + $free;
                days[n].dataset.day = i;
                n++;
            }

            for(var j = 0; j < 42; j++) {
                if(days[j].innerHTML === ""){
                    days[j].id = "disabled";

                }else if(j === day + startDay - 1){
                    if((this.options && (month === setDate.getMonth()) && (year === setDate.getFullYear())) || (!this.options && (month === today.getMonth())&&(year===today.getFullYear()))){
                        this.drawHeader(day);
                        days[j].id = "today";
                    }
                }
                if(selectedDay){
                    if((j === selectedDay.getDate() + startDay - 1)&&(month === selectedDay.getMonth())&&(year === selectedDay.getFullYear())){
                    days[j-1].className = "selected";
                    this.drawHeader(selectedDay.getDate());
                    }
                }
            }
        };

        Calendar.prototype.clickDay = function(o) {
            var selected = document.getElementsByClassName("selected"),
                len = selected.length;
            if(len !== 0){
                selected[0].className = "";
            }
            o.className = "selected";
            selectedDay = new Date(year, month, o.dataset.day);
            this.drawHeader(o.dataset.day);
            this.setCookie('selected_day', 1);

        };

        Calendar.prototype.preMonth = function() {
            if(month < 1){
                month = 11;
                year = year - 1;
            }else{
                month = month - 1;
            }
            this.drawHeader(1);
            this.drawDays();
        };

        Calendar.prototype.nextMonth = function() {
            if(month >= 11){
                month = 0;
                year =  year + 1;
            }else{
                month = month + 1;
            }
            this.drawHeader(1);
            this.drawDays();
        };

        Calendar.prototype.getOptions = function() {
            if(this.options){
                var sets = this.options.split('-');
                    setDate = new Date(sets[0], sets[1]-1, sets[2]);
                    day = setDate.getDate();
                    year = setDate.getFullYear();
                    month = setDate.getMonth();
            }
        };

         Calendar.prototype.reset = function() {
             month = today.getMonth();
             year = today.getFullYear();
             day = today.getDate();
             this.options = undefined;
             console.log("reseet");
             this.drawDays();
         };

        Calendar.prototype.setCookie = function(name, expiredays){
            if(expiredays) {
                var date = new Date();
                date.setTime(date.getTime() + (expiredays*24*60*60*1000));
                var expires = "; expires=" +date.toGMTString();
            }else{
                var expires = "";
            }
            document.cookie = name + "=" + selectedDay + expires + "; path=/";
        };

        Calendar.prototype.getCookie = function(name) {
            if(document.cookie.length){
                var arrCookie  = document.cookie.split(';'),
                    nameEQ = name + "=";
                for(var i = 0, cLen = arrCookie.length; i < cLen; i++) {
                    var c = arrCookie[i];
                    while (c.charAt(0)==' ') {
                        c = c.substring(1,c.length);

                    }
                    if (c.indexOf(nameEQ) === 0) {
                        selectedDay =  new Date(c.substring(nameEQ.length, c.length));
                    }
                }
            }
        };

        Calendar.prototype.generateSlots = function(o) {
            let url=o.dataset.url + '?unixTime=' + Math.floor(selectedDay.getTime() / 1000)
            $.get( url, function() {
            })
                .done(function(data) {
                    location. reload();
                })
                .fail(function() {
                    console.log("error generate slots ");
                });
            return false;
        };
        Calendar.prototype.clearSlots = function(o) {
            console.log("click");
            let url=o.dataset.url + '?unixTime=' + Math.floor(selectedDay.getTime() / 1000)
            $.get( url, function() {
            })
                .done(function(data) {
                    location. reload();
                })
                .fail(function() {
                    console.log("error clear slots ");
                });

            return false;
        };

        var calendar = new Calendar();
    
        
}, false);

})(jQuery);
