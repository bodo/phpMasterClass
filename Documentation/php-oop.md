
## Visibility
The visibility of a property, a method or (as of PHP 7.1.0) a constant can be defined by prefixing the declaration with the keywords public, protected or private.

Class members declared public can be accessed everywhere.

Members declared protected can be accessed only within the class itself and by inheriting and parent classes.

Members declared as private may only be accessed by the class that defines the member.



## Object Interfaces
Object interfaces allow you to create code which specifies which methods and properties a class must implement, without having to define how these methods or properties are implemented. Interfaces share a namespace with classes, traits, and enumerations, so they may not use the same name.

Interfaces are defined in the same way as a class, but with the interface keyword replacing the class keyword and without any of the methods having their contents defined.

All methods declared in an interface must be public; this is the nature of an interface.

In practice, interfaces serve two complementary purposes:

To allow developers to create objects of different classes that may be used interchangeably because they implement the same interface or interfaces. A common example is multiple database access services, multiple payment gateways, or different caching strategies. Different implementations may be swapped out without requiring any changes to the code that uses them.
To allow a function or method to accept and operate on a parameter that conforms to an interface, while not caring what else the object may do or how it is implemented. These interfaces are often named like Iterable, Cacheable, Renderable, or so on to describe the significance of the behavior.
Interfaces may define magic methods to require implementing classes to implement those methods.




## Class Abstraction
PHP has abstract classes, methods, and properties. Classes defined as abstract cannot be instantiated, and any class that contains at least one abstract method or property must also be abstract. Methods defined as abstract simply declare the method's signature and whether it is public or protected; they cannot define the implementation. Properties defined as abstract may declare a requirement for get or set behavior, and may provide an implementation for one, but not both, operations.

When inheriting from an abstract class, all methods marked abstract in the parent's class declaration must be defined by the child class, and follow the usual inheritance and signature compatibility rules.

As of PHP 8.4, an abstract class may declare an abstract property, either public or protected. A protected abstract property may be satisfied by a property that is readable/writeable from either protected or public scope.

An abstract property may be satisfied either by a standard property or by a property with defined hooks, corresponding to the required operation.



## Property Hooks
Property hooks, also known as "property accessors" in some other languages, are a way to intercept and override the read and write behavior of a property. This functionality serves two purposes:

It allows for properties to be used directly, without get- and set- methods, while leaving the option open to add additional behavior in the future. That renders most boilerplate get/set methods unnecessary, even without using hooks.
It allows for properties that describe an object without needing to store a value directly.
There are two hooks available on non-static properties: get and set. They allow overriding the read and write behavior of a property, respectively. Hooks are available for both typed and untyped properties.

A property may be "backed" or "virtual". A backed property is one that actually stores a value. Any property that has no hooks is backed. A virtual property is one that has hooks and those hooks do not interact with the property itself. In this case, the hooks are effectively the same as methods, and the object does not use any space to store a value for that property.

Property hooks are incompatible with readonly properties. If there is a need to restrict access to a get or set operation in addition to altering its behavior, use asymmetric property visibility.

> Note: Version Information
> Property hooks were introduced in PHP 8.4.







## Traits
PHP implements a way to reuse code called Traits.

Traits are a mechanism for code reuse in single inheritance languages such as PHP. A Trait is intended to reduce some limitations of single inheritance by enabling a developer to reuse sets of methods freely in several independent classes living in different class hierarchies. The semantics of the combination of Traits and classes is defined in a way which reduces complexity, and avoids the typical problems associated with multiple inheritance and Mixins.

A Trait is similar to a class, but only intended to group functionality in a fine-grained and consistent way. It is not possible to instantiate a Trait on its own. It is an addition to traditional inheritance and enables horizontal composition of behavior; that is, the application of class members without requiring inheritance.


