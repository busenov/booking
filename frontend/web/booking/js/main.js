(function($) {

    "use strict";
    document.addEventListener('DOMContentLoaded', function(){
        let
            //step1
            today = new Date(),
            monthTag =["Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь"],
            selectedDay,
            selectedWDay,
            onlyChildren=false,
            clubRaces=false,
            raceDayEl=document.getElementById('race-day'),
            onlyChildrenEl=document.getElementById('only-children'),
            clubRacesEl=document.getElementById('club-races'),
            racesTableEl=document.getElementsByClassName('result-table')[0],
            nomerPravEl=document.getElementsByClassName('nomer-prav')[0],
            orderModal=document.getElementById('orderModal'),
            step1_weekDatesEl = document.getElementById('week__dates'),
            step1_titleDateEl = document.getElementById('step1_title-date'),
            step1_btnTimerEl=document.getElementById('btn_timer'),
            //step2
            step2_incs=document.getElementsByClassName('btn-inc'),
            step2_btnTimer=document.getElementById('btn_timer'),
            //step3
            //step4
            step4_btnCopyRacer=document.getElementsByClassName('pay-table__btn'),
            step4_btnSave=document.getElementById('save-block__btn')
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
            if ($ykv_step===1) {
                this.draw();
                this.listenerStep1();
            } else if ($ykv_step===2) {
                this.listenerStep2()
            } else if ($ykv_step===3) {

            } else if ($ykv_step===4) {
                this.listenerStep4()
            }
        }
        //==============================================================================================================
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
            return date.getDate() + ' ' +  monthTag[date.getMonth()] + ' ' + date.getFullYear();
        }
        //возращаем дату в формате ДД.ММ.ГГГГ
        Booking.prototype.getDateShortStr = function (timeUnix) {
            console.log(timeUnix);
            let date=new Date(timeUnix*1000);
            return ('00'+date.getDate()).slice(-2) + '.' +  ('00'+(date.getMonth()+1)).slice(-2) + '.' + date.getFullYear();
        }
        //возвращаем числов в формате XX,XXX.XX
        Booking.prototype.getNumberFormat = function (number) {
            return Intl.NumberFormat('ru-RU', { maximumSignificantDigits: 2 }).format(
                number,
            );
        }
        //step1=========================================================================================================
        // Выводим на экран дни недели
        Booking.prototype.draw  = function() {
            console.log('draw');
        }
        Booking.prototype.listenerStep1 = function () {
            console.log('listenerStep1');
            let
                that = this,
                wDay = document.getElementsByClassName('week__date'),
                orderBtn=document.getElementsByClassName('result-table__btn'),
                step1_btnChangeWeek = document.getElementsByClassName('btn-change-week')
            ;
            //при подгружаем предыдущую неделю
            if (step1_btnChangeWeek) {
                for (let i = 0; i < step1_btnChangeWeek.length; i++) {
                    step1_btnChangeWeek[i].addEventListener('click', function(){that.step1_changeWeek(this);});
                }
            }
            //изменение фильтра Только дети
            onlyChildrenEl.addEventListener('change',function (){that.changeOnlyChildren(this);})
            //изменение фильтра Показать клубные заезды
            clubRacesEl.addEventListener('change',function (){that.changeClubRaces(this);})
            //при клике на дате
            for (let i = 0; i < wDay.length; i++) {
                wDay[i].addEventListener('click', function(){that.step1_clickDay(this);});
            }
            //при клике на кнопке заказать
            for (let i = 0; i < orderBtn.length; i++) {
                orderBtn[i].addEventListener('click', function(){that.clickOrder(this);});
            }
            step1_btnTimerEl.addEventListener('click',function(){document.location=this.dataset.action})

        }
        // Выводим заезды
        Booking.prototype.drawRaces  = function(wDay) {
            let
                that = this,
                slotsByDay,
                html='',
                currentTime=null,
                hour=null
            ;

            if (slotsByDay=$ykv_calendar[wDay]) {
                for(let slotId in slotsByDay) {

                    if (!isNaN(slotId)) {
                        let currentHour = this.getHourBySec(slotsByDay[slotId]['begin']);
                        let icon='';
                        let orderSlotQty='';
                        if ($ykv_order && $ykv_order['items'][slotId] && ($ykv_order['items'][slotId]['qty'])) {
                            orderSlotQty=$ykv_order['items'][slotId]['qty'];
                        }
                        if (slotsByDay[slotId]['isChild']) {
                            icon+='<img src="/booking/img/child.png" class="result-table__icon"> ';
                        }
                        if (slotsByDay[slotId]['isClub']) {
                            icon+='<img src="/booking/img/star.png" class="result-table__icon"> ';
                        }
                        //показывать только детские?
                        if (onlyChildren) {
                            if (slotsByDay[slotId]['isChild']===false) {
                                continue;
                            }
                        }
                        //показывать клубные?
                        if (!clubRaces) {
                            if (slotsByDay[slotId]['isClub']!==false) {
                                continue;
                            }
                        }
                        // console.log(slotsByDay[slotId]);
                        if (currentHour!==hour) {
                            hour=currentHour;
                            html += '<div class="result-table__time-row">'+ hour +':00</div>'

                        }
                        let buttonData='';
                        buttonData+='data-action="'+$ykv_urlOrderModalAjax+'?slot_id='+ slotId + '" ';

                        html += '<div class="result-table__row">';
                        html+='' +
                        '<div class="result-table__info-block">'+
                            '<div class="result-table__time">'+this.getTimeBySec(slotsByDay[slotId]['begin'])+' - '+this.getTimeBySec(slotsByDay[slotId]['end'])+'</div>'+
                            '<div class="result-table__info">'+icon+slotsByDay[slotId]['typeName']+' Свободно: ' + slotsByDay[slotId]['free'] + ' мест</div>'+
                            '<div class="result-table__order" id="result-table__slot_id_'+slotId+'">'+ orderSlotQty+ '</div>'+
                        '</div>'+
                        '<button class="result-table__btn btn"'+buttonData+'>Забронировать</button>'
                        ;
                        html += '</div>';
                    }
                };

            }
            if (html==='') {
                html='Нет свободных заездов по заданным критериям';
            }
            racesTableEl.innerHTML=html
            let orderBtn=document.getElementsByClassName('result-table__btn')
            for (let i = 0; i < orderBtn.length; i++) {
                orderBtn[i].addEventListener('click', function(){that.clickOrder(this);});
            }
        }
        Booking.prototype.step1_clickDay = function(o) {
            let selected = document.getElementsByClassName("isActive"),
                len = selected.length;

            if(len !== 0){
                selected[0].classList.remove("isActive");
            }

            o.classList.add("isActive");

            selectedWDay=o.dataset.wday;
            raceDayEl.innerHTML=this.getDateStr(o.dataset.day);

            this.setCookie('selected_day', o.dataset.day);
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
        Booking.prototype.clickOrder = function(o) {
            console.log('clickOrder');
            let
                that = this;
            //заправшиваем аяксом модалное окно и возвращаем его
            $.get({
                url: o.dataset.action,
                processData: false,
                contentType: false,
                success: function(data){
                    if (data.status==='success') {
                        orderModal.innerHTML=data.data;
                        $(orderModal).modal('show');
                        $('#modal-form').on('beforeSubmit', function () {return false;}).on('submit', function(e){console.log(e);that.addToOrder(this);e.preventDefault();});
                        // $('#modal-form').on('beforeSubmit', function(e) {console.log(this);that.addToOrder(this)}).on('submit', function(e){e.preventDefault();});
                    }
                },
                error:function (data){
                    console.log(data)
                }
            });
            return false;
        }
        Booking.prototype.addToOrder = function(o) {
            console.log('addToOrder');
            let formData=new FormData(o);
            console.log(formData );

            $.ajax({
                url: o.getAttribute('action'),         /* Куда пойдет запрос */
                method: o.getAttribute('method'),             /* Метод передачи (post или get) */
                processData: false,
                contentType: false,
                data: formData,
                success: function(data){   /* функция которая будет выполнена после успешного запроса.  */
                    console.log(data)
                    if (data.status==='success') {
                        for (let slotId in data.order.items) {
                            console.log(slotId);
                            let resultTableSlotId = document.getElementById('result-table__slot_id_' + slotId);
                            if (resultTableSlotId) {
                                resultTableSlotId.innerHTML = data.order.items[slotId]['qty'];
                            }
                        }
                        $(orderModal).modal('hide');
                        console.log($ykv_toIssue);
                        if ($ykv_toIssue) {
                            location.href=$ykv_urlNxt
                        }
                    }
                },
                error:function (data){
                    console.log(data)
                }
            });

        }
        Booking.prototype.step1_changeWeek = function (o) {
            // console.log('step1_changeWeek');
            let that=this;
            $.get({
                url: o.dataset.action,
                processData: false,
                contentType: false,
                success: function(data){
                    if (data.status==='success') {
                        step1_weekDatesEl.innerHTML=data.html;
                        $ykv_calendar=JSON.parse(data.calendar);
                        that.listenerStep1();
                        //если сменился месяц
                        step1_titleDateEl.innerHTML=data.month
                    }
                },
                error:function (data){
                    console.log(data)
                }
            });
        }
        //step2=========================================================================================================
        Booking.prototype.listenerStep2 = function () {
            let that=this;
            //При изменении кол-ва в позиции
            if (step2_incs) {
                for (let i = 0; i < step2_incs.length; i++) {
                    step2_incs[i].addEventListener('click', function(){that.step2_clickInc(this)});
                }
            }
            if (step2_btnTimer) {
                let
                    form = document.getElementsByClassName('order-form')[0];
                step2_btnTimer.addEventListener('click', function(){form.submit()});
            }
        }
        //при клике на изменение кол-в позиций
        Booking.prototype.step2_clickInc = function (o) {
            console.log('step2_clickInc');
            let
                that=this,
                input =  o.parentNode.querySelector("input"),
                oldValue=input.value
                ;
            btnInc(o);

            $.get({
                url: input.dataset.action+'&qty='+ input.value,
                processData: false,
                contentType: false,
                success: function(data){   /* Функция которая будет выполнена после успешного запроса.  */
                    console.log(data)
                    if (data.status==='success') {
                        that.step2_changeOrderHtml(data.order);
                    } else {
                        input.value=oldValue;
                    }
                },
                error:function (data){
                    console.log(data)
                    input.value=oldValue;
                }
            });
        };
        Booking.prototype.step2_changeOrderHtml = function (order) {
            let
                that=this,
                totalEl=document.getElementById('order-total');
            for (let slotId in order.items) {
                console.log(slotId);
                if (!isNaN(slotId)) {
                    for (let carTypeId in order.items[slotId]) {
                        if (!isNaN(carTypeId)) {
                            let totalItem = document.getElementById('total_slot_id_' + slotId + '_cartype_id_' + carTypeId);
                            totalItem.innerHTML = that.getNumberFormat(order.items[slotId][carTypeId]['total']);
                        }
                    }
                }
            }
            totalEl.innerHTML = that.getNumberFormat(order.total);
        };
        //step4=========================================================================================================
        Booking.prototype.listenerStep4 = function () {
            let that=this;
            //При изменении кол-ва в позиции
            if (step4_btnCopyRacer) {
                for (let i = 0; i < step4_btnCopyRacer.length; i++) {
                    step4_btnCopyRacer[i].addEventListener('click', function(){that.step4_copyRacers(this)});
                }
            }
            //При на клике на Сохранить
            if (step4_btnSave) {
                // console.log('tut');
                // console.log(document.getElementsByClassName('pay-table__form')[0]);
                step4_btnSave.addEventListener('click', function(){document.getElementsByClassName('pay-table__form')[0].submit()});
            }

        }
        //копируем гонщиков во все остальные заезды, если поля не заполнены
        Booking.prototype.step4_copyRacers = function (o) {
            console.log('step4_copyRacers');
            let
                that=this,
                sources=[],
                races=document.getElementsByClassName('pay-table__row');

            if (races) {
                //копируем исходные данные
                for (let i = 0; i < races.length; i++) {
                    if (races[i].dataset.slot_id===o.dataset.slot_id) {
                        console.log(races[i].getElementsByClassName('racer_name')[0])
                        let
                            name=races[i].getElementsByClassName('racer_name')[0],
                            weight=races[i].getElementsByClassName('racer_weight')[0],
                            height=races[i].getElementsByClassName('racer_height')[0],
                            birthday=races[i].getElementsByClassName('racer_birthday')[0];

                        name = name ? name.value : '';
                        weight = weight ? weight.value : '';
                        height = height ? height.value : '';
                        birthday = birthday ? birthday.value : '';

                        if (name || weight || height || birthday) {
                            sources.push({
                                'name': name,
                                'weight': weight,
                                'height': height,
                                'birthday': birthday
                            });
                        }
                    }
                }
                for (let i = 0; i < races.length; i++) {
                    if (races[i].dataset.slot_id!==o.dataset.slot_id) {
                        let racer=sources.shift()
                        if (racer===undefined) break;

                        let
                            name=races[i].getElementsByClassName('racer_name')[0],
                            weight=races[i].getElementsByClassName('racer_weight')[0],
                            height=races[i].getElementsByClassName('racer_height')[0],
                            birthday=races[i].getElementsByClassName('racer_birthday')[0];

                        name.value=racer.name;
                        weight.value=racer.weight;
                        height.value=racer.height;
                        birthday.value=racer.birthday;
                    }
                }
            }

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
/**
 * Увеличиваем(уменьшаем) счетчик на 1
 * @param o
 */
function btnInc(o) {

    let
        input =  o.parentNode.querySelector("input"),
        min,max;
    if (input.dataset.min) {
        min=input.dataset.min;
    }
    if (input.dataset.max) {
        max = input.dataset.max;
    }

    if (o.dataset.inc==='+') {
        if (max===undefined) {
            input.value++;
        } else {
            if ((Number(input.value) + 1) <= max) {
                input.value++;
            }
        }

    } else if (o.dataset.inc==='-') {
        if (min===undefined) {
            input.value--;
        } else {
            if (input.value>0) {
                if ((Number(input.value) - 1) >= min) {
                    input.value--;
                }
            }
        }
    }
}

function changeOrderOnStep2() {

}