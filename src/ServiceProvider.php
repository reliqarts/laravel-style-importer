<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter;

use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;
use Monolog\Handler\StreamHandler;
use ReliqArts\Contracts\Logger as ReliqArtsLoggerContract;
use ReliqArts\ServiceProvider as ReliqArtsServiceProvider;
use ReliqArts\Services\ConfigProvider as ReliqArtsConfigProvider;
use ReliqArts\Services\Logger as ReliqArtsLogger;
use ReliqArts\StyleImporter\CSS\Extractor\ImportExtractor;
use ReliqArts\StyleImporter\CSS\Extractor\MediaBlockExtractor;
use ReliqArts\StyleImporter\CSS\Generator as GeneratorContract;
use ReliqArts\StyleImporter\CSS\Generator\RuleSetGenerator as RuleSetGenerator;
use ReliqArts\StyleImporter\CSS\Processor as ProcessorContract;
use ReliqArts\StyleImporter\CSS\Processor\Processor;
use ReliqArts\StyleImporter\HTML\ClassExtractor;
use ReliqArts\StyleImporter\HTML\Extractor;
use ReliqArts\StyleImporter\HTML\Extractor\PatternBasedExtractor as PatternBasedHTMLExtractor;
use ReliqArts\StyleImporter\HTML\IDExtractor;
use ReliqArts\StyleImporter\HTML\TagExtractor;
use ReliqArts\StyleImporter\Importer\Agent;
use ReliqArts\StyleImporter\Util\Config;
use ReliqArts\StyleImporter\Util\Logger as LoggerContract;

/**
 * Guided Image Service Provider.
 *
 * @codeCoverageIgnore
 */
final class ServiceProvider extends ReliqArtsServiceProvider
{
    protected const CONFIG_KEY = 'styleimporter';
    protected const ASSET_DIRECTORY = __DIR__ . '/..';
    protected const LOGGER_NAME = self::CONFIG_KEY . '-logger';
    protected const LOG_FILENAME = self::CONFIG_KEY;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function boot(): void
    {
        parent::boot();

        $this->addViewComposers();
        $this->handleViews();
    }

    public function register(): void
    {
        $this->configProvider = new Config(
            new ReliqArtsConfigProvider(
                resolve(ConfigRepository::class),
                $this->getConfigKey()
            )
        );

        $this->app->singleton(ClassExtractor::class, PatternBasedHTMLExtractor::class);
        $this->app->singleton(IDExtractor::class, PatternBasedHTMLExtractor::class);
        $this->app->singleton(TagExtractor::class, PatternBasedHTMLExtractor::class);
        $this->app->singleton(Extractor::class, PatternBasedHTMLExtractor::class);
        $this->app->singleton(GeneratorContract::class, RuleSetGenerator::class);
        $this->app->singleton(Importer::class, Agent::class);
        $this->app->singleton(
            ConfigProvider::class,
            function (): ConfigProvider {
                return $this->configProvider;
            }
        );
        $this->app->singleton(
            ProcessorContract::class,
            function (): ProcessorContract {
                return new Processor(
                    resolve(ImportExtractor::class),
                    resolve(MediaBlockExtractor::class),
                    resolve(GeneratorContract::class),
                    resolve(LoggerContract::class)
                );
            }
        );
        $this->app->singleton(
            LoggerContract::class,
            function (): ReliqArtsLoggerContract {
                $logger = new ReliqArtsLogger($this->getLoggerName());
                $logFile = storage_path(sprintf('logs/%s.log', self::LOG_FILENAME));
                $logger->pushHandler(new StreamHandler($logFile, ReliqArtsLogger::DEBUG));

                return $logger;
            }
        );
    }

    private function addViewComposers(): void
    {
        $viewFactory = resolve(ViewFactory::class);
        $viewFactory->composer(
            '*',
            function (View $view): void {
                $view->with('styleImporter', resolve(Importer::class));
                $view->with('styleImporterConfigProvider', resolve(ConfigProvider::class));
                $view->with($this->configProvider->getCurrentViewNameVariableName(), $view->getName());
            }
        );
    }

    private function handleViews(): void
    {
        $configKey = $this->getConfigKey();
        $viewsDirectory = sprintf('%s/resources/views', $this->getAssetDirectory());

        // Load the views...
        $this->loadViewsFrom($viewsDirectory, $configKey);

        // Allow publishing view files, with tag: views
        $this->publishes([
            $viewsDirectory => base_path(sprintf('resources/views/vendor/%s', $configKey)),
        ], sprintf('%s-views', $configKey));
    }
}
