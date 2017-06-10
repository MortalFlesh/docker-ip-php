Docker Ip
=========

[![Build Status](https://travis-ci.org/MortalFlesh/docker-ip-php.svg?branch=master)](https://travis-ci.org/MortalFlesh/docker-ip-php)
[![Coverage Status](https://coveralls.io/repos/github/MortalFlesh/docker-ip-php/badge.svg?branch=master)](https://coveralls.io/github/MortalFlesh/docker-ip-php?branch=master)

# How to use?

## Install as dependency

_TODO_

## How to run it?

### Show list of available commands
```bash
bin/docker-ip-console list
```

_TODO_: show current list here

### Distribute IP to Host

#### Usage:
```bash
vendor/bin/docker-ip-console docker-ip:distributeIpToHost [options]
```

#### Options:
       -d, --domain=DOMAIN              Your local domain
           --docker-file=DOCKER-FILE    Full path to your docker compose yml
           --hosts[=HOSTS]              Full path to your hosts file [default: "/etc/hosts"]
       -p, --placeholder[=PLACEHOLDER]  Placeholder used in DOCKER_FILE [default: "DOCKER_IP_PLACEHOLDER"]
       -h, --help                       Display this help message
       -q, --quiet                      Do not output any message
       -V, --version                    Display this application version
           --ansi                       Force ANSI output
           --no-ansi                    Disable ANSI output
       -n, --no-interaction             Do not ask any interactive question
       -v|vv|vvv, --verbose             Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

#### Example of `/etc/hosts`
```
127.0.0.1 your_domain
```

#### Example of `DOCKER_FILE.yml`
```yaml
extra_hosts:
  - "your_domain:DOCKER_IP_PLACEHOLDER"
```

### Revert changes

#### Usage:
```bash
vendor/bin/docker-ip-console docker-ip:revert [options]
```

#### Options:
    _TODO_

#### Example of `/etc/hosts`
```
#REPLACED_BY_DOCKER_IP 127.0.0.1 your_domain
{DOCKER_IP} your_domain
```

#### Example of `DOCKER_FILE.yml`
```yaml
extra_hosts:
#REPLACED_BY_DOCKER_IP       - "your_domain:DOCKER_IP_PLACEHOLDER"
  - "your_domain:{DOCKER_IP}"
```

# Development

## Install

```bash
composer install
```

## How it works?

- runs `ifconfig` to get list of available nets
- finds suitable IP for local hosts
- distribute IP to hosts for domain you specified in args
