@php
    /* @var \ReliqArts\StyleImporter\ConfigProvider $styleImporterConfigProvider */
    $skipImportVar = $styleImporterConfigProvider->getSkipStyleImportVariableName();
    $initialHtmlElements = $initialHtmlElements ?? [];
@endphp

@if(!($$skipImportVar ?? false))
    {!! $styleImporter->import($stylesheetUrl, ...$initialHtmlElements) !!}
@endif
