# Společné věci projektů

## Dev dependence

Zkopírovat si dev-dependence z `require-dev` do `require-dev` projektu. Případně také `scripts`.

## Logging

### DbLogger

Je třeba registrovat jako službu v hlavním neon souboru

```neon
services:
    - Infinityloop\Template\Logging\LogTable
    tracy.logger: Infinityloop\Template\Logging\DbLogger
```

Je třeba mít vytvořenou logovací tabulku.

```sql
create table `log`
(
    `id`      int(11) unsigned auto_increment primary key,
    `time`    datetime default current_timestamp() not null,
    `level`   varchar(20)                          not null,
    `head`    varchar(255)                         not null,
    `message` longtext                             null
)
    charset = utf8mb4;

create index `log_level_index` on `log` (level);
```

## Commandy

Je třeba zaregistrovat jako služby v `core.neon`.

```neon
services:
    - Infinityloop\Template\Command\ClearCacheCommand
    - Infinityloop\Template\Command\CompareDbCommand(%sessionName%)
    - CoolBeans\Command\SqlGeneratorCommand
```
