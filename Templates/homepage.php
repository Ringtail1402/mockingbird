<?php $view->extend() ?>

<div style="position: fixed; width: 100%; height: 100%; display: table; padding-right: 40px;">
  <div style="display: table-cell; vertical-align: middle; text-align: center;">
    <div class="hero-unit" style="width: 600px; margin: -40px auto 20px auto; text-align: center;">
      <div>
        <h1 style="color: #080;"><img src="<?php echo $asset->image('Mockingbird:mockingbird-b200.png') ?>" alt="К">азначей<sub><small>бета</small></sub>
        </h1>
        <p>Сервис учета личных финансов.</p>
      </div>
    </div>

    <div style="text-align: center; margin: auto; display: inline-block;">
      <p>
          <a class="btn btn-primary btn-large" href="<?php echo $link->url('auth.register') ?>">Регистрация</a>
          <a class="btn btn-large" href="<?php echo $link->url('auth.login') ?>">Вход</a>
          <a class="btn btn-large" href="<?php echo $link->url('auth.demo') ?>">Демо-аккаунт</a>
        </p>

    </div>
  </div>
</div>
