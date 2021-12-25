# Backend Assignment


## Setup

This project is using [Sail](https://laravel.com/docs/8.x/sail). At its heart, Sail is the docker-compose.yml file and the sail script that is stored at the root of your project. The sail script provides a CLI with convenient methods for interacting with the Docker containers defined by the docker-compose.yml file.

### How to start
```
./vendor/bin/sail up
```

or

```
alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'
```

```
sail up
```


## Command

Import the html to store the activities in the database.

```
php artisan command:html-to-roster-event-parser ./Roster\ -\ CrewConnex.html
```

## Test

Test the units of the project.

```
sail php vendor/bin/phpunit --coverage-html reports
```
