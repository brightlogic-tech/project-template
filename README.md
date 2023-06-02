# Utility classes

Commonly used patterns & services for PHP projects hosted on AWS using Bref, Nette and GraPHPinator.

## Bootstrap

TBA

## Preloading

> Simple preloader class to recursively walk directory and preload *.php files.

Create simple PHP file and initializer preloader.

```php
<?php declare(strict_types = 1);

\chdir(__DIR__);
include __DIR__ . '/../vendor/autoload.php';

$paths = [
    \BrightLogic\Template\Bootstrap::PROJECT_ROOT,
];

$preloader = new \BrightLogic\Template\Bootstrap(...$paths);
$preloader->load();
```

## StorageFile (S3)

TBA

## Mailer (SES)

TBA

## Messaging/Queue of tasks (SQS)

TBA

## Logging

> Redirects logs to database instead of filesystem.

Register services in configuration neon file.

```neon
services:
    - BrightLogic\Template\Logging\LogTable
    tracy.logger: BrightLogic\Template\Logging\DbLogger
```

`log` table needs to be created. You may use provided bean to generate this table.

```sql
CREATE TABLE `log` (
    `id`      int(11) unsigned auto_increment primary key,
    `time`    datetime default current_timestamp() not null,
    `level`   varchar(20)                          not null,
    `head`    varchar(255)                         not null,
    `message` longtext                             null
) CHARSET = utf8mb4;

CREATE INDEX `log_level_index` ON `log` (level);
```

## Commands

> Commonly used commands which ease deploying and development.

Register services in configuration neon file.

```neon
services:
    - BrightLogic\Template\Command\ClearCacheCommand
    - BrightLogic\Template\Command\CompareDbCommand(%projectName%)
    - CoolBeans\Command\SqlGeneratorCommand
```
