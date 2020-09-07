
# Privalia Code Challenge 2018

The 2018 event of the **Privalia Code Challenge** is called **Space Invaders Tribute**!
The goal is easy: You have to create a REST API to **kill the invaders** (and the other players too).

Your API will move a starship competing with the other player APIs in a real time challenge.
All the starships will start in a different random position.
The winner will be the starship with the highest score.

* **+50 points** to kill another player's starship.
* **+25 points** to kill a space invader.
* **-100 points** when killed by another player or invader.

She the [full rules](docs/rules.md) or the [API documentation](docs/api.md).

## Development

### Configuration

This repo constains the server used for the **Privalia Code Challenge 2018**.
It's a [PHP](http://php.net/) project which uses the [Symfony 3.4](https://symfony.com/) framework, a [MySQL](https://www.mysql.com/) database and a [RabbitMQ](https://www.rabbitmq.com/) messaging system.
It has been developed using **`PHP 7.2`**, **`MySQL 5.7`**, **`RabbitMQ 3.7`** and **[`docker`](https://www.docker.com/)** technologies.
The `docker` folder contains the particular images used in the development environment and some [bash](https://www.gnu.org/software/bash/) commands with helps configurating the environment.

- `docker/build.sh` - Build the docker images.
- `docker/start.sh` - Start all the project containers.
- `docker/stop.sh` - Stop all the project containers.
- `docker/composer.sh` - Execute composer inside the container (params are allowed). The containers must be started before run this script.
- `docker/console.sh` - Execute a console command inside the container (params are allowed). The containers must be started before run this script.
- `docker/phpunit.sh` - Execute phpunit unit tests inside the container (params are allowed). The containers must be started before run this script.
- `docker/bash.sh` - Access bash shell of the API container. The containers must be started before run this script.
- `docker/su.sh` - Access bash shell of the API container with the `root`user. The containers must be started before run this script.

NOTE: All these scripts assume there is an user `david` in the host.
You can change it for your user name to avoid permission problems.

### Installation

```
$ git clone git@github.com:PrivaliaTech/code-challenge-2018.git
$ cd code-challenge-2018
$ docker/build.sh
$ docker/start.sh
$ docker/composer.sh install
$ docker/console.sh doctrine:database:create
$ docker/console.sh doctrine:schema:create
$ docker/console.sh -e prod cache:clear
$ docker/console.sh -e prod cache:warmup
```

#### Params

The following program params are suitable to use with the default docker installation:

NOTE: The following params will in the file `app/config/params.yml` after running
the `docker/composer.sh install` command.
It's mandatory to clear and warmup the cache before changing any of these params
in `prod` environment.

* MySQL database:
    * database_host: **cc18_mysql**
    * database_port: **3306**
    * database_name: **cc18_db**
    * database_user: **cc18_user**
    * database_password: **cc18_pass**

* RabbitMQ message broker:
    * rabbitmq_host: **cc18_rabbitmq**
    * rabbitmq_port: **5672**
    * rabbitmq_user: **guest**
    * rabbitmq_pass: **guest**
    * rabbitmq_vhost: **/**

* Security:
    * secret: **<super-secret-string>**
    * admin_password: **null**
    * guest_password: **null**

* Game settings:
    * default_email: **<admin@email.com>**
    * default_time_format: **Y-m-d H:i:s**
    * default_timeout: **3**
    * game_start_time: **null**
    * game_end_time: **null**
    * game_free_time: **null**

### Setting passwords

There are two special users with different privileges: `admin`and `guess`.
To set the password for any of those users you have to run a console command:

```
$ docker/console.sh security:encode-password <password>
  
  Symfony Password Encoder Utility
  ================================
  
   ------------------ ---------------------------------------------------------------
    Key                Value
   ------------------ ---------------------------------------------------------------
    Encoder used       Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder
    Encoded password   $2y$12$buqWDQ.ca9ICbPg9JUNvi.WJBQu8Qi5CGBauWQTTowmFphD4qEZHC
   ------------------ ---------------------------------------------------------------
```

Then you have to copy the new password in the file `app/config/params.yml`:

```
parameters:
    (...)
    admin_password: $2y$12$buqWDQ.ca9ICbPg9JUNvi.WJBQu8Qi5CGBauWQTTowmFphD4qEZHC
    guest_password: null
```

And finally you have to clear and warmup the Symfony cache:

```
$ docker/console.sh -e prod cache:clear
$ docker/console.sh -e prod cache:warmup
```

## License

**MIT License**

Copyright (c) 2018 Privalia Venta Directa S.A.U.

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.


## Contributors

* **[David Amigo](https://github.com/davamigo)** <[davamigo@gmail.com](mailto:davamigo@gmail.com)>


## Attributions

[See the credits page](http://code-challenge-2018.privalia.com/credits).
