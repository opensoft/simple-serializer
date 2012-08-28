Simple-Serializer
================

Introduction
------------

Simple-Serializer allows you to serialize your objects into a requested output format such as JSON.
The library is written to work with DTO objects in the REST services.

Built-in features include:

- (de-)serialize object graphs
- supports boolean, integer, double, DateTime, array, T, array<T>, null types, where "T" - is some PHP object.
- configurable via YAML

Possible TODO list:

- (de-)serialize object graphs of any complexity including circular references
- configurable via PHP, XML, or annotations
- custom integrates with Doctrine ORM, et. al.

[![Build Status](https://secure.travis-ci.org/opensoft/simple-serializer.png?branch=master)](http://travis-ci.org/opensoft/simple-serializer)


Installation
------------

To install Simple-Serializer with Composer just add the following to your `composer.json` file:

.. code-block :: js

    // composer.json
    {
        // ...
        require: {
            // ...
            "opensoft/simple-serializer": "dev-master"
        }
    }

Then, you can install the new dependencies by running Composer's ``update``
command from the directory where your ``composer.json`` file is located:

.. code-block :: bash

    $ php composer.phar update

Configuration
-------------

# MyBundle\Resources\config\serializer\ClassName.yml
    Fully\Qualified\ClassName:
        properties:
            some-property:
                expose: true
                type: string
                serialized_name: foo

Serializing Objects
-------------------
Most common usage is probably to serialize objects. This can be achieved
very easily:

.. code-block :: php

    <?php

    $serializer = $this->getSerializer(); //get Serializer
    $serializer->serialize($object);

Deserializing Objects
---------------------
You can also deserialize objects from JSON representation. For
example, when accepting data via an API.

.. code-block :: php

    <?php

    $object = $this->getClassName();//get Fully\Qualified\ClassName
    $serializer = $this->getSerializer(); //get Serializer
    $object = $serializer->deserialize($jsonData, $object);
