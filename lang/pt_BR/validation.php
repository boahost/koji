<?php

return [
    'required' => 'O campo :attribute é obrigatório.',
    'email' => 'O campo :attribute deve ser um endereço de e-mail válido.',
    'unique' => 'O :attribute já está sendo utilizado.',
    'min' => [
        'string' => 'O campo :attribute deve ter pelo menos :min caracteres.',
        'numeric' => 'O campo :attribute deve ser pelo menos :min.',
    ],
    'max' => [
        'string' => 'O campo :attribute não pode ter mais que :max caracteres.',
        'numeric' => 'O campo :attribute não pode ser maior que :max.',
    ],
    'numeric' => 'O campo :attribute deve ser um número.',

    'attributes' => [
        'name' => 'nome',
        'email' => 'e-mail',
        'password' => 'senha',
        'document' => 'CPF/CNPJ',
        'commission_rate' => 'comissão',
    ],
];
