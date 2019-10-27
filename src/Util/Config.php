<?php

declare(strict_types=1);

namespace ReliqArts\StyleImporter\Util;

use ReliqArts\Contracts\ConfigProvider as ConfigAccessor;
use ReliqArts\StyleImporter\ConfigProvider;

final class Config implements ConfigProvider
{
    private const CONFIG_KEY_VARIABLE_NAME_CURRENT_VIEW_NAME = 'variable_names.current_view_name';
    private const CONFIG_KEY_VARIABLE_NAME_SKIP_STYLE_IMPORT = 'variable_names.skip_style_import';

    private const DEFAULT_VARIABLE_NAME_CURRENT_VIEW_NAME = 'styleImporterCurrentViewName';
    private const DEFAULT_VARIABLE_NAME_SKIP_STYLE_IMPORT = 'styleImporterSkipStyleImport';

    /**
     * @var ConfigAccessor
     */
    private $configAccessor;

    /**
     * ConfigProvider constructor.
     *
     * @param ConfigAccessor $configAccessor
     */
    public function __construct(ConfigAccessor $configAccessor)
    {
        $this->configAccessor = $configAccessor;
    }

    /**
     * @return string
     */
    public function getCurrentViewNameVariableName(): string
    {
        return $this->configAccessor->get(
            self::CONFIG_KEY_VARIABLE_NAME_CURRENT_VIEW_NAME,
            self::DEFAULT_VARIABLE_NAME_CURRENT_VIEW_NAME
        );
    }

    /**
     * @return string
     */
    public function getSkipStyleImportVariableName(): string
    {
        return $this->configAccessor->get(
            self::CONFIG_KEY_VARIABLE_NAME_SKIP_STYLE_IMPORT,
            self::DEFAULT_VARIABLE_NAME_SKIP_STYLE_IMPORT
        );
    }
}
