Command Bus Component
=====================

This standalone component implements the Command Pattern.

It is inspired by the Laravel `illuminate/bus` and `illuminate/queue` components, but aims to be framework agnostic.

For more information about the Command Bus Pattern:

* [http://en.wikipedia.org/wiki/Command_pattern](http://en.wikipedia.org/wiki/Command_pattern)
* [http://sourcemaking.com/design_patterns/command](http://sourcemaking.com/design_patterns/command)

# Usage

Any `Command` requires two classes: the `DoSomethingCommand` class and the `DoSomethingHandler` class.

You can instantiate the Command from your controllers when needed, and `dispatch` it onto the Bus.

The Bus will then resolve the matching Handler class, and ask it to handle the new Command.

Here's how you use it:

## An example Command class:
```php
<?php

use LinkORB\Component\Bus\CommandInterface;

class OrderConfirmCommand implements CommandInterface
{
    private $orderId;
    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }
    
    public function getOrderId()
    {
        return $this->orderId;
    }
}
```

## An example Handler class:
```php
<?php

use LinkORB\Component\Bus\HandlerInterface;
use LinkORB\Component\Bus\CommandInterface;

class OrderConfirmHandler implements HandlerInterface
{
    private $db;
    private $mailer;
    
    // the constructor automatically receives requested services from the container
    public function __construct($db, $mailer)
    {
        $this->db = $db;
        $this->mailer = $mailer;
    }

    // this method is called by the bus to handle commands
    public function handle(CommandInterface $command)
    {
        $order = $this->db->fetchOneById('orders', $command->getOrderId());
        $this->mailer->mail($order['email'], "Thanks for your order, " . $order['customername']);
        
    }
}
```


## Setting up the bus in your init script
```php

use LinkORB\Component\Bus\Bus;

// Reuse an existing container, or create a plain php array
// Any array type dependency injection container will work too (symfony, pimple, etc)
$container = array(
    'db'=>$db,
    'mailer'=>$mailer
);

// Instantiate the bus, and optionally pass a container
$bus = new Bus($container);

// now add the bus to the container, or use any other method to pass the bus to your controllers
```

## Using the bus in your Controllers:

```
class basketController
{
    public function confirmAction()
    {
        $orderid = 5;

        // Instantiate a Command
        $command = new OrderConfirmCommand($orderid);

        // Dispatch the command
        $this->bus->dispatch($command);
    }
}
```

# Calling commands from the command-line

A simple command-line dispatcher is included. It can be used like this:

    ./vendor/bin/bus bus:dispatch "Acme\Bus\DoSomethingCommand" -p color="red" -g os="linux"
    
The `-p` arguments will be passed as parameters to the command. You can pass as many as you want.
The `-g` arguments will be inserted into a simple array "container", so it can be used by the Handlers

One example command is included:

    ./vendor/bin/bus bus:dispatch "LinkORB\Component\Bus\Bus\HelloCommand" -p name="World" -g sender="me"
    
    
This will output:

    Hello, World from me!

# License

MIT (see [LICENSE.md](LICENSE.md))

# Brought to you by the LinkORB Engineering team

<img src="http://www.linkorb.com/d/meta/tier1/images/linkorbengineering-logo.png" width="200px" /><br />
Check out our other projects at [linkorb.com/engineering](http://www.linkorb.com/engineering).

Btw, we're hiring!
