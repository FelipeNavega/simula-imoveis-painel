<x-mail::message>

Olá {{ $user->name }}, seu cadastro foi aprovado no sistema {{ config('app.name') }}.

Voce pode acessar o sistema clicando no botão abaixo:

<x-mail::button :url="$loginUrl">
Fazer login
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
