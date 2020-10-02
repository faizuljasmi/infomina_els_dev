var MyFormType = {
    TEXT: "text",
    SELECT: "select",
    TEXTAREA: "textarea",
    NUMBER: "number",
    DATE_RANGE: "date_range",
    DATE: "date"
};

var MyForm = function({ parent_id, items, onchange }) {
    this.parent = $("#" + parent_id);
    this.form = this.parent.find("form");
    this.items = items;
    this.onchange = onchange;


    // reset form
    this.form.trigger("reset");

    this.registerEvent();
};

MyForm.prototype.required = function(name) {
    let el = this.elByName(name);
    el.attr("required", "required");
    el.removeAttr("readonly");
};
MyForm.prototype.disabled = function(name) {
    let el = this.elByName(name);
    el.attr("readonly", "readonly");
    el.removeAttr("required");
};
MyForm.prototype.optional = function(name) {
    let el = this.elByName(name);
    el.removeAttr("required");
    el.removeAttr("readonly");
};

MyForm.prototype.copy = function(nameFrom, nameTo) {
    return this.set(nameTo, this.get(nameFrom));
};
MyForm.prototype.isEmpty = function(name) {
    let v = this.get(name);
    return v === "" || v === null || typeof v === "undefined";
};
MyForm.prototype.get = function(name) {
    return this.elByName(name).val();
};

MyForm.prototype.set = function(name, v) {
    this.elByName(name).val(v);
};

MyForm.prototype.desc = function({ name, type }) {
    let el = this.elByName(name);
    if (type == MyFormType.SELECT) {
        return el.find("option:selected").text();
    }

    return "";
};
MyForm.prototype.registerEvent = function() {
    let obj = this;
    for (let k in this.items) {
        let item = this.items[k];
        let el = this.elByName(item.name);

        if (obj.onchange) {
            el.change(function(e) {
                e = e.currentTarget;
                let v = $(e).val();
                obj.onchange(v, e, item);
            });
        }
    }
};
MyForm.prototype.elByName = function(name) {
    if (typeof name !== "string" && name) {
        name = name.name;
    }
    return this.parent.find(`[name=${name}]`);
};

MyForm.prototype.initDateRangePicker = function(listNames) {
    for (var i in listNames) {
        let el = listNames[i];
        el.daterangepicker();
    }
};

MyForm.prototype.submit = function() {
    console.log("submit");
};

//Initialize Select2 Elements
// $(".select2").select2();

// //Initialize Select2 Elements
// $(".select2bs4").select2({
//     theme: "bootstrap4"
// });

//Datemask dd/mm/yyyy
// $("#datemask").inputmask("dd/mm/yyyy", { placeholder: "dd/mm/yyyy" });
// //Datemask2 mm/dd/yyyy
// $("#datemask2").inputmask("mm/dd/yyyy", { placeholder: "mm/dd/yyyy" });
// //Money Euro
// $("[data-mask]").inputmask();

//Date range picker
// $("#reservation").daterangepicker();
// //Date range picker with time picker
// $("#reservationtime").daterangepicker({
//     timePicker: true,
//     timePickerIncrement: 30,
//     locale: {
//         format: "MM/DD/YYYY hh:mm A"
//     }
// });
// //Date range as a button
// $("#daterange-btn").daterangepicker(
//     {
//         ranges: {
//             Today: [moment(), moment()],
//             Yesterday: [
//                 moment().subtract(1, "days"),
//                 moment().subtract(1, "days")
//             ],
//             "Last 7 Days": [moment().subtract(6, "days"), moment()],
//             "Last 30 Days": [moment().subtract(29, "days"), moment()],
//             "This Month": [
//                 moment().startOf("month"),
//                 moment().endOf("month")
//             ],
//             "Last Month": [
//                 moment()
//                     .subtract(1, "month")
//                     .startOf("month"),
//                 moment()
//                     .subtract(1, "month")
//                     .endOf("month")
//             ]
//         },
//         startDate: moment().subtract(29, "days"),
//         endDate: moment()
//     },
//     function(start, end) {
//         $("#reportrange span").html(
//             start.format("MMMM D, YYYY") +
//                 " - " +
//                 end.format("MMMM D, YYYY")
//         );
//     }
// );

//Timepicker
// $("#timepicker").datetimepicker({
//     format: "LT"
// });

// //Bootstrap Duallistbox
// $(".duallistbox").bootstrapDualListbox();

// //Colorpicker
// $(".my-colorpicker1").colorpicker();
// //color picker with addon
// $(".my-colorpicker2").colorpicker();

// $(".my-colorpicker2").on("colorpickerChange", function(event) {
//     $(".my-colorpicker2 .fa-square").css("color", event.color.toString());
// });

// $("input[data-bootstrap-switch]").each(function() {
//     $(this).bootstrapSwitch("state", $(this).prop("checked"));
// });

// let pastDates = true, availableDates = false, availableWeekDays = false

// let calendar = new VanillaCalendar({
//     selector: "#myCalendar",
//     onSelect: (data, elem) => {
//         console.log(data, elem)
//     }
// })

// let btnPastDates = document.querySelector('[name=pastDates]')
// btnPastDates.addEventListener('click', () => {
//     pastDates = !pastDates
//     calendar.set({pastDates: pastDates})
//     btnPastDates.innerText = `${(pastDates ? 'Disable' : 'Enable')} past dates`
// })

// let btnAvailableDates = document.querySelector('[name=availableDates]')
// btnAvailableDates.addEventListener('click', () => {
//     availableDates = !availableDates
//     btnAvailableDates.innerText = `${(availableDates ? 'Clear available dates' : 'Set available dates')}`
//     if (!availableDates) {
//         calendar.set({availableDates: [], datesFilter: false})
//         return
//     }
//     let dates = () => {
//         let result = []
//         for (let i = 1; i < 15; ++i) {
//             if (i % 2) continue
//             let date = new Date(new Date().getTime() + (60 * 60 * 24 * 1000) * i)
//             result.push({date: `${String(date.getFullYear())}-${String(date.getMonth() + 1).padStart(2, 0)}-${String(date.getDate()).padStart(2, 0)}`})
//         }
//         return result
//     }
//     calendar.set({availableDates: dates(), availableWeekDays: [], datesFilter: true})
// })

// let btnAvailableWeekDays = document.querySelector('[name=availableWeekDays]')
// btnAvailableWeekDays.addEventListener('click', () => {
//     availableWeekDays = !availableWeekDays
//     btnAvailableWeekDays.innerText = `${(availableWeekDays ? 'Clear available weekdays' : 'Set available weekdays')}`
//     if (!availableWeekDays) {
//         calendar.set({availableWeekDays: [], datesFilter: false})
//         return
//     }
//     let days = [{
//         day: 'monday'
//     }, {
//         day: 'tuesday'
//     }, {
//         day: 'wednesday'
//     }, {
//         day: 'friday'
//     }]
//     calendar.set({availableWeekDays: days, availableDates: [], datesFilter: true})
// })
