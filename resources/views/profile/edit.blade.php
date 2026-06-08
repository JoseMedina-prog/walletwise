<x-app-layout>
    <x-slot name="header">
        <x-ui.section-header title="Mi perfil" eyebrow="Gestiona la información y seguridad de tu cuenta." />
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-ui.card padding="p-7 sm:p-8">
                @include('profile.partials.update-profile-information-form')
            </x-ui.card>

            <x-ui.card padding="p-7 sm:p-8">
                @include('profile.partials.update-password-form')
            </x-ui.card>

            <x-ui.card padding="p-7 sm:p-8">
                @include('profile.partials.delete-user-form')
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
