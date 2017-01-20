# Brevada

## Style Guide

All `PHP` code should remain `PSR-1` and `PSR-2` compliant. All new `PHP`
classes should be compliant with the `PSR-4` autoloader standard.

Complex `SQL` queries should be broken up on multiple lines.

All `PHP` code should be documented.

## Developer Environment Setup

### Clone Repository

```
> mkdir brv && cd brv
> git clone https://github.com/RobbieGoldfarb/brv.git .
```

### Run Installation and Resolve Dependencies

The latest version of `npm` and `composer` are required.

```
> npm install
> composer update
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

TODO: Deployment documentation is in progress.
