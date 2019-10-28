<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\Util;

use Illuminate\View\View;
use ReliqArts\StyleImporter\ConfigProvider;
use ReliqArts\StyleImporter\Exception\ActiveViewHtmlRetrievalFailed;
use ReliqArts\StyleImporter\Exception\ActiveViewRetrievalFailed;
use Throwable;

class ViewAccessor
{
    private const ACTIVE_VIEW_POSITION = 2;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var BacktraceAccessor
     */
    private $backtraceAccessor;

    /**
     * ActiveViewRetriever constructor.
     *
     * @param ConfigProvider    $configProvider
     * @param BacktraceAccessor $backtraceAccessor
     */
    public function __construct(ConfigProvider $configProvider, BacktraceAccessor $backtraceAccessor)
    {
        $this->configProvider = $configProvider;
        $this->backtraceAccessor = $backtraceAccessor;
    }

    /**
     * @param null|View $view
     *
     * @throws ActiveViewHtmlRetrievalFailed
     * @return string
     */
    public function getViewHTML(?View $view = null): string
    {
        try {
            $view = $view ?? $this->deriveActiveViewFromBacktrace();

            return $view
                ->with([$this->configProvider->getSkipStyleImportVariableName() => true])
                ->render();
        } catch (Throwable $exception) {
            throw new ActiveViewHtmlRetrievalFailed(
                sprintf('Could not retrieve HTML of active view; %s', $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws ActiveViewRetrievalFailed
     * @return View
     */
    private function deriveActiveViewFromBacktrace(): View
    {
        /** @var null|View $view */
        $view = $this->backtraceAccessor->getNthObjectOfType(self::ACTIVE_VIEW_POSITION, View::class);

        if (!empty($view)) {
            return $view;
        }

        throw new ActiveViewRetrievalFailed(
            'Could not retrieve active view from backtrace.'
        );
    }
}
