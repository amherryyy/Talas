<?= view('errors/html/duno_error_page', [
    'statusCode' => '404',
    'eyebrow' => lang('App.errorNotFoundEyebrow'),
    'title' => lang('App.error404Title'),
    'message' => lang('App.error404Message'),
    'mascot' => base_url('assets/images/duno-mascot-curious.webp'),
]) ?>
