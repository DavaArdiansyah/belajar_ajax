<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}" id="token">
    <title>Laravel AJAX CRUD - SantriKoding.com</title>
    <style>
        body {
            background-color: lightgray !important;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container" style="margin-top: 50px">
        <div class="row">
            <div class="col-md-12">
                <h4 class="text-center">LARAVEL CRUD AJAX - <a href="https://santrikoding.com">WWW.SANTRIKODING.COM</a></h4>
                <div class="card border-0 shadow-sm rounded-md mt-4">
                    <div class="card-body">
                        <a href="javascript:void(0)" class="btn btn-success mb-2" id="btn-create-post">TAMBAH</a>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="table-users">
                                @foreach ($users as $user)
                                    <tr id="index_{{ $user->id }}">
                                        <td>{{ $user->name }}</td>
                                        <td class="text-center">
                                            <a href="javascript:void(0)" id="btn-edit-user" data-id="{{ $user->id }}" class="btn btn-primary btn-sm">EDIT</a>
                                            <a href="javascript:void(0)" id="btn-delete-user" data-id="{{ $user->id }}" class="btn btn-danger btn-sm">DELETE</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('components.modal.user')
    <script>
        $(document).ready(function () {
            $('#btn-create-post').on('click', showCreateModal);
            $(document).on('click', '#btn-edit-user', function () {
                let user_id = $(this).data('id');
                showEditModal(user_id);
            });
            $(document).on('click', '#btn-delete-user', function () {
                let user_id = $(this).data('id');
                deleteUser(user_id);
            });
            $('#submit').on('click', function (e) {
                e.preventDefault();
                if (validateForm()) saveUser();
            });
            $("#modal-create").on("hidden.bs.modal", function () {
                resetForm();
            });
        });

        function showCreateModal() {
            $('#title-modal').text('Tambah Data');
            $('#modal-create').modal('show');
            resetForm();
        }

        function showEditModal(user_id) {
            const url = `/user/${user_id}`;

            $.get(url, function (response) {
                $('#user_id').val(response.data.id);
                $('#name').val(response.data.name);
                $('#title-modal').text('Edit Data');
                $('#modal-create').modal('show');
            });
        }

        function saveUser() {
            const user_id = $('#user_id').val();
            const name = $('#name').val();
            const token = $("#token").attr("content");

            const url = user_id ? `/user/${user_id}` : "{{ route('user.store') }}";
            const method = user_id ? 'put' : 'post';

            $.ajax({
                url: url,
                type: method,
                cache: false,
                data: { name: name, "_token": token },
                success: function (response) {
                    handleSuccess(response, user_id);
                },
                error: handleValidationError,
            });
        }

        function deleteUser(user_id) {
            const token = $("#token").attr("content");

            $.ajax({
                url: `/user/${user_id}`,
                type: "delete",
                cache: false,
                data: { "_token": token },
                success: function (response) {
                    Swal.fire({ icon: 'success', title: `${response.message}`, showConfirmButton: false, timer: 1000 });
                    $(`#index_${user_id}`).remove();
                },
            });
        }

        function handleSuccess(response, user_id) {
            Swal.fire({
                icon: 'success',
                title: `${response.message}`,
                showConfirmButton: false,
                timer: 1000
            });

            const userRow = `
                <tr id="index_${response.data.id}">
                    <td>${response.data.name}</td>
                    <td class="text-center">
                        <a href="javascript:void(0)" id="btn-edit-user" data-id="${response.data.id}" class="btn btn-primary btn-sm">EDIT</a>
                        <a href="javascript:void(0)" id="btn-delete-user" data-id="${response.data.id}" class="btn btn-danger btn-sm">DELETE</a>
                    </td>
                </tr>
            `;

            if (user_id) {
                $(`#index_${response.data.id}`).replaceWith(userRow);
            } else {
                $('#table-users').prepend(userRow);
            }

            resetForm();
            $('#modal-create').modal('hide');
        }

        function handleValidationError(error) {
            if (error.responseJSON.name) {
                $('#alert-name').removeClass('d-none').addClass('d-block').html(error.responseJSON.name[0]);
            }
        }

        function validateForm() {
            const name = $('#name').val();
            if (!name) {
                $('#alert-name').removeClass('d-none').addClass('d-block').html('Name field is required');
                return false;
            }
            $('#alert-name').addClass('d-none');
            return true;
        }

        function resetForm() {
            $('#name').val('');
            $('#user_id').val('');
            $('#alert-name').addClass('d-none').removeClass('d-block');
        }
    </script>
</body>

</html>
