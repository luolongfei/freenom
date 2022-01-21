<?php

use \Bramus\Ansi\Writers\StreamWriter;
use \Bramus\Ansi\Writers\BufferWriter;
use \Bramus\Ansi\Writers\ProxyWriter;

class WritersTest extends PHPUnit_Framework_TestCase
{
    public function testStreamWriter()
    {

        // Start object buffering to catch any output
        ob_start();

        // Create a StreamWriter
        // @note: Using php://output instead of php://stdout — https://bugs.php.net/bug.php?id=49688
        $w = new StreamWriter('php://output');

        // Write something to the writer
        $w->write('test');

        // The written data should be echo'd (StreamWriter)
        $this->assertEquals('test', ob_get_contents());

        // Cleanup
        ob_end_clean();

    }

    public function testBufferWriter()
    {

        // Create a BufferWriter
        $w = new BufferWriter();

        // Write something to the Proxy
        $w->write('test');

        // Flush its contents
        $res = $w->flush();

        // The written data should be returned
        $this->assertEquals('test', $res);

    }

    public function testProxyWriter()
    {

        // Start object buffering to catch any output
        ob_start();

        // Create a ProxyWriter which proxies for a StreamWriter
        $w = new ProxyWriter(new StreamWriter('php://output'));

        // Write something to the Proxy
        $w->write('test');

        // Flush its contents
        $res = $w->flush();

        // The written data should be echo'd (StreamWriter)
        $this->assertEquals('test', ob_get_contents());

        // The written data should be returned too (ProxyWriter)
        $this->assertEquals('test', $res);

        // Cleanup
        ob_end_clean();

    }
}