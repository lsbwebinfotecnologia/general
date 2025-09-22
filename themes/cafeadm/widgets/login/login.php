<?php $this->layout("_login"); ?>

<div class="login">
    <article class="login_box radius">
        <h1 class="hl icon-user">login</h1>
        <div class="ajax_response"><?= flash(); ?></div>

        <form name="login" action="<?= url("/admin/login"); ?>" method="post">
            <label>
                <span class="field icon-envelope">e-mail:</span>
                <input name="email" type="email" placeholder="informe seu e-mail" required/>
            </label>

            <label>
                <span class="field icon-unlock-alt">senha:</span>
                <input name="password" type="password" placeholder="informe sua senha:" required/>
            </label>

            <button class="radius gradient gradient-green gradient-hover icon-sign-in">entrar</button>
        </form>

        <footer>
            <p>desenvolvido por www.<b>lsbwebinfo</b>.com.br</p>
            <p>&copy; <?= date("Y"); ?> - todos os direitos reservados</p>
<!--            <a target="_blank"-->
<!--               class="icon-whatsapp transition"-->
<!--               href="https://api.whatsapp.com/send?phone=5511948146533&text=olá, preciso de ajuda com o login."-->
<!--            >whatsApp: (11) 94814-6533</a>-->
        </footer>
    </article>
</div>