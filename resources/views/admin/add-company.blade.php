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
@section('main-content')
<div id="message" class="navbar-fixed-top"></div>
<div class="box">
  <div class="box-header with-border">
    <a href="{{ url('admin/add-company') }}" class="btn btn-md btn flat btn-success"><i class="fa fa-plus"></i></a> <span>Add Company</span>

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
        <i class="fa fa-minus"></i></button>
      <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
        <i class="fa fa-times"></i></button>
    </div>
  </div>
  <div class="box-body">
    <div class="tab-pane" id="settings">

      <form class="form-horizontal" id="add_company_data" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="form-group">
          <input type="hidden" id="updateId" name="updateId">
          <label for="name" class="col-sm-2 control-label">Name</label>

          <div class="col-sm-6">
            <input type="text" name="company_name" class="form-control" id="company_name" placeholder="Company Name">
          </div>
        </div>
        <div class="form-group">
          <label for="logo" class="col-sm-2 control-label">Upload Company Logo</label>

          <div class="col-sm-6">
            <input type="file" name="company_logo" class="form-control" id="company_logo" placeholder="Company Name">
          </div>
        </div>
        <div class="form-group">
          <label for="" class="col-md-2 col-sm-2 control-label"></label>

          <div class="col-md-6">
            <img id="editImage" src="" style="width: 100px;height:100px;" alt="">
          </div>
        </div>

      </form>
      <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
          <a id="save-company" class="btn btn-danger">Save Company</a>
          <a id="update-company" style="display: none;" class="btn btn-danger">Update Company</a>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- DataTable -->

<!-- Small boxes (Stat box) -->
<div class="box" id="load_table">
  <div class="box-header with-border">
    <h3 class="box-title">Published Job</h3>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
        <i class="fa fa-minus"></i></button>
      <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
        <i class="fa fa-times"></i></button>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body">
    <table id="example1" class="table table-bordered table-striped">
      <thead id="load_head">
        <tr>
          <th>S.No.</th>
          <th>Company Name</th>
          <th>Company Logo</th>
          <th>Created at</th>
          <th>Edit</th>
          <th>Delete</th>
        </tr>
      </thead>
      <tbody>
        @php
        $i=0;
        @endphp
        @foreach ($allCompany as $company)
        @php
        $i++;
        @endphp
        <tr>
          <td>{{ $i }}</td>
          <td><a href="{{ url('/admin/single-company'.'/'.$company->id.'/'.str_slug($company->company_name)) }}" target="_blank">{{ $company->company_name }}</a>
          </td>
          <td><img style="width:80px;height: 80px;" class="img-responsive" src="{{ asset('storage/admin/images/'.$company->company_logo) }}" alt=""></td>
          <td>

            @php
            $date=date_create($company->created_at);
            echo date_format($date,"l jS \of F Y h:i:s A");
            @endphp
            | <small>{{ $company->created_at->diffForHumans() }}</small>
          </td>
          <td><a onclick="editCompany('{{ $company->id }}')" class="btn btn-sm btn-warning btn-flat"><i class="fa fa-edit"></i></a></td>
          <td><a id="deleted{{ $company->id }}" onclick="deleteCompany('{{ $company->id }}')" class="btn btn-sm btn-danger btn-flate"><i class="fa fa-trash"></i></a></td>
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
  $(function() {
    $('#example1').DataTable()
    $('#example2').DataTable({
      'paging': true,
      'lengthChange': false,
      'searching': false,
      'ordering': true,
      'info': true,
      'autoWidth': false
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

  $('#editImage').hide();

  function editCompany(i) {
    $('#editImage').show();
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
@endpush