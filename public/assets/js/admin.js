document.addEventListener('DOMContentLoaded', function () {
  var body = document.getElementById('admin-body');
  var toggle = document.getElementById('sidebar-toggle');
  var overlay = document.getElementById('sidebar-overlay');

  function closeSidebar() {
    if (body) body.classList.remove('sidebar-open');
    if (toggle) toggle.setAttribute('aria-expanded', 'false');
  }

  function openSidebar() {
    if (body) body.classList.add('sidebar-open');
    if (toggle) toggle.setAttribute('aria-expanded', 'true');
  }

  if (toggle && body) {
    toggle.addEventListener('click', function () {
      if (body.classList.contains('sidebar-open')) {
        closeSidebar();
      } else {
        openSidebar();
      }
    });
  }

  if (overlay) {
    overlay.addEventListener('click', closeSidebar);
  }

  document.querySelectorAll('.admin-sidebar nav a').forEach(function (link) {
    link.addEventListener('click', function () {
      if (window.matchMedia('(max-width: 992px)').matches) {
        closeSidebar();
      }
    });
  });

  document.querySelectorAll('.copy-url').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var id = btn.getAttribute('data-target');
      var input = document.getElementById(id);
      if (!input) return;
      input.select();
      input.setSelectionRange(0, 99999);
      navigator.clipboard.writeText(input.value).then(function () {
        var prev = btn.textContent;
        btn.textContent = 'Copied!';
        setTimeout(function () { btn.textContent = prev; }, 1500);
      });
    });
  });
});
