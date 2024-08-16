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
        ], [
            'file.required' => 'O campo file é obrigatório.',
            'file.mimes' => 'O campo deve ter um formato CSV ou TXT.',
            'file.max' => 'O tamanho do arquivo excede :max KB.',
        ]);

        $headers = ['name', 'email', 'password'];
        $dataFile = array_map('str_getcsv', file($request->file('file')));

        $arrayValues = [];
        $numberRegisterRecords = 0;
        $emailReadyRegistered = [];

        foreach ($dataFile as $keyData => $valueData) {
            $values = str_getcsv($valueData[0], ';');
            $userData = [];

            foreach ($headers as $keyHeader => $header) {
                if ($header === 'email') {
                    if (User::where('email', $values[$keyHeader])->exists()) {
                        $emailReadyRegistered[] = $values[$keyHeader];
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

        if (!empty($emailReadyRegistered)) {
            return back()->with('error', 'Dados não cadastrados. E-mails já cadastrados: ' . implode(', ', $emailReadyRegistered));
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
