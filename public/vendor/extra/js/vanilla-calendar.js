/*
    Vanilla AutoComplete v0.1
    Copyright (c) 2019 Mauro Marssola
    GitHub: https://github.com/marssola/vanilla-calendar
    License: http://www.opensource.org/licenses/mit-license.php
*/
let VanillaCalendar = (function() {
    function VanillaCalendar(options) {
        function addEvent(el, type, handler) {
            if (!el) return;
            if (el.attachEvent) el.attachEvent("on" + type, handler);
            else el.addEventListener(type, handler);
        }

        function removeEvent(el, type, handler) {
            if (!el) return;
            if (el.detachEvent) el.detachEvent("on" + type, handler);
            else el.removeEventListener(type, handler);
        }
        let opts = {
            applied: [],
            holiday: [],
            approved: [],
            selector: null,
            datesFilter: false,
            pastDates: true,
            availableWeekDays: [],
            availableDates: [],
            date: new Date(),
            todaysDate: new Date(),
            button_prev: null,
            button_next: null,
            month: null,
            month_label: null,
            onSelect: (data, elem) => {},
            months: [
                "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December"
            ],
            shortWeekday: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]
        };
        for (let k in options)
            if (opts.hasOwnProperty(k)) opts[k] = options[k];

        let element = document.querySelector(opts.selector);
        if (!element) return;

        const getWeekDay = function(day) {
            return [
                "sunday",
                "monday",
                "tuesday",
                "wednesday",
                "thursday",
                "friday",
                "saturday"
            ][day];
        };

        /**
         * @author Wan Zulsarhan
         * @return YYYY-MM-DD
         */
        const getDateInput = function(date) {
            if (typeof date === "string") {
                date = date.replaceAll("-", "");
                return (
                    date.substr(0, 4) +
                    "-" +
                    date.substr(4, 2) +
                    "-" +
                    date.substr(6, 2)
                );
            }
            return (
                date.getFullYear() +
                "-" +
                String(date.getMonth() + 1).padStart("2", 0) +
                "-" +
                String(date.getDate()).padStart("2", 0)
            );
        };
        /**
         * @author Wan Zulsarhan
         * @return YYYYMMDD
         */
        const getDateDb = function(date) {
            if (typeof date === "string") {
                date = date.replaceAll("-", "");
                return date;
            }
            return (
                date.getFullYear() +
                String(date.getMonth() + 1).padStart("2", 0) +
                String(date.getDate()).padStart("2", 0)
            );
        };

        /**
         * @author Wan Zulsarhan
         * @return dateObj
         */
        const getDateObj = function(dateStr) {
            let dateDb = getDateDb(dateStr);
            return moment(dateDb, "YYYYMMDD").toDate();
        };

        /**
         * @author Wan Zulsarhan
         * @return Boolean
         */
        const isWeekend = function(dateObj) {
            return [0, 6].indexOf(dateObj.getDay()) >= 0;
        };

        const isHoliday = function(dateObj) {
            let dateDb = getDateDb(dateObj);
            return opts.holiday.indexOf(dateDb) >= 0;
        };

        //Added by Faizul
        const isApplied = function(dateObj) {
            let dateDb = getDateDb(dateObj);
            return opts.applied.indexOf(dateDb) >= 0;
        };

        //Added by Faizul
        const isApproved = function(dateObj) {
            let dateDb = getDateDb(dateObj);
            return opts.approved.indexOf(dateDb) >= 0;
        };

        const createDay = function(date) {
            let newDayElem = document.createElement("div");
            let dateElem = document.createElement("span");
            dateElem.innerHTML = date.getDate();
            newDayElem.className = "vanilla-calendar-date";
            newDayElem.setAttribute("data-calendar-date", date);

            let available_week_day = opts.availableWeekDays.filter(
                f =>
                f.day === date.getDay() ||
                f.day === getWeekDay(date.getDay())
            );
            let available_date = opts.availableDates.filter(
                f =>
                f.date ===
                date.getFullYear() +
                "-" +
                String(date.getMonth() + 1).padStart("2", 0) +
                "-" +
                String(date.getDate()).padStart("2", 0)
            );

            if (date.getDate() === 1) {
                newDayElem.style.marginLeft = date.getDay() * 14.28 + "%";
            }
            if (
                opts.date.getTime() <= opts.todaysDate.getTime() - 1 &&
                !opts.pastDates
            ) {
                newDayElem.classList.add("vanilla-calendar-date--disabled");
            } else {
                if (opts.datesFilter) {
                    if (available_week_day.length) {
                        newDayElem.classList.add(
                            "vanilla-calendar-date--active"
                        );
                        newDayElem.setAttribute(
                            "data-calendar-data",
                            JSON.stringify(available_week_day[0])
                        );
                        newDayElem.setAttribute(
                            "data-calendar-status",
                            "active"
                        );
                    } else if (available_date.length) {
                        newDayElem.classList.add(
                            "vanilla-calendar-date--active"
                        );
                        newDayElem.setAttribute(
                            "data-calendar-data",
                            JSON.stringify(available_date[0])
                        );
                        newDayElem.setAttribute(
                            "data-calendar-status",
                            "active"
                        );
                    } else {
                        newDayElem.classList.add(
                            "vanilla-calendar-date--disabled"
                        );
                    }
                } else {
                    newDayElem.classList.add("vanilla-calendar-date--active");
                    newDayElem.setAttribute("data-calendar-status", "active");
                }
            }
            if (date.toString() === opts.todaysDate.toString()) {
                newDayElem.classList.add("vanilla-calendar-date--today");
            }

            // wzs21
            //newDayElem.classList.add('vanilla-calendar-date--'+getDateDb(date));
            let dateDb = getDateDb(date);
            if (opts.applied.indexOf(dateDb) >= 0) {
                newDayElem.classList.add("vanilla-calendar-date--applied");
            }
            if (opts.approved.indexOf(dateDb) >= 0) {
                newDayElem.classList.add("vanilla-calendar-date--approved");
            }
            if (opts.holiday.indexOf(dateDb) >= 0) {
                newDayElem.classList.add("vanilla-calendar-date--holiday");
            }
            if (isWeekend(date) && opts.holiday.indexOf(dateDb) <= 0) {
                newDayElem.classList.add("vanilla-calendar-date--weekend");
            }

            newDayElem.appendChild(dateElem);
            opts.month.appendChild(newDayElem);
        };

        const removeActiveClass = function() {
            document
                .querySelectorAll(".vanilla-calendar-date--selected")
                .forEach(s => {
                    s.classList.remove("vanilla-calendar-date--selected");
                });
        };

        const selectDate = function() {
            let activeDates = element.querySelectorAll(
                "[data-calendar-status=active]"
            );
            activeDates.forEach(date => {
                date.addEventListener("click", function() {
                    removeActiveClass();
                    let datas = this.dataset;
                    let data = {};
                    if (datas.calendarDate) data.date = datas.calendarDate;
                    if (datas.calendarData)
                        data.data = JSON.parse(datas.calendarData);
                    opts.onSelect(data, this);
                    this.classList.add("vanilla-calendar-date--selected");
                });
            });
        };

        const createMonth = function() {
            clearCalendar();
            let currentMonth = opts.date.getMonth();
            while (opts.date.getMonth() === currentMonth) {
                createDay(opts.date);
                opts.date.setDate(opts.date.getDate() + 1);
            }

            opts.date.setDate(1);
            opts.date.setMonth(opts.date.getMonth() - 1);
            opts.month_label.innerHTML =
                opts.months[opts.date.getMonth()] +
                " " +
                opts.date.getFullYear();
            selectDate();
        };

        const monthPrev = function() {
            opts.date.setMonth(opts.date.getMonth() - 1);
            createMonth();
        };

        const monthNext = function() {
            opts.date.setMonth(opts.date.getMonth() + 1);
            createMonth();
        };

        const clearCalendar = function() {
            opts.month.innerHTML = "";
        };

        const createCalendar = function() {
            document.querySelector(opts.selector).innerHTML = `
            <div class="vanilla-calendar-header">
                <button type="button" class="vanilla-calendar-btn" data-calendar-toggle="previous"><svg height="24" version="1.1" viewbox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"></path></svg></button>
                <div class="vanilla-calendar-header__label" data-calendar-label="month"></div>
                <button type="button" class="vanilla-calendar-btn" data-calendar-toggle="next"><svg height="24" version="1.1" viewbox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z"></path></svg></button>
            </div>
            <div class="vanilla-calendar-legend">
                <div class="vanilla-calendar-legend-item">
                    <div class="legend legend-holiday"></div><small>Holiday</small>
                </div>
                <div class="vanilla-calendar-legend-item">
                    <div class="legend legend-weekend"></div><small>Weekend</small>
                </div>
                <div class="vanilla-calendar-legend-item">
                    <div class="legend legend-applied"></div><small>Pending</small>
                </div>
                <div class="vanilla-calendar-legend-item">
                    <div class="legend legend-approved"></div><small>Approved</small>
                </div>
            </div>
            <div class="vanilla-calendar-week"></div>
            <div class="vanilla-calendar-body" data-calendar-area="month"></div>
            `;
        };
        const setWeekDayHeader = function() {
            document.querySelector(
                `${opts.selector} .vanilla-calendar-week`
            ).innerHTML = `
                <span>${opts.shortWeekday[0]}</span>
                <span>${opts.shortWeekday[1]}</span>
                <span>${opts.shortWeekday[2]}</span>
                <span>${opts.shortWeekday[3]}</span>
                <span>${opts.shortWeekday[4]}</span>
                <span>${opts.shortWeekday[5]}</span>
                <span>${opts.shortWeekday[6]}</span>
            `;
        };

        this.init = function() {
            createCalendar();
            opts.button_prev = document.querySelector(
                opts.selector + " [data-calendar-toggle=previous]"
            );
            opts.button_next = document.querySelector(
                opts.selector + " [data-calendar-toggle=next]"
            );
            opts.month = document.querySelector(
                opts.selector + " [data-calendar-area=month]"
            );
            opts.month_label = document.querySelector(
                opts.selector + " [data-calendar-label=month]"
            );

            opts.date.setDate(1);
            createMonth();
            setWeekDayHeader();
            addEvent(opts.button_prev, "click", monthPrev);
            addEvent(opts.button_next, "click", monthNext);
        };

        this.destroy = function() {
            removeEvent(opts.button_prev, "click", monthPrev);
            removeEvent(opts.button_next, "click", monthNext);
            clearCalendar();
            document.querySelector(opts.selector).innerHTML = "";
        };

        this.reset = function() {
            this.destroy();
            this.init();
        };

        this.set = function(options) {
            for (let k in options)
                if (opts.hasOwnProperty(k)) opts[k] = options[k];
            createMonth();
            //this.reset()
        };

        /**
         * @return dateDb : YYYYMMDD
         */
        this.getDateDb = function(dateObj) {
            return getDateDb(dateObj);
        };

        /**
         * @return dateInput : YYYY-MM-DD
         */
        this.getDateInput = function(dateObj) {
            return getDateInput(dateObj);
        };

        this.isDateBigger = function(date1, date2) {
            date1 = this.getDateDb(date1);
            date2 = this.getDateDb(date2);
            return date1 > date2;
        };

        this.isDateSmaller = function(date1, date2) {
            date1 = this.getDateDb(date1);
            date2 = this.getDateDb(date2);
            return date1 < date2;
        };

        this.isDateEqual = function(date1, date2) {
            date1 = this.getDateDb(date1);
            date2 = this.getDateDb(date2);
            return date1 == date2;
        };

        this.isWeekend = function(dateStr) {
            let dateObj = getDateObj(dateStr);
            return isWeekend(dateObj);
        };

        this.isHoliday = function(dateStr) {
            let dateObj = getDateObj(dateStr);
            return isHoliday(dateObj);
        };

        //Added by Faizul
        this.isApplied = function(dateStr) {
            let dateObj = getDateObj(dateStr);
            return isApplied(dateObj);
        };

        this.isApproved = function(dateStr) {
            let dateObj = getDateObj(dateStr);
            return isApproved(dateObj);
        };

        this.getTotalWorkingDay = function(dateFrom, dateTo) {
            dateFrom = this.getDateDb(dateFrom);
            dateTo = this.getDateDb(dateTo);
            if (dateFrom == dateTo) {
                return 1;
            }

            let attempt = 200;
            let nextDateDb = dateFrom;
            let total = 1;
            for (let i = 0; i < attempt; i++) {
                let nextDay = this.nextDay(nextDateDb);
                nextDateDb = getDateDb(nextDay);

                if (!isWeekend(nextDay) && !isHoliday(nextDay)) {
                    total++;
                }
                if (nextDateDb == dateTo) {
                    return total;
                }
            }

            return total;
        };

        this.getTotalDays = function(dateFrom, dateTo) {
            dateFrom = this.getDateDb(dateFrom);
            dateTo = this.getDateDb(dateTo);
            if (dateFrom == dateTo) {
                return 1;
            }
            let attempt = 200;
            let nextDateDb = dateFrom;
            let total = 1;
            for (let i = 0; i < attempt; i++) {
                let nextDay = this.nextDay(nextDateDb);
                nextDateDb = getDateDb(nextDay);
                total++;
                if (nextDateDb == dateTo) {
                    return total;
                }
            }
            return total;
        };

        this.today = function() {
                return new Date();
            }
            /**
             * @return dateObj
             */
        this.getNextWorkingDay = function(date) {
            date = this.getDateDb(date);

            let attempt = 20;
            let nextDateDb = date;
            for (let i = 0; i < attempt; i++) {
                let nextDay = this.nextDay(nextDateDb);

                nextDateDb = getDateDb(nextDay);
                if (isWeekend(nextDay)) {
                    console.log("Weekend", nextDay);
                    continue;
                } else if (isHoliday(nextDay)) {
                    console.log("Holiday", nextDay);
                    continue;
                } else {
                    return nextDay;
                }
            }
            return null;
        };

        /**
         * @return dateObj
         */
        this.getThreePrevWorkingDay = function(date) {
            date = this.getDateDb(date);
            let attempt = 20;
            let count = 0;
            let prevDateDb = date;
            for (let i = 0; i < attempt; i++) {
                let prevDay = this.prevDay(prevDateDb);
                prevDateDb = getDateDb(prevDay);
                if (isWeekend(prevDay)) {
                    continue;
                } else if (isHoliday(prevDay)) {
                    continue;
                } else {
                    count++;
                    if (count == 3) {
                        return prevDay
                    } else {
                        continue;
                    }
                }
            }
        };

        /**
         * @return dateObj
         */
        this.getPrevWeekWorkingDay = function(date) {
            date = this.getDateDb(date);
            let attempt = 20;
            let count = 0;
            let prevDateDb = date;
            for (let i = 0; i < attempt; i++) {
                let prevDay = this.prevDay(prevDateDb);
                prevDateDb = getDateDb(prevDay);
                if (isWeekend(prevDay)) {
                    continue;
                } else if (isHoliday(prevDay)) {
                    continue;
                } else {
                    count++;
                    if (count == 7) {
                        return prevDay
                    } else {
                        continue;
                    }
                }
            }
        };

        this.nextMonth = function(dateDb) {
            return moment(dateDb, "YYYYMMDD")
                .add(30, "days")
                .toDate();
        };

        this.prevMonth = function(dateDb) {
            return moment(dateDb, "YYYYMMDD")
                .subtract(30, "days")
                .toDate();
        };

        this.prevDay = function(dateDb) {
            return moment(dateDb, "YYYYMMDD")
                .subtract(1, "days")
                .toDate();
        };

        /**
         * @return dateObj
         */
        this.nextDay = function(dateDb) {
            return moment(dateDb, "YYYYMMDD")
                .add(1, "days")
                .toDate();
        };

        this.init();
    }
    return VanillaCalendar;
})();

window.VanillaCalendar = VanillaCalendar;