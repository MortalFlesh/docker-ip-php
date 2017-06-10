Docker Ip
=========

[![Latest Stable Version](https://img.shields.io/packagist/v/mf/docker-ip-php.svg)](https://packagist.org/packages/mf/docker-ip-php)
[![Total Downloads](https://img.shields.io/packagist/dt/mf/docker-ip-php.svg)](https://packagist.org/packages/mf/docker-ip-php)
[![Build Status](https://travis-ci.org/MortalFlesh/docker-ip-php.svg?branch=master)](https://travis-ci.org/MortalFlesh/docker-ip-php)
[![Coverage Status](https://coveralls.io/repos/github/MortalFlesh/docker-ip-php/badge.svg?branch=master)](https://coveralls.io/github/MortalFlesh/docker-ip-php?branch=master)
[![License](https://img.shields.io/packagist/l/mf/docker-ip-php.svg)](https://packagist.org/packages/mf/docker-ip-php)

Finds suitable IP from `ifconfig` and then distribute this IP into hosts and docker file and allows revert changes.

# How to use?

## Install as dependency

```bash
composer require --dev mf/docker-ip
```

## How to run it?

### Show list of available commands
```bash
vendor/bin/docker-ip-console list
```

### Usage:
```bash
vendor/bin/docker-ip-console [command] [arguments]
```

#### Available commands:
      help                          Displays help for a command
      list                          Lists commands
     docker-ip
      docker-ip:distributeIpToHost  Finds suitable IP from `ifconfig` and then distribute this IP into hosts and docker file
      docker-ip:revert              Reverts changes from `distributeIpToHost` in hosts and docker file


### Distribute IP to Host
Finds suitable IP from `ifconfig` and then distribute this IP into hosts and docker file.

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
Reverts changes from `distributeIpToHost` in hosts and docker file

#### Usage:
```bash
vendor/bin/docker-ip-console docker-ip:revert [options]
```

#### Options:
           --docker-file=DOCKER-FILE  Full path to your docker compose yml
           --hosts[=HOSTS]            Full path to your hosts file [default: "/etc/hosts"]
       -h, --help                     Display this help message
       -q, --quiet                    Do not output any message
       -V, --version                  Display this application version
           --ansi                     Force ANSI output
           --no-ansi                  Disable ANSI output
       -n, --no-interaction           Do not ask any interactive question
       -v|vv|vvv, --verbose           Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

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
