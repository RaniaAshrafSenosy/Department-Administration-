<form method="POST" action="{{ route('login') }}">
    @csrf
    
    <div>
        <label for="email">Email</label>
        <input type="email" name="main_email" value="{{ old('main_email') }}" required >
        @error('main_email')
            <span class="text-red-500">{{ $message }}</span>
        @enderror
    </div>
    
    <div>
        <label for="password">Password</label>
        <input type="password" name="password" required>
        @error('password')
            <span class="text-red-500">{{ $message }}</span>
        @enderror
    </div>
    
    <button type="submit">Login</button>
</form>
