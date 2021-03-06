<?php
declare(strict_types=1);

namespace ShlinkioTest\Shlink\Common\Template\Extension;

use League\Plates\Engine;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Shlinkio\Shlink\Common\Template\Extension\TranslatorExtension;
use Zend\I18n\Translator\Translator;

class TranslatorExtensionTest extends TestCase
{
    /** @var TranslatorExtension */
    private $extension;

    public function setUp(): void
    {
        $this->extension = new TranslatorExtension($this->prophesize(Translator::class)->reveal());
    }

    /** @test */
    public function properFunctionsAreReturned()
    {
        $engine = $this->prophesize(Engine::class);
        $registerTranslate = $engine->registerFunction('translate', Argument::type('callable'))->will(function () {
        });
        $registerLocale = $engine->registerFunction('locale', Argument::type('array'))->will(function () {
        });

        $this->extension->register($engine->reveal());

        $registerTranslate->shouldHaveBeenCalledOnce();
        $registerLocale->shouldHaveBeenCalledOnce();
    }
}
