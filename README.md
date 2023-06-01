# Utility classes

## Logging

### DbLogger

Register services in configuration neon file.

```neon
services:
    - Infinityloop\Template\Logging\LogTable
    tracy.logger: Infinityloop\Template\Logging\DbLogger
```

`log` table needs to be created.

```sql
CREATE TABLE `log`
(
    `id`      int(11) unsigned auto_increment primary key,
    `time`    datetime default current_timestamp() not null,
    `level`   varchar(20)                          not null,
    `head`    varchar(255)                         not null,
    `message` longtext                             null
)
    charset = utf8mb4;

CREATE INDEX `log_level_index` ON `log` (level);
```

## Utility Commands

Register services in configuration neon file.

```neon
services:
    - Infinityloop\Template\Command\ClearCacheCommand
    - Infinityloop\Template\Command\CompareDbCommand(%projectName%)
    - CoolBeans\Command\SqlGeneratorCommand
```
