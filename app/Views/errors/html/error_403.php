<?= view('errors/html/duno_error_page', [
    'statusCode' => '403',
    'eyebrow' => lang('App.errorAccessEyebrow'),
    'title' => lang('App.error403Title'),
    'message' => lang('App.error403Message'),
    'mascot' => base_url('assets/images/duno-mascot-support.webp'),
]) ?>
