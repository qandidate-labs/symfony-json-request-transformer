symfony-json-request-transformer
================================

A Symfony event listener for decoding JSON encoded request content.

[![Build Status](https://travis-ci.org/qandidate-labs/symfony-json-request-transformer.svg?branch=master)](https://travis-ci.org/qandidate-labs/symfony-json-request-transformer)

## About

Read the blog post about this repository at http://labs.qandidate.com/blog/2014/08/13/handling-angularjs-post-requests-in-symfony/

Currently this library supports both Symfony 2 and Symfony 3. Tests run against all LTS versions of Symfony 2 and against Symfony 3.
As Symfony 3 requires PHP >5.5.9 and PHP 5.3 and 5.4 have been out of support for a while, we only test against 5.5+.

## Install

Install qandidate/symfony-json-request-transformer [through composer](http://getcomposer.org).

```bash
composer require qandidate/symfony-json-request-transformer
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
