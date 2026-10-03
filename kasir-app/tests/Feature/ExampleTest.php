<?php

it('redirects the home page to the cashier page', function () {
    $this->get('/')->assertRedirect(route('cashier.index'));
});

it('redirects guests to the login page', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

it('renders the login page', function () {
    $this->get(route('login'))->assertStatus(200);
});

it('renders the register page', function () {
    $this->get(route('register'))->assertStatus(200);
});