<?php

namespace App\Traits;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Symfony\Component\Serializer\Serializer;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Illuminate\Database\Eloquent\JsonEncodingException;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;

abstract class BaseEntity implements Arrayable, Jsonable
{
    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
    }

    /**
     * Convert the model instance to JSON.
     *
     * @param  int  $options
     *
     * @return string
     *
     * @throws \Illuminate\Database\Eloquent\JsonEncodingException
     */
    public function toJson($options = 0)
    {
        // $encoder = new JsonEncoder();
        // $loader = new AnnotationLoader(new AnnotationReader());
        // $classMetadataFactory = new ClassMetadataFactory($loader);
        // $normalizer = new ObjectNormalizer($classMetadataFactory);
        // $serializer = new Serializer([$normalizer], [$encoder]);

        // $json = $serializer->serialize($this, 'json');

        // return $json;


    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
