<?php

/*
 * This file is part of the qandidate/symfony-json-request-transformer package.
 *
 * (c) Qandidate.com <opensource@qandidate.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Qandidate\Common\Symfony\HttpKernel\EventListener;

use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

class JsonRequestTransformerListenerTest extends PHPUnit_Framework_TestCase
{
    private $listener;

    public function setUp()
    {
        $this->listener = new JsonRequestTransformerListener();
    }

    /**
     * @test
     * @dataProvider jsonContentTypes
     */
    public function it_transforms_requests_with_a_json_content_type($contentType)
    {
        $data    = array('foo' => 'bar');
        $request = $this->createRequest($contentType, json_encode($data));
        $event   = $this->createGetResponseEventMock($request);

        $this->listener->onKernelRequest($event);

        $this->assertEquals(
            $data,
            $event->getRequest()->request->all()
        );
        $this->assertNull($event->getResponse());
    }

    public function jsonContentTypes()
    {
        return array(
            array('application/json'),
            array('application/x-json'),
        );
    }

    /**
     * @test
     */
    public function it_returns_a_bad_request_response_if_json_is_invalid()
    {
        $request = $this->createRequest('application/json', '{meh}');
        $event   = $this->createGetResponseEventMock($request);

        $this->listener->onKernelRequest($event);

        $this->assertEquals(400, $event->getResponse()->getStatusCode());
    }

    /**
     * @test
     * @dataProvider notJsonContentTypes
     */
    public function it_does_not_transform_other_content_types($contentType)
    {
        $request = $this->createRequest($contentType, 'some=body');
        $event   = $this->createGetResponseEventMock($request);

        $this->listener->onKernelRequest($event);

        $this->assertEquals($request, $event->getRequest());
        $this->assertNull($event->getResponse());
    }

    /**
     * @test
     */
    public function it_does_not_replace_request_data_if_there_is_none()
    {
        $request = $this->createRequest('application/json', '');
        $event   = $this->createGetResponseEventMock($request);

        $this->listener->onKernelRequest($event);

        $this->assertEquals($request, $event->getRequest());
        $this->assertNull($event->getResponse());
    }

    /**
     * @test
     */
    public function it_does_not_replace_request_data_if_content_is_json_null()
    {
        $request = $this->createRequest('application/json', 'null');
        $event   = $this->createGetResponseEventMock($request);

        $this->listener->onKernelRequest($event);

        $this->assertEquals($request, $event->getRequest());
        $this->assertNull($event->getResponse());
    }

    public function notJsonContentTypes()
    {
        return array(
            array('application/x-www-form-urlencoded'),
            array('text/html'),
            array('text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8'),
        );
    }

    private function createRequest($contentType, $body)
    {
        $request = new Request(array(), array(), array(), array(), array(), array(), $body);
        $request->headers->set('CONTENT_TYPE', $contentType);

        return $request;
    }

    private function createGetResponseEventMock(Request $request)
    {
        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->setMethods(array('getRequest'))
            ->getMock();

        $event->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        return $event;
    }
}
