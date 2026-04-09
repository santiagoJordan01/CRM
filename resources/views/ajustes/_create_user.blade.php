<div class="panel-card">
    <h3>Crear usuario</h3>
    <form action="{{ route('ajustes.users.store') }}" method="POST" class="form-create-user">
        @csrf
        <div class="form-field">
            <input class="form-input" name="name" placeholder="Nombre" required />
        </div>
        <div class="form-field">
            <input class="form-input" name="email" type="email" placeholder="correo@ejemplo.com" required />
        </div>
        <div class="form-field">
            <input class="form-input" name="password" type="password" placeholder="Contraseña" required />
        </div>
        <div class="form-field">
            <select class="form-select" name="role" required>
                <option value="asesor">Asesor</option>
                <option value="supervisor">Supervisor</option>
                <option value="admin">Administrador</option>
            </select>
        </div>
        <div>
            <button class="btn btn-primary" type="submit">Crear usuario</button>
        </div>
    </form>
</div>
