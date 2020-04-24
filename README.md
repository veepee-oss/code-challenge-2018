
# Privalia Code Challenge 2018

The 2018 event of the **Privalia Code Challenge** is called **Space Invaders Tribute**!
The goal is easy: You have to create a REST API to **kill the invaders** (and the other players too).

Your API will move a starship competing with the other player APIs in a real time challenge.
All the starships will start in a different random position.
The winner will be the starship with the highest score.

* **+100 points** to kill another player's starship.
* **+50 points** to kill a space invader.
* **-25 points** when killed by another player or invader.

[See the full rules](http://code-challenge-2018.privalia.com/rules).

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

NOTE: All these scripts assume there is an user `david` in the host. You can change it for your user name to avoid  permission problems.

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
