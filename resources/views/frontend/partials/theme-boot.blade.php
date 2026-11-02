{{-- Apply saved theme before CSS paint to avoid flash --}}
<script>
  (function () {
    try {
      var key = 'fe-theme';
      var saved = localStorage.getItem(key);
      // var theme = saved === 'dark' || saved === 'light'
      var theme = saved === 'light' || saved === 'light'
        ? saved
        //: (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        : (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'light');
      document.documentElement.setAttribute('data-theme', theme);
      document.documentElement.style.colorScheme = theme;
    } catch (e) {
      document.documentElement.setAttribute('data-theme', 'light');
    }
  })();
</script>
