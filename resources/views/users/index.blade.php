<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <title>Importar CSV</title>
</head>
<body>
    <div class="container">
        <div class="card my-5 border-light shadow">
            <h3 class="card-header">Laravel 11 - Importar Excel</h3>
            <div class="card-body">
                @session('success')
                    <div class="alert alert-success" role="alert">
                        {{$value}}
                    </div>
                @endSession

                @session('error')
                    <div class="alert alert-danger" role="alert">
                        {{$value}}
                    </div>
                @endSession

                @if($errors->any())
                    <div class="alert alert-warning" role="alert">
                            @foreach ($errors->all() as $error)
                                    {{$error}}
                            @endforeach
                    </div>
                @endif

                <form action="{{route('users.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="input-group my-4">
                        <input type="file" name="file" class="form-control" id="file" accept=".csv" />
                        <button type="submit" class="btn btn-outline-success"><i class="fa-solid fa-file-arrow-up"></i> Importar </button>
                    </div>
                </form>

                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>E-mail</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <th>{{$user->id}}</th>
                                <td>{{$user->name}}</td>
                                <td>{{$user->email}}</td>
                                <td>
                                    <form action="/users/{{$user->id}}/destroy" method="post">
                                        @csrf
                                        @method("DELETE")
                                        <button type="submit" class="btn"><i class="fa-solid fa-trash-can" style="color: #f10909;"></i> Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="py-4">
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
