(function($) {

    "use strict";
    document.addEventListener('DOMContentLoaded', function(){
        let today = new Date(),
            year = today.getFullYear(),
            month = today.getMonth(),
            monthTag =["Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь"],
            WeeklyTag =["ПН","ВТ","СР","ЧТ","ПТ","СБ","ВС"],
            day = today.getDate(),
            selectedDay,
            onlyChildren=false,
            clubRaces=false,
            raceDayEl=document.getElementById('race-day'),
            onlyChildrenEl=document.getElementById('only-children'),
            clubRacesEl=document.getElementById('club-races')
            // formSlotId = document.getElementById('form-slot_id'),
            // btnSlot = document.getElementsByClassName('btn-slot'),
            // step2Times = document.getElementById('step2-times'),
            // step2Title = document.getElementById('step2-title');
            ;
        function Booking() {
            if (this.getCookie('onlyChildren')==='true') {
                onlyChildren = true;
                onlyChildrenEl.checked=onlyChildren;
            }
            if (this.getCookie('clubRaces')==='true') {
                clubRaces = true;
                clubRacesEl.checked=clubRaces;
            }

            let cSelectedDay=this.getCookie('selected_day')
            if (cSelectedDay) {
                selectedDay = cSelectedDay;
            } else {
                selectedDay = today;
            }
            console.log(selectedDay);
            this.draw();
        }
        // Устанавливаем куки
        Booking.prototype.setCookie = function(name, value, expiredays) {
            if(expiredays) {
                var date = new Date();
                date.setTime(date.getTime() + (expiredays*24*60*60*1000));
                var expires = "; expires=" +date.toGMTString();
            }else{
                var expires = "";
            }
            document.cookie = name + "=" + value + expires + "; path=/";
        };
        //читаем куки
        Booking.prototype.getCookie = function(name) {
            if(document.cookie.length){
                var arrCookie  = document.cookie.split(';'),
                    nameEQ = name + "=";
                for(var i = 0, cLen = arrCookie.length; i < cLen; i++) {
                    var c = arrCookie[i];
                    while (c.charAt(0)==' ') {
                        c = c.substring(1,c.length);

                    }
                    if (c.indexOf(nameEQ) === 0) {
                        return c.substring(nameEQ.length, c.length)
                        // selectedDay =  new Date(c.substring(nameEQ.length, c.length));
                    }
                }
                return null;
            }
        };
        //получаем время из юникс времени в формает ЧЧ:ММ
        Booking.prototype.getTime = function (timeUnix) {
            let hour,minute
            hour = Math.floor(timeUnix/(60*60));
            minute = Math.floor((timeUnix-(hour*60*60))/60);
            return ('00'+hour).slice(-2) + ':' + ('00'+minute).slice(-2);
        }
        //возращаем дату в формате ДД МЕСЯЦА ГГГГ
        Booking.prototype.getDateStr = function (timeUnix) {
            let date=new Date(timeUnix*1000);
            console.log(date);
            return date.getDate() + ' ' +  monthTag[date.getMonth()] + ' ' + date.getFullYear();;
        }
        // Выводим на экран дни недели
        Booking.prototype.draw  = function() {
            let
                that = this,
                wDay =  document.getElementsByClassName('day-date');

            //изменение фильтра Только дети
            onlyChildrenEl.addEventListener('change',function (){that.changeOnlyChildren(this);})
            //изменение фильтра Показать клубные заезды
            clubRacesEl.addEventListener('change',function (){that.changeClubRaces(this);})
            //при клике на дате
            for (var i = 0; i < wDay.length; i++) {
                wDay[i].addEventListener('click', function(){that.clickDay(this);});
            }
        }
        // Выводим заезды
        Booking.prototype.drawRaces  = function(wDay) {
            console.log($ykv_calendar[wDay]);
        }
        Booking.prototype.clickDay = function(o) {
            let selected = document.getElementsByClassName("selected"),
                len = selected.length;

            if(len !== 0){
                selected[0].className = "";
            }
            o.className = "selected";
            selectedDay = new Date(new Date(o.dataset.day * 1000));
            console.log(selectedDay);
            this.drawRaces(o.dataset.wday);
            raceDayEl.innerHTML=this.getDateStr(o.dataset.day);
            this.setCookie('selected_day', selectedDay);

        };

        Booking.prototype.changeOnlyChildren = function(o) {
            onlyChildren=o.checked;
            this.setCookie('onlyChildren',onlyChildren);
            console.log('changeOnlyChildren');
            console.log(o.checked);
        }
        Booking.prototype.changeClubRaces = function(o) {
            console.log('changeClubRaces');
        }

        var booking = new Booking();
    }, false);

})(jQuery);