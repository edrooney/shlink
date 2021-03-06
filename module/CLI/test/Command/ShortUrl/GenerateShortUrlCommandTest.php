<?php
declare(strict_types=1);

namespace ShlinkioTest\Shlink\CLI\Command\ShortUrl;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\UriInterface;
use Shlinkio\Shlink\CLI\Command\ShortUrl\GenerateShortUrlCommand;
use Shlinkio\Shlink\Core\Entity\ShortUrl;
use Shlinkio\Shlink\Core\Exception\InvalidUrlException;
use Shlinkio\Shlink\Core\Service\UrlShortener;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateShortUrlCommandTest extends TestCase
{
    /** @var CommandTester */
    private $commandTester;
    /** @var ObjectProphecy */
    private $urlShortener;

    public function setUp(): void
    {
        $this->urlShortener = $this->prophesize(UrlShortener::class);
        $command = new GenerateShortUrlCommand($this->urlShortener->reveal(), [
            'schema' => 'http',
            'hostname' => 'foo.com',
        ]);
        $app = new Application();
        $app->add($command);
        $this->commandTester = new CommandTester($command);
    }

    /** @test */
    public function properShortCodeIsCreatedIfLongUrlIsCorrect()
    {
        $urlToShortCode = $this->urlShortener->urlToShortCode(Argument::cetera())->willReturn(
            (new ShortUrl(''))->setShortCode('abc123')
        );

        $this->commandTester->execute([
            'longUrl' => 'http://domain.com/foo/bar',
            '--maxVisits' => '3',
        ]);
        $output = $this->commandTester->getDisplay();

        $this->assertStringContainsString('http://foo.com/abc123', $output);
        $urlToShortCode->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function exceptionWhileParsingLongUrlOutputsError()
    {
        $this->urlShortener->urlToShortCode(Argument::cetera())->willThrow(new InvalidUrlException())
                                                               ->shouldBeCalledOnce();

        $this->commandTester->execute(['longUrl' => 'http://domain.com/invalid']);
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString(
            'Provided URL "http://domain.com/invalid" is invalid.',
            $output
        );
    }

    /** @test */
    public function properlyProcessesProvidedTags()
    {
        $urlToShortCode = $this->urlShortener->urlToShortCode(
            Argument::type(UriInterface::class),
            Argument::that(function (array $tags) {
                Assert::assertEquals(['foo', 'bar', 'baz', 'boo', 'zar'], $tags);
                return $tags;
            }),
            Argument::cetera()
        )->willReturn((new ShortUrl(''))->setShortCode('abc123'));

        $this->commandTester->execute([
            'longUrl' => 'http://domain.com/foo/bar',
            '--tags' => ['foo,bar', 'baz', 'boo,zar,baz'],
        ]);
        $output = $this->commandTester->getDisplay();

        $this->assertStringContainsString('http://foo.com/abc123', $output);
        $urlToShortCode->shouldHaveBeenCalledOnce();
    }
}
