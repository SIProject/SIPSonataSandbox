# Инсталляция

Установка модуля осуществляется в развернутый и настроенный экземпляр Symfony2, используя встроенный vendors script.

Для работы модуля, необходимо предварительно установить след. расширения:

* DoctrineExtensionsBundle http://symfony.com/doc/2.0/cookbook/doctrine/common_extensions.html

### Step 1: Установка
Для установки необходимо добавить следующий код в `deps` файл проекта:

```
[ArmdCmsBundle]
    git=git@git.armd.ru:CmsBundle.git
    target=/bundles/Armd/Bundle/CmsBundle
```
Запустить vendors script, который скачает модуль:

``` bash
$ php bin/vendors install
```

### Step 2: Настройка Autoloader и AppKernel

Необходимо добавить namespace `Armd`, если он еще не существует:

``` php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'Armd' => __DIR__.'/../vendor/bundles',
));
```

и разрешить обработку этого модуля

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Armd\Bundle\CmsBundle\ArmdCmsBundle(),
    );
}
```

### Step 3: Обновление схемы баз данных
``` bash
$ php app/console doctrine:schema:update --force
```

### Step 4: Конфигурация
Необходимо сконфигурировать следующие бандлы, для корректной работы CmsBundle

    # app/config/config.yml
    framework:
        # ...
        translator:      { fallback: %locale% }

    stof_doctrine_extensions:
        orm:
            default:
                sluggable: true
                tree: true
                timestampable: true


Для обработки запросов минуя роутинг CmsBundle (например, для админки) необходимо добавить след. конфигурацию

    # app/config/config.yml
    stof_doctrine_extensions:
        orm:
            default:
                sluggable: true
    armd_cms:
        ignore_route_patterns:
            - /(.*)admin(.*)/

