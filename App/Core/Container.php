<?php
namespace App\Core;

use ReflectionClass;
use ReflectionParameter;
use Exception;

class Container
{
    private static ?Container $instance = null;
    private array $bindings = [];
    private array $instances = [];

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {}

    public function bind(string $abstract, $concrete = null): void
    {
        if ($concrete === null) {
            $concrete = $abstract;
        }
        $this->bindings[$abstract] = $concrete;
    }

    public function singleton(string $abstract, $concrete = null): void
    {
        $this->bind($abstract, $concrete);
        $this->instances[$abstract] = null;
    }

    public function resolve(string $abstract)
    {
        // Si c'est un singleton et qu'il existe déjà
        if (array_key_exists($abstract, $this->instances) && $this->instances[$abstract] !== null) {
            return $this->instances[$abstract];
        }

        // Récupérer la classe concrète
        $concrete = $this->bindings[$abstract] ?? $abstract;

        // Si c'est une closure
        if ($concrete instanceof \Closure) {
            $object = $concrete($this);
        } else {
            $object = $this->build($concrete);
        }

        // Si c'est un singleton, le stocker
        if (array_key_exists($abstract, $this->instances)) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    private function build(string $concrete)
    {
        $reflector = new ReflectionClass($concrete);

        if (!$reflector->isInstantiable()) {
            throw new Exception("La classe {$concrete} n'est pas instanciable");
        }

        $constructor = $reflector->getConstructor();

        if ($constructor === null) {
            return new $concrete;
        }

        $parameters = $constructor->getParameters();
        $dependencies = $this->resolveDependencies($parameters);

        return $reflector->newInstanceArgs($dependencies);
    }

    private function resolveDependencies(array $parameters): array
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $dependency = $this->resolveDependency($parameter);
            $dependencies[] = $dependency;
        }

        return $dependencies;
    }

    private function resolveDependency(ReflectionParameter $parameter)
    {
        $type = $parameter->getType();

        if ($type === null) {
            throw new Exception("Impossible de résoudre la dépendance {$parameter->getName()}");
        }

        if ($type->isBuiltin()) {
            throw new Exception("Impossible de résoudre la dépendance primitive {$parameter->getName()}");
        }

        return $this->resolve($type->getName());
    }
}
