@extends('dashboard.base')

@section('content')
        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i>{{ __('Notes') }}
                    </div>
                    <div class="card-body" id='search_id'>
                        <div class="row"> 
                          <a href="{{ route('notes.create') }}" class="btn btn-primary m-2">{{ __('Add Note') }}</a>
                          <div class="row" id="select-parent">
                            <select name="status_id" id="status_id" class="btn btn-primary m-2">
                              <option value="">All Status</option>
                              @foreach ($status as $item)
                                <option value="{{$item->id}}">{{$item->name}}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                        <br>
                        <table class="table table-striped table-bordered table-hover data-table">
                          <thead>
                            <tr>
                              <th title="Author">Author</th>
                              <th title="Title">Title</th>
                              <th title="Content">Content</th>
                              <th title="Applies to date">Applies to date</th>
                              <th title="Status">Status</th>
                              <th title="Note type">Note type</th>
                              <th title="Actions">Actions</th>
                            </tr>
                          </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
@endsection


@section('javascript')
    <script type="text/javascript">
        var table = '';
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            table = $('.data-table').DataTable({
                dom: 'Blfrtip',
                buttons: [{
                    extend: 'colvis',
                },
                    {
                        extend: 'collection',
                        text: "Export",
                        className: '',
                        buttons: [
                            'copy', 'csv', 'excel', 'print', 'pdf'
                        ]
                    },
                ],
                rowCallback: function (row, data, index) {
                    if (index % 2 == 0) {
                        $(row).removeClass('myodd myeven');
                        $(row).addClass('myodd');
                    } else {
                        $(row).removeClass('myodd myeven');
                        $(row).addClass('myeven');
                    }
                },
                lengthMenu: [
                    [10, 25, 50, 100, 500, -1],
                    [10, 25, 50, 100, 500, "All"]
                ],
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: {
                    "url": "{{ route('notes.dataTable') }}",
                    "type": "POST",
                    "cache": false,
                    "data": function (d) {d.status_id = $('#status_id').val();},
                },
                columns: [
                    {data: 'author'},
                    {data: 'title'},
                    {data: 'content'},
                    {data: 'applies_to_date'},
                    {
                        data: 'status',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                          return data 
                            ? "<span class='" + data.class + "'>" + data.name + "</span>"
                            : data;
                        }
                    },
                    {data: 'note_type'},
                    {
                        data: 'action',
                        searchable: false,
                        orderable: false,
                        render: function (data, type, row) {
                            const generateLinkButton = (url, title, iconClass) => {
                                  return url !== 0 
                                    ? `<a href="${url}" class="btn btn-sm btn-clean btn-icon mr-2" title="${title}"><i class="icon-md ${iconClass}"></i></a>`
                                    : '';
                            };
                            const generateDeleteButton = (url) => {
                                return url !== 0 
                                  ? `<span class="btn btn-sm btn-clean btn-icon mr-2 delete-item" data-url="${url}" title="Delete"><i class="icon-md far fa-trash-alt"></i></span>`
                                  : '';
                            };

                            const showUrl = generateLinkButton(data.showUrl, 'Show details', 'far fa-eye');
                            const EditUrl = generateLinkButton(data.EditUrl, 'Edit details', 'far fa-edit');
                            const deleteUrl = generateDeleteButton(data.deleteUrl);
                            return showUrl + EditUrl + deleteUrl;
                        }
                    },
                ]
            });
        });


        $('.data-table').on('processing.dt', function (e, settings, processing) {
            if (processing) {
                document.getElementById('search_id').style.pointerEvents = 'none';
                freezeClic = true;
            } else {
                document.getElementById('search_id').style.pointerEvents = 'auto';
                freezeClic = false;
            }
        });

        document.addEventListener("click", freezeClicFn, true);
        
        function freezeClicFn(e) {
            if (freezeClic) {
                e.stopPropagation();
                e.preventDefault();
            }
        }

        $('#status_id').on('change', function() {
          table.ajax.reload(null, false);
        });

        $(document).on("click", ".delete-item", function () {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(this).removeClass("delete-item");
                    $(this).prop("onclick", null).off("click");
                    $.ajax({
                        type: "DELETE",
                        url: $(this).attr('data-url'),
                        cache: false,
                        data: {"_token": $('meta[name="csrf-token"]').attr('content')},
                        success: function (data) {
                            if (data.code > 0) {
                                Swal.fire(
                                    'Deleted!',
                                    'Your item has been deleted.',
                                    'success'
                                );
                            } else {
                                Swal.fire({
                                    title: 'Oops...',
                                    text: "Something went wrong!",
                                    icon: 'error',
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Ok!'
                                })
                            }
                            table.ajax.reload(null, false);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            Swal.fire({
                                    title: 'General Error',
                                    text: "Something went wrong!",
                                    icon: 'error',
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Ok!'
                                })
                        }
                    });
                }
            });
        });

    </script>
@endsection

