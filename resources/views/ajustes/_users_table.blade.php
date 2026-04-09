<div class="panel-card">
    <h3>Usuarios existentes</h3>
    <table class="users-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th style="width:270px">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $u)
                <tr>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td>
                        @if(isset($superuserEmail) && $u->email === $superuserEmail)
                            <span class="badge-role">{{ ucfirst($u->role) }}</span>
                        @else
                            <form action="{{ route('ajustes.users.role', $u->id) }}" method="POST" class="role-form">
                                @csrf
                                <select class="form-select" name="role" onchange="this.form.submit()">
                                    <option value="asesor" {{ $u->role === 'asesor' ? 'selected' : '' }}>Asesor</option>
                                    <option value="supervisor" {{ $u->role === 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                    <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Administrador</option>
                                </select>
                            </form>
                        @endif
                    </td>
                    <td class="user-actions">
                        @if(isset($superuserEmail) && $u->email === $superuserEmail)
                            <em class="muted">Cuenta superusuario - no editable</em>
                        @else
                            <form action="{{ route('ajustes.users.reset', $u->id) }}" method="POST" class="inline-form">
                                @csrf
                                <input class="form-input" name="password" placeholder="Nueva contraseña" style="width:180px" required />
                                <button class="btn btn-sm btn-ghost" type="submit">Actualizar</button>
                            </form>

                            <form action="{{ route('ajustes.users.send_reset', $u->id) }}" method="POST" class="inline-form">
                                @csrf
                                <button class="btn btn-sm btn-ghost" type="submit">Enviar link</button>
                            </form>

                            <form action="{{ route('ajustes.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Eliminar usuario?');" class="inline-form">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" type="submit">Eliminar</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
