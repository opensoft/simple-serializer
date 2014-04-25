Simple-Serializer
================


[![Build Status](https://secure.travis-ci.org/opensoft/simple-serializer.png?branch=master)](http://travis-ci.org/opensoft/simple-serializer)
[![Total Downloads](https://poser.pugx.org/opensoft/simple-serializer/downloads.png)](https://packagist.org/packages/opensoft/simple-serializer)
[![Latest Stable Version](https://poser.pugx.org/opensoft/simple-serializer/v/stable.png)](https://packagist.org/packages/opensoft/simple-serializer)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/opensoft/simple-serializer/badges/quality-score.png?s=ce7bb8be75525d519d466114856681b0dbd95848)](https://scrutinizer-ci.com/g/opensoft/simple-serializer/)

Introduction
------------

Simple-Serializer allows you to serialize your objects into a requested output format such as JSON.
The library is written to work with DTO objects in the REST services.

Built-in features include:

- (de-)serialize object graphs
- supports boolean, integer, double, DateTime\<format\>, array, T, array\<T\>, null types, where "T" - is some PHP object.
- configurable via YAML
- three unserialize mode (non-strict, medium strict, strict)

Unserialize mode:
Non-Strict mode - serializer does not check incoming parameters
Medium Strict - serializer extra check incoming parameters and if they exist throw InvalidArgumentException
Strict - serializer extra check incoming parameters completely as expected if there are extra arguments or missing some, it throws an exception.


Some Restrictions:

- object must have configuration for serialize/unserialize

Possible TODO list:

- (de-)serialize object graphs of any complexity including circular references
- configurable via PHP, XML, or annotations
- custom integrates with Doctrine ORM, et. al.

It should be noted that Simple-Serializer is realy simple library with minimum configuration,
but it provides wide opportunity for create REST API.

[![Build Status](https://secure.travis-ci.org/opensoft/simple-serializer.png?branch=master)](http://travis-ci.org/opensoft/simple-serializer)


Installation
------------

To install Simple-Serializer with Composer just add the following to your `composer.json` file:

```javascript
    // composer.json
    {
        // ...
        require: {
            // ...
            "opensoft/simple-serializer": "dev-master"
        }
    }
```

Then, you can install the new dependencies by running Composer's ``update``
command from the directory where your ``composer.json`` file is located:

    $ php composer.phar update

Configuration
-------------

```yml
MyBundle\Resources\config\serializer\ClassName.yml
    Fully\Qualified\ClassName:
        properties:
            some-property:
                expose: true
                type: string
                serialized_name: foo
                since_version: 1.0
                until_version: 2.0
                groups: ['get','patch']
```

* expose
 * true
 * false (default)
* type
 * integer
 * boolean
 * double
 * string
 * array
 * T - fully qualified class name
 * array\<T\>
 * DateTime
 * DateTime\<format\>
  * format could be name of DateTime constant (COOKIE, ISO8601), string or empty (default format is ISO8601)
* serialized_name
 * default value is equal name property
* since_version
 * string
* until_version
 * string
* groups
 * array

Serializing Objects
-------------------
Most common usage is probably to serialize objects. This can be achieved
very easily:

```php
    <?php
    //get Serializer
    $serializer = $this->getSerializer();
    $string = $serializer->serialize($object);
    //Serialize array of the objects
    $string = $serializer->serialize(array($object));
    //Serialize specific groups
    $serializer->setGroups(array('get'));
    $string = $serializer->serialize($object);
    //Serialize specific version
    $serializer->setVersion('1.0');
    $string = $serializer->serialize($object);
```

Deserializing Objects
---------------------
You can also unserialize objects from JSON representation. For
example, when accepting data via an API.

```php
    <?php
    //get Fully\Qualified\ClassName
    $object = $this->getClassName();
    //get Serializer
    $serializer = $this->getSerializer();
    $object = $serializer->unserialize($jsonData, $object);
    //Unserialize array of the objects
    $objects = $serializer->unserialize($jsonData, array($object));
    //Unserialize specific groups
    $serializer->setGroups(array('get'));
    $object = $serializer->unserialize($jsonData, $object);
    //Unserialize specific version
    $serializer->setVersion('1.0');
    $object = $serializer->unserialize($jsonData, $object);
    //Strict unserialize mode
    $serializer->setStrictUnserializeMode(2);
    $object = $serializer->unserialize($jsonData, $object);
    //Medium Strict unserialize mode
    $serializer->setStrictUnserializeMode(1);
    $object = $serializer->unserialize($jsonData, $object);
    //Non-Strict unserialize mode
    $serializer->setStrictUnserializeMode(0);
    $object = $serializer->unserialize($jsonData, $object);
```
