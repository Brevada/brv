# Brevada

## Style Guide

All `PHP` code should remain `PSR-1` and `PSR-2` compliant. All new `PHP`
classes should be compliant with the `PSR-4` autoloader standard.

Complex `SQL` queries should be broken up on multiple lines.

All `PHP` code should be documented.

## Developer Environment Setup

Requires min. `PHP 5.6`.

### Clone Repository

```
> mkdir brv && cd brv
> git clone https://github.com/RobbieGoldfarb/brv.git .
```

### Configuration File

Create an .ini file outside of the project's root directory. Save the absolute path to the .ini file in an environment variable with the name `BRV_CONFIG_PATH`. Configure the file appropriately.

```
> export BRV_CONFIG_PATH=/etc/.../brevada.ini
```

Example Configuration (commented out lines are optional):
```
[general]
; debug = false
; maintenance_mode = false

[host]
host = "brevada.com"
dev_host = "brevada.local"
main_host = "beta.{HOST}"
legacy_host = "{HOST}"

[database]
db_username = 'root'
db_password = 'root'
db_host = 'localhost'
db_schema = 'redreadu_brevada'

[log]
; log_directory = "/"
```

### Run Installation and Resolve Dependencies

The latest version of `npm` and `composer` are required.

```
> npm install
```

### Build

To build all assets (css/javascript) in a development environment:
```
> npm run dev-build
```

Which is equivalent to:
```
> npm run dev-build-css && npm run dev-build-js
```

To build documentation:
```
> npm run build-docs
```

To build for production, omit the "dev-" prefix:
```
> npm run build
```

### Testing

`phpunit` (min. 5.7.4) must be installed on the dev machine and must be present
in the shell's environment variables.

```
> npm test
```

Arguments supplied to the test command are forwarded to `phpunit`.

### Clean

To clean the distribution files (remove them):
```
> npm run clean
```

## Deployment Process

Configure the environment configuration (ini) file if not already configured. Apply any schema changes per the latest PR request or Slack communication.

Execute as root:

```
> ./home/brevadat/update_brevada.sh
```
