</div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <strong>Copyright &copy; 2025 <span class="text-info">SNACKINAJA</span></strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 1.0
    </div>
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- Bootstrap -->
<script src="<?= $main_url ?>asset/AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE -->
<script src="<?= $main_url ?>asset/AdminLTE-3.2.0/dist/js/adminlte.js"></script>

<!-- OPTIONAL SCRIPTS -->
<script src="<?= $main_url ?>asset/AdminLTE-3.2.0/plugins/chart.js/Chart.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="<?= $main_url ?>asset/AdminLTE-3.2.0/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= $main_url ?>asset/AdminLTE-3.2.0/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= $main_url ?>asset/AdminLTE-3.2.0/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= $main_url ?>asset/AdminLTE-3.2.0/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?= $main_url ?>asset/AdminLTE-3.2.0/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<!-- Select2 -->
<script src="<?= $main_url ?>asset/AdminLTE-3.2.0/plugins/select2/js/select2.full.min.js"></script>

<script>
  $(function() {
    let tema = sessionStorage.getItem('tema');
    if (tema === 'dark-mode') {
      $('body').addClass('dark-mode');
      $('#cekDark').prop('checked', true);
    }

    $(document).on('click', "#cekDark", function(e) {
      if ($('#cekDark').is(':checked')) {
        $('body').addClass('dark-mode');
        sessionStorage.setItem('tema', 'dark-mode');
      } else {
        $('body').removeClass('dark-mode');
        sessionStorage.removeItem('tema');
      }
    });

    $('#tblData').DataTable({
      language: {
        decimal: ',',
        thousands: '.',
        processing: 'Sedang memproses...',
        lengthMenu: 'Tampilkan _MENU_ entri',
        zeroRecords: 'Tidak ditemukan data yang sesuai',
        info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ entri',
        infoEmpty: 'Menampilkan 0 sampai 0 dari 0 entri',
        infoFiltered: '(disaring dari total _MAX_ entri)',
        infoPostFix: '',
        search: 'Cari:',
        emptyTable: 'Tidak ada data pada tabel',
        loadingRecords: 'Memuat...',
        paginate: {
          first: 'Pertama',
          previous: 'Sebelumnya',
          next: 'Berikutnya',
          last: 'Terakhir'
        },
        aria: {
          sortAscending: ': aktifkan untuk mengurutkan kolom menaik',
          sortDescending: ': aktifkan untuk mengurutkan kolom menurun'
        }
      }
    });
  });
</script>

</body>

</html>
