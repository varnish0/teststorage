<?php

test('the home page redirects to the photo gallery', function () {
    $this->get('/')->assertRedirect('/photos');
});
