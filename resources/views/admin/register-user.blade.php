@extends('admin.layouts.app')
@section('navbar')
@if(Auth::guest('admin'))
@include('admin.layouts.partial.top-navbar')
@else
@include('admin.layouts.partial.main-header')
@endif
@endsection
@section('main-sidebar')
@if(Auth::user()->role())
@include('admin.layouts.partial.main-sidebar')
@endif
@endsection
@push('css')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css">
<style type="text/css">
  .buttons-excel {
    background: #00A656 !important;
    color: #fff !important;
    border: 1px solid #fff !important;
  }

  .buttons-copy {
    background: #F92672 !important;
    color: #fff !important;
    border: 1px solid #fff !important;
  }

  .buttons-csv {
    background: #357CA5 !important;
    color: #fff !important;
    border: 1px solid #fff !important;
  }

  .buttons-pdf {
    background: #D73925 !important;
    color: #fff !important;
    border: 1px solid #fff !important;
  }

  .buttons-print {
    background: #F99502 !important;
    color: #fff !important;
    border: 1px solid #fff !important;
  }
</style>
@endpush
@section('main-content')
<div id="message" class="navbar-fixed-top"></div>
<!-- DataTable -->

<!-- Small boxes (Stat box) -->
<div class="box" id="load_table">
  <div class="box-header with-border">
    <h3 class="box-title">Register User</h3>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
        <i class="fa fa-minus"></i></button>
      <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
        <i class="fa fa-times"></i></button>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
    <table id="example1" class="table table-bordered table-striped display nowrap" cellspacing="0" width="100%">
      <thead id="load_head">
        <tr>
          <th>S.No.</th>
          <th>Photo</th>
          <th>Name</th>
          <th>Designation</th>
          <th>Email</th>
          <th>Phone</th>
          <th>University</th>
          {{-- <th>Delete</th> --}}
        </tr>
      </thead>
      <tbody>
        @php $i=0; @endphp
        @foreach ($register_user as $user)
        @php $i++; @endphp
        <tr>
          <td>{{ $i }}</td>
          <td><img style="width:57px;" src="{{ asset('storage/employee_photo/'.$user->Photo) }}"></td>
          <td>{{ $user->FirstName }} {{ $user->LastName }}</td>
          <td>{{ $user->Possition }}</td>
          <td>{{ $user->email }}</td>
          <td>{{ $user->Phone }}</td>
          <td>{{ $user->Institution }}</td>
          {{-- <td><a href="" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a></td> --}}
        </tr>
        @endforeach
      </tbody>
      <tfoot>
      </tfoot>
    </table>
  </div>
  <!-- /.box-body -->
</div>

@endsection
@push('js')
<script type="text/javascript">
</script>
<script type="text/javascript">
  $(function() {
    $('#example1').DataTable({
      'paging': true,
      'lengthChange': true,
      'searching': true,
      'ordering': true,
      'info': true,
      'autoWidth': true,
      select: true,
      dom: 'Bfrtip',
      buttons: [
        'copy', 'csv', 'excel', 'pdf', 'print'
      ]
    })
  });


  $('#save-company').on('click', function() {
    // var dt=new FormData($('form')[0]);
    // console.log(dt);
    var company_name = $('#company_name').val();
    $('#save-company').html("");
    $('#save-company').html('<i class="fa fa-circle-o-notch fa-spin"></i> Saving..');
    $.ajax({
      url: 'addCompany',
      data: new FormData($("#add_company_data")[0]),
      dataType: 'json',
      type: 'post',
      processData: false,
      contentType: false,
      success: function(response) {
        console.log(response);
        if (response.data == 'success') {
          $('#save-company').html("");
          $('#save-company').html('Save Company');
          $('#company_name').val("");
          $('#company_logo').val("");
          $('#message').html('<div class="callout callout-success text-center"><h4>Reminder!</h4>Record add successfully!</div>');
          $("#message").show().fadeOut(3000).queue(function(n) {
            $(this).hide();
            n();
          });
          // $('#load_table').load(location.href+' #load_table');
          location.href = "{{ url('admin/add-company') }}";
        }
      },
    })

    // $.post('addCompany',{'company_name':company_name,'_token':$('input[name=_token]').val()},function(response){
    //   console.log(response);
    // })
  });

  function editCompany(i) {
    $('#save-company').hide();
    $('#update-company').show();
    $.post('editCompany', {
        'id': i,
        '_token': $('input[name=_token]').val()
      },
      function(response) {
        console.log(response.data[0].company_logo);
        $('#company_name').val(response.data[0].company_name);
        $('#updateId').val(response.data[0].id);
        // $('#company_logo').val(response.data[0].company_logo);

        $('#editImage').attr("src", "{{ asset('storage/admin/images') }}/" + response.data[0].company_logo);
      });
  }

  $('#update-company').on('click', function() {
    $(this).html("");
    $(this).html('<i class="fa fa-circle-o-notch fa-spin"></i> Updating..');
    $('#editImage').hide();
    $.ajax({
      url: 'updateCompany',
      data: new FormData($("#add_company_data")[0]),
      dataType: 'json',
      type: 'post',
      processData: false,
      contentType: false,
      success: function(response) {
        if (response.data == 'success') {
          $('#update-company').html("");
          $('#update-company').html('Update Company');
          $('#company_name').val("");
          $('#company_logo').val("");
          $('#message').html('<div class="callout callout-success text-center"><h4>Reminder!</h4>Record add successfully!</div>');
          $("#message").show().fadeOut(3000).queue(function(n) {
            $(this).hide();
            n();
          });
          location.href = "{{ url('admin/add-company') }}";
          // $('#load_table').load(location.href+' #load_table');
          // $('#editImage').load(location.href+' #editImage');
        }
      },
    })
  });

  function deleteCompany(i) {
    $('#deleted' + i).html("");
    $('#deleted' + i).html('<i class="fa fa-circle-o-notch fa-spin"></i> Deleting..');
    $.post('deleteCompany', {
      'id': i,
      '_token': $('input[name=_token]').val()
    }, function(response) {
      if (response.data == 'success') {
        $('#deleted' + i).html("");
        $('#deleted' + i).html('<i class="fa fa-trash"></i>');
        $('#message').html('<div class="callout callout-danger text-center"><h4>Reminder!</h4>Record deleted successfully!</div>');
        $("#message").show().fadeOut(3000).queue(function(n) {
          $(this).hide();
          n();
        });
      }
      // $('#load_table').load(location.href+' #load_table');
      location.href = "{{ url('admin/add-company') }}";
    })
  }
</script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>

@endpush