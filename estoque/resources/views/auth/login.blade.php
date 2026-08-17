<x-guest-layout>
    <!-- Status da Sessão -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-white">Acessar o Sistema</h2>
        <p class="text-sm text-zinc-400">Entre com suas credenciais para continuar</p>
    </div>

    <!-- BOX DE CREDENCIAIS DE TESTE -->
    <div class="mb-6 p-3.5 bg-zinc-800/60 border border-zinc-700/60 rounded-lg text-center text-sm text-zinc-300 shadow-inner">
        <span class="block font-medium text-[#FF2D20] mb-1">Login para testar o sistema:</span>
        E-mail: <strong class="text-white">admin@test.com</strong><br>
        Senha: <strong class="text-white">123</strong>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-zinc-300 mb-1">E-mail</label>
            <input id="email" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autofocus 
                   autocomplete="username"
                   placeholder="seu.email@exemplo.com"
                   class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-[#FF2D20] focus:border-transparent transition-all">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Senha -->
        <div class="mt-4">
            <label for="password" class="block text-sm font-medium text-zinc-300 mb-1">Senha</label>
            <input id="password" 
                   type="password" 
                   name="password" 
                   required 
                   autocomplete="current-password"
                   placeholder="••••••••"
                   class="w-full px-3 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-[#FF2D20] focus:border-transparent transition-all">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Lembrar-me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" 
                       type="checkbox" 
                       name="remember"
                       class="rounded border-zinc-700 bg-zinc-800 text-[#FF2D20] shadow-sm focus:ring-[#FF2D20] focus:ring-offset-zinc-900">
                <span class="ms-2 text-sm text-zinc-400">Lembrar-me</span>
            </label>
        </div>

        <!-- Botão Entrar -->
        <div class="mt-6">
            <button type="submit" 
                    class="w-full py-2.5 px-4 bg-[#FF2D20] hover:bg-[#e0261a] text-white font-semibold rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-[#FF2D20] focus:ring-offset-2 focus:ring-offset-zinc-900 transition-all">
                Entrar
            </button>
        </div>
    </form>
</x-guest-layout>
