# RabbitMQ Cluster

This project sets up a rabbit cluster as an example of how to use [RabbitMQ](http://www.rabbitmq.com/) with
SSL, mirroring, user management, and load balanced with an HA proxy. It also includes a working
example of using [Celery](http://www.celeryproject.org/).

# Setup

To run the example provided, you must have [Vagrant](http://www.vagrantup.com/) and [VirtualBox](https://www.virtualbox.org/)
installed on your machine.

```sh
git clone https://github.com/turbine-web/rabbitmq-cluster.git
cd rabbit-cluster

# Generate a certificate authority and server/client certs
# Required for the example which is over SSL
sh generateCerts.sh

# Vagrant plugins to manage the network
vagrant plugin install vagrant-auto_network
vagrant plugin install vagrant-hostmanager
vagrant plugin install vagrant-triggers

vagrant up
```

# Connecting

To connect to the rabbit servers connect to the `proxy` hostname as admin/admin.  Connecting to rabbit
requires a valid client ssl cert.  Make sure that any clients you intend to connect to this cluster have
a copy of the ssl/client directory available to them.  Additionally each client will need a copy of the
Certificate Authority cert located in ssl/testca/cacert.pem.

## Examples

### PHP

PHP AMQP users should compose in the [php-amqplib](https://github.com/videlalvaro/php-amqplib) library to connect directly
to RabbitMQ. Add the following to your composer file:

```javascript
  "require":
  {
    "videlalvaro/php-amqplib": ">=2.0.0",
  }
```

Celery users should compose in the [celery-php](https://github.com/divideandconquer/celery-php) library.  Make sure to use
the divideandconquer version until SSL support is merged into the [gjedeer](https://github.com/gjedeer/celery-php) version.

```javascript
  "require":
  {
    "massivescale/celery-php": "@dev"
  },
  "repositories": [
    {
      "type": "git",
      "url": "git@github.com:divideandconquer/celery-php.git"
    }
  ]
```

For more information check out the example folder.  Note that these examples require you to `composer install`
before they will run.

```sh
# pull down dependencies
composer install
```

To run the Celery example, which submits a task to add two numbers together and returns the value, run
the following commands:

```sh
# run the celery example with the default vagrant configuration
php example/php/celery.php

# run the celery example with a custom host, user, password, and ssl common name:
php example/php/celery.php -h proxy -u admin -p admin  --ssl-common-name <common name>
```

To see a publish / subscribe implementation, run the consumer and producer files:

```sh
# run the consumer example with the default vagrant configuration
php example/php/rabbit_consumer.php

# run the consumer example with a custom host, user, password, and ssl common name:
php example/php/rabbit_consumer.php -h proxy -u admin -p admin --ssl-common-name <common name>
```

```sh
# run the producer example with the default vagrant configuration
php example/php/rabbit_producer.php

# run the producer example with a custom host, user, password, and ssl common name:
php example/php/rabbit_producer.php -h proxy -u admin -p admin --ssl-common-name <common name>
```

# Monitoring

## haproxy

To check on the health and status of servers as haproxy sees them you can navigate to [haproxy stats](http://proxy:9090/haproxy?stats)
and log in with admin/admin.

## rabbitmq

To view the rabbitmq management page navigate to [rabbit0](http://rabbit0:15672/#/) and log in with admin/admin.
Note that you can also connect to [rabbit1](http://rabbit1:15672/#/) and [rabbit2](http://rabbit2:15672/#/).

# License

Copyright:: 2014, Warner Bros. Entertainment Inc. Developed by Turbine, Inc.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.

# Contributing

1. Fork it
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Commit your changes (`git commit -am 'Add some feature'`)
4. Push to the branch (`git push origin my-new-feature`)
5. Create new Pull Request
