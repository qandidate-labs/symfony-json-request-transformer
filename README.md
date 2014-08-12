symfony-json-request-transformer
================================

A Symfony 2 event listener for decoding JSON encoded request content.

## Install

Install qandidate/symfony-json-request-transformer [through composer](http://getcomposer.org).

```JSON
{
    "require": {
        "qandidate/symfony-json-request-transformer": "*"
    }
}
```

Register the event listener as a service:

```xml
<service id="kernel.event_listener.json_request_transformer" class="Qandidate\Common\Symfony\HttpKernel\EventListener\JsonRequestTransformerListener">
    <tag name="kernel.event_listener" event="kernel.request" method="onKernelRequest" priority="100" />
</service>
```

```yml
services:
  kernel.event_listener.json_request_transformer:
    class: Qandidate\Common\Symfony\HttpKernel\EventListener\JsonRequestTransformerListener
    tags:
      - { name: kernel.event_listener, event: kernel.request, method: onKernelRequest, priority: 100 }
```

## Example

A request with JSON content like:
```JSON
{
  "foo": "bar"
}
```

will be decoded automatically so can access the `foo` property like:

```php
echo $request->request->get('foo');
```

## License

MIT, see LICENSE.
