@component('mail::message')
# Registration Successful

Your new Glazy account has been created, you can start making recipes now!

@component('mail::button', ['url' => 'https://glazy.org/login'])
Login to Glazy
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
