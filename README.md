[![Build Status](https://travis-ci.org/zackphp/saga.svg?branch=master)](https://travis-ci.org/zackphp/saga)
[![Coverage Status](https://coveralls.io/repos/github/zackphp/saga/badge.svg?branch=master)](https://coveralls.io/github/zackphp/saga?branch=master)

`Zack/Saga` is a simple and easy to use library for event and side effects handling.
Inspired by [`redux-saga`](https://github.com/yelouafi/redux-saga) it uses PHP generators and simple effect objects to create maintainable and easy to test processes and workflows.

## Example

```php
<?php

use \Symfony\Component\EventDispatcher\EventDispatcher;
use Zack\Saga\Processor;
use Zack\Saga\SagaInterface;

class LoginSaga implements SagaInterface
{
    public function run(): \Generator
    {
        // Wait for the 'acme.event.name'
        $event = yield take('acme.user.login');
        
        // Get user from given ID.
        $user = UserProvider::find($event->getUserId());
        
        if ($user === null) {
            // Redirect to login page.
            yield dispatch('acme.router.redirect', new RedirectEvent('/login'));
            return;
        }
        
        // Create session.
        yield dispatch('acme.user.session', new UserSessionEvent($user));
        // Redirect to dashboard page.
        yield dispatch('acme.router.redirect', new RedirectEvent('/login'));
        
        // Fork saga for taking user logout event.
        yield fork(new LogoutSaga());
    }
}

$eventDispatcher = new EventDispatcher();

// Create a default Processor.
$processor = Processor::create($eventDispatcher);
$processor->run(new LoginSaga());

$eventDispatcher->dispatch('acme.user.login', new LoginEvent(1));
```

## Installation

You can install this library via [Composer](https://getcomposer.org/):

```bash
$ composer require zackphp/saga
```