<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\Tests\Unit\Util;

use Illuminate\View\View;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use ReliqArts\StyleImporter\ConfigProvider;
use ReliqArts\StyleImporter\Exception\ActiveViewHtmlRetrievalFailed;
use ReliqArts\StyleImporter\Util\BacktraceAccessor;
use ReliqArts\StyleImporter\Util\ViewAccessor;
use Throwable;

/**
 * Class ViewAccessorTest.
 *
 * @coversDefaultClass \ReliqArts\StyleImporter\Util\ViewAccessor
 *
 * @internal
 */
final class ViewAccessorTest extends TestCase
{
    private const SKIP_STYLE_IMPORT_VARIABLE_NAME = 'var';
    private const VIEW_HTML = '<html><body>hi</body></html>';

    /**
     * @var ConfigProvider|ObjectProphecy
     */
    private $configProvider;

    /**
     * @var BacktraceAccessor|ObjectProphecy
     */
    private $backtraceAccessor;

    /**
     * @var ViewAccessor
     */
    private $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->backtraceAccessor = $this->prophesize(BacktraceAccessor::class);
        $this->configProvider = $this->prophesize(ConfigProvider::class);

        $this->subject = new ViewAccessor(
            $this->configProvider->reveal(),
            $this->backtraceAccessor->reveal()
        );
    }

    public function testGetViewHTML(): void
    {
        $this->backtraceAccessor
            ->getNthObjectOfType(Argument::type('integer'), View::class)
            ->shouldBeCalledTimes(1)
            ->willReturn($this->getView());

        $this->configProvider
            ->getSkipStyleImportVariableName()
            ->shouldBeCalledTimes(1)
            ->willReturn(self::SKIP_STYLE_IMPORT_VARIABLE_NAME);

        $result = $this->subject->getViewHTML();

        $this->assertSame(self::VIEW_HTML, $result);
    }

    public function testGetViewHTMLWhenViewRetrievalFails(): void
    {
        $this->backtraceAccessor
            ->getNthObjectOfType(Argument::type('integer'), View::class)
            ->shouldBeCalledTimes(1)
            ->willReturn(null);

        $this->configProvider
            ->getSkipStyleImportVariableName()
            ->shouldNotBeCalled();

        $this->expectException(ActiveViewHtmlRetrievalFailed::class);
        $this->expectExceptionMessage('Could not retrieve HTML of active view');

        $result = $this->subject->getViewHTML();
    }

    /**
     * @throws Throwable
     *
     * @return ObjectProphecy|View
     */
    private function getView(): View
    {
        /** @var ObjectProphecy|View $view */
        $view = $this->prophesize(View::class);
        $view
            ->with(Argument::type('array'))
            ->willReturn($view);
        $view
            ->render()
            ->willReturn(self::VIEW_HTML);

        return $view->reveal();
    }
}
