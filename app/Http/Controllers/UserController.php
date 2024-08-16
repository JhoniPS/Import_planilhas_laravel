<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::paginate(15);

        return view('users.index', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ],[
            'file.required' => 'O campo file é obrigatorio.',
            'file.mimes' => 'O campo deve ter um formato CSV ou TXT.',
            'file.max' => 'Tamanho do arquivo execede :max Mb.',
        ]);

        $headers = ['name', 'email', 'password'];

        $dataFile = array_map('str_getcsv', file($request->file('file')));

        $arrayValues = [];
        $numberRegisterRecords = 0;
        $emailReadyRegistered = false;

        foreach ($dataFile as $keyData => $valueData) {
            $values = explode(";", $valueData[0]);
            $userData = [];

            foreach ($headers as $keyHeader => $header) {
                if($header === 'email') {
                    if(User::where('email', $values[$keyHeader])->first()) {
                        $emailReadyRegistered .= $values[$keyHeader] . ", ";
                    }
                }

                if ($header === 'password') {
                    $userData[$header] = Hash::make($values[$keyHeader], ['rounds' => 12]);
                } else {
                    $userData[$header] = $values[$keyHeader];
                }
            }

            $numberRegisterRecords++;

            $arrayValues[] = $userData;
        }

        if($emailReadyRegistered) {
            return back()->with('error', 'Dados não cadastrados.' . ' Existem e-mails já cadastrados: '. $emailReadyRegistered);
        }

        User::insert($arrayValues);

        return back()->with('success', 'Dados inseridos com sucesso! Quantidade: ' . $numberRegisterRecords);
    }
    public function destroy(string $id): RedirectResponse
    {
        if(!$user = User::find($id)) {
            return redirect()->route('users.index')->with('error','Usuário não encontrado!.');
        }

        $user->destroy($id);

        return redirect()->route('users.index')->with('success', 'Usuário deletado com sucesso!');
    }
}
