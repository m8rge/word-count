<?php

namespace m8rge\tests;


use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use phpmock\phpunit\PHPMock;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class CountCommandTest extends \PHPUnit_Framework_TestCase
{
    /** @var  vfsStreamDirectory */
    protected $vfs;
    
    use PHPMock;

    protected function setUp()
    {
        parent::setUp();

        $this->getFunctionMock(__NAMESPACE__, "file_exists")->expects($this->any())->willReturn(true);
        $this->getFunctionMock(__NAMESPACE__, "is_file")->expects($this->any())->willReturn(true);
        $this->vfs = vfsStream::setup();
    }

    private function assertResult($inputFileContents, $expectedOutput)
    {
        vfsStream::newFile('file.txt')->at($this->vfs)->setContent($inputFileContents);
        $output = new BufferedOutput();
        \App::$app->get('count')->run(new ArrayInput(['filename' => vfsStream::url('root/file.txt')]), $output);
        $this->assertEquals($expectedOutput, trim($output->fetch()));
    }

    public function testSimple()
    {
        $this->assertResult('abc', 'abc 1');
        $this->assertResult('abc Abc', 'abc 2');
        $this->assertResult('abc1 Abc1', 'abc 2');
    }

    public function testSeparators()
    {
        $this->assertResult("abc\tabc abc\rabc\nabc", 'abc 5');
    }

    public function testUtf()
    {
        $this->assertResult('абв', 'абв 1');
        $this->assertResult('абв АБВ', 'абв 2');
        $this->assertResult('абв1 АБВ2', 'абв 2');
    }

    public function testOrder()
    {
        $this->assertResult('a bc cde 1', "a 1\nbc 1\ncde 1");
        $this->assertResult('cde bc a 1', "a 1\nbc 1\ncde 1");
    }

    public function testEdgeCase()
    {
        $this->assertResult('123.', '');
        $this->assertResult('r 123.', 'r 1');
    }
}
