ZendDbBundle
============

[![Build Status](https://travis-ci.org/Haehnchen/ZendDbBundle.png?branch=master)](https://travis-ci.org/Haehnchen/ZendDbBundle)

Bundle that wraps Zend/Db to Symfony2 and Doctrine, so that you can use your Entity and Repository names as Table alias.


## Installation

Installation is a quick 2 step process:

1. Download ZendDbBundle using composer
2. Enable the Bundle

### Step 1: Download ZendDbBundle using composer

Add ZendDbBundle in your composer.json (see versions: [espend/zend-db-bundle](https://packagist.org/packages/espend/zend-db-bundle)):

```js
{
    "require": {
        "espend/zend-db-bundle": "dev-master"
    }
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ php composer.phar update espend/zend-db-bundle
```

Composer will install the bundle to your project's `vendor` directory.

### Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new espend\ZendDbBundle\espendZendDbBundle(),
    );
}
```
## Services
All Services are generated on CompilerPass on the Symfony2 database parameters and attached to `zend.db.manager`. 
Since the manager supports more the one connection `getManager()` will hold all possible connection. If none Parameter give the `default` connection will used.
There are also some adapter services depending on connection `zend.db.adapter.<name>`, if need to call it without manager. The default adapter is alway accessable on `zend.db.adapter`

## Basic Usage

For better usagage and autocomplete put this function somewhere where you have a container.
You should only use this services, because it is auto configured to use platform and driver on container parameters

``` php
/**
 * @return \espend\ZendDbBundle\Zend\Db\ZendDbConnection
 */
private function getZend() {
  return $this->container->get('zend.db.manager')->getManager();
}
```
### Doctrine Repositories and Table names

All Doctrine Entity names can used on top zend/db and will resloved on Doctrine before query the database
```
espendHomeBundle:Homework
espend\HomeBundle\Entity\Homework
espend\HomeBundle\Entity\HomeworkFood
```

Tables alias names are generated on entity names so:
```
espendHomeBundle:HomeworkFood -> homework_food.id
espendHomeBundle:Homework -> homework.id
espend\HomeBundle\Entity\HomeworkFood -> homework_food.id
```


### SQL Query Examples

#### Select
Result set from Database to demonstrate the select statemets
``` js
[
   {
      "id":"1",
      "name":"name"
   }
   {
      "id":"2",
      "name":"name2"
   }   
]
```

``` php
$select = $this->getZend()->getQueryBuilder()->select('espendHomeBundle:Homework');
$select->where(array(
  'status' => 0,
  'type' => 'cleanup',
));
$this->getZend()->fetchArray($select); // return  [{id:1, name:name}, {id:2, name:name2}]
$this->getZend()->fetchColumn($select); // return  {id:1, name:name}
$this->getZend()->fetchField($select); // return 1
```

You can also set alias to select statements, if non provided its generated on entity name
``` php
$this->getZend()->getQueryBuilder()->select(array('no_homework' =>'espendHomeBundle:Homework'));
$this->getZend()->getQueryBuilder()->select(array('nice_homework' =>'espend\HomeBundle\Entity\Homework'));
```

#### Update
``` php
$update = $this->getZend()->getQueryBuilder()->update('espendHomeBundle:Homework');
$update->set(array(
  'type' => 'todo',
));
$update->where(array(
  'status' => 0,
));
$this->getZend()->execute($update);
```

#### Insert
``` php
$insert = $this->getZend()->getQueryBuilder()->insert('espendHomeBundle:Homework');
$insert->values(array(
  'status' => 1,
  'type' => 'cleanup',
));
$this->getZend()->execute($insert);
```

#### Delete
``` php
$delete = $this->getZend()->getQueryBuilder()->delete('espendHomeBundle:Homework');
$delete->where(array(
  'status' => 0,
  'type' => 'cleanup',
));
$this->getZend()->execute($delete);
```

#### Join
``` php
$select = $this->getZend()->getQueryBuilder()->select('espendHomeBundle:Homework');
$select->join('espendHomeBundle:HomeworkFood', 'homework.id = homework_food.id');
```
JOINs also supports alias overwrite
``` php
$select = $this->getZend()->getQueryBuilder()->select('espendHomeBundle:Homework');
$select->join(array('user' => 'espendHomeBundle:HomeworkFood'), 'user.id = homework_food.id');

```

For more syntax example see: [Zend\Db\Sql](http://framework.zend.com/manual/2.1/en/modules/zend.db.sql.html)
