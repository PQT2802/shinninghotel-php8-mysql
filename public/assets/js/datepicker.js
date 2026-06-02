document.addEventListener('DOMContentLoaded', function () {
  if (typeof flatpickr === 'undefined') {
    return;
  }

  var localeCode = document.body.getAttribute('data-locale') || 'en';
  var fpLocale = localeCode === 'vi' && flatpickr.l10ns.vi ? flatpickr.l10ns.vi : flatpickr.l10ns.default;

  function resolvePlaceholder(input) {
    var ph = input.getAttribute('placeholder');
    if (ph) {
      return ph;
    }
    if (input.hasAttribute('data-booking-check-in')) {
      return localeCode === 'vi' ? 'Chọn ngày nhận phòng' : 'Check-in date';
    }
    if (input.hasAttribute('data-booking-check-out')) {
      return localeCode === 'vi' ? 'Chọn ngày trả phòng' : 'Check-out date';
    }
    return localeCode === 'vi' ? 'Chọn ngày' : 'Select date';
  }

  function applyAltInputPlaceholder(instance, placeholder) {
    if (!instance.altInput || !placeholder) {
      return;
    }
    instance.altInput.setAttribute('placeholder', placeholder);
    instance.altInput.classList.add('form-control');
  }

  document.querySelectorAll('[data-datepicker]').forEach(function (input) {
    var isCheckOut = input.hasAttribute('data-booking-check-out');
    var isCheckIn = input.hasAttribute('data-booking-check-in');
    var hiddenName = input.getAttribute('data-date-name') || input.getAttribute('name');
    var placeholder = resolvePlaceholder(input);

    var hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.name = hiddenName;
    if (input.value) {
      hidden.value = input.value;
    }
    input.removeAttribute('name');
    input.setAttribute('autocomplete', 'off');
    input.parentNode.appendChild(hidden);

    var fp = flatpickr(input, {
      locale: fpLocale,
      dateFormat: 'Y-m-d',
      altInput: true,
      altFormat: localeCode === 'vi' ? 'd/m/Y' : 'M j, Y',
      minDate: isCheckOut ? null : 'today',
      defaultDate: input.value || null,
      disableMobile: true,
      onReady: function () {
        applyAltInputPlaceholder(fp, placeholder);
      },
      onChange: function (selectedDates, dateStr) {
        hidden.value = dateStr;
        if (isCheckIn) {
          input.dispatchEvent(new CustomEvent('shinning:checkin', { detail: { date: dateStr } }));
        }
      },
    });

    applyAltInputPlaceholder(fp, placeholder);

    if (isCheckIn) {
      input.addEventListener('shinning:checkin', function (e) {
        var checkOutInput = document.querySelector('[data-booking-check-out]');
        if (!checkOutInput || !checkOutInput._flatpickr) {
          return;
        }
        var d = e.detail.date;
        if (!d) {
          return;
        }
        checkOutInput._flatpickr.set('minDate', d);
        var outVal = checkOutInput.parentNode.querySelector('input[type="hidden"]');
        if (outVal && outVal.value && outVal.value <= d) {
          var next = new Date(d);
          next.setDate(next.getDate() + 1);
          checkOutInput._flatpickr.setDate(next, true);
        }
      });
      if (input.value) {
        input.dispatchEvent(new CustomEvent('shinning:checkin', { detail: { date: input.value } }));
      }
    }

    input._flatpickr = fp;
  });
});
