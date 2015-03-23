    <section style='background:#f3f3f3; margin:0px; padding:10px;'>
    <header>
    <div style="display:block">Remuneraciones</div>
    <div style="display:inline-block"><h1>Hola <?= $user->nombre.' '.$user->apellido ?></h1></div>
    </header>
        <section style='background:white; padding:10px; border-radius:1em; margin:10px;'>
            <p>Haz solicitado restablecimiento de contraseña, pulsa aqui para realizarlo: <?= base_url('registro/forget/'.$_SESSION['key']) ?></p>
        </section>
        <footer style='text-align:center'>
        Copyrigth Remuneraciones
        </footer>
    </section>