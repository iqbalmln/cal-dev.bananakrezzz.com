
  
  <!--   Core JS Files   -->
  <script src="src/assets/js/core/popper.min.js"></script>
  <script src="src/assets/js/core/bootstrap.min.js"></script>
  <script src="src/assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="src/assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="src/assets/js/plugins/chartjs.min.js"></script>
  
  <script src="src/assets/js/argon-dashboard.min.js?v=2.1.0"></script>
  {{-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>

<script>
  if (window.innerWidth >= 768) {
      document.getElementById('mobile-content').style.display = 'none';
      // document.getElementById('desktop-message').style.display = 'block';
  }
</script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
      // Ambil elemen dengan id 'harga'
      let hargaElement = document.getElementById("harga");
      
      // Ambil teks di dalam elemen
      let textContent = hargaElement.textContent;

      // Pisahkan setiap item dengan '+', lalu trim dan format setiap angka
      let formattedText = textContent
          .split('+')
          .map(item => item.trim())
          .filter(item => item) // Hilangkan item kosong jika ada
          .map(item => Number(item).toLocaleString()) // Format angka dengan koma
          .join(' + ');

      // Setel teks yang sudah diformat kembali ke elemen
      hargaElement.textContent = formattedText;
  });
</script>
</html>