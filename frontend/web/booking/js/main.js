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
            selectedWDay,
            onlyChildren=false,
            clubRaces=false,
            raceDayEl=document.getElementById('race-day'),
            onlyChildrenEl=document.getElementById('only-children'),
            clubRacesEl=document.getElementById('club-races'),
            racesTableEl=document.getElementsByClassName('result-table')[0],
            nomerPravEl=document.getElementsByClassName('nomer-prav')[0]
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
            let cSelectedWDay=this.getCookie('selected_wday')
            if (cSelectedWDay) {
                selectedWDay = cSelectedWDay;
            } else {
                // selectedWDay = today;
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
        //получаем время из кол-ва секунд с начала дня в формате ЧЧ:ММ
        Booking.prototype.getTimeBySec = function (secBeginningDay) {
            let hour,minute
            hour = Math.floor(secBeginningDay/(60*60));
            minute = Math.floor((secBeginningDay-(hour*60*60))/60);
            return ('00'+hour).slice(-2) + ':' + ('00'+minute).slice(-2);
        }
        //получаем Час из кол-ва секунд с начала дня в формате ЧЧ
        Booking.prototype.getHourBySec = function (secBeginningDay) {
            let hour
            hour = Math.floor(secBeginningDay/(60*60));
            return ('00'+hour).slice(-2);
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
                wDay =  document.getElementsByClassName('week__date');

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
            let
                slotsByDay,
                html='',
                currentTime=null,
                hour=null
            ;

            if (slotsByDay=$ykv_calendar[wDay]) {
                for(let key in slotsByDay) {

                    if (!isNaN(key)) {
                        let currentHour = this.getHourBySec(slotsByDay[key]['begin']);

                        //показывать только детские?
                        if (onlyChildren) {
                            if (slotsByDay[key]['isChild']===false) {
                                continue;
                            }
                        }
                        //показывать клубные?
                        if (!clubRaces) {
                            if (slotsByDay[key]['isClub']!==false) {
                                continue;
                            }
                        }

                        if (currentHour!==hour) {
                            if (hour!==null) {

                            }
                            hour=currentHour;
                            html += '<div class="result-table__time-row">'+ hour +':00</div>'

                        }

                        html += '<div class="result-table__row">';
                        html+='' +
                        '<div class="result-table__info-block">'+
                            '<div class="result-table__time">'+this.getTimeBySec(slotsByDay[key]['begin'])+' - '+this.getTimeBySec(slotsByDay[key]['end'])+'</div>'+
                            '<div class="result-table__info">Свободно: ' + slotsByDay[key]['qty'] + ' мест</div>'+
                        '</div>'+
                        '<button class="result-table__btn btn" data-bs-toggle="modal" data-bs-target="#exampleModal">Забронировать</button>'
                        ;
                        html += '</div>';
                    }
                };

            }
            if (html==='') {
                html='Нет свободных заездов по заданным критериям';
            }

            racesTableEl.innerHTML=html
        }
        Booking.prototype.clickDay = function(o) {
            let selected = document.getElementsByClassName("isActive"),
                len = selected.length;

            if(len !== 0){
                selected[0].classList.remove("isActive");
            }
            console.log(selected)
            console.log(o)
            o.classList.add("isActive");

            selectedDay = new Date(new Date(o.dataset.day * 1000));
            selectedWDay=o.dataset.wday;
            raceDayEl.innerHTML=this.getDateStr(o.dataset.day);
            this.setCookie('selected_day', selectedDay);
            this.setCookie('selected_wday', selectedWDay);

            this.drawRaces(o.dataset.wday);

        };

        Booking.prototype.changeOnlyChildren = function(o) {
            console.log('changeOnlyChildren');
            onlyChildren=o.checked;
            this.setCookie('onlyChildren',onlyChildren);
            this.drawRaces(selectedWDay);
        }
        Booking.prototype.changeClubRaces = function(o) {
            console.log('changeClubRaces');
            clubRaces=o.checked;

            console.log(o.checked);
            console.log(clubRaces);
            //проверяем права
            if (o.checked) {
                nomerPravEl.classList.remove('hidden');
            } else {
                nomerPravEl.classList.add('hidden');
            }

            this.setCookie('clubRaces',clubRaces);
            this.drawRaces(selectedWDay);
        }


        var booking = new Booking();
    }, false);

})(jQuery);

//количество в таблице
function buttonPlus() {
    document.getElementById('inc').value++;
}
function buttonMinus() {
    if (document.getElementById('inc').value > 0) {
        document.getElementById('inc').value--;
    }
}