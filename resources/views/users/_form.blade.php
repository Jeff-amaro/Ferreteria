@php
    $roleOptions = collect($roles)->mapWithKeys(fn ($role) => [$role->value => $role->label()])->all();
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <x-input name="name" label="Nombre completo" :value="$user?->name" required />
    <x-input name="email" label="Correo electrónico" type="email" :value="$user?->email" required />
    <x-select name="role" label="Rol" :options="$roleOptions" :selected="$user?->role?->value" placeholder="Selecciona un rol" required />
    <x-input name="dni" label="DNI" :value="$user?->dni" maxlength="8" inputmode="numeric" />
    <x-input name="phone" label="Teléfono" :value="$user?->phone" />
    <x-input name="address" label="Dirección" :value="$user?->address" />
    <x-input name="password" label="Contraseña" type="password" :required="! $user" :help="$user ? 'Déjala vacía para conservar la contraseña actual.' : 'Mínimo 8 caracteres.'" autocomplete="new-password" />
    <x-input name="password_confirmation" label="Confirmar contraseña" type="password" :required="! $user" autocomplete="new-password" />
</div>

<div class="mt-5">
    <input type="hidden" name="active" value="0">
    <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
        <input type="checkbox" name="active" value="1" class="rounded border-slate-300 text-primary-600 focus:ring-primary-500" @checked(old('active', $user?->active ?? true))>
        <span>
            <span class="block text-sm font-semibold text-slate-800">Usuario activo</span>
            <span class="block text-xs text-slate-500">Los usuarios inactivos no pueden iniciar sesión.</span>
        </span>
    </label>
</div>
